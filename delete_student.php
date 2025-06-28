<?php
include 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Step 1: Display the enrollment_no dropdown if no student is selected for deletion
if (!isset($_POST['select_enrollment']) && !isset($_POST['delete'])) {
    $sql = "SELECT enrollment_no, student_name FROM students";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "
        <form method='POST' action='' class='card p-4'>
            <div class='mb-3'>
                <label for='enrollment_no' class='form-label'><b>Select Enrollment No to Delete:</b></label>
                <select class='form-select' name='enrollment_no' id='enrollment_no'>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($row['enrollment_no']) . "'>" 
                 . htmlspecialchars($row['enrollment_no']) . " - " . htmlspecialchars($row['student_name']) . "</option>";
        }

        echo "
                </select>
            </div>
            <div class='text-center'>
                <input type='submit' class='btn btn-danger' name='select_enrollment' value='Select'>
            </div>
        </form>";
    } else {
        echo "<div class='alert alert-warning'>No students found.</div>";
    }

    $conn->close();
    exit;
}

// Step 2: Show a confirmation modal after selecting a student
if (isset($_POST['select_enrollment'])) {
    $enrollment_no = $conn->real_escape_string($_POST['enrollment_no']);

    $sql = "SELECT * FROM students WHERE enrollment_no = '$enrollment_no'";
    $result = $conn->query($sql);

    if (!$result) {
        die('Error retrieving student: ' . $conn->error);
    }

    if ($result->num_rows == 0) {
        die('No student found with that enrollment number.');
    } else {
        $row = $result->fetch_assoc();
    }

    echo "
    <div class='modal fade show' id='deleteModal' tabindex='-1' aria-labelledby='deleteModalLabel' aria-modal='true' role='dialog' style='display: block;'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='deleteModalLabel'>Confirm Deletion</h5>
                </div>
                <div class='modal-body'>
                    <p>Are you sure you want to delete the following student?</p>
                    <p><strong>Name: </strong>" . htmlspecialchars($row['student_name']) . "</p>
                    <p><strong>Enrollment No: </strong>" . htmlspecialchars($row['enrollment_no']) . "</p>
                </div>
                <div class='modal-footer'>
                    <form method='POST' action=''>
                        <input type='hidden' name='enrollment_no' value='" . htmlspecialchars($row['enrollment_no']) . "'>
                        <button type='submit' name='delete' class='btn btn-danger'>Yes, Delete</button>
                        <a href='delete_student.php' class='btn btn-secondary'>Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js'></script>
    <script>
        var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        myModal.show();
    </script>";
    $conn->close();
    exit;
}

// Step 3: Delete the student from the database if confirmed
if (isset($_POST['delete'])) {
    $enrollment_no = $conn->real_escape_string($_POST['enrollment_no']);

    $delete_sql = "DELETE FROM students WHERE enrollment_no = '$enrollment_no'";

    if ($conn->query($delete_sql) === TRUE) {
        header('Location: view_students.php');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error deleting record: " . $conn->error . "</div>";
    }

    $conn->close();
}
?>
