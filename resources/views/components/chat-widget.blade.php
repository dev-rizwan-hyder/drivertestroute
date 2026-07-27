<!-- Chat Widget Component -->
<div id="chat-widget-container" class="fixed bottom-6 right-6 z-40">
    <!-- Chat Button -->
    <button
        id="chat-toggle-btn"
        type="button"
        class="group flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 hover:-translate-y-1 transition-all duration-200"
        aria-label="Open chat"
        title="Ask a question"
    >
        <svg class="h-6 w-6 text-white group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white opacity-0 group-hover:opacity-100 transition-opacity">
            <span class="inline-block h-2 w-2 rounded-full bg-white animate-pulse"></span>
        </span>
    </button>

    <!-- Chat Box -->
    <div
        id="chat-box"
        class="absolute bottom-20 right-0 hidden w-96 max-w-[calc(100vw-2rem)] rounded-2xl border border-slate-200 bg-white shadow-2xl flex flex-col max-h-96 sm:max-h-[600px]"
    >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 bg-gradient-to-r from-blue-600 to-cyan-500 px-5 py-4 rounded-t-2xl">
            <div>
                <h3 class="text-lg font-bold text-white">Help & FAQ</h3>
                <p class="text-xs text-blue-100 mt-0.5">We're here to help</p>
            </div>
            <button
                id="chat-close-btn"
                type="button"
                class="text-white hover:bg-white/20 rounded-lg p-2 transition"
                aria-label="Close chat"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Messages Container -->
        <div id="chat-messages" class="flex-1 overflow-y-auto space-y-4 p-5 bg-slate-50">
            @php
                $questions = \App\Models\ChatQuestion::getActive();
            @endphp

            @if($questions->isEmpty())
                <div class="text-center py-8">
                    <p class="text-slate-500 text-sm">No questions available yet.</p>
                </div>
            @else
                @foreach($questions as $question)
                    <div class="chat-message-group" data-question-id="{{ $question->id }}">
                        <!-- Question -->
                        <button
                            type="button"
                            class="chat-question-btn w-full text-left rounded-lg border border-slate-200 bg-white p-3 hover:border-blue-300 hover:bg-blue-50 transition duration-200 cursor-pointer"
                        >
                            <p class="text-sm font-semibold text-slate-900">{{ $question->question }}</p>
                            <svg class="h-4 w-4 text-slate-400 mt-2 inline-block float-right transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Answer (Hidden by default) -->
                        <div class="chat-answer hidden mt-2 pl-4 border-l-2 border-blue-500 bg-blue-50 rounded-lg p-3">
                            <p class="text-sm text-slate-700 leading-relaxed">{{ $question->answer }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 text-center rounded-b-2xl">
            <p class="text-xs text-slate-500">
                Can't find an answer? <a href="{{ route('contact') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Contact us</a>
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatToggleBtn = document.getElementById('chat-toggle-btn');
        const chatCloseBtn = document.getElementById('chat-close-btn');
        const chatBox = document.getElementById('chat-box');
        const chatMessages = document.getElementById('chat-messages');

        // Toggle chat box visibility
        chatToggleBtn.addEventListener('click', function() {
            chatBox.classList.toggle('hidden');
        });

        // Close chat box
        chatCloseBtn.addEventListener('click', function() {
            chatBox.classList.add('hidden');
        });

        // Close on background click (optional)
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#chat-widget-container')) {
                chatBox.classList.add('hidden');
            }
        });

        // Handle question clicks to show/hide answers
        const questionBtns = document.querySelectorAll('.chat-question-btn');
        questionBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const messageGroup = this.closest('.chat-message-group');
                const answer = messageGroup.querySelector('.chat-answer');
                
                // Hide all other answers
                document.querySelectorAll('.chat-answer').forEach(a => {
                    if (a !== answer) {
                        a.classList.add('hidden');
                    }
                });

                // Toggle current answer
                answer.classList.toggle('hidden');

                // Scroll to answer
                if (!answer.classList.contains('hidden')) {
                    setTimeout(() => {
                        answer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 100);
                }
            });
        });

        // Prevent closing when clicking inside chat box
        chatBox.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>

<style>
    @media (max-width: 640px) {
        #chat-widget-container {
            bottom: 1rem;
            right: 1rem;
        }

        #chat-box {
            width: calc(100vw - 2rem);
            max-width: 100%;
            max-height: calc(100vh - 120px);
        }
    }

    #chat-messages {
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.5) transparent;
    }

    #chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    #chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    #chat-messages::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.5);
        border-radius: 3px;
    }

    #chat-messages::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.7);
    }
</style>
