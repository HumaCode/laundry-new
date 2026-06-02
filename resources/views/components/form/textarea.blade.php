@props([
    'label' => null,
    'name',
    'id' => null,
    'placeholder' => null,
    'icon' => null,
    'required' => false,
    'rows' => 2,
    'fullWidth' => true,
    'formField' => false,
    'formGroup' => false,
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
@else
    <div class="form-group">
        @if($label)
            <label for="{{ $id }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
        @endif
        <textarea 
            class="form-control" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            rows="{{ $rows }}" 
            placeholder="{{ $placeholder }}" 
            {{ $required ? 'required' : '' }} 
            {{ $attributes }}
        ></textarea>
        <div class="invalid-feedback"></div>
    </div>
@endif
