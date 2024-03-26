@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form class="login-form" method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3 d-flex-items mt-3">
                            <div class="form-label">{{ __('Username') }}</div>
                            <input id="username" type="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required autocomplete="username" autofocus>
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 d-flex-items">
                            <div class="form-label">{{ __('Password') }}</div>
                            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check d-flex-items">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember">
                            <div class="form-check-div">{{ __('Remember Me') }}</div>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>

                        <div class="mt-3">
                            <p>{{ __('Don\'t have an account yet?') }} <a href="{{ route('register') }}">{{ __('Register here') }}</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
