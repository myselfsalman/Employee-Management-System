<?php

include 'connect.php';

if (isset($_POST['submit'])) {
    $photo = $_FILES['photo']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);

    // Move the uploaded file to the server
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $dob = $_POST['dob'];

        $sql = "INSERT INTO `crud` (photo, fullname, email, mobile, dob)
                VALUES ('$photo', '$fullname', '$email', '$mobile', '$dob')";

        $result = mysqli_query($con, $sql);
        if ($result) {
            echo "Inserted successfully";
            header('Location: homepage.php');
            exit();
        } else {
            die(mysqli_error($con));
        }
    } else {
        die("Failed to upload file.");
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="addstyle.css">
    <title>Add New Employee</title>
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>

        <div class="container">
            <h1 class="text">Add an Employee</h1>
            <form method="post" enctype="multipart/form-data" id="add-employee-form">
                <label for="photo">Photo</label>
                <input type="file" name="photo" id="photo" accept="image/*" required>

                <label for="fullname">Full Name</label>
                <input type="text" name="fullname" id="fullname" placeholder="Enter your full name" required>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email address" required>

                <label for="mobile">Mobile</label>
                <input type="text" name="mobile" id="mobile" placeholder="Enter your phone number" required>

                <label for="dob">Date of Birth</label>
                <input type="date" name="dob" id="dob" required>

                <button type="submit" name="submit" class="btn btn-primary mt-3">Add Employee</button>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>