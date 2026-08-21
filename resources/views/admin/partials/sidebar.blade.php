@php
    $user = auth()->user();
    $link = function (string $name, string|array $pattern = null, string|array $except = []) {
        $pattern = $pattern ?: $name.'*';
        $active = request()->routeIs($pattern) && ($except === [] || ! request()->routeIs($except));

        return $active ? 'admin-nav-link is-active' : 'admin-nav-link';
    };
    $badge = function (int $count): string {
        return $count > 0
            ? '<span class="admin-nav-badge">'.e($count > 99 ? '99+' : (string) $count).'</span>'
            : '';
    };
@endphp
<div class="admin-sidebar-inner">
    <div class="admin-sidebar__brand">
        <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__brand-link">
            @if ($logo = \App\Models\Setting::logoUrl())
                <img src="{{ $logo }}" alt="" class="admin-sidebar__logo">
            @else
                <span class="admin-sidebar__mark" aria-hidden="true">G</span>
            @endif
            <span class="admin-sidebar__brand-copy">
                <span class="admin-sidebar__brand-name">Gownsea</span>
                <span class="admin-sidebar__brand-meta">Admin panel</span>
            </span>
        </a>
        <button
            type="button"
            class="admin-sidebar__close lg:hidden"
            @click="sidebar = false"
            aria-label="Close menu"
        >
            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
    </div>

    <nav class="admin-sidebar-nav" aria-label="Admin">
        @if($user?->hasPermission('dashboard'))
        <div class="admin-nav-group">
            <p class="admin-nav-section">Overview</p>
            <a class="{{ $link('admin.dashboard', 'admin.dashboard') }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
        </div>
        @endif
        @if($user?->hasPermission('catalogue'))
        <div class="admin-nav-group">
            <p class="admin-nav-section">Catalogue</p>
            <a class="{{ $link('admin.catalogue.products') }}" href="{{ route('admin.catalogue.products.index') }}">Products</a>
            <a class="{{ $link('admin.catalogue.categories') }}" href="{{ route('admin.catalogue.categories.index') }}">Categories</a>
        </div>
        @endif
        @if($user?->hasPermission('sales') || $user?->hasPermission('customers'))
        <div class="admin-nav-group">
            <p class="admin-nav-section">Sales</p>
            @if($user?->hasPermission('sales'))
                <a class="{{ $link('admin.sales') }}" href="{{ route('admin.sales.index') }}">
                    <span>Sales / Orders</span>
                    {!! $badge((int) ($adminBadges['sales'] ?? 0)) !!}
                </a>
            @endif
            @if($user?->hasPermission('customers'))
                <a class="{{ $link('admin.customers') }}" href="{{ route('admin.customers.index') }}">Customers</a>
            @endif
        </div>
        @endif
        @if($user?->hasPermission('leads') || $user?->hasPermission('inquiries') || $user?->hasPermission('activities'))
        <div class="admin-nav-group">
            <p class="admin-nav-section">CRM</p>
            @if($user?->hasPermission('leads'))
                <a class="{{ $link('admin.leads', 'admin.leads.*', 'admin.leads.pipeline') }}" href="{{ route('admin.leads.index') }}">
                    <span>Leads</span>
                    {!! $badge((int) ($adminBadges['leads'] ?? 0)) !!}
                </a>
                <a class="{{ $link('admin.leads.pipeline') }}" href="{{ route('admin.leads.pipeline') }}">Pipeline</a>
            @endif
            @if($user?->hasPermission('inquiries'))
                <a class="{{ $link('admin.inquiries.products') }}" href="{{ route('admin.inquiries.products') }}">
                    <span>Product Inquiries</span>
                    {!! $badge((int) ($adminBadges['inquiries'] ?? 0)) !!}
                </a>
                <a class="{{ $link('admin.inquiries.general') }}" href="{{ route('admin.inquiries.general') }}">General Inquiries</a>
            @endif
            @if($user?->hasPermission('activities'))
                <a class="{{ $link('admin.activities') }}" href="{{ route('admin.activities.index') }}">
                    <span>Activities</span>
                    {!! $badge((int) ($adminBadges['activities'] ?? 0)) !!}
                </a>
            @endif
        </div>
        @endif
        @if($user?->hasPermission('reports'))
        <div class="admin-nav-group">
            <p class="admin-nav-section">Analytics</p>
            <a class="{{ $link('admin.reports') }}" href="{{ route('admin.reports.index') }}">Reports</a>
        </div>
        @endif
        @if($user?->hasPermission('users') || $user?->hasPermission('settings'))
        <div class="admin-nav-group">
            <p class="admin-nav-section">Administration</p>
            @if($user?->hasPermission('users'))
                <a class="{{ $link('admin.users') }}" href="{{ route('admin.users.index') }}">Users</a>
                <a class="{{ $link('admin.roles') }}" href="{{ route('admin.roles.index') }}">Roles &amp; Permissions</a>
            @endif
            @if($user?->hasPermission('settings'))
                <a class="{{ $link('admin.settings') }}" href="{{ route('admin.settings.edit') }}">Website Settings</a>
                <a class="{{ $link('admin.hero-carousel') }}" href="{{ route('admin.hero-carousel.edit') }}">Hero Carousel</a>
            @endif
        </div>
        @endif
    </nav>

    <div class="admin-sidebar__footer">
        <div class="admin-sidebar__user">
            <span class="admin-sidebar__avatar" aria-hidden="true">{{ strtoupper(substr((string) $user?->name, 0, 1)) }}</span>
            <div class="admin-sidebar__user-copy">
                <p class="admin-sidebar__user-name">{{ $user?->name }}</p>
                <p class="admin-sidebar__user-role">{{ str_replace('_', ' ', (string) $user?->role) }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="admin-logout">Log out</button>
        </form>
    </div>
</div>
