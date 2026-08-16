@extends('layouts.admin-auth')
@section('title', 'Forgot password')
@section('heading', 'Gownsea')
@section('lede', 'Enter your email and we will send a reset link.')
@section('content')
    <form method="POST" action="{{ route('admin.password.email') }}" class="admin-login__form">
        @csrf
        <label class="admin-login__label">
            Email
            <span class="admin-login__field">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="4" y="6.5" width="16" height="11" rx="2" stroke="currentColor" stroke-width="1.6"/>
                    <path d="m6 8.5 6 4.2 6-4.2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input type="email" name="email" required>
            </span>
        </label>
        <div class="admin-login__row">
            <span></span>
            <a class="admin-login__link" href="{{ route('admin.login') }}">Back to sign in</a>
        </div>
        <button type="submit" class="admin-login__submit">Send reset link</button>
    </form>
@endsection
