<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";

$message = "";
$messageType = "";

if (isset($_POST['submit'])) {
    $patient_id  = (int)($_POST['patient_id'] ?? 0);
    $subject     = trim($_POST['subject'] ?? '');
    $report      = trim($_POST['report'] ?? '');
    $result      = trim($_POST['result'] ?? '');
    $report_date = trim($_POST['report_date'] ?? '');

    if ($patient_id <= 0 || $subject === "" || $report === "" || $result === "" || $report_date === "") {
        $message = "Please fill in all required fields.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO investigations (patient_id, subject, report, result, report_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $patient_id, $subject, $report, $result, $report_date);

        if ($stmt->execute()) {
            header("Location: investigation_view.php");
            exit();
        } else {
            $message = "Failed to save investigation record.";
            $messageType = "danger";
        }

        $stmt->close();
    }
}

$patients = $conn->query("SELECT id, firstname, lastname FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Investigation - SHMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f4f8fb;">

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Add Investigation Record</h2>
                    <p class="text-muted mb-0">Smart Healthcare Management System</p>
                </div>
                <!--<a class="btn btn-secondary" href="investigation_view.php">Back</a>-->
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">Investigation Information</h4>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Select Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select Patient</option>
                                <?php while ($p = $patients->fetch_assoc()): ?>
                                    <option value="<?php echo (int)$p['id']; ?>">
                                        <?php echo htmlspecialchars($p['firstname'] . ' ' . $p['lastname'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Report</label>
                            <textarea name="report" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Result</label>
                            <select name="result" class="form-select" required>
                                <option value="">Select Result</option>
                                <option value="Open">Open</option>
                                <option value="Under Review">Under Review</option>
                                <option value="Resolved">Resolved</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Report Date</label>
                            <input type="date" name="report_date" class="form-control" required>
                        </div>

                        <button type="submit" name="submit" class="btn btn-primary">Save Investigation</button>
                        <a href="investigation_view.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>