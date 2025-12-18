<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Disaster.php';
require_once __DIR__ . '/models/Donation.php';

$disasterModel = new Disaster();
$donationModel = new Donation();

// Get active disasters
$activeDisasters = $disasterModel->getActive();

// Get donation statistics
$donationStats = $donationModel->getStatistics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Disaster Relief Management System - Coordinating relief efforts and donations for disaster-affected communities">
    <title>Disaster Relief Management System</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 80px;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(37, 99, 235, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
            z-index: 0;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            margin-bottom: var(--spacing-md);
            line-height: 1.1;
        }
        
        .hero p {
            font-size: clamp(1.125rem, 2vw, 1.5rem);
            color: var(--text-secondary);
            margin-bottom: var(--spacing-xl);
        }
        
        .hero-buttons {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Stats Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-top: var(--spacing-2xl);
        }
        
        .stat-card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            padding: var(--spacing-lg);
            text-align: center;
            transition: all var(--transition-base);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: rgba(37, 99, 235, 0.5);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: var(--spacing-xs);
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 1.125rem;
            font-weight: 500;
        }
        
        /* Disasters Section */
        .disasters-section {
            padding: var(--spacing-2xl) 0;
        }
        
        .disaster-card {
            background: var(--dark-surface);
            border-radius: var(--radius-xl);
            padding: var(--spacing-lg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all var(--transition-base);
        }
        
        .disaster-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            border-color: rgba(37, 99, 235, 0.3);
        }
        
        .disaster-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: var(--spacing-md);
        }
        
        .disaster-type {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(37, 99, 235, 0.2);
            border: 1px solid var(--primary-light);
            border-radius: var(--radius-lg);
            color: var(--primary-light);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .disaster-info {
            margin-bottom: var(--spacing-md);
        }
        
        .disaster-info p {
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            margin-bottom: var(--spacing-xs);
        }
        
        /* Features Section */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--spacing-xl);
            margin-top: var(--spacing-xl);
        }
        
        .feature-card {
            text-align: center;
            padding: var(--spacing-xl);
            background: rgba(30, 41, 59, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            transition: all var(--transition-base);
        }
        
        .feature-card:hover {
            background: rgba(30, 41, 59, 0.5);
            border-color: rgba(37, 99, 235, 0.5);
            transform: translateY(-10px);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto var(--spacing-md);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }
        
        .feature-title {
            font-size: 1.5rem;
            margin-bottom: var(--spacing-sm);
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-2xl);
            padding: var(--spacing-2xl);
            text-align: center;
            margin: var(--spacing-2xl) 0;
        }
        
        /* Footer */
        .footer {
            background: var(--dark-surface);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: var(--spacing-xl) 0;
            margin-top: var(--spacing-2xl);
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-xl);
        }
        
        .footer-section h3 {
            margin-bottom: var(--spacing-md);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: var(--spacing-xs);
        }
        
        .footer-bottom {
            text-align: center;
            margin-top: var(--spacing-xl);
            padding-top: var(--spacing-xl);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">🆘 DRMS</a>
            <ul class="navbar-menu">
                <li><a href="#disasters">Active Disasters</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="views/donations.php">Donate</a></li>
                <li><a href="views/volunteer.php">Volunteer</a></li>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <li><a href="views/admin/dashboard.php">Dashboard</a></li>
                    <li><a href="controllers/AuthController.php?action=logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="views/auth/login.php" class="btn btn-primary">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content fade-in">
                <h1>Coordinating Relief.<br>Saving Lives.</h1>
                <p>A comprehensive platform for managing disaster relief operations, coordinating volunteers, and facilitating donations to help communities in crisis.</p>
                
                <div class="hero-buttons">
                    <a href="views/donations.php" class="btn btn-primary btn-large">Make a Donation</a>
                    <a href="views/volunteer.php" class="btn btn-outline btn-large">Become a Volunteer</a>
                </div>
                
                <!-- Stats -->
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
                    <div class="stat-card">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support Available</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Active Disasters Section -->
    <section id="disasters" class="disasters-section">
        <div class="container">
            <h2 class="text-center mb-4">Active Disasters</h2>
            <p class="text-center mb-4" style="font-size: 1.125rem;">Current disaster events requiring immediate assistance</p>
            
            <?php if (!empty($activeDisasters)): ?>
                <div class="grid grid-2">
                    <?php foreach ($activeDisasters as $disaster): ?>
                        <div class="disaster-card">
                            <div class="disaster-header">
                                <div>
                                    <h3><?php echo htmlspecialchars($disaster['disaster_name']); ?></h3>
                                    <span class="disaster-type"><?php echo htmlspecialchars($disaster['disaster_type']); ?></span>
                                </div>
                                <span class="badge badge-<?php echo $disaster['severity'] === 'catastrophic' ? 'error' : ($disaster['severity'] === 'severe' ? 'warning' : 'info'); ?>">
                                    <?php echo htmlspecialchars($disaster['severity']); ?>
                                </span>
                            </div>
                            
                            <div class="disaster-info">
                                <p>📍 <?php echo htmlspecialchars($disaster['location']); ?></p>
                                <p>👥 Affected: <?php echo number_format($disaster['affected_population']); ?> people</p>
                                <p>🏕️ Relief Camps: <?php echo $disaster['camp_count'] ?? 0; ?></p>
                            </div>
                            
                            <p><?php echo htmlspecialchars(substr($disaster['description'], 0, 150)) . '...'; ?></p>
                            
                            <div class="card-footer">
                                <a href="views/donations.php?disaster_id=<?php echo $disaster['disaster_id']; ?>" class="btn btn-primary">
                                    Donate Now
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <p style="font-size: 1.25rem;">No active disasters at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section">
        <div class="container">
            <h2 class="text-center mb-4">System Features</h2>
            <p class="text-center mb-4" style="font-size: 1.125rem;">Comprehensive tools for effective disaster relief management</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🚨</div>
                    <h3 class="feature-title">Disaster Tracking</h3>
                    <p>Real-time monitoring and management of disaster events with detailed information and status updates.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🏕️</div>
                    <h3 class="feature-title">Relief Camps</h3>
                    <p>Coordinate relief camp operations, track capacity, and manage resources efficiently.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📦</div>
                    <h3 class="feature-title">Resource Management</h3>
                    <p>Track inventory of food, medicine, shelter materials, and other essential supplies.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3 class="feature-title">Donation Portal</h3>
                    <p>Secure platform for monetary and material donations with transparent tracking.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🤝</div>
                    <h3 class="feature-title">Volunteer Coordination</h3>
                    <p>Register volunteers, manage assignments, and track contributions effectively.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Analytics Dashboard</h3>
                    <p>Comprehensive reports and insights for data-driven decision making.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section">
        <div class="container">
            <div class="cta-section">
                <h2>Join Our Mission to Help Communities in Need</h2>
                <p style="font-size: 1.25rem; margin-bottom: var(--spacing-xl);">
                    Every contribution makes a difference. Whether you donate, volunteer, or spread awareness, you're helping save lives.
                </p>
                <div class="flex-center gap-2">
                    <a href="views/auth/register.php" class="btn btn-primary btn-large">Get Started</a>
                    <a href="views/volunteer.php" class="btn btn-secondary btn-large">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About DRMS</h3>
                    <p>The Disaster Relief Management System is dedicated to coordinating effective relief efforts and supporting communities affected by disasters.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#disasters">Active Disasters</a></li>
                        <li><a href="views/donations.php">Make a Donation</a></li>
                        <li><a href="views/volunteer.php">Volunteer</a></li>
                        <li><a href="views/auth/login.php">Login</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Contact</h3>
                    <ul class="footer-links">
                        <li>📧 info@drms.org</li>
                        <li>📞 1-800-RELIEF</li>
                        <li>📍 Emergency Response Center</li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Emergency</h3>
                    <p>For immediate assistance during a disaster, please contact your local emergency services.</p>
                    <a href="tel:911" class="btn btn-error mt-2">Call 911</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Disaster Relief Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
