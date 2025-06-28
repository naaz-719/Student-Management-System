<?php
include 'db.php';

// Initialize variables
$enrollment_no = '';
$student_name = '';
$department = '';
$phone = '';

// Check if enrollment_no is set in GET
if (isset($_GET['enrollment_no'])) {
    $enrollment_no = $_GET['enrollment_no'];

    // Prepare statement to fetch student data
    $stmt = $conn->prepare("SELECT * FROM students WHERE enrollment_no = ?");
    $stmt->bind_param("s", $enrollment_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $student_name = $student['student_name'];
        $department = $student['department'];
        $phone = $student['phone'];
    } else {
        echo "<div class='alert alert-danger'>Student not found.</div>";
        exit;
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>No student specified.</div>";
    exit;
}

// Update student data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = $_POST['student_name'];
    $department = $_POST['department'];
    $phone = $_POST['phone'];

    // Prepare update statement - do NOT update enrollment_no (PK) here, just update other fields
    $stmt = $conn->prepare("UPDATE students SET student_name = ?, department = ?, phone = ? WHERE enrollment_no = ?");
    $stmt->bind_param("ssss", $student_name, $department, $phone, $enrollment_no);

    if ($stmt->execute()) {
        header('Location: view_students.php');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error updating student: " . $stmt->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            display: flex;
        }
        .sidebar {
            min-width: 250px;
            background-color: #f8f9fa;
            height: 100vh;
            padding: 15px;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #000;
        }
        .sidebar a:hover {
            background-color: #ddd;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>Admin</h4>
        <a href="add_attendance.php">Add Attendance</a>
        <a href="view_attendance.php">View Attendance</a>
        <a href="add_marks.php">Add Marks</a>
        <a href="view_marks.php">View Marks</a>
        <a href="add_student.php">Add Student</a>
        <a href="view_students.php">View Students</a>
        <a href="add_subject.php">Add Subject</a>
        <a href="view_subjects.php">View Subjects</a>
        <a href="logout.php" class="nav-link" style="color: black;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <h2>Edit Student</h2>
        <form action="edit_student.php?enrollment_no=<?php echo urlencode($enrollment_no); ?>" method="POST">
            <div class="mb-3">
                <label for="enrollment_no" class="form-label">Enrollment No (cannot be changed)</label>
                <input type="text" class="form-control" id="enrollment_no" name="enrollment_no" value="<?php echo htmlspecialchars($enrollment_no); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="student_name" class="form-label">Student Name</label>
                <input type="text" class="form-control" id="student_name" name="student_name" value="<?php echo htmlspecialchars($student_name); ?>" required>
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" name="department" value="<?php echo htmlspecialchars($department); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update Student</button>
        </form>
    </div>
</body>
</html>
