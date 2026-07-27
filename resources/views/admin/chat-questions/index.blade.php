@extends('layouts.admin')

@section('title', 'Chat Questions')

@section('content')
    <section>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Chat Management</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900 tracking-tight">Chat Questions</h1>
                <p class="mt-2 text-sm text-slate-500">Manage FAQ questions and answers displayed in the chat widget.</p>
            </div>

            <a href="{{ route('admin.chat-questions.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-700 to-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-blue-500/10 hover:from-blue-800 hover:to-cyan-700 transition">
                Add Question
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/70 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Order</th>
                            <th class="px-5 py-3">Question</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Created</th>
                            <th class="px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($chatQuestions as $question)
                            <tr class="hover:bg-slate-50/40 transition">
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-blue-50 text-xs font-bold text-blue-700">
                                        {{ $question->order }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900 max-w-xs truncate">{{ $question->question }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    @if($question->is_active)
                                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-600/10">Active</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-600 text-xs">
                                    {{ $question->created_at?->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.chat-questions.edit', $question) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.chat-questions.destroy', $question) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-semibold text-red-700 shadow-sm hover:bg-red-50 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                                    No chat questions yet. <a href="{{ route('admin.chat-questions.create') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Create one</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $chatQuestions->links() }}
        </div>
    </section>
@endsection
