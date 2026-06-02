@props([
    'label' => null,
    'name',
    'id' => null,
    'required' => false,
    'fullWidth' => false,
])

@php
    $id = $id ?? $name;
@endphp

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
