<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";


if (isset($_GET['id'])) {
    $appointment_id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM appointments WHERE id=?");
    $stmt->bind_param("i", $appointment_id);

    if ($stmt->execute()) {
        header("Location: appointment_view.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
} else {
    header("Location: appointment_view.php");
    exit();
}
?>