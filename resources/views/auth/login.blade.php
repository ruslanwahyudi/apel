@extends('layouts.app')

@section('content')
<div class="hold-transition login-page">
    <div class="login-box">
        
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Silahkan Login</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               placeholder="Email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Password" required autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                @if (Route::has('password.request'))
                    <p class="mb-1">
                        <!-- <a href="{{ route('password.request') }}">I forgot my password</a> -->
                    </p>
                @endif
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
</div>

<style>
.login-page {
    background: #e9ecef;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.login-box {
    width: 400px;
    margin: 0 auto;
}
.login-logo {
    margin-bottom: 25px;
    text-align: center;
    font-size: 35px;
    font-weight: 300;
}
.login-logo a {
    color: #495057;
    text-decoration: none;
}
.login-box-msg {
    margin: 0;
    text-align: center;
    padding: 0 20px 20px;
    color: #666;
}
.login-card-body {
    background: #ffffff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}
.input-group-text {
    background-color: transparent;
}
.btn-block {
    display: block;
    width: 100%;
}
.icheck-primary {
    margin-top: 8px;
}
@media (max-width: 576px) {
    .login-box {
        width: 90%;
        margin: 20px;
    }
    .login-logo {
        font-size: 28px;
    }
}
</style>

<!-- Tambahkan Font Awesome jika belum ada di layout utama -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection
