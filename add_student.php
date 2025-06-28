<?php include 'db.php'; 

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <style>
        body { display: flex; }
        .sidebar {
            min-width: 250px; background-color: #f8f9fa; height: 100vh; padding: 15px;
        }
        .sidebar a {
            display: block; padding: 10px; text-decoration: none; color: #000;
        }
        .sidebar a:hover { background-color: #ddd; }
        .content { flex-grow: 1; padding: 20px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4><b>Admin</b></h4>
        <a href="index.php">Home</a>
        <a href="add_attendance.php">Add Attendance</a>
        <a href="view_attendance.php">View Attendance</a>
        <a href="add_marks.php">Add Marks</a>
        <a href="view_marks.php">View Marks</a>
        <a href="add_student.php">Add Student</a>
        <a href="view_students.php">View Students</a>
        <a href="add_subject.php">Add Subject</a>
        <a href="view_subjects.php">View Subjects</a>
        <a href="logout.php" class="nav-link" style="color: black;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="content">
        <h2>Add Student</h2>
        <form action="add_student.php" method="POST">
            <div class="mb-3">
                <label for="enrollment_no" class="form-label">Enrollment No</label>
                <input type="text" class="form-control" id="enrollment_no" name="enrollment_no" required />
            </div>
            <div class="mb-3">
                <label for="student_name" class="form-label">Student Name</label>
                <input type="text" class="form-control" id="student_name" name="student_name" required />
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" name="department" required />
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" />
            </div>
            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enrollment_no = $_POST['enrollment_no'];
            $student_name = $_POST['student_name'];
            $department = $_POST['department'];
            $phone = $_POST['phone'] ?? null;

            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO students (enrollment_no, student_name, department, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $enrollment_no, $student_name, $department, $phone);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Student added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        }
        ?>
    </div>
</body>
</html>

