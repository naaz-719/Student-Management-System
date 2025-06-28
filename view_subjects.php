<?php
include 'db.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$sql = "SELECT * FROM subjects ORDER BY department, subject_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Subjects</title>
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
        .department-header {
            background-color: #e9ecef;
            font-weight: bold;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
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
        <h2>Subjects Grouped by Department</h2>

        <?php
        if ($result->num_rows > 0) {
            $currentDepartment = null;

            while ($row = $result->fetch_assoc()) {
                // Check if department changed, if yes print header and start new table
                if ($row['department'] !== $currentDepartment) {
                    // Close previous table except for first iteration
                    if ($currentDepartment !== null) {
                        echo "</tbody></table>";
                    }
                    $currentDepartment = $row['department'];
                    echo "<div class='department-header'>{$currentDepartment}</div>";
                    echo "<table class='table table-bordered'>";
                    echo "<thead><tr>
                            <th>Subject ID</th>
                            <th>Subject Name</th>
                            <th>Actions</th>
                          </tr></thead><tbody>";
                }

                // Print subject row
                echo "<tr>
                        <td>" . htmlspecialchars($row['subject_id']) . "</td>
                        <td>" . htmlspecialchars($row['subject_name']) . "</td>
                        <td>
                            <a href='edit_subject.php?subject_id=" . urlencode($row['subject_id']) . "' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='delete_subject.php?subject_id=" . urlencode($row['subject_id']) . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>
                        </td>
                      </tr>";
            }

            // Close last table after loop
            echo "</tbody></table>";
        } else {
            echo "<p>No subjects found.</p>";
        }
        ?>
    </div>
</body>
</html>
