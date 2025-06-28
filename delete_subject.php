<?php
include 'db.php';

if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];

    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("s", $subject_id);

    if ($stmt->execute()) {
        header('Location: view_subjects.php');
        exit;
    } else {
        echo "Error deleting subject: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No subject specified.";
}
?>
