@extends('layouts.admin')

@push('styles')
<style>
    .search-form {
        display: none;
    }
    
    .search-form.active {
        display: block;
    }
    
    #search-container {
        position: relative;
    }
    
    .table-row {
        display: table-row;
    }
    
    .table-row.hidden {
        display: none;
    }
    
    .table-caption-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .table-caption-wrapper h3 {
        margin: 0;
        font-weight: bold;
        color: #333;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <div class="row mt-4">
            <!-- <div class="col-md-4">
                <div class="card" style="background: white;">
                    <div class="card-body d-flex align-items-start">
                        <img src="{{asset('images/settings.png')}}" alt="Icon 3" class="me-3" style="width: 90px; height: 90px;">
                        <div>
                            <h5 class="card-title" style="color:#062c0f;">Updates and Deletions made by Administrators</h5>
                            <h1 class="mt-4 ms-auto" style="width: fit-content; font-weight: bold; font-size: 50px; color: #062c0f;"> </h1>
                        </div>
                    </div>
                </div>
            </div>] -->
            <div class="col-md-4">
                <div class="card h-100" style="border-radius: 14px; position: relative; overflow: hidden; background-image: url('{{ asset('backgrounds/user_icon.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat;">
                    <div class="card-body d-flex align-items-center justify-content-end" style="position: relative; z-index: 2;">
                        <div class="card-content" style="position: relative; z-index: 2;">
                            <p class="card-title" style="color: #333; font-weight: 400; font-size: 15px; height: 4px;">Total Actions Performed by Admin</p>
                            <p class="card-value ms-auto" style="color: #333; font-weight: 600; font-size: 56px;">
                                <strong>
                                {{ $totalAudits ?? 0 }}
                                </strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr style="border: 2px solidrgb(223, 227, 231); margin-bottom: 20px;">

        <!-- Admin Audit Table -->
        <div class="table-caption-wrapper">
            <h3>Admin Trail Data</h3>
            <div class="d-flex align-items-center gap-2">
                <span id="search-label">Search Admin</span>
                <button class="btn btn-outline-success btn-sm" id="search-toggle" type="button">
                    <i class="bi bi-search"></i>
                </button>
                <div id="search-form" class="search-form">
                    <input class="form-control form-control-sm" id="live-search-input" type="text" placeholder="Search by name..." />
                </div>
            </div>
        </div>

        <table class="table caption-top">
            <caption>List of data updated or deleted</caption>
            <thead>
                <tr>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white; border-top-left-radius: 10px;">
                        <i class="bi bi-person me-2"></i>First Name
                    </th>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                        <i class="bi bi-person-badge me-2"></i>Last Name
                    </th>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                        <i class="bi bi-tag me-2"></i>Action
                    </th>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                        <i class="bi bi-info-circle me-2"></i>Details
                    </th>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                        <i class="bi bi-calendar-event me-2"></i>Performed on
                    </th>
                    <th scope="col" class="text-end" style="background-color: rgb(21, 130, 45); color: white; border-top-right-radius: 10px;">
                        <i class="bi bi-gear me-2"></i>Actions
                    </th>
                </tr>
            </thead>
            <tbody id="audits-table-body">
                @forelse ($audits as $audit)
                    <tr class="table-row" 
                        data-firstname="{{ strtolower($audit->firstname ?? '') }}" 
                        data-lastname="{{ strtolower($audit->lastname ?? '') }}">
                        <td>{{ $audit->firstname ?? 'N/A' }}</td>
                        <td>{{ $audit->lastname ?? 'N/A' }}</td>
                        <td>{{ $audit->action ? ucfirst($audit->action) : 'N/A' }}</td>
                        <td>{{ $audit->action_performed ?? 'N/A' }}</td>
                        <td>{{ $audit->performed_on ? \Carbon\Carbon::parse($audit->performed_on)->format('M d, Y h:i A') : 'N/A' }}</td>
                        <td class="text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-sm btn-info text-white" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#auditDetailsModal{{ $audit->key }}">
                                    <i class="bi bi-eye"></i> Details
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Audit Details Modal -->
                    <div class="modal fade" id="auditDetailsModal{{ $audit->key }}" tabindex="-1" aria-labelledby="auditDetailsModalLabel{{ $audit->key }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header text-white" style="background-image: url('{{ asset('backgrounds/bg1.png') }}'); background-size: cover; background-position: center left; background-repeat: no-repeat; padding-top: 0; padding-left: 0; padding-bottom: 0; padding-right: 20px;">
                                    <div style="background-image: url('{{ asset('backgrounds/user_icon.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat; width:48px; height: 48px;">
                                    </div>
                                    <h5 class="modal-title" id="auditDetailsModalLabel{{ $audit->key }}" style="margin-left: 8px; font-size: 18px;">
                                        Audit Details
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Admin First Name</label>
                                            <p class="form-control-plaintext">{{ $audit->firstname ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Admin Last Name</label>
                                            <p class="form-control-plaintext">{{ $audit->lastname ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Action</label>
                                            <p class="form-control-plaintext">{{ $audit->action ? ucfirst($audit->action) : 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Action Performed</label>
                                            <p class="form-control-plaintext">{{ $audit->action_performed ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">User/Collector UID</label>
                                            <p class="form-control-plaintext">{{ $audit->uid ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Admin ID</label>
                                            <p class="form-control-plaintext">{{ $audit->admin_id ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Performed on</label>
                                            <p class="form-control-plaintext">
                                                {{ $audit->performed_on ? \Carbon\Carbon::parse($audit->performed_on)->format('F d, Y') : 'N/A' }}
                                            </p>
                                        </div>
                                        @if($audit->performed_on)
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Performed Time</label>
                                            <p class="form-control-plaintext">
                                                {{ \Carbon\Carbon::parse($audit->performed_on)->format('h:i A') }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No audit records found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot id="empty-message" style="display: none;">
                <tr>
                    <td colspan="6" class="text-center">No audit records found matching your search</td>
                </tr>
            </tfoot>
        </table>

        {{-- Pagination --}}
        @include('components.pagination', ['paginator' => $audits])
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById("search-toggle");
        const searchForm = document.getElementById("search-form");
        const searchInput = document.getElementById("live-search-input");
        const tableBody = document.getElementById("audits-table-body");
        const emptyMessage = document.getElementById("empty-message");
        const tableRows = tableBody.querySelectorAll("tr.table-row");

        // Toggle search bar
        if (toggleBtn && searchForm) {
            toggleBtn.addEventListener("click", function () {
                searchForm.classList.toggle("active");
                if (searchForm.classList.contains("active")) {
                    searchInput.focus();
                }
            });
        }

        // Live search functionality
        if (searchInput) {
            searchInput.addEventListener("input", function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;

                tableRows.forEach(function(row) {
                    const firstname = row.getAttribute("data-firstname") || "";
                    const lastname = row.getAttribute("data-lastname") || "";
                    
                    const matches = searchTerm === "" || 
                                  firstname.includes(searchTerm) || 
                                  lastname.includes(searchTerm);

                    if (matches) {
                        row.classList.remove("hidden");
                        visibleCount++;
                    } else {
                        row.classList.add("hidden");
                    }
                });

                // Show/hide empty message
                if (visibleCount === 0 && searchTerm !== "") {
                    if (emptyMessage) {
                        emptyMessage.style.display = "table-row-group";
                    }
                } else {
                    if (emptyMessage) {
                        emptyMessage.style.display = "none";
                    }
                }
            });
        }
    });
</script>
@endpush
