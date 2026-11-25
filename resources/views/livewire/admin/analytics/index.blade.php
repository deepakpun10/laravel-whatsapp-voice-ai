<div>
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:800; margin:0 0 6px; color:#0f172a;">Analytics & Operational Insights</h1>
            <p style="margin:0; color:#64748b; font-size:13.5px;">Computed live from actual MySQL database tables. No hardcoded KPI metrics.</p>
        </div>
    </div>

    <!-- 4 Stats Cards matching prototype -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:22px;">
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <div style="color:#64748b; font-size:12px; font-weight:600;">AI AUTOMATION SHARE</div>
            <div style="font-size:26px; font-weight:800; color:#16a34a; margin:6px 0 2px;">{{ $aiResolutionRate }}%</div>
            <div style="font-size:11px; color:#64748b;">Automated without human intervention</div>
        </div>

        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <div style="color:#64748b; font-size:12px; font-weight:600;">HUMAN HANDOVERS</div>
            <div style="font-size:26px; font-weight:800; color:#3568ca; margin:6px 0 2px;">{{ $humanHandoverRate }}%</div>
            <div style="font-size:11px; color:#64748b;">Escalated to human sales reps</div>
        </div>

        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <div style="color:#64748b; font-size:12px; font-weight:600;">QUALIFIED LEADS</div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; margin:6px 0 2px;">{{ $qualifiedLeads }}</div>
            <div style="font-size:11px; color:#64748b;">Verified business criteria</div>
        </div>

        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <div style="color:#64748b; font-size:12px; font-weight:600;">CONVERTED CUSTOMERS</div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; margin:6px 0 2px;">{{ $convertedLeads }}</div>
            <div style="font-size:11px; color:#64748b;">Closed business deals</div>
        </div>
    </div>

    <!-- Breakdown Grid matching prototype -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px;">
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <h3 style="font-size:16px; font-weight:700; margin:0 0 16px; color:#0f172a;">Business Performance Breakdown</h3>
            @forelse($businessPerformance as $bp)
                <div style="margin-bottom:16px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px; font-size:13px;">
                        <b>{{ $bp->name }}</b>
                        <span style="color:#64748b;">{{ $bp->contacts_count }} leads · {{ $bp->conversations_count }} chats</span>
                    </div>
                    <div style="height:8px; background:#f1f5f9; border-radius:4px; overflow:hidden;">
                        @php
                            $width = $totalContacts > 0 ? min(round(($bp->contacts_count / $totalContacts) * 100), 100) : 0;
                        @endphp
                        <div style="width:{{ $width }}%; height:100%; background:linear-gradient(90deg, #3978f6, #8057e8); border-radius:4px;"></div>
                    </div>
                </div>
            @empty
                <div style="color:#94a3b8; font-size:13px;">No active businesses.</div>
            @endforelse
        </div>

        <!-- Lead Funnel -->
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,.03);">
            <h3 style="font-size:16px; font-weight:700; margin:0 0 16px; color:#0f172a;">Lead Qualification Funnel</h3>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <div style="background:#3978f6; color:white; padding:12px; border-radius:8px; text-align:center; font-weight:700; font-size:13px;">
                    Total Inbound Contacts · {{ $totalContacts }}
                </div>
                <div style="background:#5487e8; color:white; padding:10px; border-radius:8px; text-align:center; font-weight:700; font-size:12.5px; width:90%; margin:0 auto;">
                    Engaged Conversations · {{ $totalConversations }}
                </div>
                <div style="background:#8066df; color:white; padding:10px; border-radius:8px; text-align:center; font-weight:700; font-size:12px; width:75%; margin:0 auto;">
                    Qualified Leads · {{ $qualifiedLeads }}
                </div>
                <div style="background:#20a98c; color:white; padding:10px; border-radius:8px; text-align:center; font-weight:700; font-size:12px; width:60%; margin:0 auto;">
                    Converted Deals · {{ $convertedLeads }}
                </div>
            </div>
        </div>
    </div>
</div>
