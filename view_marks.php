<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Join marks with students and subjects to get student names and subject names
$sql = "
    SELECT 
        m.enrollment_no,
        s.student_name,
        m.subject_id,
        sub.subject_name,
        m.marks
    FROM marks m
    INNER JOIN students s ON m.enrollment_no = s.enrollment_no
    INNER JOIN subjects sub ON m.subject_id = sub.subject_id
    ORDER BY s.student_name, sub.subject_name
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Marks</title>
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
        .average-marks {
            font-weight: bold;
            margin-bottom: 30px;
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
        <h2>Marks Records</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php
            $current_student = '';
            $marks_sum = 0;
            $marks_count = 0;

            while ($row = $result->fetch_assoc()):
                if ($current_student != $row['student_name']):
                    if ($current_student != ''):
                        // Show average marks for previous student
                        $average = $marks_count > 0 ? round($marks_sum / $marks_count, 2) : 0;
                        echo "<div class='average-marks'>Average Marks: {$average}</div>";
                        // Close previous table
                        echo '</tbody></table>';
                    endif;

                    // Reset sum and count for new student
                    $marks_sum = 0;
                    $marks_count = 0;

                    // New student header
                    echo "<h4>" . htmlspecialchars($row['student_name']) . " (Enrollment No: " . htmlspecialchars($row['enrollment_no']) . ")</h4>";
                    echo '<table class="table table-bordered mb-4">';
                    echo '<thead><tr><th>Subject ID</th><th>Subject Name</th><th>Marks</th><th>Actions</th></tr></thead><tbody>';

                    $current_student = $row['student_name'];
                endif;

                // Add current marks to sum and increment count
                $marks_sum += $row['marks'];
                $marks_count++;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['subject_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['marks']); ?></td>
                    <td>
                        <a href="edit_marks.php?enrollment_no=<?php echo urlencode($row['enrollment_no']); ?>&subject_id=<?php echo urlencode($row['subject_id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_marks.php?enrollment_no=<?php echo urlencode($row['enrollment_no']); ?>&subject_id=<?php echo urlencode($row['subject_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php
            // Show average for last student
            $average = $marks_count > 0 ? round($marks_sum / $marks_count, 2) : 0;
            echo "<div class='average-marks'>Average Marks: {$average}</div>";
            ?>
            </tbody></table>
        <?php else: ?>
            <div class="alert alert-info">No marks records found.</div>
        <?php endif; ?>

    </div>
</body>
</html>

