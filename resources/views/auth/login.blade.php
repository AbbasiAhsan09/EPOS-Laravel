@extends('layouts.app')

@section('content')
<div class="login-main-container">
    <div class="login-form">
        <div class="login-form-details">

            <div class="login-page-icon">
                <img src="{{asset("images/logo.png")}}" width="200px" alt="" class="login-logo">
            </div>

            <div class="login-page-content">
                <h2>User Login</h2>
                <p>Login to access your account</p>
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                {{-- <input type="email" class="login-form-input" placeholder="Email"> --}}

                <input id="email" type="email" class="login-form-input" placeholder="Email or Username" 
                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ 'Invalid Credentials' }}</strong>
                    </div>
                @enderror
                <input
                id="password" type="password" 
               class="login-form-input"
                name="password" placeholder="*******" required autocomplete="current-password">
                <div class="login-more-action">
                    <div class="">
                       <input type="checkbox"> Remember me
                    </div>
                    <div class="">
                        <a href="">Forgot Password?</a>
                    </div>
                </div>
                <button class="login-form-button" type="submit">Login</button>
                @error('password')
                <div class="invalid-feedback" role="alert">
                    <strong>{{ 'Invalid Credentials' }}</strong>
                </div>
            @enderror
            </form>
        </div>
    </div>
    <div class="slider-section">
        <!-- <div class="login-slider-wrapper">
            <div class="login-slider">
                Testing
            </div>
        </div> -->
    </div>
</div>

<style>
    *{
        overflow: hidden !important;
    }
</style>

@endsection
