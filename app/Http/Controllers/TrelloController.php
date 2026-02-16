<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class TrelloController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('trello')->redirect();
    }

    public function callback()
    {
        try {
            $trelloUser = Socialite::driver('trello')->user();

            $user = Auth::user();
            $user->update([
                'trello_id' => $trelloUser->id,
                'trello_token' => $trelloUser->token,
                'trello_token_secret' => $trelloUser->tokenSecret,
            ]);

            return redirect()->route('profile.edit')->with('status', 'trello-connected');
        }
        catch (\Exception $e) {
            return redirect()->route('profile.edit')->with('error', 'Fehler bei der Verbindung mit Trello: ' . $e->getMessage());
        }
    }
}
