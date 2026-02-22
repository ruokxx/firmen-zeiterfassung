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

        $search = $request->input('search'); // Baustelle
        $userSearch = $request->input('user_search'); // Mitarbeiter
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $siteDetails = collect();

        // Only search if at least one filter is provided
        if ($search || $userSearch || $dateFrom || $dateTo) {
            $query = \App\Models\TimeEntry::with(['workDay.user', 'constructionSite']);

            // Filter by Construction Site
            if ($search) {
                $keywords = array_filter(explode(' ', $search));
                $query->whereHas('constructionSite', function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $lowerKeyword = mb_strtolower($keyword);
                        $q->whereRaw('LOWER(name) LIKE ?', ["%{$lowerKeyword}%"]);
                    }
                });
            }

            // Filter by User
            if ($userSearch) {
                $keywords = array_filter(explode(' ', $userSearch));
                $query->whereHas('workDay.user', function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $lowerKeyword = mb_strtolower($keyword);
                        $q->where(function ($sub) use ($lowerKeyword) {
                                    $sub->whereRaw('LOWER(name) LIKE ?', ["%{$lowerKeyword}%"])
                                        ->orWhereRaw('LOWER(first_name) LIKE ?', ["%{$lowerKeyword}%"])
                                        ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$lowerKeyword}%"]);
                                }
                                );
                            }
                        });
            }

            // Filter by Date
            if ($dateFrom || $dateTo) {
                $query->whereHas('workDay', function ($q) use ($dateFrom, $dateTo) {
                    if ($dateFrom) {
                        $q->whereDate('date', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $q->whereDate('date', '<=', $dateTo);
                    }
                });
            }

            // Get the filtered entries
            $timeEntries = $query->get();

            foreach ($timeEntries as $entry) {
                if ($entry->workDay && $entry->workDay->user && $entry->constructionSite) {
                    $siteDetails->push([
                        'date' => $entry->workDay->date,
                        'user_name' => $entry->workDay->user->name,
                        'site_name' => $entry->constructionSite->name,
                        'hours' => $entry->hours,
                    ]);
                }
            }

            // Sort by date descending
            $siteDetails = $siteDetails->sortByDesc('date');
        }

        return view('admin.construction_sites.index', compact(
            'siteDetails', 'search', 'userSearch', 'dateFrom', 'dateTo'
        ));
    }
}
