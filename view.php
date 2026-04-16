<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $sql = "SELECT id, firstname, lastname, email, gender 
            FROM users
            WHERE firstname LIKE ? 
               OR lastname LIKE ? 
               OR email LIKE ?
            ORDER BY id ASC";

    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id, firstname, lastname, email, gender 
            FROM users 
            ORDER BY id ASC";
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
    <title>Smart Healthcare Management System - Patient Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="mb-1">Smart Healthcare Management System</h2>
                    <p class="text-muted mb-0">Patient Records Management</p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <form method="GET" class="d-flex gap-2">
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control" 
                            placeholder="Search patient..." 
                            value="<?php echo e($search); ?>"
                        >
                        <button type="submit" class="btn btn-primary">Search</button>
                        <!--<a href="view.php" class="btn btn-secondary">Reset</a>-->
                    </form>

                    <a class="btn btn-success" href="form.php"> Add New Patient</a>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">Patient List</h4>

                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Gender</th>
                                        <!--<th>Password</th>-->
                                        <th width="180">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo e($row['id']); ?></td>
                                            <td><?php echo e($row['firstname']); ?></td>
                                            <td><?php echo e($row['lastname']); ?></td>
                                            <td><?php echo e($row['email']); ?></td>
                                            <td><?php echo e($row['gender']); ?></td>
                                            <!--<td>••••••••</td>-->
                                            <td>
                                                <a class="btn btn-sm btn-info text-white" href="update.php?id=<?php echo (int)$row['id']; ?>">Edit</a>
                                                <a class="btn btn-sm btn-danger" href="delete.php?id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Are you sure you want to delete this patient?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            No patient records found.
                        </div>
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