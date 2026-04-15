<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include "db.php";


if (isset($_GET['id'])) {
    $doctor_id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM doctors WHERE id=?");
    $stmt->bind_param("i", $doctor_id);

    if ($stmt->execute()) {
        header("Location: doctor_view.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
} else {
    header("Location: doctor_view.php");
    exit();
}
?>