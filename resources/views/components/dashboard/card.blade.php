@props([
    'title',
    'icon',
    'iconStyle' => '',
    'noPadding' => false,
])

<div class="card h-100">
    <div class="card-header">
        <div class="card-title">
            @if($icon)
                <div class="card-title-icon" style="{{ $iconStyle }}">
                    <i class="fas fa-{{ $icon }}"></i>
                </div>
            @endif
            {{ $title }}
        </div>
        @if(isset($headerAction))
            {{ $headerAction }}
        @endif
    </div>
    @if($noPadding)
        {{ $slot }}
    @else
        <div class="card-body">
            {{ $slot }}
        </div>
    @endif
</div>
