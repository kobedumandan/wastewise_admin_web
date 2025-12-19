<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    <title>WasteWise</title>
</head>

<body style="background-image: url('{{ asset('images/landingbg.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh; overflow: hidden;">


    @include('navbar.landingnavbar')

    <!-- Main content -->
    <!-- Bottom Left Wrapper -->
<div class="bottom-left-wrapper">
    <h2 class="slogan-text">Dispose Right,<br>Keep It Bright</h2>
    <a href="{{ route('admin.login') }}" class="btn btn-light square-btn border-0">Get Started</a>
</div>

    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
