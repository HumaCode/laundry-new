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
    'formField' => false,
    'fullWidth' => false,
])

@php
    $id = $id ?? $name;
@endphp

@if($formField)
    <div class="form-field{{ $fullWidth ? ' full' : '' }}">
        @if($label)
            <label for="{{ $id }}">{{ $label }} @if($required)<span class="req">*</span>@endif</label>
        @endif
        @if($icon)
            <div class="input-icon-wrap">
                <input 
                    type="{{ $type }}" 
                    class="form-control" 
                    id="{{ $id }}" 
                    name="{{ $name }}" 
                    value="{{ $value }}" 
                    placeholder="{{ $placeholder }}" 
                    @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                    @if($required) required @endif
                    @if($autofocus) autofocus @endif
                    {{ $attributes }}
                >
                <i class="{{ $icon }} icon"></i>
            </div>
        @else
            <input 
                type="{{ $type }}" 
                class="form-control" 
                id="{{ $id }}" 
                name="{{ $name }}" 
                value="{{ $value }}" 
                placeholder="{{ $placeholder }}" 
                @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                @if($required) required @endif
                @if($autofocus) autofocus @endif
                {{ $attributes }}
            >
        @endif
    </div>
@else
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
@endif
