<?php
include 'connect.php';

if (isset($_GET['deleteid'])) {
    $id = intval($_GET['deleteid']); // Sanitize and validate ID

    $sql = "DELETE FROM `crud` WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        header('Location: homepage.php');
        exit();
    } else {
        die('Error deleting record: ' . mysqli_error($con));
    }
}
?>
