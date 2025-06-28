<?php 
include 'db.php'; 
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch students for dropdown
$students = [];
$student_sql = "SELECT enrollment_no, student_name FROM students ORDER BY student_name";
$student_result = $conn->query($student_sql);
if ($student_result->num_rows > 0) {
    while ($row = $student_result->fetch_assoc()) {
        $students[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body { display: flex; }
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
        <a href="logout.php" class="nav-link" style="color: black;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <h2>Add Attendance</h2>
        <form action="add_attendance.php" method="POST" id="attendanceForm">
            <div class="mb-3">
                <label for="student_select" class="form-label">Student</label>
                <select class="form-select" id="student_select" required>
                    <option value="" disabled selected>Select student</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo htmlspecialchars($student['enrollment_no']); ?>">
                            <?php echo htmlspecialchars($student['student_name'] . " (" . $student['enrollment_no'] . ")"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <!-- Hidden input to store enrollment_no for submission -->
                <input type="hidden" id="enrollment_no" name="enrollment_no" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Attendance Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Attendance Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="" disabled selected>-- Select --</option>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Attendance</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enrollment_no = $_POST['enrollment_no'];
            $date = $_POST['date'];
            $status = $_POST['status'];

            // Optional: Validate enrollment_no exists in DB before insert

            $sql = "INSERT INTO attendance (enrollment_no, date, status) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $enrollment_no, $date, $status);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Attendance added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
            $stmt->close();
        }
        ?>
    </div>

<script>
    const studentSelect = document.getElementById('student_select');
    const enrollmentInput = document.getElementById('enrollment_no');

    studentSelect.addEventListener('change', () => {
        enrollmentInput.value = studentSelect.value;
    });
</script>
</body>
</html>
