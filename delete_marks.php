<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);  // sanitize to integer

    $sql = "DELETE FROM marks WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header('Location: view_marks.php');
        exit; // stop further execution after redirect
    } else {
        echo "Error deleting marks: " . $conn->error;
    }

    $conn->close();
} else {
    echo "No marks ID specified.";
}
?>
