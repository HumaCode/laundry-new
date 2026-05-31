@props([
    'title',
    'value',
    'icon',
    'trend' => '',
    'trendType' => 'up',
    'footerText' => '',
    'progress' => '',
    'theme' => 'blue',
    'delayClass' => 'd1',
])

@php
    $progressGradients = [
        'blue' => 'linear-gradient(90deg,var(--info),var(--primary))',
        'green' => 'linear-gradient(90deg,var(--secondary),var(--cyan))',
        'purple' => 'linear-gradient(90deg,var(--purple),var(--pink))',
        'orange' => 'linear-gradient(90deg,var(--orange),var(--warning))',
    ];
    $gradient = $progressGradients[$theme] ?? 'var(--primary)';
@endphp

<div class="stat-card {{ $theme }} animate-fade-up {{ $delayClass }} h-100">
    <div class="stat-header">
        <div class="stat-icon {{ $theme }}"><i class="fas fa-{{ $icon }}"></i></div>
        @if($trend)
            <div class="stat-trend {{ $trendType }}"><i class="fas fa-arrow-{{ $trendType }}"></i> {{ $trend }}</div>
        @endif
    </div>
    <div class="stat-value">{{ $value }}</div>
    <div class="stat-label">{{ $title }}</div>
    <div class="stat-footer">
        <span>{{ $footerText }}</span>
        @if($progress)
            <div class="stat-mini-bar">
                <div class="stat-mini-fill" style="width:{{ $progress }}; background:{{ $gradient }}"></div>
            </div>
        @endif
    </div>
</div>
