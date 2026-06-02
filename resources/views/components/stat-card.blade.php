@props([
    'title',
    'value',
    'icon',
    'theme' => 'c1', // c1, c2, c3, c4, c5, or blue, green, purple, orange, pink
    'trend' => '',
    'trendType' => 'up', // up, down
    'footerText' => '',
    'progress' => '',
    'valueId' => '',
    'subId' => '',
    'subClass' => '', // e.g. 'ok', 'warn'
    'sparklineId' => '',
    'valueClass' => '',
    'delayClass' => '', // d1, d2, d3, d4, d5
    'clickable' => false,
])

<div {{ $attributes->merge([
    'class' => 'stat-card ' . $theme . ($delayClass ? ' fade-in ' . $delayClass : '') . ($clickable ? ' clickable' : '')
]) }}>
    <div class="stat-header">
        <div class="stat-icon {{ $theme }}"><i class="fas fa-{{ $icon }}"></i></div>
        @if(isset($trendSlot))
            {{ $trendSlot }}
        @elseif($trend)
            <div class="stat-trend {{ $trendType }}">
                <i class="fas fa-arrow-{{ $trendType == 'up' ? 'up' : 'down' }}"></i> {{ $trend }}
            </div>
        @endif
    </div>
    <div class="stat-value {{ $valueClass }}" @if($valueId) id="{{ $valueId }}" @endif>{{ $value }}</div>
    <div class="stat-label">{{ $title }}</div>
    
    @if($footerText || $sparklineId || $progress)
        <div class="stat-footer">
            <span @if($subId) id="{{ $subId }}" @endif class="{{ $subClass }}">{{ $footerText }}</span>
            @if($sparklineId)
                <div class="kpi-sparkline" id="{{ $sparklineId }}"></div>
            @endif
            @if($progress)
                <div class="stat-mini-bar">
                    @php
                        $progressGradients = [
                            'c1' => 'linear-gradient(90deg,var(--info),var(--primary))',
                            'c2' => 'linear-gradient(90deg,var(--secondary),var(--cyan))',
                            'c3' => 'linear-gradient(90deg,var(--purple),var(--pink))',
                            'c4' => 'linear-gradient(90deg,var(--orange),var(--warning))',
                            'c5' => 'linear-gradient(90deg,var(--pink),var(--purple))',
                            'blue' => 'linear-gradient(90deg,var(--info),var(--primary))',
                            'green' => 'linear-gradient(90deg,var(--secondary),var(--cyan))',
                            'purple' => 'linear-gradient(90deg,var(--purple),var(--pink))',
                            'orange' => 'linear-gradient(90deg,var(--orange),var(--warning))',
                            'pink' => 'linear-gradient(90deg,var(--pink),var(--purple))',
                        ];
                        $gradient = $progressGradients[$theme] ?? 'var(--primary)';
                    @endphp
                    <div class="stat-mini-fill" style="width:{{ $progress }}; background:{{ $gradient }}"></div>
                </div>
            @endif
        </div>
    @elseif($subClass || $subId)
        <div class="stat-sub {{ $subClass }}" @if($subId) id="{{ $subId }}" @endif>—</div>
    @endif
</div>
