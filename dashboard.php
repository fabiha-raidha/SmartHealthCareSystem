<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$patientCount = 0;
$doctorCount = 0;
$appointmentCount = 0;
$prescriptionCount = 0;

$result1 = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($result1) {
    $patientCount = $result1->fetch_assoc()['total'];
}

$result2 = $conn->query("SELECT COUNT(*) AS total FROM doctors");
if ($result2) {
    $doctorCount = $result2->fetch_assoc()['total'];
}

$result3 = $conn->query("SELECT COUNT(*) AS total FROM appointments");
if ($result3) {
    $appointmentCount = $result3->fetch_assoc()['total'];
}

$result4 = $conn->query("SELECT COUNT(*) AS total FROM prescriptions");
if ($result4) {
    $prescriptionCount = $result4->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Dashboard</h2>
                    <p class="text-muted mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
                </div>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5>Total Patients</h5>
                            <h2><?php echo $patientCount; ?></h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5>Total Doctors</h5>
                            <h2><?php echo $doctorCount; ?></h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5>Total Appointments</h5>
                            <h2><?php echo $appointmentCount; ?></h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5>Total Prescriptions</h5>
                            <h2><?php echo $prescriptionCount; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">
                    <h4>System Overview</h4>
                    <p class="text-muted mb-0">
                        This Smart Healthcare Management System allows admin users to manage patients, doctors, appointments, and prescriptions from one dashboard.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>