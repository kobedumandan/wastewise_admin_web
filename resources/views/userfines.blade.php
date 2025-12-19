@extends('layouts.admin')

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

        <!-- Total Paid and Unpaid Fines Cards -->
        <div class="row mt-4 mb-4">
            <div class="col-md-4">
                <div class="card h-100" style="border-radius: 14px; position: relative; overflow: hidden; background-image: url('{{ asset('backgrounds/payments.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat;">
                    <div class="card-body d-flex align-items-center justify-content-end" style="position: relative; z-index: 2;">
                        <div class="card-content" style="position: relative; z-index: 2;">
                            <p class="card-title" style="color: #333; font-weight: 400; font-size: 15px; height: 4px;">Total Paid Fines</p>
                            <p class="card-value ms-auto" style="color: #333; font-weight: 600; font-size: 56px;">
                                <strong>
                                {{ $totalPaidFines ?? 0 }}
                                </strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100" style="border-radius: 14px; position: relative; overflow: hidden; background-image: url('{{ asset('backgrounds/payments.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat;">
                    <div class="card-body d-flex align-items-center justify-content-end" style="position: relative; z-index: 2;">
                        <div class="card-content" style="position: relative; z-index: 2;">
                            <p class="card-title" style="color: #333; font-weight: 400; font-size: 15px; height: 4px;">Total Unpaid Fines</p>
                            <p class="card-value ms-auto" style="color: #333; font-weight: 600; font-size: 56px;">
                                <strong>
                                {{ $totalUnpaidFines ?? 0 }}
                                </strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr style="border: 2px solidrgb(223, 227, 231); margin-bottom: 20px;">

        <!-- Filter and Search Form -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <label for="paymentFilter" class="form-label mb-0 fw-bold">Filter by Payment Status:</label>
                <form action="{{ route('userfines') }}" method="GET" id="filter-form" style="display: inline;">
                    <select name="payment_status" id="paymentFilter" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="unpaid" {{ request('payment_status', 'unpaid') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="all" {{ request('payment_status') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </form>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span id="search-label">Search Fines</span>
                <button class="btn btn-outline-success btn-sm" id="search-toggle" type="button">
                    <i class="bi bi-search"></i>
                </button>
                <input class="form-control form-control-sm" id="live-search-input" type="text" placeholder="Search by name..." value="" style="display: {{ request('payment_status') === 'all' ? 'block' : 'none' }}; width: 250px;" />
            </div>
        </div>

        <!-- Fines Table -->
        <table class="table caption-top">
            <caption>List of User Fines</caption>
            <thead>
                <tr>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white; border-top-left-radius: 10px;">
                        <i class="bi bi-person me-2"></i>First Name
                    </th>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                        <i class="bi bi-person-badge me-2"></i>Last Name
                    </th>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                        <i class="bi bi-cash-coin me-2"></i>Amount
                    </th>
                    <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                        <i class="bi bi-calendar-event me-2"></i>Issued on
                    </th>
                    <th scope="col" class="text-end" style="background-color: rgb(21, 130, 45); color: white; border-top-right-radius: 10px;">
                        <i class="bi bi-gear me-2"></i>Actions
                    </th>
                </tr>
            </thead>
            <tbody id="fines-table-body">
                @forelse ($fines as $fine)
                    <tr class="table-row" 
                        data-firstname="{{ strtolower($fine->firstname ?? '') }}" 
                        data-lastname="{{ strtolower($fine->lastname ?? '') }}">
                        <td>{{ $fine->firstname ?? 'N/A' }}</td>
                        <td>{{ $fine->lastname ?? 'N/A' }}</td>
                        <td>₱{{ number_format($fine->amount ?? 0, 2) }}</td>
                        <td>{{ $fine->date ? \Carbon\Carbon::parse($fine->date)->format('M d, Y') : 'N/A' }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-info text-white" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#fineDetailsModal{{ $fine->key }}">
                                <i class="bi bi-eye"></i> Details
                            </button>
                        </td>
                    </tr>
                    
                    {{-- Fine Details Modal --}}
                    <div class="modal fade" id="fineDetailsModal{{ $fine->key }}" tabindex="-1" aria-labelledby="fineDetailsModalLabel{{ $fine->key }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header text-white" style="background-image: url('{{ asset('backgrounds/bg1.png') }}'); background-size: cover; background-position: center left; background-repeat: no-repeat; padding-top: 0; padding-left: 0; padding-bottom: 0; padding-right: 20px;">
                                    <div style="background-image: url('{{ asset('backgrounds/user_icon.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat; width:48px; height: 48px;">
                                    </div>
                                    <h5 class="modal-title" id="fineDetailsModalLabel{{ $fine->key }}" style="margin-left: 8px; font-size: 18px;">
                                        Fine Details
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">First Name</label>
                                            <p class="form-control-plaintext">{{ $fine->firstname ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Last Name</label>
                                            <p class="form-control-plaintext">{{ $fine->lastname ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Purok</label>
                                            <p class="form-control-plaintext">{{ $fine->purok ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Household Number</label>
                                            <p class="form-control-plaintext">{{ $fine->household_number ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Amount</label>
                                            <p class="form-control-plaintext">₱{{ number_format($fine->amount ?? 0, 2) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Issued on</label>
                                            <p class="form-control-plaintext">{{ $fine->date ? \Carbon\Carbon::parse($fine->date)->format('F d, Y h:i A') : 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Violation Details</label>
                                            <p class="form-control-plaintext" id="violationDetails{{ $fine->key }}">Loading...</p>
                                        </div>
                                    </div>
                                    @if(isset($fine->paid) && $fine->paid)
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Payment Status</label>
                                            <p class="form-control-plaintext"><span class="badge bg-success">Paid</span></p>
                                        </div>
                                        @if(isset($fine->paid_on))
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Paid On</label>
                                            <p class="form-control-plaintext">{{ \Carbon\Carbon::parse($fine->paid_on)->format('F d, Y h:i A') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    @if(!isset($fine->paid) || !$fine->paid)
                                    <form action="{{ route('mark.fine.paid') }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to mark this fine as paid?');">
                                        @csrf
                                        <input type="hidden" name="fine_key" value="{{ $fine->key }}">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Mark as Paid
                                        </button>
                                    </form>
                                    @endif
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Load violation details via AJAX --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const modal = document.getElementById('fineDetailsModal{{ $fine->key }}');
                            if (modal) {
                                modal.addEventListener('show.bs.modal', function() {
                                    const detailsElement = document.getElementById('violationDetails{{ $fine->key }}');
                                    if (detailsElement && detailsElement.textContent === 'Loading...') {
                                        fetch('{{ route("get.fine.details", $fine->key) }}')
                                            .then(response => response.json())
                                            .then(data => {
                                                detailsElement.textContent = data.violation_details || 'N/A';
                                            })
                                            .catch(error => {
                                                detailsElement.textContent = 'N/A';
                                            });
                                    }
                                });
                            }
                        });
                    </script>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No fines found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot id="empty-message" style="display: none;">
                <tr>
                    <td colspan="5" class="text-center">No fines found matching your search</td>
                </tr>
            </tfoot>
        </table>

        {{-- Pagination --}}
        @if(isset($paymentFilter) && $paymentFilter === 'all')
            {{-- Don't show pagination when filter is "all" for live search --}}
        @else
            @include('components.pagination', ['paginator' => $fines])
        @endif
    </div>
@endsection

@push('styles')
<style>
    .table-row {
        display: table-row;
    }
    
    .table-row.hidden {
        display: none;
    }
    
    #live-search-input {
        transition: all 0.3s ease;
    }
    
    #live-search-input.active {
        display: block !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('live-search-input');
        const searchToggle = document.getElementById('search-toggle');
        const searchLabel = document.getElementById('search-label');
        const paymentFilter = document.getElementById('paymentFilter');
        const tableBody = document.getElementById('fines-table-body');
        const tableRows = tableBody.querySelectorAll('tr.table-row');
        const emptyMessage = document.getElementById('empty-message');
        
        // Toggle search input visibility
        if (searchToggle && searchInput) {
            // Show search input if filter is "all"
            if (paymentFilter && paymentFilter.value === 'all') {
                searchInput.style.display = 'block';
                searchLabel.style.display = 'none';
            }
            
            searchToggle.addEventListener('click', function() {
                const isActive = searchInput.style.display === 'block' || searchInput.classList.contains('active');
                
                if (!isActive) {
                    // Show search input
                    searchInput.style.display = 'block';
                    searchInput.classList.add('active');
                    searchInput.focus();
                    searchLabel.style.display = 'none';
                    
                    // If filter is not "all", change it
                    if (paymentFilter && paymentFilter.value !== 'all') {
                        window.location.href = '{{ route("userfines") }}?payment_status=all';
                        return;
                    }
                } else {
                    // Hide search input
                    searchInput.style.display = 'none';
                    searchInput.classList.remove('active');
                    searchInput.value = '';
                    filterTable('');
                    searchLabel.style.display = 'inline';
                }
            });
        }
        
        // Live search functionality
        if (searchInput) {
            // When user starts typing, automatically change filter to "all" if not already
            searchInput.addEventListener('focus', function() {
                if (paymentFilter && paymentFilter.value !== 'all') {
                    // Redirect to show all fines for searching
                    window.location.href = '{{ route("userfines") }}?payment_status=all';
                    return;
                }
            });
            
            // Live search as user types
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                // If filter is not "all" and user is searching, change it immediately
                if (searchTerm && paymentFilter && paymentFilter.value !== 'all') {
                    window.location.href = '{{ route("userfines") }}?payment_status=all';
                    return;
                }
                
                // Filter table rows in real-time
                filterTable(searchTerm);
            });
        }
        
        function filterTable(searchTerm) {
            let visibleCount = 0;
            
            if (!tableRows || tableRows.length === 0) {
                return;
            }
            
            tableRows.forEach(function(row) {
                const firstname = row.getAttribute('data-firstname') || '';
                const lastname = row.getAttribute('data-lastname') || '';
                
                const matches = searchTerm === '' || 
                              firstname.includes(searchTerm) || 
                              lastname.includes(searchTerm);
                
                if (matches) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });
            
            // Show/hide empty message
            if (visibleCount === 0 && searchTerm !== '') {
                if (emptyMessage) {
                    emptyMessage.style.display = 'table-row-group';
                }
            } else {
                if (emptyMessage) {
                    emptyMessage.style.display = 'none';
                }
            }
        }
    });
</script>
@endpush
