@props([
    'label' => null,
    'name',
    'id' => null,
    'placeholder' => null,
    'icon' => null,
    'required' => false,
    'rows' => 2,
    'fullWidth' => true,
])

@php
    $id = $id ?? $name;
@endphp

<div class="form-field{{ $fullWidth ? ' full' : '' }}">
    @if($label)
        <label for="{{ $id }}">{{ $label }} @if($required)<span class="req">*</span>@endif</label>
    @endif
    @if($icon)
        <div class="input-icon-wrap">
            <textarea 
                class="form-control" 
                id="{{ $id }}" 
                name="{{ $name }}" 
                rows="{{ $rows }}" 
                placeholder="{{ $placeholder }}" 
                style="padding-left:2.75rem;resize:vertical" 
                {{ $required ? 'required' : '' }} 
                {{ $attributes }}
            ></textarea>
            <i class="{{ $icon }} icon" style="top:1rem;transform:none"></i>
        </div>
    @else
        <textarea 
            class="form-control" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            rows="{{ $rows }}" 
            placeholder="{{ $placeholder }}" 
            style="resize:vertical" 
            {{ $required ? 'required' : '' }} 
            {{ $attributes }}
        ></textarea>
    @endif
</div>
