@props([
    'label' => null,
    'name',
    'id' => null,
    'required' => false,
    'fullWidth' => false,
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
        <select 
            class="form-control" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            {{ $required ? 'required' : '' }} 
            {{ $attributes }}
        >
            {{ $slot }}
        </select>
    </div>
@else
    <div class="form-group">
        @if($label)
            <label for="{{ $id }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
        @endif
        <select 
            class="form-control" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            {{ $required ? 'required' : '' }} 
            {{ $attributes }}
        >
            {{ $slot }}
        </select>
        <div class="invalid-feedback"></div>
    </div>
@endif
