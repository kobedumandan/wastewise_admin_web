@extends('layouts.admin')

@push('styles')
<style>
    .search-input {
        display: none;
        width: 200px;
    }
    
    .search-input.active {
        display: block;
    }
    
    #search-container {
        position: relative;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <!-- Total Admins Registered Card -->
        <div class="row mt-4 mb-4">
            <!-- <div class="col-md-4">
                <div class="total_users_registered card h-100">
                    <div class="card-body d-flex align-items-start">
                        <div class="card-icon">
                            <img src="{{asset('images/profile.png')}}" alt="Icon 3" class="me-3"
                                style="width: 90px; height: 90px;">
                        </div>
                        <div class="card-content">
                            <h5 class="card-title">Total Administrators Registered</h5>
                            <p class="card-value ms-auto"><strong>{{ $totalAdmins->total_admins ?? 0 }}</strong></p>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="col-md-4">
                <div class="card h-100" style="border-radius: 14px; position: relative; overflow: hidden; background-image: url('{{ asset('backgrounds/user_icon.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat;">
                    <div class="card-body d-flex align-items-center justify-content-end" style="position: relative; z-index: 2;">
                        <div class="card-content" style="position: relative; z-index: 2;">
                            <p class="card-title" style="color: #333; font-weight: 400; font-size: 15px; height: 4px;">Total Administrators Registered</p>
                            <p class="card-value ms-auto" style="color: #333; font-weight: 600; font-size: 56px;">
                                <strong>
                                {{ $totalAdmins->total_admins ?? 0 }}
                                </strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr style="border: 2px solidrgb(223, 227, 231); margin-bottom: 20px;">

        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <div class="d-flex align-items-center gap-2">
                <h2 style="font-weight: bold; color: #333; margin: 0;">
                    Logs
                    </h2>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap" id="search-container">
                <span id="search-label" class="text-dark">Search Admin</span>
                <button class="btn btn-outline-success btn-sm" id="search-toggle" type="button">
                    <i class="bi bi-search"></i>
                </button>
                <input class="form-control form-control-sm search-input" id="admin-log-search-input" type="text" placeholder="Search by Lastname, Username..." />
            </div>
        </div>

        <div class="table-responsive mb-5">
            <table class="table caption-top">
                <caption>List of admin accounts</caption>
                <thead>
                    <tr>
                        <th style="background-color: rgb(21, 130, 45); color: white; border-top-left-radius: 10px;">
                            <i class="bi bi-person-badge me-2"></i>Lastname
                        </th>
                        <th style="background-color: rgb(21, 130, 45); color: white;">
                            <i class="bi bi-telephone me-2"></i>Cellphone Number
                        </th>
                        <th style="background-color: rgb(21, 130, 45); color: white;">
                            <i class="bi bi-person-circle me-2"></i>Username
                        </th>
                        <th style="background-color: rgb(21, 130, 45); color: white;">
                            <i class="bi bi-clock-history me-2"></i>Time In
                        </th>
                        <th style="background-color: rgb(21, 130, 45); color: white; border-top-right-radius: 10px;">
                            <i class="bi bi-clock me-2"></i>Time Out
                        </th>
                    </tr>
                </thead>
                <tbody id="admin-logs-table-body">
                    @forelse ($logs as $log)
                        <tr data-lname="{{ strtolower($log->last_name ?? '') }}" 
                            data-username="{{ strtolower($log->admin_username ?? '') }}"
                            data-cell="{{ strtolower($log->cell_number ?? '') }}">
                            <td>{{ $log->last_name ?? 'N/A' }}</td>
                            <td>{{ $log->cell_number ?? 'N/A' }}</td>
                            <td>{{ $log->admin_username ?? 'N/A' }}</td>
                            <td>{{ $log->admin_timein ? \Carbon\Carbon::parse($log->admin_timein)->format('M d, Y h:i A') : 'N/A' }}</td>
                            <td>{{ $log->admin_timeout ? \Carbon\Carbon::parse($log->admin_timeout)->format('M d, Y h:i A') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No admin logs found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot style="background-color: #D1E7DD; padding: 0.25rem 0.5rem;">
                    <tr>
                        <td colspan="5">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <img class="mb-3" src="{{ asset('images/wastewiselogo.png') }}" alt="Logo" style="height: 30px;">
                                    <h5 class="" style="font-size: 15px; color: #033402;">WasteWise</h5>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-size: 15px; color: #033402;">Total: {{ $logs->total() }}</h5>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Pagination --}}
        <div id="pagination-container">
            @include('components.pagination', ['paginator' => $logs])
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('admin-log-search-input');
        const tableBody = document.querySelector('#admin-logs-table-body');
        const tableRows = tableBody.querySelectorAll('tr');
        const searchToggle = document.getElementById('search-toggle');
        const searchLabel = document.getElementById('search-label');

        // Toggle search input visibility
        searchToggle.addEventListener('click', function() {
            searchInput.classList.toggle('active');
            if (searchInput.classList.contains('active')) {
                searchInput.focus();
                searchLabel.style.display = 'none'; // Hide label when input is active
            } else {
                searchInput.value = ''; // Clear search when closing
                filterTable(''); // Show all rows
                searchLabel.style.display = 'inline'; // Show label when input is hidden
            }
        });

        searchInput.addEventListener('keyup', function() {
            const searchTerm = searchInput.value.toLowerCase();
            filterTable(searchTerm);
        });

        function filterTable(searchTerm) {
            let foundResults = false;
            const existingEmptyRow = tableBody.querySelector('tr:not([data-lname]):not([data-username]):not([data-cell])');
            
            tableRows.forEach(row => {
                // Skip the empty state row if it exists
                if (!row.dataset.lname && !row.dataset.username && !row.dataset.cell) {
                    // This is the empty state row, hide it when searching
                    if (searchTerm) {
                        row.style.display = 'none';
                    }
                    return;
                }

                const lastName = row.dataset.lname || '';
                const username = row.dataset.username || '';
                const cellNumber = row.dataset.cell || '';

                if (lastName || username || cellNumber) {
                    if (searchTerm === '' || lastName.includes(searchTerm) || username.includes(searchTerm) || cellNumber.includes(searchTerm)) {
                        row.style.display = '';
                        foundResults = true;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            const noResultsRow = tableBody.querySelector('.no-results-row');
            const paginationContainer = document.getElementById('pagination-container');
            
            if (!foundResults && searchTerm && tableRows.length > 0) {
                if (!noResultsRow) {
                    const newRow = document.createElement('tr');
                    newRow.classList.add('no-results-row');
                    newRow.innerHTML = `<td colspan="5" class="text-center">No admin logs found matching your search.</td>`;
                    tableBody.appendChild(newRow);
                }
                // Hide pagination when search returns no results
                if (paginationContainer) {
                    paginationContainer.style.display = 'none';
                }
            } else {
                if (noResultsRow) {
                    noResultsRow.remove();
                }
                // Show pagination when there are results or no search term
                if (paginationContainer) {
                    paginationContainer.style.display = '';
                }
            }
        }
    });
</script>
@endpush
