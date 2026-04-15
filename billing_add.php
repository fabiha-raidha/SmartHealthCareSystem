<?php
include "db.php";

$message = "";
$messageType = "";

if (isset($_POST['submit'])) {
    $patient_id    = (int)($_POST['patient_id'] ?? 0);
    $bill_date     = trim($_POST['bill_date'] ?? '');
    $total_amount  = trim($_POST['total_amount'] ?? '');
    $bill_status   = trim($_POST['bill_status'] ?? '');
    $payment_date  = trim($_POST['payment_date'] ?? '');
    $remarks       = trim($_POST['remarks'] ?? '');

    if ($patient_id <= 0 || $bill_date === "" || $total_amount === "" || $bill_status === "") {
        $message = "Please fill in all required fields.";
        $messageType = "danger";
    } else {
        if ($payment_date === "") {
            $payment_date = null;
        }

        $stmt = $conn->prepare("INSERT INTO billing (patient_id, bill_date, total_amount, bill_status, payment_date, remarks) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdsss", $patient_id, $bill_date, $total_amount, $bill_status, $payment_date, $remarks);

        if ($stmt->execute()) {
            header("Location: billing_view.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }

        $stmt->close();
    }
}

$patients = $conn->query("SELECT id, firstname, lastname FROM users ORDER BY firstname ASC, lastname ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Billing Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f4f8fb;">

<div class="container py-5">
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 750px;">
        <div class="card-body p-4">
            <h2 class="mb-1">Accounts Module</h2>
            <p class="text-muted">Add Billing Record</p>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Select Patient</label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">Select Patient</option>
                        <?php while ($patient = $patients->fetch_assoc()): ?>
                            <option value="<?php echo (int)$patient['id']; ?>">
                                <?php echo htmlspecialchars($patient['firstname'] . ' ' . $patient['lastname']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bill Date</label>
                    <input type="date" name="bill_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Total Amount</label>
                    <input type="number" step="0.01" name="total_amount" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bill Status</label>
                    <select name="bill_status" class="form-select" required>
                        <option value="">Select Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                        <option value="Unpaid">Unpaid</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Date</label>
                    <input type="date" name="payment_date" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" name="submit" class="btn btn-primary">Save Billing</button>
                <a href="billing_view.php" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>