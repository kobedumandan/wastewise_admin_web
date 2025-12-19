<head>
    <link rel="stylesheet" href="../CSS/sidebar_navbar.css">
</head>

<div class="sidebar-main"> <!-- div that dictates sidebar width and length-->
    <aside id="sidebar">
        <div class="d-flex"> <!-- div for sidebar toggle -->
            <button id="toggle-btn" type="button">
                <img src="../imgs/bumble_gradient_logo.svg" width="29px">
            </button>
            <div class="sidebar-logo">
                <a class="fs-5 fw-bold">Admin</a>
            </div>
        </div>
        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a role="button" onclick="window.location.href = 'dashboard.php';" class="sidebar-link">
                    <i class="lni lni-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a role="button" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse"
                    data-bs-target="#branch-opt" aria-expanded="false" aria-controls="branch-opt">
                    <i class="lni lni-home"></i>
                    <span>Branches</span>
                </a>
                <ul id="branch-opt" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a role="button" onclick="window.location.href = 'branch_add.php';" class="sidebar-link">Add Branch</a>
                    </li>
                    <li class="sidebar-item">
                        <a role="button" onclick="window.location.href = 'branch_list.php';" class="sidebar-link">Branch List</a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a role="button" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse"
                    data-bs-target="#employees-opt" aria-expanded="false" aria-controls="employees-opt">
                    <i class="lni lni-user"></i>
                    <span>Employees</span>
                </a>
                <ul id="employees-opt" class="sidebar-dropdown list-unstyled collapse"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a role="button" onclick="window.location.href = 'staff_list.php';" class="sidebar-link">Staff</a>
                    </li>
                    <li class="sidebar-item">
                        <a role="button" onclick="window.location.href = 'd_riders_list.php';" class="sidebar-link">Delivery Riders</a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a role="button" onclick="window.location.href = 'parcel_add.php';" class="sidebar-link">
                    <i class="lni lni-package"></i>
                    <span>Add Parcel</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a role="button" onclick="window.location.href = 'parcel_list.php';" class="sidebar-link">
                    <i class="lni lni-list"></i>
                    <span>Parcels List</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="rider_load.php" class="sidebar-link">
                    <i class="lni lni-scooter"></i>
                    <span>Rider Load</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a role="button" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse"
                    data-bs-target="#reports-opt" aria-expanded="false" aria-controls="reports-opt">
                    <i class="lni lni-agenda"></i>
                    <span>Reports</span>
                </a>
                <ul id="reports-opt" class="sidebar-dropdown list-unstyled collapse"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a role="button" onclick="window.location.href = 'login_history.php';" class="sidebar-link">Login History</a>
                    </li>
                </ul>
            </li>
            <!-- <li class="sidebar-item">
                <a class="sidebar-link">
                    <i class="lni lni-cog"></i>
                    <span>Settings</span>
                </a>
            </li> -->
        </ul>
        <div class="sidebar-footer">
            <a role="button" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#confirmLogout">
                <i class="lni lni-exit"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>
</div>


<!-- JQuery Script for Changing Tabs
<script src="JQuery/changeTab.js"></script> -->

<!-- // Modal -->
<div class="modal fade" id="confirmLogout" tabindex="-1" aria-labelledby="logoutLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="logoutLabel">Confirm</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to Log out?
            </div>
            <form action="../functions/LogoutHandler.php" method="POST">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Log Out</button>
                </div>
            </form>
        </div>
    </div>
</div>