<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";


if (isset($_GET['id'])) {
    $prescription_id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM prescriptions WHERE id=?");
    $stmt->bind_param("i", $prescription_id);

    if ($stmt->execute()) {
        header("Location: prescription_view.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
} else {
    header("Location: prescription_view.php");
    exit();
}
?>