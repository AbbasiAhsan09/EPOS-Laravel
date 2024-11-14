@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center align-items-center " style="height: 90vh">
        <div class="col-md-4">
            <div class="card">
                <div class="login-form-wrapper"  >
                    <div class="login-form">
                        <div class="logo d-flex justify-content-center align-items-center my-3">
                            <img src="{{asset("images/logo.png")}}" width="120px" alt="" >
                        </div>
                        <div class="form d-flex justify-content-center align-items-center">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
        
                                <div class="row mb-3">
                                    {{-- <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label> --}}
        
                                    <div class="col-md-12">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email or Username" 
                                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ 'Invalid Credentials' }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
        
                                <div class="row mb-3">
                                    {{-- <label for="password" class="col-md-4 col-form-label text-md-end">
                                        </label> --}}
        
                                    <div class="col-md-12">
                                        <input id="password" type="password" class="form-control 
                                        @error('password') is-invalid @enderror" name="password" placeholder="*******" required autocomplete="current-password">
        
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ 'Invalid Credentials' }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
        
                                {{-- <div class="row mb-3">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        
                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>
                                </div> --}}
        
                                <div class="row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="text-primary btn" style="width: 100%">
                                            {{ __('Login') }}
                                        </button>
        
                                        {{-- @if (Route::has('password.request'))
                                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                                {{ __('Forgot Your Password?') }}
                                            </a>
                                        @endif --}}
                                    </div>
                                </div>
                            </form> 
                        </div>
                    </div>
                </div>
               
            </div>
            
                <p class="contact-line my-5" >
                     Copyright 	&#169; resereved to TradeWise POS {{date('Y')}}
                </p>
        </div>
    </div>
</div>
<style>
    .login-form input{
            text-align: center;
            border-bottom: 2px solid gray;
}
.contact-line{
    text-align: center;
    font-size: 14px;
}
</style>
@endsection
