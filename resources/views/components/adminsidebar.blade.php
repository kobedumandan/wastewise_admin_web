{{-- Admin Sidebar Component --}}
{{-- Main sidebar navigation structure --}}
{{-- Edit navigation items below --}}

<div class="sidebar-main">
    <aside id="sidebar">
        <div class="d-flex">
            <button id="toggle-btn" type="button">
                <i class="bi bi-list"></i>
            </button>
            <div class="sidebar-logo">
                <img src="{{ asset('images/wastewiselogo.png') }}" width="29px">
                <a class="fs-5 fw-bold">Admin</a>
            </div>
        </div>
        <ul class="sidebar-nav">
            {{-- Dashboard --}}
            <li class="sidebar-item">
                <a role="button" href="{{ route('admin.admindashboard') }}" class="sidebar-link {{ request()->routeIs('admin.admindashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Details Dropdown --}}
            <li class="sidebar-item">
                <a role="button" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse"
                    data-bs-target="#details-opt" aria-expanded="false" aria-controls="details-opt">
                    <i class="bi bi-people"></i>
                    <span>Details</span>
                </a>
                <ul id="details-opt" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a role="button" href="{{ route('userdetails') }}" class="sidebar-link {{ request()->routeIs('userdetails') ? 'active' : '' }}">
                            <i class="bi bi-person"></i>
                            <span>User Details</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a role="button" href="{{ route('collectordetails') }}" class="sidebar-link {{ request()->routeIs('collectordetails') ? 'active' : '' }}">
                            <i class="bi bi-person-badge"></i>
                            <span>Collector Details</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Logs Dropdown --}}
            <li class="sidebar-item">
                <a role="button" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse"
                    data-bs-target="#logs-opt" aria-expanded="false" aria-controls="logs-opt">
                    <i class="bi bi-journal-text"></i>
                    <span>Logs</span>
                </a>
                <ul id="logs-opt" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a role="button" href="{{ route('adminlogs') }}" class="sidebar-link {{ request()->routeIs('adminlogs') ? 'active' : '' }}">
                            <i class="bi bi-person-gear"></i>
                            <span>Admin Logs</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a role="button" href="{{ route('collectorlogs') }}" class="sidebar-link {{ request()->routeIs('collectorlogs') ? 'active' : '' }}">
                            <i class="bi bi-person-badge"></i>
                            <span>Collector Logs</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Audits Dropdown --}}
            <li class="sidebar-item">
                <a role="button" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse"
                    data-bs-target="#audits-opt" aria-expanded="false" aria-controls="audits-opt">
                    <i class="bi bi-shield-check"></i>
                    <span>Audits</span>
                </a>
                <ul id="audits-opt" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a role="button" href="{{ route('adminaudit') }}" class="sidebar-link {{ request()->routeIs('adminaudit') ? 'active' : '' }}">
                            <i class="bi bi-person-gear"></i>
                            <span>Admin Audits</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a role="button" href="{{ route('collectoraudit') }}" class="sidebar-link {{ request()->routeIs('collectoraudit') ? 'active' : '' }}">
                            <i class="bi bi-person-badge"></i>
                            <span>Collector Audits</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Fines --}}
            <li class="sidebar-item">
                <a role="button" href="{{ route('userfines') }}" class="sidebar-link {{ request()->routeIs('userfines') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i>
                    <span>Fines</span>
                </a>
            </li>

            {{-- Requests Dropdown --}}
            <li class="sidebar-item">
                <a role="button" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse"
                    data-bs-target="#requests-opt" aria-expanded="false" aria-controls="requests-opt">
                    <i class="bi bi-inbox"></i>
                    <span>Requests</span>
                </a>
                <ul id="requests-opt" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a role="button" href="{{ route('change.credential.requests') }}" class="sidebar-link {{ request()->routeIs('change.credential.requests') ? 'active' : '' }}">
                            <i class="bi bi-key"></i>
                            <span>Change Requests</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Schedule --}}
            <li class="sidebar-item">
                <a role="button" href="{{ route('scheduling') }}" class="sidebar-link {{ request()->routeIs('scheduling') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i>
                    <span>Schedule</span>
                </a>
            </li>

            {{-- Register Collector --}}
            <li class="sidebar-item">
                <a role="button" href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#registerCollectorModal">
                    <i class="bi bi-person-plus"></i>
                    <span>Register Collector</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a role="button" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>
</div>

{{-- Include Modals Component --}}
@include('components.admin-modals')

{{-- Include Scripts Component --}}
@include('components.admin-sidebar-scripts')
