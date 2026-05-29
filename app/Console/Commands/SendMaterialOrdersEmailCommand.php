<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\MaterialOrder;
use App\Mail\OpenMaterialOrdersMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendMaterialOrdersEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'material-orders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily email with open material orders to the boss.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if materials feature is enabled system-wide
        $materialsEnabled = Setting::where('key', 'materials_enabled')->value('value') !== '0';
        if (!$materialsEnabled) {
            $this->info('Materials feature is system-wide disabled. Aborting.');
            return 0;
        }

        // 1. Check if the feature is enabled
        $isEnabled = Setting::where('key', 'material_email_enabled')->value('value') === '1';
        if (!$isEnabled) {
            $this->info('Material orders email is disabled in settings.');
            return 0;
        }

        // 2. Fetch the boss email or a fallback generic admin email
        $bossEmail = Setting::where('key', 'boss_email')->value('value');
        if (empty($bossEmail)) {
            $bossEmail = env('MAIL_FROM_ADDRESS'); // Fallback if no boss email is set
        }

        if (empty($bossEmail)) {
            $this->error('No boss email or fallback email configured.');
            Log::warning('SendMaterialOrdersEmailCommand: No boss email configured.');
            return 1;
        }

        // 3. Query all open material orders
        $openOrders = MaterialOrder::with('user')->where('is_ordered', false)->get();

        if ($openOrders->isEmpty()) {
            $this->info('No open material orders found.');
            return 0;
        }

        // 4. Send the email
        try {
            Mail::to($bossEmail)->send(new OpenMaterialOrdersMail($openOrders));
            $this->info('Successfully sent open material orders email to ' . $bossEmail);
        }
        catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            Log::error('SendMaterialOrdersEmailCommand: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
