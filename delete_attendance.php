<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sanitize id

    $sql = "DELETE FROM attendance WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header('Location: view_attendance.php');
        exit;  // stop execution after redirect
    } else {
        echo "Error deleting attendance: " . $conn->error;
    }

    $conn->close();
} else {
    echo "No attendance ID specified.";
}
?>
