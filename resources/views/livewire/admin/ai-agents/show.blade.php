<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <a href="{{ route('admin.ai-agents.index') }}" style="color:#3978f6; font-size:13px; text-decoration:none; font-weight:600;">
                ← Back to AI Agents
            </a>
            <h1 style="font-size:24px; font-weight:800; margin:6px 0 4px; color:#0f172a;">{{ $agent->name }}</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">
                Business: <b>{{ $agent->business->name }}</b> · Model: {{ $agent->model }}
            </p>
        </div>
        <div style="display:flex; gap:10px;">
            <button
                wire:click="toggle"
                style="padding:8px 14px; border:1px solid #cbd5e1; background:white; color:#334155; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;"
            >
                {{ $agent->is_active ? 'Pause Agent' : 'Activate Agent' }}
            </button>
            <a href="{{ route('admin.ai-agents.edit', $agent) }}" style="padding:8px 16px; background:#3978f6; color:white; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                Edit Agent
            </a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1.2fr; gap:20px;">
        <!-- Left Column: Agent Configuration Details -->
        <div style="display:flex; flex-direction:column; gap:20px;">
            <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
                <h3 style="font-size:15px; font-weight:700; margin:0 0 12px; color:#0f172a;">Agent Profile</h3>
                <div style="font-size:12.5px; color:#475569; line-height:1.6; margin-bottom:14px;">
                    <div>Tone: <b>{{ ucfirst($agent->personality_tone) }}</b></div>
                    <div>Unknown Query Policy: <b>{{ ucfirst(str_replace('_', ' ', $agent->unknown_query_policy)) }}</b></div>
                    <div>Handover Policy: <b>{{ ucfirst(str_replace('_', ' ', $agent->human_handover_policy)) }}</b></div>
                </div>

                <div style="border-top:1px solid #f1f5f9; padding-top:12px; margin-bottom:14px;">
                    <div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:4px;">Instructions:</div>
                    <p style="background:#f8fafc; border-radius:8px; padding:10px; font-size:12px; color:#334155; margin:0; line-height:1.5; white-space:pre-wrap;">{{ $agent->system_instructions ?: 'Default sales agent instructions.' }}</p>
                </div>

                <div style="border-top:1px solid #f1f5f9; padding-top:12px;">
                    <div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">Assigned Knowledge Bases:</div>
                    @forelse($agent->knowledgeBases as $kb)
                        <div style="background:#edf3ff; color:#3568ca; padding:4px 8px; border-radius:6px; font-size:12px; font-weight:600; margin-bottom:4px;">
                            📄 {{ $kb->name }}
                        </div>
                    @empty
                        <div style="color:#94a3b8; font-size:12px;">No specific KB assigned (queries general business knowledge).</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Interactive Agent Playground -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03); display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <h3 style="font-size:15px; font-weight:700; margin:0 0 6px; color:#0f172a;">Live Agent Playground & RAG Inspector</h3>
                <p style="color:#64748b; font-size:12px; margin:0 0 12px;">
                    Simulate customer queries. Inspect strictly retrieved RAG chunks and real AI responses.
                </p>

                <textarea
                    wire:model="testQuery"
                    rows="3"
                    placeholder="Ask a question (e.g. What is your pricing for ecommerce websites? or What is the wholesale price for 10 quintals of garlic?)..."
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; font-size:12.5px; margin-bottom:12px;"
                ></textarea>

                <button
                    wire:click="testAgent"
                    style="width:100%; background:#3978f6; color:white; border:0; border-radius:8px; padding:9px; font-size:13px; font-weight:700; cursor:pointer;"
                >
                    Run Agent Simulation
                </button>

                @if($testResult)
                    <div style="margin-top:16px; border:1px solid #e2e8f0; border-radius:10px; padding:14px; background:#f8fafc;">
                        <div style="font-size:11.5px; color:#64748b; margin-bottom:4px;">
                            AI Reply (Generated via {{ $testResult['provider'] }}):
                        </div>
                        <div style="font-size:13px; color:#0f172a; line-height:1.5; font-weight:500; background:white; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:12px;">
                            {{ $testResult['reply'] }}
                        </div>

                        <div style="font-size:11.5px; font-weight:700; color:#475569; margin-bottom:6px;">
                            Scoped RAG Chunks Retrieved ({{ count($testResult['retrieved_chunks']) }}):
                        </div>
                        @forelse($testResult['retrieved_chunks'] as $chunk)
                            <div style="background:white; border:1px solid #e2e8f0; border-radius:6px; padding:8px 10px; margin-bottom:6px; font-size:11.5px;">
                                <div style="color:#3568ca; font-weight:700; margin-bottom:2px;">
                                    {{ $chunk['kb_name'] }} · {{ $chunk['document_title'] }} (Relevance: {{ round($chunk['score'] * 100) }}%)
                                </div>
                                <div style="color:#475569;">"{{ Str::limit($chunk['content'], 120) }}"</div>
                            </div>
                        @empty
                            <div style="color:#94a3b8; font-size:11.5px; font-style:italic;">
                                No relevant chunks found in this business's knowledge base. Agent responds with default fallback policy without guessing.
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>

            <div style="margin-top:16px; border-top:1px solid #f1f5f9; padding-top:10px; font-size:11px; color:#64748b;">
                🛡️ <b>RAG Isolation Guarantee</b>: Only documents belonging to <b>{{ $agent->business->name }}</b> can be retrieved.
            </div>
        </div>
    </div>
</div>
