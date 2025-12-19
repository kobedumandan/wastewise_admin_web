{{-- Admin Modals Component --}}
{{-- All modals used by the admin sidebar --}}

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
                <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword()" style="cursor: pointer;">
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

<!-- Password Error Modal -->
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

<!-- Register Success Modal -->
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

<!-- Register Error Modal -->
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

<!-- Logout Modal -->
<div class="modal fade text-dark" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to log out?
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
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

