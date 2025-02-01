<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Desa Banyupelle</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            margin-top: 5%;
            margin-bottom: 5%;
        }
        .login-form {
            padding: 5%;
            background: #fff;
            box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.2), 0 9px 26px 0 rgba(0, 0, 0, 0.19);
            border-radius: 10px;
        }
        .login-form h3 {
            text-align: center;
            margin-bottom: 5%;
            color: #333;
            font-weight: 600;
        }
        .login-form .brand-wrapper {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-form .brand-wrapper img {
            height: 100px;
            margin-bottom: 10px;
        }
        .login-form .form-group {
            margin-bottom: 20px;
        }
        .login-form .form-control {
            border-radius: 5px;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        .login-form .form-control:focus {
            box-shadow: none;
            border-color: #4e73df;
        }
        .login-form .btn-login {
            background: #4e73df;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }
        .login-form .btn-login:hover {
            background: #2e59d9;
        }
        .login-form .forgot-link {
            color: #4e73df;
            text-decoration: none;
            font-size: 14px;
        }
        .login-form .forgot-link:hover {
            text-decoration: underline;
        }
        .login-form .remember-me {
            font-size: 14px;
        }
        .alert {
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-form">
                    <div class="brand-wrapper">
                        <img src="{{ asset('storage/'.$settings->app_logo ?? 'assets/images/logo.png') }}" alt="logo">
                        <h3>Desa Banyupelle</h3>
                    </div>

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                </div>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                </div>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" placeholder="Password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox remember-me">
                                <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                <label class="custom-control-label" for="remember">Ingat Saya</label>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-login btn-block">
                                Masuk <i class="fa fa-sign-in ml-1"></i>
                            </button>
                        </div>

                        @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a class="forgot-link" href="{{ route('password.request') }}">
                                Lupa Password?
                            </a>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
