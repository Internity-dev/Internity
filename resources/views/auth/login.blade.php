@extends('layouts.app')

@section('content')
<div id="login-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 d-flex flex-column align-items-center justify-content-center text-center side-left">
                <img src="{{ asset('/img/logo-internity.png') }}" alt="Internity" />
                <h1>Welcome to Internity!</h1>
                <p>
                    Kontrol dan pantau kegiatan <span>PKL</span> dengan mudah.
                </p>
            </div>

            <div class="col-md-6 text-white d-flex align-items-center justify-content-center side-right">
                <form action="{{ route('login') }}" method="POST" class="w-100" style="max-width: 450px;">
                    @csrf
                    @method('POST')

                    <h1 class="text-center">Sign In</h1>

                    {{-- Input Email Start --}}
                    <div class="form-group input-field">
                        <label for="email">Email</label>
                        <input type="email" class="input" id="email" name="email" required autocomplete="off" />
                    </div>
                    {{-- Input Email End --}}

                    {{-- Input Password Start --}}
                    <div class="form-group input-field">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="input" required>
                        <span class="eye" onclick="myFunction()">
                            <i id="show" class="bi bi-eye-fill"></i>
                            <i id="hide" class="bi bi-eye-slash-fill"></i>
                        </span>
                    </div>
                    {{-- Input Password End --}}

                    {{-- Login Validation Start --}}
                    @error('email')
                    <div class="alert alert-dark text-white help-block">{{ $message }}</div>
                    @enderror
                    {{-- Login Validation End --}}

                    <div class="remember">
                        <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                    </div>

                    <div class="input-field">
                        <button type="submit" class="submit">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    function myFunction() {
        var passField = document.getElementById("password");
        var showPass = document.getElementById("show");
        var hidePass = document.getElementById("hide");

        if (passField.type === "password") {
            passField.type = "text";
            showPass.style.display = "block";
            hidePass.style.display = "none";
        } else {
            passField.type = "password";
            showPass.style.display = "none";
            hidePass.style.display = "block";
        }
    }
</script>