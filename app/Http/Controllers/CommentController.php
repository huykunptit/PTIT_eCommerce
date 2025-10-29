<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with(['post','user'])->latest()->paginate(10);
        return view('admin.comment.index', compact('comments'));
    }

    public function edit(Comment $comment)
    {
        return view('admin.comment.edit', compact('comment'));
    }

    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $comment->update($validated);

        return redirect()->route('comment.index')->with('success', 'Comment updated successfully!');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->route('comment.index')->with('success', 'Comment deleted successfully!');
    }
}


