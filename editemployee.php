<?php

include 'connect.php';

$id = $_GET['updateid'];

// Fetch the current data
$sql = "SELECT * FROM `crud` WHERE id=$id";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

$photo = $row['photo'];
$fullname = $row['fullname'];
$email = $row['email'];
$mobile = $row['mobile'];
$dob = $row['dob'];

if (isset($_POST['submit'])) {
    // Handle file upload
    $photo = $row['photo']; // Default to existing photo
    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);

        if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                // File uploaded successfully
            } else {
                die("Failed to upload file.");
            }
        } else {
            die("File upload error: " . $_FILES['photo']['error']);
        }
    }

    // Retrieve form data
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
    $dob = mysqli_real_escape_string($con, $_POST['dob']);

    // Update query
    $sql = "UPDATE `crud` SET photo='$photo', fullname='$fullname', email='$email', mobile='$mobile', dob='$dob' WHERE id=$id";
    $result = mysqli_query($con, $sql);

    if ($result) {
        header('Location: homepage.php');
        exit();
    } else {
        die(mysqli_error($con));
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="edit.css">
    <title>Edit Employee</title>
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>
        <h1 class="text">Edit Employee</h1>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Photo</label>
                <input type="file" class="form-control" name="photo" placeholder="Select Photo">
                <?php if ($photo): ?>
                    <img src="uploads/<?php echo htmlspecialchars($photo); ?>" alt="Photo" width="100" class="mt-2">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" class="form-control" name="fullname" placeholder="Enter name" value="<?php echo htmlspecialchars($fullname); ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label>Mobile</label>
                <input type="text" class="form-control" name="mobile" placeholder="Enter your number" value="<?php echo htmlspecialchars($mobile); ?>">
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($dob); ?>">
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Update</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
