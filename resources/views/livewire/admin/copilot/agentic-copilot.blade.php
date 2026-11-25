<div>
    <!-- Floating Copilot Launch Trigger Button (Bottom Right) -->
    <div style="position:fixed; bottom:28px; right:28px; z-index:9999;">
        <button
            type="button"
            wire:click="toggleOpen"
            style="background:linear-gradient(135deg, #3978f6 0%, #6366f1 100%); color:white; border:0; padding:12px 20px; border-radius:30px; font-size:13.5px; font-weight:700; cursor:pointer; box-shadow:0 10px 28px rgba(57,120,246,0.35); display:inline-flex; align-items:center; gap:9px; transition:transform .18s ease;"
            onmouseover="this.style.transform='scale(1.04)'"
            onmouseout="this.style.transform='scale(1)'"
            title="Open Pundeer AI Agentic Copilot"
        >
            <span style="font-size:18px;">🤖</span>
            <span>AI Copilot</span>
            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#4ade80; box-shadow:0 0 8px #4ade80;"></span>
        </button>
    </div>

    <!-- Copilot Chat Window Popup / Drawer (Clean Light SaaS Theme) -->
    @if($isOpen)
        <div
            x-data="{
                listening: false,
                recognition: null,
                init() {
                    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
                        this.recognition = new SpeechRec();
                        this.recognition.continuous = false;
                        this.recognition.interimResults = false;
                        this.recognition.lang = 'hi-IN';

                        this.recognition.onresult = (event) => {
                            const transcript = event.results[0][0].transcript;
                            @this.set('inputMessage', transcript);
                            this.listening = false;
                        };

                        this.recognition.onerror = () => { this.listening = false; };
                        this.recognition.onend = () => { this.listening = false; };
                    }

                    window.addEventListener('copilot-speak', (e) => {
                        if ('speechSynthesis' in window && e.detail.text) {
                            window.speechSynthesis.cancel();
                            const utterance = new SpeechSynthesisUtterance(e.detail.text);
                            utterance.lang = 'hi-IN';
                            utterance.rate = 1.0;
                            window.speechSynthesis.speak(utterance);
                        }
                    });

                    this.scrollToBottom();
                },
                scrollToBottom() {
                    this.$nextTick(() => {
                        if (this.$refs.msgContainer) {
                            this.$refs.msgContainer.scrollTop = this.$refs.msgContainer.scrollHeight;
                        }
                    });
                },
                startListening() {
                    if (!this.recognition) {
                        alert('Voice recognition is not supported in this browser. Please use Google Chrome.');
                        return;
                    }
                    if (this.listening) {
                        this.recognition.stop();
                        this.listening = false;
                    } else {
                        this.listening = true;
                        this.recognition.start();
                    }
                }
            }"
            x-effect="scrollToBottom()"
            style="position:fixed; bottom:86px; right:28px; width:450px; max-width:calc(100vw - 36px); height:620px; max-height:calc(100vh - 110px); background:#ffffff; border:1px solid #cbd5e1; border-radius:18px; box-shadow:0 20px 45px rgba(27,43,76,0.18); z-index:9999; display:flex; flex-direction:column; overflow:hidden; font-family:Inter, system-ui, -apple-system, sans-serif;"
        >
            <!-- Header (Light Modern Aesthetic) -->
            <div style="background:#ffffff; border-bottom:1px solid #e2e8f0; padding:14px 18px; display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg, #4387ff, #8a5ce8); display:grid; place-items:center; font-size:17px; color:white;">
                        🤖
                    </div>
                    <div>
                        <div style="font-weight:800; font-size:14px; color:#0f172a; display:flex; align-items:center; gap:6px;">
                            <span>Pundeer AI Copilot</span>
                            <span style="font-size:10px; background:#edf3ff; color:#3568ca; padding:2px 7px; border-radius:12px; font-weight:700; border:1px solid #dbeafe;">Agentic</span>
                        </div>
                        <div style="font-size:11px; color:#64748b;">System Control &amp; Voice Assistant</div>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:8px;">
                    <!-- Voice Speaker Toggle Button -->
                    <button
                        type="button"
                        wire:click="toggleVoice"
                        style="background:#f8fafc; color:{{ $voiceEnabled ? '#16a34a' : '#64748b' }}; border:1px solid #e2e8f0; padding:4px 9px; border-radius:6px; font-size:11.5px; font-weight:700; cursor:pointer;"
                        title="{{ $voiceEnabled ? 'Mute AI Voice' : 'Enable AI Voice' }}"
                    >
                        {{ $voiceEnabled ? '🔊 Voice ON' : '🔇 Muted' }}
                    </button>

                    <!-- Close Button -->
                    <button
                        type="button"
                        wire:click="toggleOpen"
                        style="background:#f8fafc; border:1px solid #e2e8f0; color:#64748b; font-size:14px; border-radius:6px; width:28px; height:28px; display:grid; place-items:center; cursor:pointer;"
                        title="Close Copilot"
                    >
                        ✕
                    </button>
                </div>
            </div>

            <!-- Quick Action Suggestion Pills -->
            <div style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:8px 12px; display:flex; gap:6px; overflow-x:auto; white-space:nowrap;">
                <button
                    type="button"
                    wire:click="sendQuickPrompt('Pundeer Technologies ke lie pura system tyaar kr')"
                    style="background:#ffffff; color:#3568ca; border:1px solid #cbd5e1; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:700; cursor:pointer;"
                >
                    🚀 Setup System
                </button>
                <button
                    type="button"
                    wire:click="sendQuickPrompt('System status check karo')"
                    style="background:#ffffff; color:#334155; border:1px solid #cbd5e1; padding:4px 9px; border-radius:12px; font-size:11px; font-weight:600; cursor:pointer;"
                >
                    ⚡ Health Status
                </button>
                <button
                    type="button"
                    wire:click="sendQuickPrompt('Naya business banao')"
                    style="background:#ffffff; color:#334155; border:1px solid #cbd5e1; padding:4px 9px; border-radius:12px; font-size:11px; font-weight:600; cursor:pointer;"
                >
                    🏢 New Business
                </button>
                <button
                    type="button"
                    wire:click="sendQuickPrompt('Knowledge base status band karo')"
                    style="background:#ffffff; color:#b91c1c; border:1px solid #fecaca; padding:4px 9px; border-radius:12px; font-size:11px; font-weight:600; cursor:pointer;"
                >
                    ⏸️ KB Off/On
                </button>
                <button
                    type="button"
                    wire:click="sendQuickPrompt('Recent leads dikhao')"
                    style="background:#ffffff; color:#15803d; border:1px solid #bbf7d0; padding:4px 9px; border-radius:12px; font-size:11px; font-weight:600; cursor:pointer;"
                >
                    📋 Leads
                </button>
            </div>

            <!-- Chat Messages Container (Light, Clean, Zero Horizontal Scroll) -->
            <div
                x-ref="msgContainer"
                style="flex:1; overflow-y:auto; overflow-x:hidden; padding:16px; display:flex; flex-direction:column; gap:12px; background:#f8fafc;"
            >
                @foreach($messages as $msg)
                    @if($msg['sender'] === 'user')
                        <!-- User Message (Right Side - Vibrant Blue) -->
                        <div style="align-self:flex-end; max-width:85%; background:#3978f6; color:white; padding:10px 14px; border-radius:14px 14px 2px 14px; font-size:12.5px; line-height:1.45; box-shadow:0 3px 10px rgba(57,120,246,0.22); word-break:break-word;">
                            {{ $msg['text'] }}
                            <div style="font-size:9.5px; opacity:0.8; text-align:right; margin-top:4px;">{{ $msg['timestamp'] }}</div>
                        </div>
                    @else
                        <!-- Agent Message (Left Side - Clean White Card) -->
                        <div style="align-self:flex-start; max-width:92%; background:#ffffff; color:#1e293b; border:1px solid #e2e8f0; padding:12px 14px; border-radius:14px 14px 14px 2px; font-size:12.5px; line-height:1.5; box-shadow:0 2px 8px rgba(0,0,0,0.03); word-break:break-word;">
                            <div style="display:flex; align-items:center; gap:6px; margin-bottom:6px; font-size:11px; font-weight:700; color:#3568ca;">
                                <span>🤖 Copilot</span>
                                <span style="color:#94a3b8; font-weight:normal;">• {{ $msg['timestamp'] }}</span>
                            </div>

                            <div style="white-space:pre-line; color:#1e293b;">{{ $msg['text'] }}</div>

                            <!-- INLINE INTERACTIVE FORM FIELDS (When missing details are required) -->
                            @if(!empty($msg['missing_fields']) && ($msg['action_status'] ?? '') === 'pending_input')
                                <div style="margin-top:12px; padding:12px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px;">
                                    <div style="font-size:11px; font-weight:700; color:#334155; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px;">
                                        Required Information
                                    </div>

                                    @foreach($msg['missing_fields'] as $field)
                                        <div style="margin-bottom:10px;">
                                            <label style="display:block; font-size:11px; font-weight:600; color:#475569; margin-bottom:3px;">
                                                {{ $field['label'] }}
                                            </label>

                                            @if($field['type'] === 'text')
                                                <input
                                                    type="text"
                                                    wire:model="inlineFormValues.{{ $msg['id'] }}.{{ $field['key'] }}"
                                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                                    style="width:100%; background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; padding:7px 10px; border-radius:6px; font-size:12px; box-sizing:border-box;"
                                                >
                                            @elseif($field['type'] === 'select')
                                                <select
                                                    wire:model="inlineFormValues.{{ $msg['id'] }}.{{ $field['key'] }}"
                                                    style="width:100%; background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; padding:7px 10px; border-radius:6px; font-size:12px; box-sizing:border-box;"
                                                >
                                                    @foreach($field['options'] as $optVal => $optLabel)
                                                        <option value="{{ $optVal }}">{{ $optLabel }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif($field['type'] === 'checkbox')
                                                <label style="display:flex; align-items:center; gap:8px; font-size:11.5px; color:#334155; cursor:pointer;">
                                                    <input
                                                        type="checkbox"
                                                        wire:model="inlineFormValues.{{ $msg['id'] }}.{{ $field['key'] }}"
                                                        style="width:15px; height:15px;"
                                                    >
                                                    <span>{{ $field['label'] }}</span>
                                                </label>
                                            @endif
                                        </div>
                                    @endforeach

                                    <!-- Error Reason & Step-by-Step Fix (if any) -->
                                    @if(!empty($msg['error_reason']))
                                        <div style="margin-top:8px; padding:8px 10px; background:#fef2f2; border:1px solid #fecaca; border-radius:6px; color:#b91c1c; font-size:11px;">
                                            <b>Problem:</b> {{ $msg['error_reason'] }}
                                            @if(!empty($msg['suggested_fix']))
                                                <div style="margin-top:2px; color:#991b1b;"><b>Ese karein:</b> {{ $msg['suggested_fix'] }}</div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Submit Inline Action Button -->
                                    <button
                                        type="button"
                                        wire:click="executeInlineAction('{{ $msg['id'] }}')"
                                        style="margin-top:8px; width:100%; background:#16a34a; color:white; border:0; padding:8px; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer;"
                                    >
                                        ✓ Execute Action &amp; Submit
                                    </button>
                                </div>
                            @endif

                            @if(($msg['action_status'] ?? '') === 'executed')
                                <div style="margin-top:8px; padding:4px 8px; background:#ecfdf5; border:1px solid #a7f3d0; border-radius:6px; color:#15803d; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:4px;">
                                    ✓ Action Successfully Executed in System
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Listening Indicator -->
            <div x-show="listening" style="background:#fee2e2; color:#b91c1c; padding:7px 14px; font-size:11.5px; font-weight:600; display:flex; align-items:center; gap:8px; border-top:1px solid #fecaca;">
                <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#ef4444; animation:pulse 1s infinite;"></span>
                <span>Sun raha hoon... Kripya bolein (Speaking in Hindi / English)</span>
            </div>

            <!-- Input Bar & Voice Controls (Zero Lag, wire:model instead of wire:model.live) -->
            <div style="background:#ffffff; border-top:1px solid #e2e8f0; padding:12px; display:flex; align-items:center; gap:8px;">
                <!-- Microphone Voice Recognition Button -->
                <button
                    type="button"
                    @click="startListening()"
                    :style="listening ? 'background:#ef4444; color:white; border-color:#ef4444;' : 'background:#f8fafc; color:#64748b; border-color:#cbd5e1;'"
                    style="border:1px solid; width:38px; height:38px; border-radius:8px; display:grid; place-items:center; cursor:pointer; font-size:16px; shrink:0; transition:all .15s;"
                    title="Click to Speak (Bol kar command dein)"
                >
                    🎤
                </button>

                <!-- Input Text Box (High Speed, No per-keystroke server calls) -->
                <input
                    type="text"
                    wire:model="inputMessage"
                    wire:keydown.enter="sendMessage"
                    placeholder="Bolein ya likhein (e.g. 'Pundeer Technologies setup karo')..."
                    style="flex:1; background:#f8fafc; border:1px solid #cbd5e1; color:#0f172a; padding:9px 12px; border-radius:8px; font-size:12.5px; outline:none; transition:border-color .15s;"
                    onfocus="this.style.borderColor='#3978f6'"
                    onblur="this.style.borderColor='#cbd5e1'"
                >

                <!-- Send Button -->
                <button
                    type="button"
                    wire:click="sendMessage"
                    style="background:#3978f6; color:white; border:0; width:38px; height:38px; border-radius:8px; display:grid; place-items:center; cursor:pointer; font-size:15px; shrink:0; transition:background .15s;"
                    onmouseover="this.style.background='#2563eb'"
                    onmouseout="this.style.background='#3978f6'"
                    title="Send Command"
                >
                    ➤
                </button>
            </div>
        </div>
    @endif
</div>
