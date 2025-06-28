<?php
include 'db.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Join attendance with students to get student_name
$sql = "SELECT attendance.*, students.student_name 
        FROM attendance 
        LEFT JOIN students ON attendance.enrollment_no = students.enrollment_no";
$result = $conn->query($sql);

// Get total and average attendance per student
$summarySql = "
    SELECT s.student_name, s.enrollment_no,
        COUNT(a.status) AS total_days,
        SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present_days,
        ROUND((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.status)) * 100, 2) AS attendance_percentage
    FROM students s
    LEFT JOIN attendance a ON s.enrollment_no = a.enrollment_no
    GROUP BY s.enrollment_no, s.student_name
    ORDER BY s.student_name
";
$summaryResult = $conn->query($summarySql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Attendance</title>
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
        .summary-table {
            margin-top: 40px;
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
        <a href="logout.php" class="nav-link" style="color: black;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="content">
        <h2>Attendance Records</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Enrollment No</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['student_name'] ?? 'Unknown'); ?></td>
                            <td><?php echo htmlspecialchars($row['enrollment_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <a href="edit_attendance.php?enrollment_no=<?php echo urlencode($row['enrollment_no']); ?>&date=<?php echo urlencode($row['date']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_attendance.php?enrollment_no=<?php echo urlencode($row['enrollment_no']); ?>&date=<?php echo urlencode($row['date']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h3>Attendance Summary</h3>
        <table class="table table-striped summary-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Enrollment No</th>
                    <th>Total Days</th>
                    <th>Days Present</th>
                    <th>Average Attendance (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($summaryResult->num_rows > 0): ?>
                    <?php while ($summary = $summaryResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($summary['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($summary['enrollment_no']); ?></td>
                            <td><?php echo htmlspecialchars($summary['total_days']); ?></td>
                            <td><?php echo htmlspecialchars($summary['present_days']); ?></td>
                            <td><?php echo htmlspecialchars($summary['attendance_percentage']); ?>%</td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No attendance summary available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
