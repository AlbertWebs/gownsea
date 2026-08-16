@extends('layouts.admin-auth')
@section('title', 'Reset password')
@section('heading', 'Gownsea')
@section('lede', 'Choose a new password for your admin account.')
@section('content')
    <form method="POST" action="{{ route('admin.password.update') }}" class="admin-login__form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label class="admin-login__label">
            Email
            <input class="admin-login__input" type="email" name="email" value="{{ $email }}" required>
        </label>
        <label class="admin-login__label">
            Password
            <input class="admin-login__input" type="password" name="password" required>
        </label>
        <label class="admin-login__label">
            Confirm password
            <input class="admin-login__input" type="password" name="password_confirmation" required>
        </label>
        <button type="submit" class="admin-login__submit">Reset password</button>
    </form>
@endsection
