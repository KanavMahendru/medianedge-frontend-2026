<div class="section">
    <div class="section-head" style="margin-bottom:15px;">
        <div class="section-title">Recent Developments</div>
        <div class="section-meta">Updated 2 hours ago</div>
    </div>

    <div class="dev-grid" style="display:grid; grid-template-columns: repeat(3, 1fr); gap:15px;">
        @foreach($recentDevelopments as $dev)
        <div class="dev-card" style="background:var(--bg-panel); border:1px solid var(--border-main); border-radius:12px; padding:16px; display:flex; flex-direction:column; gap:10px; transition: all 0.2s ease;">
            <div class="dev-header" style="display:flex; align-items:center; justify-content:space-between;">
                <div class="dev-sources" style="display:flex; align-items:center; gap:4px;">
                    @foreach($dev['sources_icons'] ?? [] as $icon)
                        <div class="src-dot" style="width:14px; height:14px; border-radius:50%; background:{{ $icon['bg'] }}; color:{{ $icon['color'] }}; font-size:8px; display:flex; align-items:center; justify-content:center; border:1px solid var(--bg-panel); margin-right:-6px;">
                            {{ $icon['char'] }}
                        </div>
                    @endforeach
                    <span style="font-size:11px; color:var(--text-muted); margin-left:12px;">{{ $dev['time'] }}</span>
                </div>
            </div>
            
            <div class="dev-title" style="font-size:14px; font-weight:600; color:var(--text-main); line-height:1.4; margin-top:4px;">
                {{ $dev['title'] }}
            </div>
            
            <div style="width:100%; height:1px; background:var(--border-light); opacity:0.3; margin:2px 0;"></div>

            <div class="dev-body" style="font-size:12px; color:var(--text-muted); line-height:1.6; display:-webkit-box; -webkit-line-clamp:4; -webkit-box-orient:vertical; overflow:hidden;">
                {{ $dev['content'] }}
            </div>
        </div>
        @endforeach
    </div>
</div>
