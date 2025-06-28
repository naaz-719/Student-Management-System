<?php
session_start();
include 'db.php';

// Initialize variables
$enrollment_no = '';
$subject_id = '';
$marks = 0;

// Check if enrollment_no and subject_id are set
if (isset($_GET['enrollment_no']) && isset($_GET['subject_id'])) {
    $enrollment_no = $_GET['enrollment_no'];
    $subject_id = $_GET['subject_id'];

    // Prepare and execute select query
    $stmt = $conn->prepare("SELECT * FROM marks WHERE enrollment_no = ? AND subject_id = ?");
    $stmt->bind_param("ss", $enrollment_no, $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $marks = $row['marks'];
    } else {
        echo "<div class='alert alert-danger'>Marks record not found.</div>";
        exit;
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Invalid parameters.</div>";
    exit;
}

// Update marks if form submitted
if (isset($_POST['update'])) {
    // Note: enrollment_no and subject_id are primary keys; ideally, don't change them here.
    $marks = $_POST['marks'];

    // Validate marks - optional but recommended
    if (!is_numeric($marks) || $marks < 0) {
        echo "<div class='alert alert-danger'>Invalid marks value.</div>";
    } else {
        // Prepare update query
        $stmt = $conn->prepare("UPDATE marks SET marks = ? WHERE enrollment_no = ? AND subject_id = ?");
        $stmt->bind_param("iss", $marks, $enrollment_no, $subject_id);

        if ($stmt->execute()) {
            header('Location: view_marks.php');
            exit;
        } else {
            echo "<div class='alert alert-danger'>Error updating marks: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Marks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Edit Marks</h2>
    <form method="POST" action="" class="card p-4">
        <div class="mb-3">
            <label for="enrollment_no" class="form-label">Enrollment No (cannot be changed)</label>
            <input type="text" class="form-control" id="enrollment_no" name="enrollment_no" value="<?php echo htmlspecialchars($enrollment_no); ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="subject_id" class="form-label">Subject ID (cannot be changed)</label>
            <input type="text" class="form-control" id="subject_id" name="subject_id" value="<?php echo htmlspecialchars($subject_id); ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="marks" class="form-label">Marks</label>
            <input type="number" class="form-control" name="marks" id="marks" value="<?php echo htmlspecialchars($marks); ?>" min="0" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary" name="update">Update Marks</button>
        </div>
    </form>
</div>
</body>
</html>
