<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatQuestion;
use Illuminate\Http\Request;

class ChatQuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->is_admin, 403);
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chatQuestions = ChatQuestion::orderBy('order')->paginate(20);
        return view('admin.chat-questions.index', compact('chatQuestions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.chat-questions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        ChatQuestion::create($validated);

        return redirect()->route('admin.chat-questions.index')
            ->with('success', 'Chat question created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ChatQuestion $chatQuestion)
    {
        return view('admin.chat-questions.show', compact('chatQuestion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChatQuestion $chatQuestion)
    {
        return view('admin.chat-questions.edit', compact('chatQuestion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChatQuestion $chatQuestion)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $chatQuestion->update($validated);

        return redirect()->route('admin.chat-questions.index')
            ->with('success', 'Chat question updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChatQuestion $chatQuestion)
    {
        $chatQuestion->delete();

        return redirect()->route('admin.chat-questions.index')
            ->with('success', 'Chat question deleted successfully!');
    }
}
