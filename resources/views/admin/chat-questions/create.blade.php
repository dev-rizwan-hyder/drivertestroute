@extends('layouts.admin')

@section('title', 'Add Chat Question')

@section('content')
    <section>
        <div class="mb-8">
            <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Chat Management</p>
            <h1 class="mt-2 text-3xl font-black text-slate-900 tracking-tight">Add Chat Question</h1>
            <p class="mt-2 text-sm text-slate-500">Create a new FAQ question to be displayed in the chat widget.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @include('admin.chat-questions.form')
        </div>
    </section>
@endsection
