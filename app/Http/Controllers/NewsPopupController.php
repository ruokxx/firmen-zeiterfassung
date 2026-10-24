<?php

namespace App\Http\Controllers;

use App\Models\NewsPopup;
use App\Models\NewsPopupRead;
use Illuminate\Http\Request;

class NewsPopupController extends Controller
{
    public function dismiss(Request $request, NewsPopup $newsPopup)
    {
        $user = auth()->user();

        if ($user) {
            NewsPopupRead::updateOrCreate(
                [
                    'news_popup_id' => $newsPopup->id,
                    'user_id' => $user->id,
                ],
                [
                    'dismissed_at' => now(),
                    'last_seen_date' => today(),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Popup als gelesen markiert.',
        ]);
    }
}
