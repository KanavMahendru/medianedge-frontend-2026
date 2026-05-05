<div class="section">
    <div class="section-head" style="margin-bottom:15px;">
        <div class="section-title">Recent Developments</div>
        <div class="section-meta">Updated 2 hours ago</div>
    </div>

    <div class="dev-grid">
        @foreach($recentDevelopments as $dev)
        <div class="dev-card">
            <div class="dev-header">
                <div class="dev-sources">
                    @foreach($dev['sources_icons'] ?? [] as $icon)
                        <div class="src-dot" style="background:{{ $icon['bg'] }}; color:{{ $icon['color'] }};">
                            {{ $icon['char'] }}
                        </div>
                    @endforeach
                    <span class="dev-time">{{ $dev['time'] }}</span>
                </div>
            </div>
            
            <div class="dev-title">
                {{ $dev['title'] }}
            </div>
            
            <div class="dev-divider"></div>
 
            <div class="dev-body">
                {{ $dev['content'] }}
            </div>
        </div>
        @endforeach
    </div>
</div>
