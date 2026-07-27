@php
    $isEdit = isset($chatQuestion);
    $action = $isEdit ? route('admin.chat-questions.update', $chatQuestion) : route('admin.chat-questions.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<form method="POST" action="{{ $action }}" class="max-w-2xl">
    @csrf
    @method($method)

    <div class="space-y-6">
        <!-- Question Field -->
        <div>
            <label for="question" class="block text-sm font-semibold text-slate-900 mb-2">Question</label>
            <input
                type="text"
                id="question"
                name="question"
                value="{{ old('question', $chatQuestion->question ?? '') }}"
                placeholder="e.g., How do I start a route?"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 transition @error('question') border-red-500 @enderror"
                required
            >
            @error('question')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Answer Field -->
        <div>
            <label for="answer" class="block text-sm font-semibold text-slate-900 mb-2">Answer</label>
            <textarea
                id="answer"
                name="answer"
                rows="8"
                placeholder="Provide a detailed answer..."
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 transition @error('answer') border-red-500 @enderror"
                required
            >{{ old('answer', $chatQuestion->answer ?? '') }}</textarea>
            @error('answer')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Order Field -->
        <div>
            <label for="order" class="block text-sm font-semibold text-slate-900 mb-2">Display Order</label>
            <input
                type="number"
                id="order"
                name="order"
                value="{{ old('order', $chatQuestion->order ?? 0) }}"
                min="0"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 transition @error('order') border-red-500 @enderror"
                required
            >
            <p class="mt-1 text-xs text-slate-500">Lower numbers appear first in the chat widget</p>
            @error('order')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Active Status -->
        <div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    {{ old('is_active', $chatQuestion->is_active ?? true) ? 'checked' : '' }}
                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                >
                <span class="text-sm font-semibold text-slate-900">Active</span>
            </label>
            <p class="mt-1 text-xs text-slate-500">Only active questions will be displayed in the chat widget</p>
        </div>
    </div>

    <!-- Buttons -->
    <div class="mt-8 flex items-center gap-3">
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-700 to-cyan-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/10 hover:from-blue-800 hover:to-cyan-700 transition"
        >
            {{ $isEdit ? 'Update Question' : 'Create Question' }}
        </button>
        <a href="{{ route('admin.chat-questions.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition"
        >
            Cancel
        </a>
    </div>
</form>
