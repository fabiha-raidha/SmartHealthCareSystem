<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";

$message = "";
$messageType = "";

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (isset($_POST['update'])) {
    $id           = (int)($_POST['id'] ?? 0);
    $patient_id   = (int)($_POST['patient_id'] ?? 0);
    $bill_date    = trim($_POST['bill_date'] ?? '');
    $total_amount = trim($_POST['total_amount'] ?? '');
    $bill_status  = trim($_POST['bill_status'] ?? '');
    $payment_date = trim($_POST['payment_date'] ?? '');
    $remarks      = trim($_POST['remarks'] ?? '');

    if ($patient_id <= 0 || $bill_date === "" || $total_amount === "" || $bill_status === "") {
        $message = "Please fill in all required fields.";
        $messageType = "danger";
    } else {
        if ($payment_date === "") {
            $payment_date = null;
        }

        $stmt = $conn->prepare("UPDATE billing SET patient_id=?, bill_date=?, total_amount=?, bill_status=?, payment_date=?, remarks=? WHERE id=?");
        $stmt->bind_param("isdsssi", $patient_id, $bill_date, $total_amount, $bill_status, $payment_date, $remarks, $id);

        if ($stmt->execute()) {
            header("Location: billing_view.php");
            exit();
        } else {
            $message = "Failed to update billing record.";
            $messageType = "danger";
        }

        $stmt->close();
    }
}

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: billing_view.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM billing WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: billing_view.php");
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();

$patients = $conn->query("SELECT id, firstname, lastname FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Billing - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Update Billing Record</h2>
                    <p class="text-muted mb-0">Smart Healthcare Management System</p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">Edit Billing Information</h4>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo e($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Select Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select Patient</option>
                                <?php while ($patient = $patients->fetch_assoc()): ?>
                                    <option value="<?php echo (int)$patient['id']; ?>" <?php echo ((int)$patient['id'] === (int)$row['patient_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($patient['firstname'] . ' ' . $patient['lastname']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bill Date</label>
                            <input type="date" name="bill_date" class="form-control" value="<?php echo e($row['bill_date']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total Amount</label>
                            <input type="number" step="0.01" name="total_amount" class="form-control" value="<?php echo e($row['total_amount']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bill Status</label>
                            <select name="bill_status" class="form-select" required>
                                <option value="Pending" <?php echo ($row['bill_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Paid" <?php echo ($row['bill_status'] === 'Paid') ? 'selected' : ''; ?>>Paid</option>
                                <option value="Unpaid" <?php echo ($row['bill_status'] === 'Unpaid') ? 'selected' : ''; ?>>Unpaid</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" value="<?php echo e($row['payment_date']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3"><?php echo e($row['remarks']); ?></textarea>
                        </div>

                        <button type="submit" name="update" class="btn btn-info text-white">Update Billing</button>
                        <a href="billing_view.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>