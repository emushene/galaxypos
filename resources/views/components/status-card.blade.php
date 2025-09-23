@props([
    'icon', 
    'iconBgColor' => 'bg-secondary',
])

<div class="status-card">
    
    <!-- Icon -->
    <div {{ $attributes->merge(['class' => 'status-icon ' . $iconBgColor]) }}>
        <i class="fa fa-{{ $icon }}"></i>
    </div>

    <!-- Title / Message -->
    <h2 class="status-title {{ $title->attributes->get('class') }}">{{ $title }}</h2>
    <p class="status-message">
        {{ $message }}
    </p>

    <!-- Actions -->
    <div class="d-grid gap-3">
        {{ $actions }}
    </div>

    <!-- Footer -->
    @if (isset($footer))
        <div class="text-center mt-4">
            {{ $footer }}
        </div>
    @endif
</div>
