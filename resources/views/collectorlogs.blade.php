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
        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <div class="d-flex align-items-center gap-2">
                <h2 style="font-weight: bold; color: #333; margin: 0;">
                    Logs
                 </h2>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap" id="search-container">
                <span id="search-label" class="text-dark">Search Collector</span>
                <button class="btn btn-outline-success btn-sm" id="search-toggle" type="button">
                    <i class="bi bi-search"></i>
                </button>
                <input class="form-control form-control-sm search-input" id="collector-log-search-input" type="text" placeholder="Search by First Name, Last Name..." />
            </div>
        </div>

        <div class="table-responsive mb-5">
            <table class="table caption-top">
                <caption>List of Collector Time Logs</caption>
                <thead>
                    <tr>
                        <th scope="col" style="background-color: rgb(21, 130, 45); color: white; border-top-left-radius: 10px;">
                            <i class="bi bi-person me-2"></i>First Name
                        </th>
                        <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                            <i class="bi bi-person-badge me-2"></i>Last Name
                        </th>
                        <th scope="col" style="background-color: rgb(21, 130, 45); color: white;">
                            <i class="bi bi-clock-history me-2"></i>Time In
                        </th>
                        <th scope="col" style="background-color: rgb(21, 130, 45); color: white; border-top-right-radius: 10px;">
                            <i class="bi bi-clock me-2"></i>Time Out
                        </th>
                    </tr>
                </thead>
                <tbody id="collector-logs-table-body">
                    @forelse ($logs as $log)
                        <tr data-fname="{{ strtolower($log->firstname ?? '') }}" 
                            data-lname="{{ strtolower($log->lastname ?? '') }}">
                            <td>{{ $log->firstname ?? 'N/A' }}</td>
                            <td>{{ $log->lastname ?? 'N/A' }}</td>
                            <td>{{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('M d, Y h:i A') : 'N/A' }}</td>
                            <td>{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('M d, Y h:i A') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No collector logs found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot style="background-color: #D1E7DD; padding: 0.25rem 0.5rem;">
                    <tr>
                        <td colspan="4">
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
        const searchInput = document.getElementById('collector-log-search-input');
        const tableBody = document.querySelector('#collector-logs-table-body');
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
            
            tableRows.forEach(row => {
                // Skip the empty state row if it exists
                if (!row.dataset.fname && !row.dataset.lname) {
                    // This is the empty state row, hide it when searching
                    if (searchTerm) {
                        row.style.display = 'none';
                    } else {
                        row.style.display = '';
                    }
                    return;
                }

                const firstName = row.dataset.fname || '';
                const lastName = row.dataset.lname || '';

                if (firstName || lastName) {
                    if (searchTerm === '' || firstName.includes(searchTerm) || lastName.includes(searchTerm)) {
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
                    newRow.innerHTML = `<td colspan="4" class="text-center">No collector logs found matching your search.</td>`;
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
