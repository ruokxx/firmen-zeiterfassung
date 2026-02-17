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
        // 1. Get unique documents (paginated) based on file_path
        // Using distinct IDs might be better if we had a Document model, but here unique upload = unique file_path
        $documents = UserDocument::select('file_path', 'title', 'created_at', 'description')
            ->groupBy('file_path', 'title', 'created_at', 'description')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // 2. Fetch assignments for these documents
        $paths = $documents->pluck('file_path');
        $assignments = UserDocument::whereIn('file_path', $paths)
            ->with(['user', 'creator'])
            ->get()
            ->groupBy('file_path');

        return view('admin.documents.index', compact('documents', 'assignments'));
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
