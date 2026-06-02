@props([
    'type' => 'button',
    'variant' => 'primary', // primary, outline, success, danger, warning
    'id' => null,
    'icon' => null,
])

<button 
    type="{{ $type }}" 
    @if($id) id="{{ $id }}" @endif
    {{ $attributes->merge(['class' => 'modal-btn modal-btn-' . $variant]) }}
>
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $slot }}
</button>
