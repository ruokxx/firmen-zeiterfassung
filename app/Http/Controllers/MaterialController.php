<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialTransaction;
use App\Models\Setting;
use App\Mail\LowStockAlertMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaterialController extends Controller
{
    /**
     * Display the 'Lager' view for all users.
     */
    public function index()
    {
        $materials = Material::orderBy('name')->get();
        return view('materials.index', compact('materials'));
    }

    /**
     * Display the 'Materialverwaltung' view for admins/chefs/materialwarts.
     */
    public function manage()
    {
        $this->authorizeManagement();
        $materials = Material::orderBy('name')->get();
        // Get the target email
        $lowStockEmail = Setting::where('key', 'low_stock_email_address')->value('value') ?? '';
        $dailyReportEnabled = Setting::where('key', 'material_daily_report_enabled')->value('value') === '1';
        $dailyReportTime = Setting::where('key', 'material_daily_report_time')->value('value') ?? '18:00';
        return view('materials.manage', compact('materials', 'lowStockEmail', 'dailyReportEnabled', 'dailyReportTime'));
    }

    /**
     * Store a new material.
     */
    public function store(Request $request)
    {
        $this->authorizeManagement();
        $request->validate([
            'name' => 'required|string|max:255|unique:materials,name',
            'stock_count' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        Material::create($request->all());

        return redirect()->back()->with('success', 'Material erfolgreich hinzugefügt.');
    }

    /**
     * Update an existing material.
     */
    public function update(Request $request, Material $material)
    {
        $this->authorizeManagement();
        $request->validate([
            'name' => 'required|string|max:255|unique:materials,name,' . $material->id,
            'stock_count' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        $material->update($request->all());

        return redirect()->back()->with('success', 'Material erfolgreich aktualisiert.');
    }

    /**
     * Delete a material.
     */
    public function destroy(Material $material)
    {
        $this->authorizeManagement();
        $material->delete();
        return redirect()->back()->with('success', 'Material erfolgreich gelöscht.');
    }

    /**
     * Book/Take or Add material stock from the public Lager view.
     */
    public function transaction(Request $request, Material $material)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:added,taken',
        ]);

        $numQuantity = (int)$request->quantity;

        // Check Roles for Action
        if ($request->type === 'taken' && auth()->user()->role === 'azubi') {
            return redirect()->back()->with('error', 'Azubis dürfen selbst keine Materialien entnehmen.');
        }

        if ($request->type === 'added' && !(auth()->user()->is_admin || auth()->user()->is_chef || auth()->user()->is_materialwart)) {
            return redirect()->back()->with('error', 'Nur Chefs und Materialwarte dürfen Materialien auffüllen.');
        }

        DB::beginTransaction();
        try {
            $oldStock = $material->stock_count;

            if ($request->type === 'taken') {
                if ($numQuantity > $oldStock) {
                    return redirect()->back()->with('error', 'Nicht genügend Bestand für Material: ' . $material->name);
                }
                $material->stock_count -= $numQuantity;
            }
            else {
                $material->stock_count += $numQuantity;
            }

            $material->save();

            // Log Transaction
            MaterialTransaction::create([
                'material_id' => $material->id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'quantity' => $numQuantity,
            ]);

            DB::commit();

            // Trigger Email and Order if type is taken and stock drops specifically across the threshold
            if ($request->type === 'taken' && $oldStock > $material->low_stock_threshold && $material->stock_count <= $material->low_stock_threshold) {
                $this->sendLowStockAlert($material);

                // Automatically create a MaterialOrder if one doesn't exist
                $existingOrder = \App\Models\MaterialOrder::where('item_name', $material->name)
                    ->where('is_ordered', false)
                    ->exists();

                if (!$existingOrder) {
                    \App\Models\MaterialOrder::create([
                        // Assigning to System/Admin or the User who triggered it?
                        // Usually the user who initiated the drop is fine.
                        'user_id' => auth()->id(),
                        'item_name' => $material->name,
                    ]);
                }
            }

            $action = $request->type === 'taken' ? 'entnommen' : 'hinzugefügt';
            return redirect()->back()->with('success', "Erfolgreich $numQuantity Stück $action.");

        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Fehler bei der Buchung: ' . $e->getMessage());
        }
    }

    /**
     * Display Statistics view for admins/chefs/materialwarts.
     */
    public function stats()
    {
        $this->authorizeManagement();

        $month = request('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        // Get most taken materials this month
        $stats = MaterialTransaction::with('material')
            ->where('type', 'taken')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->select('material_id', DB::raw('SUM(quantity) as total_taken'))
            ->groupBy('material_id')
            ->orderByDesc('total_taken')
            ->get();

        $recentTransactions = MaterialTransaction::with(['material', 'user'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('materials.stats', compact('stats', 'month', 'recentTransactions'));
    }

    /**
     * Clear all material statistics (accessible only for super admins).
     */
    public function clearStats()
    {
        $user = auth()->user();
        if (!$user->is_super_admin) {
            abort(403, 'Nur Server-Admins können die Statistiken zurücksetzen.');
        }

        // Delete all transactions to reset stats
        MaterialTransaction::truncate();

        // Optional: Do we want to reset stock counts to 0? Usually not, but if requested they could be. 
        // For now, truncating transactions will clear the 'Most taken' and 'Recent Transactions' lists.

        return redirect()->back()->with('success', 'Material Statistiken wurden erfolgreich zurückgesetzt.');
    }

    /**
     * Update global material settings.
     */
    public function updateSettings(Request $request)
    {
        $user = auth()->user();
        if (!$user->is_admin && !$user->is_chef) {
            abort(403, 'Nur Chefs und Admins dürfen die Einstellungen bearbeiten.');
        }

        $request->validate([
            'low_stock_email_address' => 'nullable|email',
            'material_daily_report_enabled' => 'nullable|boolean',
            'material_daily_report_time' => 'nullable|date_format:H:i',
            'material_reminder_time' => 'nullable|date_format:H:i',
        ]);

        Setting::updateOrCreate(
        ['key' => 'low_stock_email_address'],
        ['value' => $request->low_stock_email_address]
        );

        Setting::updateOrCreate(
        ['key' => 'material_daily_report_enabled'],
        ['value' => $request->has('material_daily_report_enabled') ? '1' : '0']
        );

        Setting::updateOrCreate(
        ['key' => 'material_daily_report_time'],
        ['value' => $request->material_daily_report_time ?? '18:00']
        );

        Setting::updateOrCreate(
        ['key' => 'material_reminder_time'],
        ['value' => $request->material_reminder_time ?? '17:00']
        );

        return redirect()->back()->with('success', 'Einstellungen erfolgreich gespeichert.');
    }

    /**
     * Send a test email for material settings.
     */
    public function sendTestEmail()
    {
        $user = auth()->user();
        if (!$user->is_admin && !$user->is_chef) {
            abort(403, 'Nur Chefs und Admins dürfen die Einstellungen bearbeiten.');
        }

        $targetEmail = Setting::where('key', 'low_stock_email_address')->value('value');

        if (empty($targetEmail)) {
            return redirect()->back()->with('error', 'Bitte zuerst eine E-Mail-Adresse eintragen und speichern, bevor du testest.');
        }

        try {
            // Sende den Tagesbericht als Test
            $transactions = \App\Models\MaterialTransaction::with(['user', 'material'])
                ->orderByDesc('created_at')
                ->limit(3)
                ->get();

            $todayStr = \Carbon\Carbon::today()->format('d.m.Y') . ' (TEST-E-MAIL)';
            Mail::to($targetEmail)->send(new \App\Mail\DailyMaterialReportMail($transactions, $todayStr));

            return redirect()->back()->with('success', 'Test-E-Mail (Tagesbericht-Format) wurde erfolgreich an ' . $targetEmail . ' versendet.');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Fehler beim Senden der Test-E-Mail: ' . $e->getMessage());
        }
    }

    /**
     * Helper to send low stock alert.
     */
    private function sendLowStockAlert(Material $material)
    {
        $email = Setting::where('key', 'low_stock_email_address')->value('value');
        if ($email) {
            try {
                Mail::to($email)->send(new LowStockAlertMail($material));
            }
            catch (\Exception $e) {
                // Log failure silently so it doesn't interrupt user action
                \Illuminate\Support\Facades\Log::error('Low stock email failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Helper to authorize only management roles.
     */
    private function authorizeManagement()
    {
        $user = auth()->user();
        if (!$user->is_admin && !$user->is_chef && !$user->is_materialwart) {
            abort(403, 'Unauthorized access.');
        }
    }
}
