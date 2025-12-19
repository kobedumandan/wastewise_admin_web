<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/aboutUs.css') }}">

    <title>WasteWise</title>
    <style>
        /* Gradient navbar for About Us page */
        .navbar {
            background: linear-gradient(to right, #041E1B, #033202, #082000) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .hero-header {
            background: url('{{ asset('images/tanom.png') }}') no-repeat center center;
            background-size: cover;
            height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.6);
            padding: 2em;
            box-sizing: border-box;
        }
        .hero-header h1 {
            font-size: 3em;
            font-weight: bold;
            padding: 0 1em;
            text-align: center;
            margin: 0;
        }
        .section {
            padding: 5em 2em;
            max-width: 1100px;
            margin: auto;
        }
        .highlight {
            color: #2c7a36;
            font-weight: bold;
        }
        .section h2 {
            font-size: 2.5em;
            margin: 0.5em 0 1.5em 0;
        }
        .section h3 {
            margin-bottom: 1em;
        }
        .section p {
            font-size: 1.15em;
            line-height: 1.8;
            margin-bottom: 2em;
        }
    </style>
</head>

<body>
 @include('navbar.landingnavbar')
    <!-- Navbar -->
   

    <!-- Main content -->
    <header class="hero-header">
        <h1>WasteWise: Dispose Right, Make it Bright</h1>
    </header>
    
    <section class="section">
        <h3 class="highlight">OUR COMMITMENT</h3>
        <h2>Organized Waste Collection with Accountability</h2>
        <div class="row g-5">
            <div class="col-md-6">
                <h4 class="highlight">OUR MISSION</h4>
                <h3>Efficient, Scheduled Waste Collection</h3>
                <p>At WasteWise, our mission is to streamline waste collection through a reliable, scheduled system. We aim to reduce missed pickups, minimize overflow, and ensure every household contributes to a cleaner environment through proper and timely disposal.</p>
            </div>
            <div class="col-md-6">
                <h4 class="highlight">OUR VISION</h4>
                <h3>Promoting Compliance and Waste Segregation</h3>
                <p>We envision communities that follow best practices in waste management. To encourage responsibility, we enforce fines for unsegregated waste—ensuring that every resident actively participates in reducing landfill use and protecting the environment.</p>
            </div>
        </div>
    </section>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
