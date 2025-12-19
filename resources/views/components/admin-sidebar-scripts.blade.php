{{-- Admin Sidebar JavaScript Component --}}
{{-- All JavaScript for sidebar functionality --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarMain = document.querySelector('.sidebar-main');
    const toggleBtn = document.getElementById('toggle-btn');
    
    // Toggle sidebar expand/collapse
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const isExpanding = !sidebar.classList.contains('expand');
            sidebar.classList.toggle('expand');
            sidebarMain.classList.toggle('expand');
            
            // Update body class for main content positioning
            if (isExpanding) {
                document.body.classList.add('sidebar-expanded');
            } else {
                document.body.classList.remove('sidebar-expanded');
            }
            
            localStorage.setItem('sidebarExpanded', sidebar.classList.contains('expand'));
        });
    }
    
    // Restore sidebar state from localStorage
    if (localStorage.getItem('sidebarExpanded') === 'true') {
        sidebar.classList.add('expand');
        sidebarMain.classList.add('expand');
        document.body.classList.add('sidebar-expanded');
    }
    
    // Auto-expand dropdown if active item is inside
    const activeDropdownItem = document.querySelector('.sidebar-dropdown .sidebar-link.active');
    if (activeDropdownItem) {
        const dropdown = activeDropdownItem.closest('.sidebar-dropdown');
        if (dropdown) {
            const collapseId = dropdown.getAttribute('id');
            const trigger = document.querySelector(`[data-bs-target="#${collapseId}"]`);
            if (trigger && !trigger.classList.contains('collapsed')) {
                const bsCollapse = new bootstrap.Collapse(dropdown, {
                    toggle: false
                });
                bsCollapse.show();
            }
        }
    }
});

// Password toggle function
function togglePassword() {
    const passwordInput = document.getElementById("password");
    if (passwordInput) {
        passwordInput.type = passwordInput.type === "password" ? "text" : "password";
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerCollectorForm');
    if (form) {
        const passwordInput = document.getElementById('password');
        
        form.addEventListener('submit', function (e) {
            if (passwordInput && passwordInput.value.length < 8) {
                e.preventDefault();
                const errorModal = new bootstrap.Modal(document.getElementById('passwordErrorModal'));
                errorModal.show();
            }
        });
    }
    
    // Show error modal only if there are collector registration errors (check for collector-specific error keys)
    @if ($errors->any() && ($errors->has('username') || $errors->has('email') || $errors->has('coll_fname') || $errors->has('coll_lname') || $errors->has('collcell_num') || $errors->has('password')))
        const collectorModal = new bootstrap.Modal(document.getElementById('registerCollectorModal'));
        collectorModal.show();
        
        setTimeout(() => {
            const errorModal = new bootstrap.Modal(document.getElementById('registerErrorModal'));
            errorModal.show();
        }, 500);
    @endif

    // Show success modal
    @if (session('register_success'))
        const successModal = new bootstrap.Modal(document.getElementById('registerSuccessModal'));
        successModal.show();
    @endif
});
</script>

