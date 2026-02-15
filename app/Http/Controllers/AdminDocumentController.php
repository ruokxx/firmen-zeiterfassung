<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDocumentController extends Controller
{
    public function index()
    {
        $documents = UserDocument::with(['user', 'creator'])->latest()->paginate(20);
        return view('admin.documents.index', compact('documents'));
    }

    public function create(Request $request)
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $selectedUser = $request->query('user_id');
        return view('admin.documents.create', compact('users', 'selectedUser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // Max 10MB
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        foreach ($request->user_ids as $userId) {
            UserDocument::create([
                'user_id' => $userId,
                'created_by' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $path,
            ]);
        }

        return redirect()->route('admin.documents.index')->with('success', 'Dokument erfolgreich verteilt.');
    }
}
