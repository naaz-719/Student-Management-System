<?php
include 'db.php';

// Initialize variables
$enrollment_no = '';
$date = '';
$status = '';

if (isset($_GET['enrollment_no']) && isset($_GET['date'])) {
    $enrollment_no = $_GET['enrollment_no'];
    $date = $_GET['date'];

    // Fetch attendance record
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE enrollment_no = ? AND date = ?");
    $stmt->bind_param("ss", $enrollment_no, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $status = $row['status'];
    } else {
        echo "<div class='alert alert-danger'>Attendance record not found.</div>";
        exit;
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Invalid parameters.</div>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Status should be 'Present' or 'Absent'
    $status = $_POST['status'];

    // Validate status
    if (!in_array($status, ['Present', 'Absent'])) {
        echo "<div class='alert alert-danger'>Invalid status selected.</div>";
    } else {
        $stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE enrollment_no = ? AND date = ?");
        $stmt->bind_param("sss", $status, $enrollment_no, $date);

        if ($stmt->execute()) {
            header('Location: view_attendance.php');
            exit;
        } else {
            echo "<div class='alert alert-danger'>Error updating attendance: " . $stmt->error . "</div>";
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
    <title>Edit Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h2>Edit Attendance</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="enrollment_no" class="form-label">Enrollment No (cannot be changed)</label>
            <input type="text" class="form-control" id="enrollment_no" name="enrollment_no" value="<?php echo htmlspecialchars($enrollment_no); ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date (cannot be changed)</label>
            <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Present" <?php echo $status == 'Present' ? 'selected' : ''; ?>>Present</option>
                <option value="Absent" <?php echo $status == 'Absent' ? 'selected' : ''; ?>>Absent</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Attendance</button>
    </form>
</div>
</body>
</html>
