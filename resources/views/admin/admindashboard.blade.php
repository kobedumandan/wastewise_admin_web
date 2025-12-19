@extends('layouts.admin')

@section('content')
    <div class="container">

        <div class="row mt-4">
            <!-- <div class="col-md-4">
                <div class="total_waste_disposed card">
                    <div class="card-body d-flex align-items-end">
                        <div class="card-icon">
                            <img src="{{asset('images/sackofwaste.png')}}" alt="Icon 1" class="me-3"
                                style="width: 90px; height: 90px;">
                        </div>
                        <div class="card-content">
                            <h5 class="card-title">Total Waste Disposed Properly</h5>
                            <p class="card-value ms-auto"><strong>{{ $wasteSegregated->total_waste_segregated ?? 0 }}</strong></p>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="col-md-4">
                <div class="card h-100" style="border-radius: 14px; position: relative; overflow: hidden; background-image: url('{{ asset('backgrounds/waste.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat;">
                    <div class="card-body d-flex align-items-center justify-content-end" style="position: relative; z-index: 2;">
                        <div class="card-content" style="position: relative; z-index: 2;">
                            <p class="card-title" style="color: #333; font-weight: 400; font-size: 15px; height: 4px;">Total Waste Disposed Properly</p>
                            <p class="card-value ms-auto" style="color: #333; font-weight: 600; font-size: 56px;">
                                <strong>
                                {{ $wasteSegregated->total_waste_segregated ?? 0 }}
                                </strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="col-md-4">
                <div class="total_penalty_payments card h-100">
                    <div class="card-body d-flex align-items-start">
                        <div class="card-icon">
                            <img src="{{asset('images/money.png')}}" alt="Icon 1" class="me-3"
                                style="width: 90px; height: 90px;">
                        </div>
                        <div class="card-content">
                            <h5 class="card-title">Total Penalty Payments</h5>
                            <div class="d-flex align-items-end justify-content-end">
                                <p class="card-value-misc">₱</p>
                                <h1 class="card-value"><strong>{{ number_format($totalamount->total_amount ?? 0, 2) }}</strong></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="col-md-4">
                <div class="card h-100" style="border-radius: 14px; position: relative; overflow: hidden; background-image: url('{{ asset('backgrounds/payments.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat;">
                    <div class="card-body d-flex align-items-center justify-content-end" style="position: relative; z-index: 2;">
                        <div class="card-content" style="position: relative; z-index: 2;">
                            <p class="card-title" style="color: #333; font-weight: 400; font-size: 15px; height: 4px;">Total Penalty Payments</p>
                            <div class="d-flex align-items-center justify-content-end">
                                <p class="card-value-misc mb-0" style="color: #333; font-weight: 600; font-size: 28px; margin-right: 4px; transform: translateY(8px);">₱</p>
                                <p class="card-value mb-0" style="color: #333; font-weight: 600; font-size: 56px;">
                                    <strong>
                                    {{ number_format($totalamount->total_amount ?? 0, 2) }}
                                    </strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="col-md-4">
                <div class="total_users_registered card h-100">
                    <div class="card-body d-flex align-items-start">
                        <div class="card-icon">
                            <img src="{{asset('images/profile.png')}}" alt="Icon 3" class="me-3"
                                style="width: 90px; height: 90px;">
                        </div>
                        <div class="card-content">
                            <h5 class="card-title">Total Users Registered</h5>
                            <p class="card-value ms-auto"><strong>{{ $totalUsers->total_users ?? 0 }}</strong></p>
                        </div>
                    </div>
                </div>
            </div> -->

            <div class="col-md-4">
                <div class="card h-100" style="border-radius: 14px; position: relative; overflow: hidden; background-image: url('{{ asset('backgrounds/user_icon.png') }}'); background-size: contain; background-position: center left; background-repeat: no-repeat;">
                    <div class="card-body d-flex align-items-center justify-content-end" style="position: relative; z-index: 2;">
                        <div class="card-content" style="position: relative; z-index: 2;">
                            <p class="card-title" style="color: #333; font-weight: 400; font-size: 15px; height: 4px;">Total Users Registered</p>
                            <p class="card-value ms-auto" style="color: #333; font-weight: 600; font-size: 56px;">
                                <strong>
                                {{ $totalUsers->total_users ?? 0 }}
                                </strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-body d-flex align-items-start">
                        <div id="curve_chart" style="width: 100%; height: 500px; min-height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    var userData = {!! json_encode($chartData) !!};
    var chart;

    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable(userData);

        var options = {
            title: 'Daily Registered Users for {{ date("F Y") }}',
            curveType: 'function',
            legend: { position: 'bottom' },
            width: '100%',
            height: 500
        };

        var chartElement = document.getElementById('curve_chart');
        chart = new google.visualization.LineChart(chartElement);
        chart.draw(data, options);
    }

    // Redraw chart on window resize and sidebar toggle
    window.addEventListener('resize', function() {
        if (chart) {
            chart.draw(google.visualization.arrayToDataTable(userData), {
                title: 'Daily Registered Users for May',
                curveType: 'function',
                legend: { position: 'bottom' },
                width: '100%',
                height: 500
            });
        }
    });

    // Listen for sidebar toggle events
    document.addEventListener('DOMContentLoaded', function() {
        // Check if sidebar toggle button exists
        var sidebarToggle = document.querySelector('[data-bs-toggle="offcanvas"]');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                // Wait for sidebar animation to complete, then redraw chart
                setTimeout(function() {
                    if (chart) {
                        chart.draw(google.visualization.arrayToDataTable(userData), {
                            title: 'Daily Registered Users for {{ date("F Y") }}',
                            curveType: 'function',
                            legend: { position: 'bottom' },
                            width: '100%',
                            height: 500
                        });
                    }
                }, 300); // Adjust delay based on sidebar animation duration
            });
        }

        // Also listen for offcanvas events
        var offcanvasElement = document.querySelector('.offcanvas');
        if (offcanvasElement) {
            offcanvasElement.addEventListener('shown.bs.offcanvas', function() {
                setTimeout(function() {
                    if (chart) {
                        chart.draw(google.visualization.arrayToDataTable(userData), {
                            title: 'Daily Registered Users for May',
                            curveType: 'function',
                            legend: { position: 'bottom' },
                            width: '100%',
                            height: 500
                        });
                    }
                }, 300);
            });

            offcanvasElement.addEventListener('hidden.bs.offcanvas', function() {
                setTimeout(function() {
                    if (chart) {
                        chart.draw(google.visualization.arrayToDataTable(userData), {
                            title: 'Daily Registered Users for May',
                            curveType: 'function',
                            legend: { position: 'bottom' },
                            width: '100%',
                            height: 500
                        });
                    }
                }, 300);
            });
        }
    });
</script>
@endpush
