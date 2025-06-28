<?php
// Start session and include database
session_start();
include 'db.php';

// Initialize variables
$subject_id = '';
$subject_name = '';
$department = '';

// Check if a specific subject is selected for editing
if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("s", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $subject_name = $row['subject_name'];
        $department = $row['department'];
    } else {
        echo "<div class='alert alert-danger'>Subject not found.</div>";
        exit;
    }
    $stmt->close();
}

// Update the subject record if the form is submitted
if (isset($_POST['update'])) {
    $subject_name = $_POST['subject_name'];
    $department = $_POST['department'];

    // Validate input here if needed

    // Prepare update statement
    $stmt = $conn->prepare("UPDATE subjects SET subject_name = ?, department = ? WHERE subject_id = ?");
    $stmt->bind_param("sss", $subject_name, $department, $subject_id);

    if ($stmt->execute()) {
        // Redirect after successful update
        header('Location: view_subjects.php');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error updating subject: " . $stmt->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Subject</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Edit Subject</h2>
    
    <form method="POST" action="" class="card p-4">
        <div class="mb-3">
            <label for="subject_name" class="form-label">Subject Name</label>
            <input type="text" class="form-control" name="subject_name" id="subject_name" value="<?php echo htmlspecialchars($subject_name); ?>" required>
        </div>

        <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <input type="text" class="form-control" name="department" id="department" value="<?php echo htmlspecialchars($department); ?>" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary" name="update">Update Subject</button>
        </div>
    </form>
</div>
</body>
</html>
