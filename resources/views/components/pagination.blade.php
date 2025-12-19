@if ($paginator->hasPages())
    <style>
        .pagination {
            font-family: 'Poppins', sans-serif;
        }
        
        .pagination .page-link {
            color: #156725;
            background-color: #ffffff;
            border: 1px solid #156725;
            padding: 0.5rem 0.75rem;
            margin: 0 0.125rem;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
            font-weight: 500;
            text-decoration: none;
        }
        
        .pagination .page-link:hover:not(.disabled) {
            color: #ffffff;
            background-color: #156725;
            border-color: #156725;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(21, 103, 37, 0.3);
        }
        
        .pagination .page-item.active .page-link {
            color: #ffffff;
            background-color: #156725;
            border-color: #156725;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(21, 103, 37, 0.3);
        }
        
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .pagination .page-link:focus {
            box-shadow: 0 0 0 0.25rem rgba(165, 159, 25, 0.25);
            outline: none;
        }
    </style>
    
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mt-4">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&laquo; Previous</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
                
                // Adjust if we're near the start or end
                if ($endPage - $startPage < 4) {
                    if ($startPage == 1) {
                        $endPage = min($lastPage, $startPage + 4);
                    } else {
                        $startPage = max(1, $endPage - 4);
                    }
                }
            @endphp

            {{-- First Page --}}
            @if ($startPage > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if ($startPage > 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            {{-- Page Numbers --}}
            @for ($page = $startPage; $page <= $endPage; $page++)
                @if ($page == $currentPage)
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            {{-- Last Page --}}
            @if ($endPage < $lastPage)
                @if ($endPage < $lastPage - 1)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next &raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Next &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif

