<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>WasteWise - Admin Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  @vite('resources/sass/app.scss','resources/js/app.js')
  <style>
    body {
      background: linear-gradient(to right, #031214, #033402, #071901, #041000);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .welcome-card {
      background: white;
      border-radius: 15px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="welcome-card text-center">
          <img src="{{ asset('images/wastewiselogo.png') }}" alt="WasteWise Logo" width="100" class="mb-4">
          <h1 class="mb-3" style="color: #033402; font-weight: bold;">WasteWise</h1>
          <h5 class="mb-4 text-muted">Admin Portal</h5>
          <p class="mb-4">Manage waste collection and administration efficiently.</p>
          <a href="{{ route('admin.login') }}" class="btn btn-success btn-lg px-5">Admin Login</a>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
