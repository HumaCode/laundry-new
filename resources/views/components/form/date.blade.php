@props([
    'label' => null,
    'name',
    'id' => null,
    'placeholder' => null,
    'icon' => 'fas fa-calendar-alt',
    'required' => false,
    'clearCallback' => null,
    'fullWidth' => false,
])

@php
    $id = $id ?? $name;
@endphp

<div class="form-field{{ $fullWidth ? ' full' : '' }}">
    @if($label)
        <label for="{{ $id }}">{{ $label }} @if($required)<span class="req">*</span>@endif</label>
    @endif
    <div class="flatpickr-wrap">
        <span class="flatpickr-calendar-icon"><i class="{{ $icon }}"></i></span>
        <input 
            class="form-control flatpickr-input-custom" 
            id="{{ $id }}" 
            name="{{ $name }}"
            type="text" 
            placeholder="{{ $placeholder }}" 
            readonly
            {{ $required ? 'required' : '' }}
            {{ $attributes }}
        >
        @if($clearCallback)
            <button type="button" class="flatpickr-clear-btn" id="{{ $id }}-clear"
                onclick="{{ $clearCallback }}" title="Hapus tanggal">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>
</div>
