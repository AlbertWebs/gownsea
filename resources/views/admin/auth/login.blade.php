@extends('layouts.admin-auth')
@section('title', 'Admin Login')
@section('heading', 'Gownsea')
@section('lede', 'Sign in to manage content and settings.')
@section('content')
    <form method="POST" action="{{ route('admin.login.attempt') }}" class="admin-login__form" x-data="{ show: false }">
        @csrf
        <label class="admin-login__label">
            Email
            <span class="admin-login__field">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="4" y="6.5" width="16" height="11" rx="2" stroke="currentColor" stroke-width="1.6"/>
                    <path d="m6 8.5 6 4.2 6-4.2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" autofocus>
            </span>
        </label>

        <label class="admin-login__label">
            Password
            <span class="admin-login__field">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="6" y="10" width="12" height="9" rx="2" stroke="currentColor" stroke-width="1.6"/>
                    <path d="M8.5 10V8.2a3.5 3.5 0 0 1 7 0V10" stroke="currentColor" stroke-width="1.6"/>
                </svg>
                <input :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password">
                <button type="button" class="admin-login__toggle" @click="show = !show" x-text="show ? 'Hide' : 'Show'"></button>
            </span>
        </label>

        <div class="admin-login__row">
            <label class="admin-login__remember">
                <input type="checkbox" name="remember" value="1">
                Remember me
            </label>
            <a class="admin-login__link" href="{{ route('home') }}">Back to site</a>
        </div>

        <button type="submit" class="admin-login__submit">Log in</button>
    </form>

    <p class="admin-login__footnote">
        Protected by two-step verification after login. If you don’t have access, contact an administrator.
    </p>
@endsection
