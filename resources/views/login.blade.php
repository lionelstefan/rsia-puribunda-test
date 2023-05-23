<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{mix('css/login.css')}}" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Login</title>
</head>

<body>
    @if(Session::has('success'))
    <div class="alert alert-success">
        {{Session::get('success')}}
    </div>
    @endif
    @if(Session::has('fail'))
    <div class="alert alert-danger">
        {{Session::get('fail')}}
    </div>
    @endif
    <div class="wrap">
        <div class="box">
            <div class="content">
                <form method="POST" action="/postLogin" enctype="multipart/form-data" class="mt-4">
                    @csrf
                    <div class="logo-wrap">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <h1>Welcome Back!</h1>
                    <div class="input-box">
                        <input type="text" id="user_name" name="user_name" autocomplete="off" required>
                        <i class="fa-solid fa-user"></i>
                        <span>Username</span>
                    </div>
                    <div class="input-box">
                        <input type="password" id="password" name="password" required>
                        <i class="fa-solid fa-lock"></i>
                        <span>Password</span>
                    </div>
                    {{-- <div class="links">
                        <a href="#">Forgot Password?</a>
                        <a href="#">Sign Up</a>
                    </div> --}}
                    <div class="input-box">
                        <input type="submit" value="Login">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
