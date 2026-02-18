<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminConstructionSiteController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $search = $request->input('search');
        $siteDetails = collect();

        if ($search) {
            // Find construction sites matching the search term
            $sites = \App\Models\ConstructionSite::where('name', 'like', "%{$search}%")->with(['timeEntries.workDay.user', 'timeEntries.workDay'])->get();

            foreach ($sites as $site) {
                foreach ($site->timeEntries as $entry) {
                    $siteDetails->push([
                        'date' => $entry->workDay->date,
                        'user_name' => $entry->workDay->user->name,
                        'site_name' => $site->name,
                        'hours' => $entry->hours,
                    ]);
                }
            }

            // Sort by date descending
            $siteDetails = $siteDetails->sortByDesc('date');
        }

        return view('admin.construction_sites.index', compact('siteDetails', 'search'));
    }
}
