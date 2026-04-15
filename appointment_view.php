<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $sql = "SELECT appointments.*, users.firstname, users.lastname, doctors.name AS doctor_name
            FROM appointments
            LEFT JOIN users ON appointments.patient_id = users.id
            LEFT JOIN doctors ON appointments.doctor_id = doctors.id
            WHERE users.firstname LIKE ?
               OR users.lastname LIKE ?
               OR doctors.name LIKE ?
               OR appointments.appointment_date LIKE ?
               OR appointments.status LIKE ?
            ORDER BY appointments.id ASC";

    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT appointments.*, users.firstname, users.lastname, doctors.name AS doctor_name
            FROM appointments
            LEFT JOIN users ON appointments.patient_id = users.id
            LEFT JOIN doctors ON appointments.doctor_id = doctors.id
            ORDER BY appointments.id ASC";
    $result = $conn->query($sql);
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="mb-1">Appointment Management</h2>
                    <p class="text-muted mb-0">Smart Healthcare Management System</p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <form method="GET" class="d-flex gap-2">
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control" 
                            placeholder="Search appointment..." 
                            value="<?php echo e($search); ?>"
                        >
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>

                    <a class="btn btn-success" href="appointment_form.php"> Add New Appointment</a>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">Appointment List</h4>

                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th width="180">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo e($row['id']); ?></td>
                                            <td><?php echo e($row['firstname'] . ' ' . $row['lastname']); ?></td>
                                            <td><?php echo e($row['doctor_name']); ?></td>
                                            <td><?php echo e($row['appointment_date']); ?></td>
                                            <td><?php echo e($row['appointment_time']); ?></td>
                                            <td><?php echo e($row['status']); ?></td>
                                            <td>
                                                <a class="btn btn-sm btn-info text-white" href="appointment_update.php?id=<?php echo (int)$row['id']; ?>">Edit</a>
                                                <a class="btn btn-sm btn-danger" href="appointment_delete.php?id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">No appointment records found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    $stmt->close();
}
$conn->close();
?>