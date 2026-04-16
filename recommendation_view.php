<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $sql = "SELECT recommendations.*, users.firstname, users.lastname
            FROM recommendations
            LEFT JOIN users ON recommendations.patient_id = users.id
            WHERE CAST(recommendations.id AS CHAR) LIKE ?
               OR users.firstname LIKE ?
               OR users.lastname LIKE ?
               OR recommendations.recommendation LIKE ?
               OR recommendations.preventive_care LIKE ?
            ORDER BY recommendations.id ASC";

    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT recommendations.*, users.firstname, users.lastname
            FROM recommendations
            LEFT JOIN users ON recommendations.patient_id = users.id
            ORDER BY recommendations.id ASC";
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
    <title>Recommendations - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="mb-1">Recommendations & Preventive Care</h2>
                    <p class="text-muted mb-0">Smart Healthcare Management System</p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <form method="GET" class="d-flex gap-2">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by ID / Patient / Recommendation"
                            value="<?php echo e($search); ?>"
                        >
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>

                    <a class="btn btn-success" href="recommendation_form.php">Add Recommendation</a>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">Recommendation List</h4>

                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Recommendation</th>
                                        <th>Preventive Care</th>
                                        <th>Follow-up Advice</th>
                                        <th>Record Date</th>
                                        <th width="180">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo e($row['id']); ?></td>
                                            <td><?php echo e($row['firstname'] . ' ' . $row['lastname']); ?></td>
                                            <td><?php echo e($row['recommendation']); ?></td>
                                            <td><?php echo e($row['preventive_care']); ?></td>
                                            <td><?php echo e($row['follow_up_advice']); ?></td>
                                            <td><?php echo e($row['record_date']); ?></td>
                                            <td>
                                                <a class="btn btn-sm btn-info text-white" href="recommendation_update.php?id=<?php echo (int)$row['id']; ?>">Edit</a>
                                                <a class="btn btn-sm btn-danger" href="recommendation_delete.php?id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Are you sure you want to delete this recommendation record?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">No recommendation records found.</div>
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