<?php
ob_start();
require_once '../inc/config.php'; ?>
<?php require '../inc/header.php'; ?>
<?php $employee_name = $_SESSION['employee_name'];
$employee_id = $_SESSION['employee_id'];
$employee_role = $_SESSION['employee_role'];

$sql = "SELECT * FROM employees WHERE employ_id = '$employee_id'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
$employee_name = $row['emp_name'];
$employee_mobile = $row['mobile'];
$address = $row['address'];
$bank_account = $row['bank_account'];
$nic = $row['nic'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_name = $_POST['employee_name'];
    $employee_mobile = $_POST['employee_mobile'];
    $address = $_POST['address'];
    $bank_account = $_POST['bank_account'];
    $nic = $_POST['nic'];

    $sql = "UPDATE employees SET emp_name = '$employee_name', mobile = '$employee_mobile', address = '$address', bank_account = '$bank_account', nic = '$nic' WHERE employ_id = '$employee_id'";
    insert_query($sql, "Name : $employee_name, Mobile : $employee_mobile, Address : $address, Bank Account : $bank_account, NIC : $nic", "Employee Profile Updated");
    // refresh the page
    header("Location: /profile/index.php");
    ob_end_clean();
    exit;
}
ob_end_flush();

// Retrieve the employee's profile image
// Your database query
$query = "SELECT dp FROM employees WHERE employ_id = ?";

// Prepare and execute the statement
$stmt = $con->prepare($query);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $image = $row['dp'];

    // Determine the MIME type (assuming it's JPEG, adjust if needed)
    $mime_type = 'image/jpeg';

    // Encode the image data to base64
    $base64_image = base64_encode($image);

    // Output the image tag
    $profile_image = '<img src="data:' . $mime_type . ';base64,' . $base64_image . '" alt="Avatar" id="profile-image" />';
} else {
    echo "No image found for this employee.";
}

// Close the statement and connection
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $employee_name ?> | Profile </title>
    <link rel="stylesheet" href="/profile/profile.css">
</head>

<?php include 'UCPnav.php';
?>

<body>
    <div class="container">
        <div class="profile-header">
            <div class="profile-header-left">
                <div class="profile-image">
                    <?php echo $profile_image; ?>
                    <!-- Add icon for change avatar as a button -->
                    <button onclick="uploadAvatar(<?php echo $employee_id ?>)" id="change-avatar" class="btn"><i class="fas fa-user-edit"></i> </button>
                </div>
            </div>
            <div class="profile-header-titles">
                <h1 id="profile-name"><?php echo $employee_name ?></h1>
                <p id="profile-role"><?php echo $employee_role ?></p>
            </div>
        </div>
        <form id="profile-form" method="POST" action="">
            <div class="form-group">
                <label for="last-name">Name</label>
                <input type="text" id="last-name" name="employee_name" value="<?php echo $employee_name ?>" required>
            </div>
            <div class="form-group">
                <label for="mobile-number">Mobile Number *</label>
                <input type="text" id="mobile-number" name="employee_mobile" value="<?php echo $employee_mobile ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Residential Address</label>
                <input type="text" id="address" name="address" value="<?php echo $address ?>">
            </div>
            <div class="form-group">
                <label for="bank-account">Bank Account</label>
                <input type="text" id="bank-account" name="bank_account" value="<?php echo $bank_account ?>">
            </div>
            <div class="form-group">
                <label for="id">NIC Number</label>
                <input type="text" id="id" name="nic" value="<?php echo $nic ?>">
            </div>
            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>

</body>

</html>

<script>
    function uploadAvatar(emp_id) {
        // Add Image Upload button with Swal Alert

        // Send file to server using AJAX (/profile/uploadAvatar.php)
        Swal.fire({
            title: 'Upload Avatar',
            input: 'file',
            inputAttributes: {
                accept: 'image/*',
                'aria-label': 'Upload your profile picture'
            },
            showCancelButton: true,
            confirmButtonText: 'Upload',
            showLoaderOnConfirm: true,
            preConfirm: (file) => {
                if (!file) {
                    Swal.showValidationMessage('No file selected');
                    return;
                }

                const formData = new FormData();
                formData.append('file', file);
                formData.append('emp_id', emp_id);

                if (file.size > 1000000) {
                    Swal.showValidationMessage('File size should be less than 1MB');
                    return;
                }
                if (file.type !== 'image/jpeg' && file.type !== 'image/png') {
                    Swal.showValidationMessage('File type should be JPG or PNG');
                    return;
                }

                Swal.fire({
                    title: 'Your uploaded picture',
                    imageUrl: URL.createObjectURL(file),
                    imageAlt: 'The uploaded picture',
                    confirmButtonText: 'Save',
                    showCancelButton: true,
                    preConfirm: () => {
                        return fetch('/profile/uploadAvatar.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText);
                                    console.log(response);
                                }
                                return response.json();
                            })
                            .then(data => {
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Your avatar has been uploaded successfully!',
                                    icon: 'success'
                                });
                                // refresh the page
                                location.reload();
                                console.log(data);
                            })
                            .catch(error => {
                                Swal.showValidationMessage(
                                    `Request failed: ${error}`
                                );
                                console.log(error);
                            });
                    }
                });
            },
        });
    }
</script>
<?php end_db_con(); ?>