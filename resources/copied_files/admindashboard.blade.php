<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/userdetails.css') }}">
    

    <title>WasteWise</title>
</head>
    
<body style="background: linear-gradient(to right, #031214, #033402,#071901,#041000);">

        <!-- Navbar -->
         @include('navbar.adminnavbar')

{{-- <div class="">
    @if($errors->any())
        <div class="alert alert-danger w-50 mx-auto mt-4 alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<div style="margin-top: 150px;">
    @if(session('success'))
        <div class="alert alert-success w-50 mx-auto mt-4 alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div> --}}

        <div class="container text-light" style="margin-top: 150px;">
            <h1 style="font-weight: bold">Admin Dashboard</h1>
            <hr>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-start">
                            <img src="{{asset('images/sackofwaste.png')}}" alt="Icon 1" class="me-3"
                                style="width: 90px; height: 90px;">
                            <div>
                                <h5 class="card-title">Total Waste Disposed Properly &emsp13;&emsp13;&emsp13;</h5>
                                <h1 class="mt-4 ms-auto" style="width: fit-content; font-weight: bold;"><strong>{{ $wasteSegregated->total_waste_segregated ?? 0 }}</strong></h1>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-start">
                            <img src="{{asset('images/money.png')}}" alt="Icon 1" class="me-3"
                                style="width: 90px; height: 90px;">
                            <div>
                                <h5 class="card-title">Total Penalty Payments &emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;</h5>
                                <h1 class="mt-4 ms-auto" style="width: fit-content; font-weight: bold;"><strong>₱{{ number_format($totalamount->total_amount ?? 0, 2) }}</strong></h1>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-start">
                            <img src="{{asset('images/profile.png')}}" alt="Icon 3" class="me-3"
                                style="width: 90px; height: 90px;">
                            <div>
                                <h5 class="card-title">Total Users Registered &emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;</h5>
                                <h1 class="mt-5 ms-auto" style="width: fit-content; font-weight: bold;"><strong>{{ $totalUsers->total_users ?? 0 }}</strong></h1>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <div class="card">
                        <div class="card-body d-flex align-items-start">

                            <div id="curve_chart" style="width: 1500px; height: 500px"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


              
         <div class="container text-light" style="margin-top: 100px;">
            <h1 style="font-weight: bold">Recent Activities</h1>
            <hr>

           <table class="table caption-top">
    <caption>List of users</caption>
    <thead class="table-success">
        <tr>
            <th scope="col">First name</th>
            <th scope="col">Last name</th>
            <th scope="col">Purok</th>
            <th scope="col">Cellphone number</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($adminActivities as $activity)
            <tr>
                
                <td>{{ $activity->firstname }}</td>
                <td>{{ $activity->lastname }}</td>
                <td>{{ $activity->purok }}</td>
                <td>{{ $activity->cell_num }}</td>
                <td>{{ $activity->action }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
            
        </div>

        {{-- <div class="modal fade text-dark" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to log out?
                    </div>
                    <div class="modal-footer border-0 ">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel

                        </button>

                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-box-arrow-right"></i>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div> --}}


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>



</body>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    var userData = {!! json_encode($chartData) !!};

    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable(userData);

        var options = {
            title: 'Daily Registered Users for May',
            curveType: 'function',
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
        chart.draw(data, options);
    }
</script>
</html>
