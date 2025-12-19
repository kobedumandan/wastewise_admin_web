<div class="">
        <nav class="navbar navbar-expand-lg fixed-top navbar text-light">
    <div class="container-fluid">
        <a class="navbar-brand ms-5 text-light" href="{{ route('landing') }}">
            <img class="me-2 mb-2" src="{{ asset('images/wastewiselogo.png') }}" width="50">WasteWise
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse text-light" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-5 text-light align-items-center">

                <li class="nav-item mx-3">
                    <a class="nav-link text-light" aria-current="page" href="{{ route('admin.admindashboard') }}">Dashboard</a>
                </li>

                <li class="nav-item me-4">
                    <select onchange="window.location.href=this.value"
                            class="bg-transparent text-light border-0 p-1"
                            style="height: 40px; padding: 6px 12px; background: none; color: white; border-radius: 0; box-shadow: none; appearance: none; cursor: pointer;">
                        <option selected disabled class="bg-light text-dark">Details</option>
                        <option value="{{ route('userdetails') }}" class="bg-light text-dark">User</option>
                        <option value="{{ route('collectordetails') }}" class="bg-light text-dark">Collector</option>
                    </select>
                </li>

                <li class="nav-item me-4">
                    <select onchange="window.location.href=this.value"
                            class="bg-transparent text-light border-0 p-1"
                            style="height: 40px; padding: 6px 12px; background: none; color: white; border-radius: 0; box-shadow: none; appearance: none; cursor: pointer;">
                        <option selected disabled class="bg-light text-dark">Logs</option>
                        <option value="{{ route('adminlogs') }}" class="bg-light text-dark">Admin</option>
                        <option value="{{ route('collectorlogs') }}" class="bg-light text-dark">Collector</option>
                    </select>
                </li>

                <li class="nav-item me-4">
                    <select onchange="window.location.href=this.value"
                            class="bg-transparent text-light border-0 p-1"
                            style="height: 40px; padding: 6px 12px; background: none; color: white; border-radius: 0; box-shadow: none; appearance: none; cursor: pointer;">
                        <option selected disabled class="bg-light text-dark">Audits</option>
                        <option value="{{ route('adminaudit') }}" class="bg-light text-dark">Admin</option>
                        <option value="{{ route('collectoraudit') }}" class="bg-light text-dark">Collector</option>
                    </select>
                </li>

                <li class="nav-item me-3" style="margin-left: -10px;">
                    <a class="nav-link text-light" href="{{ route('userfines') }}">Fines</a>
                </li>

                <li class="nav-item mx-3">
                    <a class="nav-link text-light" href="{{ route('scheduling') }}">Schedule</a>
                </li>

                <li class="nav-item mx-3">
                    <a class="nav-link text-light" href="#" data-bs-toggle="modal" data-bs-target="#registerCollectorModal">Register Collector</a>
                </li>

                <li class="nav-item mx-3">
                    <a class="nav-link text-light" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        Logout
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>


             


    <!-- Register Collector Modal -->
<div class="modal fade" id="registerCollectorModal" tabindex="-1" aria-labelledby="registerCollectorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-light text-dark">
      <div class="modal-header text-white" style="background-image: url('{{ asset('backgrounds/bg1.png') }}'); background-size: cover; background-position: center left; background-repeat: no-repeat; padding-top: 0; padding-left: 0; padding-bottom: 0; padding-right: 20px;">
        <div style="background-image: url('{{ asset('backgrounds/user_icon.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat; width:48px; height: 48px;">
        </div>
        <h5 class="modal-title" id="registerCollectorModalLabel" style="margin-left: 8px; font-size: 18px;">
          Register Collector
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="registerCollectorForm" method="POST" action="{{ route('collectorsignup.store') }}">
        @csrf
        <div class="modal-body">
          <div class="row">
            <!-- Firstname -->
            <div class="col-md-6 mb-3">
              <label for="coll_fname" class="form-label">Firstname</label>
              <input type="text" id="coll_fname" name="coll_fname" class="form-control border border-success text-dark" placeholder="Enter first name" required>
            </div>

            <!-- Lastname -->
            <div class="col-md-6 mb-3">
              <label for="coll_lname" class="form-label">Lastname</label>
              <input type="text" id="coll_lname" name="coll_lname" class="form-control border border-success text-dark" placeholder="Enter last name" required>
            </div>

            <!-- Cellphone -->
            <div class="col-md-6 mb-3">
              <label for="collcell_num" class="form-label">Cellphone Number</label>
              <input type="text" id="collcell_num" name="collcell_num" class="form-control border border-success text-dark" placeholder="Enter cellphone number" required>
            </div>

            <!-- Username -->
            <div class="col-md-6 mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" id="username" name="username" class="form-control border border-success text-dark" placeholder="Enter username" required>
            </div>

            <!-- Email -->
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" id="email" name="email" class="form-control border border-success text-dark" placeholder="Enter email address" required>
            </div>

            <!-- Password -->
            <div class="col-12 mb-3">
              <label for="password" class="form-label">Password</label>
              <div class="position-relative">
                <input type="password" id="password" name="password" class="form-control border border-success text-dark pe-5" placeholder="Enter password" required>
                <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="toggle()" style="cursor: pointer;">
                  <img src="{{ asset('images/dikita.png') }}" width="20" alt="Eye Icon">
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success w-100" type="submit">Register</button>
        </div>
      </form>
    </div>
  </div>
</div>

        <div class="modal fade" id="passwordErrorModal" tabindex="-1" aria-labelledby="passwordErrorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-danger text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="passwordErrorModalLabel">Registration Error</h5>
        <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Password requires at least 8 characters!
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="registerSuccessModal" tabindex="-1" aria-labelledby="registerSuccessModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-success text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="registerSuccessModalLabel">Success</h5>
        <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Collector account registered successfully!
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="registerErrorModal" tabindex="-1" aria-labelledby="registerErrorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-danger text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="registerErrorModalLabel">Registration Error</h5>
        <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @if ($errors->any())
          @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div>



 <div class="modal fade text-dark" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to log out?
                    </div>
                    <div class="modal-footer border-0 ">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel

                        </button>

                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-box-arrow-right"></i>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



<script>
function toggle() {
    const passwordInput = document.getElementById("password");
    passwordInput.type = passwordInput.type === "password" ? "text" : "password";
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerCollectorForm');
    const passwordInput = document.getElementById('password');

    form.addEventListener('submit', function (e) {
        if (passwordInput.value.length < 8) {
            e.preventDefault();
            const errorModal = new bootstrap.Modal(document.getElementById('passwordErrorModal'));
            errorModal.show();
        }
    });
});

    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->any())
            // Show the Register Collector Modal first
            const collectorModal = new bootstrap.Modal(document.getElementById('registerCollectorModal'));
            collectorModal.show();

            // Slight delay to make sure it's mounted, then show the error modal on top
            setTimeout(() => {
                const errorModal = new bootstrap.Modal(document.getElementById('registerErrorModal'));
                errorModal.show();
            }, 500);
        @endif

        @if (session('register_success'))
            const successModal = new bootstrap.Modal(document.getElementById('registerSuccessModal'));
            successModal.show();
        @endif
    });

</script>
   
    
<script>
    function toggle() {
        const passwordInput = document.getElementById("password");
        passwordInput.type = passwordInput.type === "password" ? "text" : "password";
    }


    
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</div>
