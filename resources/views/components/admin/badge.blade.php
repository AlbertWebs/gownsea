@props(['status'])

@php
    $tone = match ((string) $status) {
        'published', 'active', 'completed', 'won', 'paid', 'delivered' => 'ok',
        'draft', 'pending', 'new', 'quoted', 'partial' => 'warn',
        'overdue', 'urgent', 'lost' => 'danger',
        'featured' => 'navy',
        'inactive', 'disabled', 'archived', 'cancelled', 'hidden' => '',
        default => 'info',
    };
@endphp
<span {{ $attributes->class('admin-badge'.($tone ? ' admin-badge--'.$tone : '')) }}>{{ str_replace('_', ' ', $status) }}</span>
