<?php
include 'connect.php';

// Define items per page
$itemsPerPage = 10;

// Determine the current page number
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = max($page, 1); // Ensure page is at least 1

// Determine the sorting parameters
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validate sorting parameters
$validColumns = ['fullname', 'email', 'mobile', 'dob'];
if (!in_array($sortColumn, $validColumns)) {
    $sortColumn = 'id';
}
$sortOrder = $sortOrder === 'DESC' ? 'DESC' : 'ASC';

// Handle search parameters
$searchName = isset($_GET['name']) ? mysqli_real_escape_string($con, $_GET['name']) : '';
$searchEmail = isset($_GET['email']) ? mysqli_real_escape_string($con, $_GET['email']) : '';
$searchDob = isset($_GET['dob']) ? mysqli_real_escape_string($con, $_GET['dob']) : '';
$searchMobile = isset($_GET['mobile']) ? mysqli_real_escape_string($con, $_GET['mobile']) : '';

// Calculate the offset
$offset = ($page - 1) * $itemsPerPage;

// Build the SQL query for counting total records
$countSql = "SELECT COUNT(*) as total FROM `crud` WHERE 
        fullname LIKE '%$searchName%' AND 
        email LIKE '%$searchEmail%' AND 
        dob LIKE '%$searchDob%' AND 
        mobile LIKE '%$searchMobile%'";
$countResult = mysqli_query($con, $countSql);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];

// Build the SQL query with search, sorting, and pagination
$sql = "SELECT * FROM `crud` WHERE 
        fullname LIKE '%$searchName%' AND 
        email LIKE '%$searchEmail%' AND 
        dob LIKE '%$searchDob%' AND 
        mobile LIKE '%$searchMobile%'
        ORDER BY `$sortColumn` $sortOrder
        LIMIT $itemsPerPage OFFSET $offset";
$result = mysqli_query($con, $sql);

// Calculate total pages
$totalPages = ceil($totalRecords / $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styles for the confirmation popup */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-radius: 8px;
            z-index: 1000;
        }

        .popup button {
            margin: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .popup button.yes {
            background-color: #007bff;
            color: white;
        }

        .popup button.no {
            background-color: #ccc;
        }

        /* Overlay to darken the background when the popup is visible */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Sorting icon styles */
        .sort-icon {
            cursor: pointer;
            margin-left: 5px;
        }

        /* Pagination styles */
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .pagination a {
            margin: 0 5px;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: #0056b3;
        }

        .pagination a:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="content">
            <!-- Search Bar -->
            <form method="GET" action="homepage.php">
                <div class="search-bar">
                    <input type="text" name="name" value="<?php echo htmlspecialchars($searchName); ?>" placeholder="Name">
                    <input type="date" name="dob" value="<?php echo htmlspecialchars($searchDob); ?>" placeholder="Date of Birth">
                    <input type="email" name="email" value="<?php echo htmlspecialchars($searchEmail); ?>" placeholder="Email">
                    <input type="text" name="mobile" value="<?php echo htmlspecialchars($searchMobile); ?>" placeholder="Mobile">
                    <button type="submit"><img src="search_icon.png" alt="Search"></button>
                </div>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>
                            <a href="?sort=fullname&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                Full Name
                                <img src="sort_icon.png" width="15px" height="15px" alt="Sort" class="sort-icon">
                            </a>
                        </th>
                        <th>
                            <a href="?sort=email&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                Email
                                <img src="sort_icon.png" width="15px" height="15px" alt="Sort" class="sort-icon">
                            </a>
                        </th>
                        <th>
                            <a href="?sort=mobile&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                Mobile
                                <img src="sort_icon.png" width="15px" height="15px" alt="Sort" class="sort-icon">
                            </a>
                        </th>
                        <th>
                            <a href="?sort=dob&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                Date Of Birth
                                <img src="sort_icon.png" width="15px" height="15px" alt="Sort" class="sort-icon">
                            </a>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row["id"];
                            $photo = $row['photo'];
                            $fullname = $row['fullname'];
                            $email = $row['email'];
                            $mobile = $row['mobile'];
                            $dob = $row['dob'];

                            echo '<tr>
                                <td><img src="uploads/' . htmlspecialchars($photo) . '" width="100px" height="100px" alt="Photo"></td>
                                <td>' . htmlspecialchars($fullname) . '</td>
                                <td>' . htmlspecialchars($email) . '</td>
                                <td>' . htmlspecialchars($mobile) . '</td>
                                <td>' . htmlspecialchars($dob) . '</td>
                                <td>
                                    <a href="editemployee.php?updateid=' . htmlspecialchars($id) . '"><img src="edit_icon.png" width="25px" height="25px" alt="Edit"></a>
                                    <a href="javascript:void(0);" onclick="showPopup(' . htmlspecialchars($id) . ')"><img src="deleteicon.png" width="25px" height="25px" alt="Delete"></a>
                                </td>
                            </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">No records found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>">
                        <<< >>>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>">>></a>
                        <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Confirmation Popup -->
    <div class="overlay" id="overlay"></div>
    <div class="popup" id="popup">
        <p>Are you sure you want to delete this record?</p>
        <button class="yes" onclick="confirmDelete()">Yes</button>
        <button class="no" onclick="cancelDelete()">No</button>
    </div>

    <script>
        let deleteId = null;

        function showPopup(id) {
            deleteId = id;
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('popup').style.display = 'block';
        }

        function confirmDelete() {
            if (deleteId !== null) {
                window.location.href = `deleteemployee.php?deleteid=${deleteId}`;
            }
        }

        function cancelDelete() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('popup').style.display = 'none';
            deleteId = null;
        }

        // Handle image resizing
        document.getElementById('upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        const maxWidth = 800;
                        const maxHeight = 800;
                        let width = img.width;
                        let height = img.height;

                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }

                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }

                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob(function(blob) {
                            const resizedFile = new File([blob], file.name, {
                                type: file.type
                            });
                            const formData = new FormData();
                            formData.append('photo', resizedFile);

                            // Use AJAX to submit the resized image
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'upload.php', true);
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    alert('Image uploaded and resized successfully');
                                } else {
                                    alert('Error uploading image');
                                }
                            };
                            xhr.send(formData);
                        }, file.type);
                    };
                    img.src = event.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                alert('Please select a valid image file.');
            }
        });
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>