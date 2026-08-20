@php
    $user = auth()->user();
    $link = function (string $name, string|array $pattern = null, string|array $except = []) {
        $pattern = $pattern ?: $name.'*';
        $active = request()->routeIs($pattern) && ($except === [] || ! request()->routeIs($except));

        return $active ? 'admin-nav-link is-active' : 'admin-nav-link';
    };
@endphp
<div class="admin-sidebar-inner">
    <a href="{{ route('admin.dashboard') }}" class="mb-6 inline-flex items-center gap-2 font-serif text-xl font-semibold text-white">
        @if ($logo = \App\Models\Setting::logoUrl())
            <img src="{{ $logo }}" alt="" class="h-8 w-auto max-w-[9rem] rounded-sm bg-white object-contain p-1">
        @endif
        Gownsea Admin
    </a>
    <nav class="admin-sidebar-nav min-h-0 flex-1 space-y-5 overflow-y-auto text-sm">
        @if($user?->hasPermission('dashboard'))
        <div>
            <p class="admin-nav-section">Overview</p>
            <a class="{{ $link('admin.dashboard', 'admin.dashboard') }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
        </div>
        @endif
        @if($user?->hasPermission('catalogue'))
        <div>
            <p class="admin-nav-section">Catalogue</p>
            <a class="{{ $link('admin.catalogue.products') }}" href="{{ route('admin.catalogue.products.index') }}">Products</a>
            <a class="{{ $link('admin.catalogue.categories') }}" href="{{ route('admin.catalogue.categories.index') }}">Categories</a>
        </div>
        @endif
        @if($user?->hasPermission('sales') || $user?->hasPermission('customers'))
        <div>
            <p class="admin-nav-section">Sales</p>
            @if($user?->hasPermission('sales'))
                <a class="{{ $link('admin.sales') }}" href="{{ route('admin.sales.index') }}">Sales / Orders @if(($adminBadges['sales'] ?? 0) > 0)<span class="ml-auto text-xs">{{ $adminBadges['sales'] }}</span>@endif</a>
            @endif
            @if($user?->hasPermission('customers'))
                <a class="{{ $link('admin.customers') }}" href="{{ route('admin.customers.index') }}">Customers</a>
            @endif
        </div>
        @endif
        @if($user?->hasPermission('leads') || $user?->hasPermission('inquiries') || $user?->hasPermission('activities'))
        <div>
            <p class="admin-nav-section">CRM</p>
            @if($user?->hasPermission('leads'))
                <a class="{{ $link('admin.leads', 'admin.leads.*', 'admin.leads.pipeline') }}" href="{{ route('admin.leads.index') }}">Leads @if(($adminBadges['leads'] ?? 0) > 0)<span class="text-xs">{{ $adminBadges['leads'] }}</span>@endif</a>
                <a class="{{ $link('admin.leads.pipeline') }}" href="{{ route('admin.leads.pipeline') }}">Pipeline</a>
            @endif
            @if($user?->hasPermission('inquiries'))
                <a class="{{ $link('admin.inquiries.products') }}" href="{{ route('admin.inquiries.products') }}">Product Inquiries @if(($adminBadges['inquiries'] ?? 0) > 0)<span class="text-xs">{{ $adminBadges['inquiries'] }}</span>@endif</a>
                <a class="{{ $link('admin.inquiries.general') }}" href="{{ route('admin.inquiries.general') }}">General Inquiries</a>
            @endif
            @if($user?->hasPermission('activities'))
                <a class="{{ $link('admin.activities') }}" href="{{ route('admin.activities.index') }}">Activities @if(($adminBadges['activities'] ?? 0) > 0)<span class="text-xs">{{ $adminBadges['activities'] }}</span>@endif</a>
            @endif
        </div>
        @endif
        @if($user?->hasPermission('reports'))
        <div>
            <p class="admin-nav-section">Analytics</p>
            <a class="{{ $link('admin.reports') }}" href="{{ route('admin.reports.index') }}">Reports</a>
        </div>
        @endif
        @if($user?->hasPermission('users') || $user?->hasPermission('settings'))
        <div>
            <p class="admin-nav-section">Administration</p>
            @if($user?->hasPermission('users'))
                <a class="{{ $link('admin.users') }}" href="{{ route('admin.users.index') }}">Users</a>
                <a class="{{ $link('admin.roles') }}" href="{{ route('admin.roles.index') }}">Roles & Permissions</a>
            @endif
            @if($user?->hasPermission('settings'))
                <a class="{{ $link('admin.settings') }}" href="{{ route('admin.settings.edit') }}">Website Settings</a>
            @endif
        </div>
        @endif
    </nav>
    <div class="mt-auto border-t border-white/20 pt-4">
        <p class="mb-3 text-xs text-white/80">{{ $user?->name }} · {{ str_replace('_', ' ', $user?->role) }}</p>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="admin-logout">Logout</button>
        </form>
    </div>
</div>
