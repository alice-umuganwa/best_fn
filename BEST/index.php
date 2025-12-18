<?php
session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Disaster.php';
require_once __DIR__ . '/models/Donation.php';

// ---------------- ROUTING LOGIC ----------------
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove base path /BEST
$path = str_replace('/BEST', '', $path);
$path = rtrim($path, '/');

if ($path === '' || $path === '/') {
    // Home page logic
    $disasterModel = new Disaster();
    $donationModel = new Donation();

    $activeDisasters = $disasterModel->getActive();
    $donationStats  = $donationModel->getStatistics();

} elseif ($path === '/login') {
    include __DIR__ . '/views/auth/login.php';
    exit;

} elseif ($path === '/register') {
    include __DIR__ . '/views/auth/register.php';
    exit;

} elseif ($path === '/dashboard') {
    include __DIR__ . '/views/admin/dashboard.php';
    exit;

} elseif ($path === '/volunteer') {
    include __DIR__ . '/views/volunteer.php';
    exit;

} else {
    http_response_code(404);
    echo 'Page not found';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disaster Relief Management System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="container">
        <a href="/BEST" class="navbar-brand">🆘 DRMS</a>
        <ul class="navbar-menu">
            <li><a href="#disasters">Active Disasters</a></li>
            <li><a href="#features">Features</a></li>
            <li><a href="views/donations.php">Donate</a></li>
            <li><a href="views/volunteer.php">Volunteer</a></li>

            <?php if (!empty($_SESSION['logged_in'])): ?>
                <li><a href="views/admin/dashboard.php">Dashboard</a></li>
                <li><a href="controllers/AuthController.php?action=logout">Logout</a></li>
            <?php else: ?>
                <li><a href="views/auth/login.php" class="btn btn-primary">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <h1>Coordinating Relief.<br>Saving Lives.</h1>
        <p>Managing disaster relief, donations, and volunteers in one platform.</p>

        <div class="hero-buttons">
            <a href="views/donations.php" class="btn btn-primary">Make a Donation</a>
            <a href="views/volunteer.php" class="btn btn-outline">Become a Volunteer</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($activeDisasters); ?></div>
                <div class="stat-label">Active Disasters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($donationStats['total_donations'] ?? 0); ?></div>
                <div class="stat-label">Total Donations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?php echo number_format($donationStats['total_amount'] ?? 0); ?></div>
                <div class="stat-label">Funds Raised</div>
            </div>
        </div>
    </div>
</section>

<!-- DISASTERS -->
<section id="disasters" class="disasters-section">
    <div class="container">
        <h2>Active Disasters</h2>

        <?php if (!empty($activeDisasters)): ?>
            <div class="grid grid-2">
                <?php foreach ($activeDisasters as $disaster): ?>
                    <div class="disaster-card">
                        <h3><?php echo htmlspecialchars($disaster['disaster_name']); ?></h3>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($disaster['disaster_type']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($disaster['location']); ?></p>
                        <p><strong>Affected:</strong> <?php echo number_format($disaster['affected_population']); ?> people</p>

                        <a href="views/donations.php?disaster_id=<?php echo $disaster['disaster_id']; ?>" class="btn btn-primary">Donate</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No active disasters currently.</p>
        <?php endif; ?>
    </div>
</section>

<!-- FEATURES -->
<section id="features" class="section">
    <div class="container">
        <h2>System Features</h2>
        <ul>
            <li>Disaster tracking</li>
            <li>Donation management</li>
            <li>Volunteer coordination</li>
            <li>Analytics dashboard</li>
        </ul>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Disaster Relief Management System</p>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
