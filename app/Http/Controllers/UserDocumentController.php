<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserDocumentController extends Controller
{
    public function update(Request $request, UserDocument $document)
    {
        // Ensure user owns the document
        if ($document->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'response_file' => 'nullable|file|max:10240',
            'is_completed' => 'boolean',
        ]);

        if ($request->hasFile('response_file')) {
            // Delete old file if exists
            if ($document->response_file_path) {
                Storage::delete('public/' . $document->response_file_path);
            }
            $path = $request->file('response_file')->store('document_responses', 'public');
            $document->response_file_path = $path;
        }

        if ($request->has('is_completed')) {
            $document->is_completed = $request->boolean('is_completed');
        }

        $document->save();

        return back()->with('success', 'Dokument aktualisiert.');
    }

    public function download(UserDocument $document)
    {
        if ($document->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        return Storage::download('public/' . $document->file_path, $document->title);
    }

    public function downloadResponse(UserDocument $document)
    {
        if ($document->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        if (!$document->response_file_path) {
            abort(404);
        }

        return Storage::download('public/' . $document->response_file_path, 'Antwort_' . $document->title);
    }
}
