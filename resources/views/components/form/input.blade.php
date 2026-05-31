@props([
    'label' => null,
    'type' => 'text',
    'name',
    'id' => null,
    'placeholder' => null,
    'icon' => null,
    'required' => false,
    'autofocus' => false,
    'autocomplete' => null,
    'value' => null,
    'isPassword' => false,
])

@php
    $id = $id ?? $name;
@endphp

<div class="form-group">
    @if($label)
        <label class="form-label" for="{{ $id }}">{{ $label }}</label>
    @endif
    <div class="input-wrapper">
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if($required) required @endif
            @if($autofocus) autofocus @endif
            value="{{ $value }}"
            {{ $attributes->merge(['class' => 'form-control']) }}
        >
        @if($icon)
            <i class="{{ $icon }} input-icon"></i>
        @endif

        @if($isPassword || $type === 'password')
            <button 
                type="button" 
                class="password-toggle" 
                id="{{ $id }}ToggleBtn"
                aria-label="Toggle password visibility"
            >
                <i class="fas fa-eye"></i>
            </button>
        @endif
    </div>
</div>
