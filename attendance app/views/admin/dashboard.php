<?php
require_once '../../includes/auth.php';
$auth = new Auth();

if(!$auth->isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Attendance System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Dashboard Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: #1a73e8;
            color: white;
            padding: 1rem;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 1rem 0;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            margin-top: 2rem;
        }

        .menu-item {
            padding: 0.8rem 1rem;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: background 0.3s;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .menu-item i {
            margin-right: 10px;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 2rem;
            background: #f0f2f5;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .chart-container {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .sidebar-header h2,
            .menu-item span {
                display: none;
            }

            .menu-item {
                justify-content: center;
            }

            .menu-item i {
                margin: 0;
            }
        }

        @media (max-width: 480px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <div class="sidebar-menu">
                <div class="menu-item">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </div>
                <div class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>School Settings</span>
                </div>
                <div class="menu-item">
                    <i class="fas fa-chalkboard"></i>
                    <span>Classes</span>
                </div>
                <div class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Teachers</span>
                </div>
                <div class="menu-item">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                </div>
                <div class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </div>
                <div class="menu-item" id="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <img src="https://icons8.com/icon/9q3GMpxNIMjC/user" alt="User">
                    <span><?php echo $_SESSION['username']; ?></span>
                </div>
            </div>
           
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Students</h3>
                    <p class="total-students">Loading...</p>
                </div>
                <div class="stat-card">
                    <h3>Total Teachers</h3>
                    <p class="total-teachers">Loading...</p>
                </div>
                <div class="stat-card">
                    <h3>Today's Attendance</h3>
                    <p class="today-attendance">Loading...</p>
                </div>
                <div class="stat-card">
                    <h3>Total Classes</h3>
                    <p class="total-classes">Loading...</p>
                </div>
            </div>

            <!-- Attendance Chart -->
            <div class="chart-container">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load Statistics
            loadDashboardStats();
            
            // Initialize Attendance Chart
            initializeAttendanceChart();
            
            // Logout Handler
            $('#logout').click(function() {
                $.ajax({
                    url: '../../ajax/logout.php',
                    type: 'POST',
                    success: function(response) {
                        window.location.href = '../../index.php';
                    }
                });
            });
        });

        function loadDashboardStats() {
            $.ajax({
                url: '../../ajax/dashboard-stats.php',
                type: 'GET',
                success: function(response) {
                    const data = JSON.parse(response);
                    $('.total-students').text(data.totalStudents);
                    $('.total-teachers').text(data.totalTeachers);
                    $('.today-attendance').text(data.todayAttendance + '%');
                    $('.total-classes').text(data.totalClasses);
                }
            });
        }

        function initializeAttendanceChart() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                    datasets: [{
                        label: 'Weekly Attendance',
                        data: [95, 88, 92, 85, 90],
                        borderColor: '#1a73e8',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
