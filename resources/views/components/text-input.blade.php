@props(['disabled' => false, 'icon' => null])

<div class="relative">
    @if($icon)
    <div class="input-icon">
        <i class="fas fa-{{ $icon }}"></i>
    </div>
    @endif
    
    <input {{ $disabled ? 'disabled' : '' }} 
           {!! $attributes->merge([
               'class' => 'form-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 ' . ($icon ? 'input-with-icon' : '')
           ]) !!}>
</div>
