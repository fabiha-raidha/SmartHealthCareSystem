<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";

$message = "";
$messageType = "";

if (isset($_POST['update'])) {
    $id            = (int)($_POST['id'] ?? 0);
    $patient_id    = (int)($_POST['patient_id'] ?? 0);
    $company_name  = trim($_POST['company_name'] ?? '');
    $policy_number = trim($_POST['policy_number'] ?? '');
    $claim_amount  = trim($_POST['claim_amount'] ?? '');
    $claim_status  = trim($_POST['claim_status'] ?? '');
    $remarks       = trim($_POST['remarks'] ?? '');

    if ($patient_id <= 0 || $company_name === "" || $policy_number === "" || $claim_amount === "" || $claim_status === "") {
        $message = "Please fill in all required fields.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("UPDATE insurance SET patient_id=?, company_name=?, policy_number=?, claim_amount=?, claim_status=?, remarks=? WHERE id=?");
        $stmt->bind_param("issdssi", $patient_id, $company_name, $policy_number, $claim_amount, $claim_status, $remarks, $id);

        if ($stmt->execute()) {
            header("Location: insurance_view.php");
            exit();
        } else {
            $message = "Failed to update insurance record.";
            $messageType = "danger";
        }

        $stmt->close();
    }
}

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: insurance_view.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['id'];

$stmt = $conn->prepare("SELECT * FROM insurance WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: insurance_view.php");
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();

$patients = $conn->query("SELECT id, firstname, lastname FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Insurance - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Update Insurance Record</h2>
                    <p class="text-muted mb-0">Smart Healthcare Management System</p>
                </div>
                <a class="btn btn-secondary" href="insurance_view.php">Back</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">Edit Insurance Information</h4>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Select Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select Patient</option>
                                <?php while ($p = $patients->fetch_assoc()): ?>
                                    <option value="<?php echo (int)$p['id']; ?>" <?php if ($p['id'] == $row['patient_id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($p['firstname'] . ' ' . $p['lastname'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($row['company_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Policy Number</label>
                            <input type="text" name="policy_number" class="form-control" value="<?php echo htmlspecialchars($row['policy_number'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Claim Amount</label>
                            <input type="number" step="0.01" name="claim_amount" class="form-control" value="<?php echo htmlspecialchars($row['claim_amount'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Claim Status</label>
                            <select name="claim_status" class="form-select" required>
                                <option value="Pending" <?php if ($row['claim_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Approved" <?php if ($row['claim_status'] == 'Approved') echo 'selected'; ?>>Approved</option>
                                <option value="Rejected" <?php if ($row['claim_status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3"><?php echo htmlspecialchars($row['remarks'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <button type="submit" name="update" class="btn btn-info text-white">Update Insurance</button>
                        <a href="insurance_view.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>