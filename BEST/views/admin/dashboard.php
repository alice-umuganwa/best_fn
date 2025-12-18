<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/Disaster.php';
require_once __DIR__ . '/../../models/ReliefCamp.php';
require_once __DIR__ . '/../../models/Donation.php';

// Require admin or staff role
AuthController::requireLogin();
if (!in_array($_SESSION['role'], ['admin', 'staff'])) {
    header('Location: ../../index.php');
    exit;
}

// Initialize models
$disasterModel = new Disaster();
$campModel = new ReliefCamp();
$donationModel = new Donation();

// Get statistics
$activeDisasters = $disasterModel->getAll(['status' => 'active']);
$allCamps = $campModel->getAll();
$donationStats = $donationModel->getStatistics();

// Calculate totals
$totalDisasters = count($activeDisasters);
$totalCamps = count($allCamps);
$totalOccupancy = array_sum(array_column($allCamps, 'current_occupancy'));
$totalDonations = $donationStats['total_donations'] ?? 0;
$totalAmount = $donationStats['total_amount'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DRMS</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        
        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }
        
        .sidebar {
            background: var(--dark-surface);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: var(--spacing-lg);
        }
        
        .sidebar-header {
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-lg);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: var(--spacing-sm);
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: var(--spacing-sm) var(--spacing-md);
            color: var(--text-secondary);
            border-radius: var(--radius-md);
            transition: all var(--transition-base);
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(37, 99, 235, 0.2);
            color: var(--primary-light);
        }
        
        .main-content {
            padding: var(--spacing-xl);
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
        }
        
        .stat-card {
            background: var(--dark-surface);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            padding: var(--spacing-lg);
            transition: all var(--transition-base);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: rgba(37, 99, 235, 0.5);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: var(--spacing-md);
        }
        
        .stat-icon.primary {
            background: rgba(37, 99, 235, 0.2);
        }
        
        .stat-icon.success {
            background: rgba(16, 185, 129, 0.2);
        }
        
        .stat-icon.warning {
            background: rgba(245, 158, 11, 0.2);
        }
        
        .stat-icon.info {
            background: rgba(59, 130, 246, 0.2);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: var(--spacing-xs);
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: var(--spacing-lg);
        }
        
        .section-card {
            background: var(--dark-surface);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            padding: var(--spacing-lg);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .disaster-item {
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            background: rgba(30, 41, 59, 0.5);
            margin-bottom: var(--spacing-md);
            transition: all var(--transition-base);
        }
        
        .disaster-item:hover {
            background: rgba(30, 41, 59, 0.8);
        }
        
        .disaster-item h4 {
            margin-bottom: var(--spacing-xs);
            font-size: 1.125rem;
        }
        
        .disaster-meta {
            display: flex;
            gap: var(--spacing-md);
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        @media (max-width: 1024px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">🆘 DRMS</div>
                <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.5rem;">
                    <?php echo ucfirst($_SESSION['role']); ?> Panel
                </p>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="manage_disasters.php">🚨 Disasters</a></li>
                <li><a href="manage_camps.php">🏕️ Relief Camps</a></li>
                <li><a href="manage_resources.php">📦 Resources</a></li>
                <li><a href="manage_donations.php">💰 Donations</a></li>
                <li><a href="manage_volunteers.php">🤝 Volunteers</a></li>
                <li><a href="reports.php">📈 Reports</a></li>
                <li><a href="../../index.php">🏠 Home</a></li>
                <li><a href="../../controllers/AuthController.php?action=logout">🚪 Logout</a></li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="dashboard-header">
                <div>
                    <h1>Dashboard</h1>
                    <p style="color: var(--text-secondary);">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
                </div>
                
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-weight: 600;"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">
                            <?php echo ucfirst($_SESSION['role']); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">🚨</div>
                    <div class="stat-value"><?php echo $totalDisasters; ?></div>
                    <div class="stat-label">Active Disasters</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon success">🏕️</div>
                    <div class="stat-value"><?php echo $totalCamps; ?></div>
                    <div class="stat-label">Relief Camps</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon warning">👥</div>
                    <div class="stat-value"><?php echo number_format($totalOccupancy); ?></div>
                    <div class="stat-label">People Sheltered</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon info">💰</div>
                    <div class="stat-value">$<?php echo number_format($totalAmount); ?></div>
                    <div class="stat-label">Total Donations</div>
                </div>
            </div>
            
            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Active Disasters -->
                <div class="section-card">
                    <div class="section-header">
                        <h2>Active Disasters</h2>
                        <a href="manage_disasters.php" class="btn btn-primary">Manage</a>
                    </div>
                    
                    <?php if (!empty($activeDisasters)): ?>
                        <?php foreach (array_slice($activeDisasters, 0, 5) as $disaster): ?>
                            <div class="disaster-item">
                                <div class="flex-between">
                                    <h4><?php echo htmlspecialchars($disaster['disaster_name']); ?></h4>
                                    <span class="badge badge-<?php echo $disaster['severity'] === 'catastrophic' ? 'error' : 'warning'; ?>">
                                        <?php echo htmlspecialchars($disaster['severity']); ?>
                                    </span>
                                </div>
                                <div class="disaster-meta">
                                    <span>📍 <?php echo htmlspecialchars($disaster['location']); ?></span>
                                    <span>👥 <?php echo number_format($disaster['affected_population']); ?> affected</span>
                                    <span>🏕️ <?php echo $disaster['camp_count'] ?? 0; ?> camps</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: var(--text-muted); padding: var(--spacing-xl);">
                            No active disasters at the moment.
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Actions -->
                <div class="section-card">
                    <div class="section-header">
                        <h2>Quick Actions</h2>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                        <a href="manage_disasters.php?action=create" class="btn btn-primary" style="width: 100%;">
                            🚨 Report New Disaster
                        </a>
                        <a href="manage_camps.php?action=create" class="btn btn-secondary" style="width: 100%;">
                            🏕️ Create Relief Camp
                        </a>
                        <a href="manage_resources.php?action=add" class="btn btn-outline" style="width: 100%;">
                            📦 Add Resources
                        </a>
                        <a href="manage_volunteers.php" class="btn btn-outline" style="width: 100%;">
                            🤝 Manage Volunteers
                        </a>
                        <a href="reports.php" class="btn btn-outline" style="width: 100%;">
                            📈 Generate Report
                        </a>
                    </div>
                    
                    <div style="margin-top: var(--spacing-xl); padding-top: var(--spacing-xl); border-top: 1px solid rgba(255, 255, 255, 0.1);">
                        <h3 style="margin-bottom: var(--spacing-md);">System Status</h3>
                        <div style="display: flex; flex-direction: column; gap: var(--spacing-sm);">
                            <div class="flex-between">
                                <span>Database</span>
                                <span class="badge badge-success">Online</span>
                            </div>
                            <div class="flex-between">
                                <span>Last Backup</span>
                                <span style="color: var(--text-muted); font-size: 0.875rem;">Today</span>
                            </div>
                            <div class="flex-between">
                                <span>Version</span>
                                <span style="color: var(--text-muted); font-size: 0.875rem;">1.0.0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../../assets/js/main.js"></script>
</body>
</html>
