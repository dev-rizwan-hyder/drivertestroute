<!-- Chat Widget Component -->
<div id="chat-widget-container" class="fixed bottom-6 right-6 z-40">
    <!-- Chat Button -->
    <button
        id="chat-toggle-btn"
        type="button"
        class="group relative flex h-16 w-16 sm:h-[88px] sm:w-[88px] items-center justify-center rounded-full bg-white p-1 border-[3px] border-blue-500/40 shadow-2xl shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-105 active:scale-95 transition-all duration-300 overflow-hidden"
        aria-label="Open chat"
        title="Ask a question"
    >
        <img src="{{ asset('images/chat_logo.jpeg') }}" alt="Chat Support" class="h-full w-full rounded-full object-cover scale-125 transform group-hover:scale-135 transition-transform duration-300">
        <span class="absolute top-1 right-1 flex h-4.5 w-4.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4.5 w-4.5 bg-emerald-500 border-2 border-white"></span>
        </span>
    </button>

    <!-- Chat Box -->
    <div
        id="chat-box"
        class="absolute bottom-full mb-3 right-0 hidden w-96 max-w-[calc(100vw-2rem)] rounded-2xl border border-slate-200 bg-white shadow-2xl flex flex-col max-h-96 sm:max-h-[550px] z-50"
    >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 bg-gradient-to-r from-blue-600 to-cyan-500 px-5 py-4 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/chat_logo.jpeg') }}" alt="Chat Logo" class="h-10 w-10 rounded-full border-2 border-white/40 object-cover shadow-sm shrink-0">
                <div>
                    <h3 class="text-lg font-bold text-white leading-snug">Help & FAQ</h3>
                    <p class="text-xs text-blue-100">We're here to help</p>
                </div>
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
                            class="chat-question-btn group w-full flex items-center justify-between gap-3 text-left rounded-lg border border-slate-200 bg-white p-3 hover:border-blue-300 hover:bg-blue-50 transition duration-200 cursor-pointer"
                        >
                            <span class="text-sm font-semibold text-slate-900 leading-snug">{{ $question->question }}</span>
                            <svg class="h-4 w-4 text-slate-400 shrink-0 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Answer (Hidden by default) -->
                        <div class="chat-answer hidden mt-2 border-l-2 border-blue-500 bg-blue-50 rounded-r-lg p-3">
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
            max-width: 384px;
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
