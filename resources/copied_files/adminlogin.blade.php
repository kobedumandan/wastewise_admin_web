<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>WasteWise</title>
</head>
<body style="background-image: url('{{ asset('images/bgwasted.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh; overflow: hidden;">

    <!-- Navbar -->
     @include('navbar.landingnavbar')
    <!-- Main content -->
    <div class="container-fluid min-vh-100 d-flex align-items-center pt-5">
        <div class="row w-100">
            <div class="col-md-5 d-flex justify-content-start">
                <div class="p-4 glass" style="max-width: 450px; width: 100%; border-radius: 15px; margin-left: 100px; margin-bottom: 100px;">
                    <div class="header-text mb-4 text-center">
                        <h3 class="mb-1 text-light">Welcome <span class="span">Admin!</span></h3>
                    </div>
                    <div class="text-center">
                        <img class="img-fluid mb-4" src="{{ asset('images/admin.png') }}" width="50" alt="User Icon">
                    </div>

                    <!-- Login form -->
                    <form method="POST" action="{{ route('adminlogin.submit') }}">
                        @csrf
                        <p class="text-light">Username</p>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="admin_username" style="background-color: aliceblue" aria-label="admin_username" value="{{ old('admin_username') }}" />
                            <button class="btn" style="background-color: aliceblue" type="button">
                                <img src="{{ asset('images/userlogin.png') }}" width="18" alt="User Icon">
                            </button>
                        </div>
                    
                        <p class="text-light">Password</p>
                        <div class="mb-4 position-relative">
                            <input type="password" class="form-control pe-5" name="admin_password" id="password" style="background-color: aliceblue;" aria-label="admin_password" />
                           <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;" onclick="togglePassword()">
        <img id="eyeIcon" src="{{ asset('images/dikita.png') }}" width="20" alt="Toggle Visibility">
    </span>
                        </div>
                    
                        <div class="input-group mb-3">
                            <button class="btn btn-lg w-100 fs-6 text-light" style="background-color: #13BE37">Log In</button>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>
                    

                   
                </div>
            </div>
        </div>
    </div>

    
    <script>
         function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.src = "{{ asset('images/kita.png') }}"; // Change this to your "eye-slash" image
        } else {
            passwordInput.type = 'password';
            eyeIcon.src = "{{ asset('images/dikita.png') }}"; // Change this to your "eye" image
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
