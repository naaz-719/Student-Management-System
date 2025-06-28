<?php 
include 'db.php'; 

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch students with their departments
$students = [];
$student_sql = "SELECT enrollment_no, student_name, department FROM students ORDER BY student_name";
$student_result = $conn->query($student_sql);
if ($student_result->num_rows > 0) {
    while ($row = $student_result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Fetch all subjects with their departments for JS filtering
$subjects = [];
$subject_sql = "SELECT subject_id, subject_name, department FROM subjects ORDER BY subject_name";
$subject_result = $conn->query($subject_sql);
if ($subject_result->num_rows > 0) {
    while ($row = $subject_result->fetch_assoc()) {
        $subjects[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Marks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
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
    <h2>Add Marks</h2>
    <form action="add_marks.php" method="POST" id="marksForm">
        <div class="mb-3">
            <label for="student_name" class="form-label">Student Name (Enrollment No.)</label>
            <select class="form-select" id="student_name" name="student_name" required>
                <option value="" disabled selected>Select student</option>
                <?php foreach ($students as $student): ?>
                    <option 
                        value="<?php echo htmlspecialchars($student['enrollment_no']); ?>" 
                        data-department="<?php echo htmlspecialchars($student['department']); ?>">
                        <?php 
                            echo htmlspecialchars($student['student_name']) . " (" . htmlspecialchars($student['enrollment_no']) . ")";
                        ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="subject_id" class="form-label">Subject</label>
            <select class="form-select" id="subject_id" name="subject_id" required>
                <option value="" disabled selected>Select subject</option>
                <!-- Subjects will be populated dynamically -->
            </select>
        </div>

        <div class="mb-3">
            <label for="marks" class="form-label">Marks</label>
            <input type="number" class="form-control" id="marks" name="marks" required />
        </div>

        <button type="submit" class="btn btn-primary">Add Marks</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $enrollment_no = $_POST['student_name']; 
        $subject_id = $_POST['subject_id'];
        $marks = $_POST['marks'];

        // Get student's department from DB for validation
        $stmt = $conn->prepare("SELECT department FROM students WHERE enrollment_no = ?");
        $stmt->bind_param("s", $enrollment_no);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            echo "<div class='alert alert-danger'>Invalid student selected.</div>";
        } else {
            $student_data = $result->fetch_assoc();
            $department = $student_data['department'];

            // Validate subject belongs to that department
            $stmt2 = $conn->prepare("SELECT * FROM subjects WHERE subject_id = ? AND department = ?");
            $stmt2->bind_param("ss", $subject_id, $department);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2->num_rows === 0) {
                echo "<div class='alert alert-danger'>Selected subject does not belong to the student's department.</div>";
            } else {
                // Insert marks
                $stmt3 = $conn->prepare("INSERT INTO marks (enrollment_no, subject_id, marks) VALUES (?, ?, ?)");
                $stmt3->bind_param("ssi", $enrollment_no, $subject_id, $marks);
                if ($stmt3->execute()) {
                    echo "<div class='alert alert-success'>Marks added successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
                }
                $stmt3->close();
            }
            $stmt2->close();
        }
        $stmt->close();
    }
    ?>
</div>

<script>
    const subjects = <?php echo json_encode($subjects); ?>;
    const studentSelect = document.getElementById('student_name');
    const subjectSelect = document.getElementById('subject_id');

    studentSelect.addEventListener('change', function() {
        // Get department of selected student from data attribute
        const selectedOption = this.options[this.selectedIndex];
        const dept = selectedOption.getAttribute('data-department');

        // Clear subject dropdown
        subjectSelect.innerHTML = '<option value="" disabled selected>Select subject</option>';

        // Filter subjects by student's department
        const filteredSubjects = subjects.filter(sub => sub.department === dept);

        // Populate subjects dropdown
        filteredSubjects.forEach(sub => {
            const option = document.createElement('option');
            option.value = sub.subject_id;
            option.textContent = `${sub.subject_name} (${sub.subject_id})`;
            subjectSelect.appendChild(option);
        });
    });
</script>

</body>
</html>
