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
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                @foreach ($errors->all() as $error)
                    <p class="mb-0"><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</p>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Total Collectors Registered Card -->
        <div class="row mt-4 mb-4">
            <!-- <div class="col-md-4">
                <div class="total_users_registered card h-100">
                    <div class="card-body d-flex align-items-start">
                        <div class="card-icon">
                            <img src="{{asset('images/profile.png')}}" alt="Icon 3" class="me-3"
                                style="width: 90px; height: 90px;">
                        </div>
                        <div class="card-content">
                            <h5 class="card-title">Total Collectors Registered</h5>
                            <p class="card-value ms-auto"><strong>{{ $totalCollectors->total_collectors ?? 0 }}</strong></p>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="col-md-4">
                <div class="card h-100" style="border-radius: 14px; position: relative; overflow: hidden; background-image: url('{{ asset('backgrounds/user_icon.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat;">
                    <div class="card-body d-flex align-items-center justify-content-end" style="position: relative; z-index: 2;">
                        <div class="card-content" style="position: relative; z-index: 2;">
                            <p class="card-title" style="color: #333; font-weight: 400; font-size: 15px; height: 4px;">Total Collectors Registered</p>
                            <p class="card-value ms-auto" style="color: #333; font-weight: 600; font-size: 56px;">
                                <strong>
                                {{ $totalCollectors->total_collectors ?? 0 }}
                                </strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr style="border: 2px solidrgb(223, 227, 231); margin-bottom: 20px;">

        <!-- Collectors Table -->
        <div class="table-caption-wrapper">
            <h3>List of Registered Collectors</h3>
            <div class="d-flex align-items-center gap-2 flex-wrap" id="search-container">
                <span id="search-label">Search Collector</span>
                <button class="btn btn-outline-success btn-sm" id="search-toggle" type="button">
                    <i class="bi bi-search"></i>
                </button>
                <div id="search-form" class="search-form">
                    <input class="form-control form-control-sm" id="live-search-input" type="text" placeholder="Search by name, email..." />
                </div>
            </div>
        </div>

        <table class="table caption-top">
            <caption>List of Registered Collectors</caption>
            <thead>
                <tr>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white; border-top-left-radius: 10px;">
                        <i class="bi bi-person me-2"></i>First Name
                    </th>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                        <i class="bi bi-person-badge me-2"></i>Last Name
                    </th>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                        <i class="bi bi-envelope me-2"></i>Email
                    </th>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                        <i class="bi bi-telephone me-2"></i>Cellphone
                    </th>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                        <i class="bi bi-calendar-event me-2"></i>Registered Date
                    </th>
                    <th scope="col" class="text-end" style="background-color: rgb(21, 130, 45); color: white; border-top-right-radius: 10px;">
                        <i class="bi bi-gear me-2"></i>Actions
                    </th>
                </tr>
            </thead>
            <tbody id="collectors-table-body">
                @forelse ($collectors as $collector)
                    <tr class="table-row" data-firstname="{{ strtolower($collector->coll_fname ?? '') }}" 
                        data-lastname="{{ strtolower($collector->coll_lname ?? '') }}" 
                        data-email="{{ strtolower($collector->email ?? '') }}">
                        <td>{{ $collector->coll_fname ?? 'N/A' }}</td>
                        <td>{{ $collector->coll_lname ?? 'N/A' }}</td>
                        <td>{{ $collector->email ?? 'N/A' }}</td>
                        <td>{{ $collector->collcell_num ?? 'N/A' }}</td>
                        <td>{{ $collector->created_at ? \Carbon\Carbon::parse($collector->created_at)->format('M d, Y') : 'N/A' }}</td>
                        <td class="text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-sm btn-info text-white" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#collectorDetailsModal{{ $collector->key }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                                <button type="button" class="btn btn-sm btn-warning text-white" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editCollectorModal{{ $collector->key }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <form action="{{ route('delete.collector', $collector->key) }}" method="POST" style="display:inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this collector? This will also remove their Firebase Auth account. This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Collector Details Modal -->
                    <div class="modal fade" id="collectorDetailsModal{{ $collector->key }}" tabindex="-1" aria-labelledby="collectorDetailsModalLabel{{ $collector->key }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header text-white" style="background-image: url('{{ asset('backgrounds/bg1.png') }}'); background-size: cover; background-position: center left; background-repeat: no-repeat; padding-top: 0; padding-left: 0; padding-bottom: 0; padding-right: 20px;">
                                    <div style="background-image: url('{{ asset('backgrounds/user_icon.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat; width:48px; height: 48px;">
                                    </div>
                                    <h5 class="modal-title" id="collectorDetailsModalLabel{{ $collector->key }}" style="margin-left: 8px; font-size: 18px;">
                                        Collector Details
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">First Name</label>
                                            <p class="form-control-plaintext">{{ $collector->coll_fname ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Last Name</label>
                                            <p class="form-control-plaintext">{{ $collector->coll_lname ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Email</label>
                                            <p class="form-control-plaintext">{{ $collector->email ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Cellphone</label>
                                            <p class="form-control-plaintext">{{ $collector->collcell_num ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Registered Date</label>
                                            <p class="form-control-plaintext">
                                                {{ $collector->created_at ? \Carbon\Carbon::parse($collector->created_at)->format('F d, Y') : 'N/A' }}
                                            </p>
                                        </div>
                                        @if($collector->created_at)
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Registered Time</label>
                                            <p class="form-control-plaintext">
                                                {{ \Carbon\Carbon::parse($collector->created_at)->format('h:i A') }}
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

                    <!-- Edit Collector Modal -->
                    <div class="modal fade" id="editCollectorModal{{ $collector->key }}" tabindex="-1" aria-labelledby="editCollectorModalLabel{{ $collector->key }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header text-white" style="background-image: url('{{ asset('backgrounds/bg1.png') }}'); background-size: cover; background-position: center left; background-repeat: no-repeat; padding-top: 0; padding-left: 0; padding-bottom: 0; padding-right: 20px;">
                                    <div style="background-image: url('{{ asset('backgrounds/user_icon.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat; width:48px; height: 48px;">
                                    </div>
                                    <h5 class="modal-title" id="editCollectorModalLabel{{ $collector->key }}" style="margin-left: 8px; font-size: 18px;">
                                        Edit Collector Details
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('update.collector', $collector->key) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="edit_coll_fname{{ $collector->key }}" class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="edit_coll_fname{{ $collector->key }}" name="coll_fname" value="{{ $collector->coll_fname ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_coll_lname{{ $collector->key }}" class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="edit_coll_lname{{ $collector->key }}" name="coll_lname" value="{{ $collector->coll_lname ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_email{{ $collector->key }}" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="edit_email{{ $collector->key }}" name="email" value="{{ $collector->email ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_collcell_num{{ $collector->key }}" class="form-label fw-bold">Cellphone</label>
                                                <input type="text" class="form-control" id="edit_collcell_num{{ $collector->key }}" name="collcell_num" value="{{ $collector->collcell_num ?? '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_username{{ $collector->key }}" class="form-label fw-bold">Username</label>
                                                <input type="text" class="form-control" id="edit_username{{ $collector->key }}" name="username" value="{{ $collector->username ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No collectors found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot id="empty-message" style="display: none;">
                <tr>
                    <td colspan="6" class="text-center">No collectors found matching your search</td>
                </tr>
            </tfoot>
        </table>

        {{-- Pagination --}}
        @include('components.pagination', ['paginator' => $collectors])
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById("search-toggle");
        const searchForm = document.getElementById("search-form");
        const searchInput = document.getElementById("live-search-input");
        const tableBody = document.getElementById("collectors-table-body");
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
                    const email = row.getAttribute("data-email") || "";
                    
                    const matches = searchTerm === "" || 
                                  firstname.includes(searchTerm) || 
                                  lastname.includes(searchTerm) || 
                                  email.includes(searchTerm);

                    if (matches) {
                        row.classList.remove("hidden");
                        visibleCount++;
                    } else {
                        row.classList.add("hidden");
                    }
                });

                // Show/hide empty message
                if (visibleCount === 0 && searchTerm !== "") {
                    emptyMessage.style.display = "table-row-group";
                } else {
                    emptyMessage.style.display = "none";
                }
            });
        }
    });
</script>
@endpush
