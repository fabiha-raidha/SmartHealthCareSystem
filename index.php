<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Healthcare Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #dff6ff, #eef7ff, #f7fbff);
            font-family: Arial, sans-serif;
        }

        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .welcome-card {
            background: #ffffff;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .left-panel {
            background: linear-gradient(135deg, #0d6efd, #20c997);
            color: white;
            padding: 50px 40px;
            height: 100%;
        }

        .right-panel {
            padding: 50px 40px;
        }

        .system-title {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .feature-box {
            background: rgba(255,255,255,0.15);
            border-radius: 15px;
            padding: 12px 16px;
            margin-bottom: 12px;
        }

        .btn-custom {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
        }

        .mini-text {
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .system-title {
                font-size: 2rem;
            }

            .left-panel,
            .right-panel {
                padding: 30px 22px;
            }
        }
    </style>
</head>
<body>

<div class="container hero-section">
    <div class="row justify-content-center w-100">
        <div class="col-lg-10">
            <div class="welcome-card">
                <div class="row g-0">
                    
                    <div class="col-md-6">
                        <div class="left-panel d-flex flex-column justify-content-center">
                            <h1 class="system-title mb-3">Smart Healthcare Management System</h1>
                            <p class="mb-4">
                                A modern platform to manage patients, doctors, appointments, and prescriptions in one place.
                            </p>

                            <div class="feature-box">Patient Management</div>
                            <div class="feature-box">Doctor Management</div>
                            <div class="feature-box">Appointment & Prescription</div>
                            <div class="feature-box">Billing , Insurance & investigation</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="right-panel d-flex flex-column justify-content-center h-100">
                            <h2 class="fw-bold mb-2">Welcome</h2>
                            <p class="mini-text mb-4">
                                Please log in to continue or create a new admin account to access the system dashboard.
                            </p>

                            <div class="d-grid gap-3">
                                <a href="admin_login.php" class="btn btn-primary btn-lg btn-custom">Login</a>
                                <a href="admin_register.php" class="btn btn-outline-success btn-lg btn-custom">Registration</a>
                            </div>

                            <div class="mt-4">
                                <p class="mini-text mb-0">
                                    Secure access for admin to manage the whole healthcare system efficiently.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>