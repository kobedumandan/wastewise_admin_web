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
    
    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.85em;
        font-weight: 500;
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

        <!-- Header Card -->
        <div class="row mt-4 mb-4">
        <div class="col-md-4">
                <div class="card h-100" style="border-radius: 14px; position: relative; overflow: hidden; background-image: url('{{ asset('backgrounds/user_icon.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat;">
                    <div class="card-body d-flex align-items-center justify-content-end" style="position: relative; z-index: 2;">
                        <div class="card-content" style="position: relative; z-index: 2;">
                            <p class="card-title" style="color: #333; font-weight: 400; font-size: 15px; height: 4px;">Total Pending Requests</p>
                            <p class="card-value ms-auto" style="color: #333; font-weight: 600; font-size: 56px;">
                                <strong>
                                {{ $totalPendingRequests ?? 0 }}
                                </strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-start">
                        <img src="{{asset('images/settings.png')}}" alt="Icon" class="me-3" style="width: 90px; height: 90px;">
                        <div>
                            <h5 class="card-title" style="color:#062c0f;">Total Pending Requests</h5>
                            <h1 class="mt-4 ms-auto" style="width: fit-content; font-weight: bold; font-size: 50px; color: #062c0f;">{{ $totalPendingRequests ?? 0 }}</h1>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>

        <hr style="border: 2px solidrgb(223, 227, 231); margin-bottom: 20px;">


        <!-- Requests Table -->
        <div class="table-caption-wrapper mb-4">
            <h3>List of Change Requests</h3>
        </div>

        <!-- Filter and Search Form -->
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <label for="statusFilter" class="form-label mb-0 fw-bold">Filter by Status:</label>
                <form action="{{ route('change.credential.requests') }}" method="GET" id="filter-form" style="display: inline;">
                    <select name="status" id="statusFilter" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </form>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span id="search-label">Search Requests</span>
                <button class="btn btn-outline-success btn-sm" id="search-toggle" type="button">
                    <i class="bi bi-search"></i>
                </button>
                <div id="search-form" class="search-form">
                    <input class="form-control form-control-sm" id="live-search-input" type="text" placeholder="Search by name, email..." />
                </div>
            </div>
        </div>

        <div class="table-responsive mb-5">
            <table class="table caption-top">
                <caption>List of {{ request('status', 'pending') }} credential change requests</caption>
                <thead class="table-success">
                    <tr>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Requested On</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="requests-table-body">
                    @forelse ($requests as $req)
                        <tr class="table-row" 
                            data-firstname="{{ strtolower($req->firstname ?? '') }}" 
                            data-lastname="{{ strtolower($req->lastname ?? '') }}"
                            data-email="{{ strtolower($req->email ?? '') }}">
                            <td>{{ $req->firstname ?? 'N/A' }}</td>
                            <td>{{ $req->lastname ?? 'N/A' }}</td>
                            <td>{{ $req->email ?? 'N/A' }}</td>
                            <td>
                                {{ $req->created_at ? \Carbon\Carbon::parse($req->created_at)->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-sm btn-info text-white" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#requestDetailsModal{{ $req->key }}">
                                        <i class="bi bi-eye"></i> Details
                                    </button>
                                    @php
                                        $currentStatus = $req->status ?? 'pending';
                                    @endphp
                                    @if($currentStatus !== 'accepted' && $currentStatus !== 'rejected')
                                    <form action="{{ route('accept.credential.request') }}" method="POST" style="display:inline;" 
                                          onsubmit="return confirm('Are you sure you want to accept this credential change request?');">
                                        @csrf
                                        <input type="hidden" name="request_key" value="{{ $req->key }}">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-circle"></i> Accept
                                        </button>
                                    </form>
                                    <form action="{{ route('reject.credential.request') }}" method="POST" style="display:inline;" 
                                          onsubmit="return confirm('Are you sure you want to reject this credential change request?');">
                                        @csrf
                                        <input type="hidden" name="request_key" value="{{ $req->key }}">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Request Details Modal --}}
                        <div class="modal fade" id="requestDetailsModal{{ $req->key }}" tabindex="-1" aria-labelledby="requestDetailsModalLabel{{ $req->key }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="requestDetailsModalLabel{{ $req->key }}">
                                            <i class="bi bi-key me-2"></i>
                                            Credential Change Request Details
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">First Name</label>
                                                <p class="form-control-plaintext">{{ $req->user_fname ?? $req->firstname ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Last Name</label>
                                                <p class="form-control-plaintext">{{ $req->user_lname ?? $req->lastname ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">UID</label>
                                                <p class="form-control-plaintext">{{ $req->uid ?? $req->user_id ?? 'N/A' }}</p>
                                            </div>
                                            @if(isset($req->purok))
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Purok</label>
                                                <p class="form-control-plaintext">{{ $req->purok ?? 'N/A' }}</p>
                                            </div>
                                            @endif
                                            @if(isset($req->household_number))
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Household Number</label>
                                                <p class="form-control-plaintext">{{ $req->household_number ?? 'N/A' }}</p>
                                            </div>
                                            @endif
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Requested On</label>
                                                <p class="form-control-plaintext">
                                                    {{ $req->created_at ? \Carbon\Carbon::parse($req->created_at)->format('F d, Y h:i A') : 'N/A' }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Status</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge {{ ($req->status ?? 'pending') === 'accepted' ? 'bg-success' : (($req->status ?? 'pending') === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                                        {{ ucfirst($req->status ?? 'pending') }}
                                                    </span>
                                                </p>
                                            </div>
                                            @if(($req->status ?? 'pending') !== 'pending' && isset($req->processed_at))
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Processed On</label>
                                                <p class="form-control-plaintext">
                                                    {{ $req->processed_at ? \Carbon\Carbon::parse($req->processed_at)->format('F d, Y h:i A') : 'N/A' }}
                                                </p>
                                            </div>
                                            @endif
                                            @if(($req->status ?? 'pending') !== 'pending' && isset($req->processed_by))
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Processed By</label>
                                                <p class="form-control-plaintext">{{ $req->processed_by ?? 'N/A' }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        @php
                                            $modalStatus = $req->status ?? 'pending';
                                        @endphp
                                        @if($modalStatus !== 'accepted' && $modalStatus !== 'rejected')
                                        <form action="{{ route('accept.credential.request') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="request_key" value="{{ $req->key }}">
                                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to accept this request?');">
                                                <i class="bi bi-check-circle"></i> Accept Request
                                            </button>
                                        </form>
                                        <form action="{{ route('reject.credential.request') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="request_key" value="{{ $req->key }}">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this request?');">
                                                <i class="bi bi-x-circle"></i> Reject Request
                                            </button>
                                        </form>
                                        @else
                                        
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No {{ request('status', 'pending') }} credential change requests found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot id="empty-message" style="display: none;">
                    <tr>
                        <td colspan="5" class="text-center">No requests found matching your search</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Pagination --}}
        @include('components.pagination', ['paginator' => $requests])
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById("search-toggle");
        const searchForm = document.getElementById("search-form");
        const searchInput = document.getElementById("live-search-input");
        const tableBody = document.getElementById("requests-table-body");
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
