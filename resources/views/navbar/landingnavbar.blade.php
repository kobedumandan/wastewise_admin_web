<nav class="navbar navbar-expand-lg fixed-top navbar">
    <div class="container-fluid">
        <a class="navbar-brand ms-5 text-light" href="{{ route('landing') }}">
            <img class="me-2 mb-2" src="{{ asset('images/wastewiselogo.png') }}" width="50">WasteWise
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-5">
                <li class="nav-item m-3">
                    <a class="nav-link text-light" aria-current="page" href="{{ route('landing') }}">Home</a>
                </li>

                <li class="nav-item m-3">
                    <a class="nav-link text-light" aria-current="page" href="{{ route('aboutus') }}">About Us</a>
                </li>

                <li class="nav-item m-3">
                    <a class="nav-link text-light" aria-current="page" href="{{ route('admin.login') }}">Admin Login</a>
                </li>

            </ul>
        </div>
    </div>
</nav>
