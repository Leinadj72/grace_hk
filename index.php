<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome | My Hookup Site</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                url('assets/banner.jpg') center center/cover no-repeat;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }

        .hero h1 {
            font-size: 3rem;
        }

        .btn-group a {
            min-width: 140px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">My Hookup Site</a>
            <div class="d-flex">
                <a href="pages/login.php" class="btn btn-outline-light me-2">Login</a>
                <a href="pages/register.php" class="btn btn-outline-light">Register</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 class="mb-4">Find Your Next Hookup</h1>
            <p class="lead mb-5">Discreet bookings, premium content, real connections.</p>
            <div class="btn-group">
                <a href="pages/public_gallery.php" class="btn btn-warning me-2">Public Gallery</a>
                <a href="pages/login.php" class="btn btn-outline-light">Book Now</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2>What We Offer</h2>
            <p class="mt-3">
                Access exclusive content and discreet companionship through our verified platform.
                Our models are available for bookings, and premium users unlock private nudes, videos, and more.
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        &copy; <?= date('Y') ?> My Hookup Site. All rights reserved.
    </footer>

</body>

</html>