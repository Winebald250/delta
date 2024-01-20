<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
   exit(); // Added exit after header to stop further execution
}

if (isset($_POST['add_info'])) {

   $title = mysqli_real_escape_string($conn, $_POST['title']);
   $info = mysqli_real_escape_string($conn, $_POST['message']); // Updated variable title to 'message'
   $file = $_FILES['file']['name'];
   $file_type = $_FILES['file']['type'];
   $file_size = $_FILES['file']['size'];
   $file_tmp = $_FILES['file']['tmp_name'];
   $allowed_extensions = array("jpg", "jpeg", "png", "mp4");

   $image_folder = 'uploaded_img/';

   $select_info_title = mysqli_query($conn, "SELECT title FROM `consequences` WHERE title = '$title'") or die('query failed');

   if (mysqli_num_rows($select_info_title) > 0) {
      $message[] = 'consequence already added'; // Updated message
   } else {
      if (in_array(pathinfo($file, PATHINFO_EXTENSION), $allowed_extensions)) {
         $add_info_query = mysqli_query($conn, "INSERT INTO `consequences` (title, info, file) VALUES ('$title', '$info', '$file')") or die('query failed');

         if ($add_info_query) {
            if ($file_size > 2000000000) {
               $message[] = 'File size is too large';
            } else {
               move_uploaded_file($file_tmp, $image_folder . $file);
               $message[] = 'consequence added successfully!';
            }
         } else {
            $message[] = 'consequence could not be added!';
         }
      } else {
         $message[] = 'Invalid file type. Allowed types: jpg, jpeg, png, mp4';
      }
   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_image_query = mysqli_query($conn, "SELECT file FROM `consequences` WHERE id = '$delete_id'") or die('query failed');
   $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
   unlink('uploaded_img/' . $fetch_delete_image['file']);
   mysqli_query($conn, "DELETE FROM `consequences` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_consequences.php');
   exit(); // Added exit after header to stop further execution
}

if (isset($_POST['update_info'])) {

   $update_p_id = $_POST['update_p_id'];
   $update_title = mysqli_real_escape_string($conn, $_POST['update_title']);
   $update_info = mysqli_real_escape_string($conn, $_POST['update_info']);

   mysqli_query($conn, "UPDATE `consequences` SET title = '$update_title'WHERE id = '$update_p_id'") or die('query failed');

   $update_file = $_FILES['update_file']['name'];
   $update_file_tmp = $_FILES['update_file']['tmp_name'];
   $update_file_size = $_FILES['update_file']['size'];
   $update_folder = 'uploaded_img/';
   $update_old_file = $_POST['update_old_file'];

   if (!empty($update_file)) {
      if ($update_file_size > 2000000000) {
         $message[] = 'File size is too large';
      } else {
         unlink('uploaded_img/' . $update_old_file);
         move_uploaded_file($update_file_tmp, $update_folder . $update_file);
         mysqli_query($conn, "UPDATE `consequences` SET file = '$update_file' WHERE id = '$update_p_id'") or die('query failed');
      }
   }

   header('location:admin_consequences.php?updated=true');
   exit(); // Added exit after header to stop further execution
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>consequences</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

   <?php include 'admin_header.php'; ?>

   <!-- consequences CRUD section starts  -->

   <section class="add-consequences">

      <h1 class="title">consequences</h1>

      <form action="" method="post" enctype="multipart/form-data">
         <input type="text" name="title" class="box" placeholder="Enter title" required>
         <input type="file" name="file" accept="image/jpg, video/mp4, image/jpeg, image/png" class="box">
         <textarea name="message" class="box" placeholder="Enter info" id="" cols="30" rows="10" required></textarea>
         <input type="submit" value="add consequence" name="add_info" class="btn">
      </form>

   </section>

   <!-- consequences CRUD section ends -->

   <!-- show consequences  -->

   <section class="show-consequences">
      <div class="box-container">
         <?php
            $select_consequences = mysqli_query($conn, "SELECT * FROM `consequences`") or die('query failed');
            if (mysqli_num_rows($select_consequences) > 0) {
               while ($fetch_consequences = mysqli_fetch_assoc($select_consequences)) {
         ?>
                  <div class="box">
                     <?php if (strpos($fetch_consequences['file'], '.mp4') !== false) : ?>
                        <video width="100%" height="auto" controls>
                           <source src="uploaded_img/<?php echo $fetch_consequences['file']; ?>" type="video/mp4">
                           Your browser does not support the video tag.
                        </video>
                     <?php else : ?>
                        <img src="uploaded_img/<?php echo $fetch_consequences['file']; ?>" alt="">
                     <?php endif; ?>
                     <div class="title"><?php echo $fetch_consequences['title']; ?></div>
                     <div class="info"><?php echo $fetch_consequences['info']; ?></div>
                     <a href="admin_consequences.php?update=<?php echo $fetch_consequences['id']; ?>" class="option-btn">update</a>
                     <a href="admin_consequences.php?delete=<?php echo $fetch_consequences['id']; ?>" class="delete-btn" onclick="return confirm('delete this consequence?');">delete</a>
                  </div>
<?php
               }
            } else {
               echo '<p class="empty">no consequences added yet!</p>';
            }
         ?>
      </div>
   </section>

   <section class="edit-consequences-form">
      <?php
         if (isset($_GET['update'])) {
            $update_id = $_GET['update'];
            $update_query = mysqli_query($conn, "SELECT * FROM `consequences` WHERE id = '$update_id'") or die('query failed');
            if (mysqli_num_rows($update_query) > 0) {
               while ($fetch_update = mysqli_fetch_assoc($update_query)) {
      ?>
                  <form action="" method="post" enctype="multipart/form-data">
                     <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                     <input type="hidden" name="update_old_file" value="<?php echo $fetch_update['file']; ?>">
                     <?php if (strpos($fetch_update['file'], '.mp4') !== false) : ?>
                        <video width="100%" height="auto" controls>
                           <source src="uploaded_img/<?php echo $fetch_update['file']; ?>" type="video/mp4">
                           Your browser does not support the video tag.
                        </video>
                     <?php else : ?>
                        <img src="uploaded_img/<?php echo $fetch_update['file']; ?>" alt="">
                     <?php endif; ?>
                     <input type="text" name="update_title" value="<?php echo $fetch_update['title']; ?>" class="box" required placeholder="enter title">
                     <input type="file" name="update_file" class="box" accept="image/jpg, image/jpeg, image/png, video/mp4">
                     <input type="submit" value="update" name="update_info" class="btn">
                     <input type="button" value="cancel" onclick="location.href='admin_consequences.php';" class="option-btn">
                  </form>
      <?php
               }
            }
         } else {
            echo '<script>document.querySelector(".edit-consequences-form").style.display = "none";</script>';
         }
      ?>
   </section>

   <!-- custom admin js file link  -->
   <script src="js/admin_script.js"></script>
</body>

</html>
