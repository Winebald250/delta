<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
   exit(); // Added exit after header to stop further execution
}

// Initialize $message variable
$message = array();

function addBlog($conn) {
   global $message; // Make $message variable accessible inside the function

   if (isset($_POST['add_info'])) {
      $title = mysqli_real_escape_string($conn, $_POST['title']);
      $file = $_FILES['file']['name'];
      $file_size = $_FILES['file']['size'];
      $allowed_extensions = array("jpg", "jpeg", "png", "mp4");
      $image_folder = 'uploaded_img/';

      $select_info_title = mysqli_query($conn, "SELECT blogtitle FROM `blogs` WHERE blogtitle = '$title'") or die('query failed');

      if (mysqli_num_rows($select_info_title) > 0) {
         $message[] = 'Blog already added'; // Updated message
      } else {
         if (in_array(pathinfo($file, PATHINFO_EXTENSION), $allowed_extensions)) {
            // Additional input for added by
            $addedby = mysqli_real_escape_string($conn, $_POST['addedby']);
            
            $add_info_query = mysqli_query($conn, "INSERT INTO `blogs` (blogtitle, file, addedby) VALUES ('$title', '$file', '$addedby')") or die('query failed');

            if ($add_info_query) {
               if ($file_size > 2000000000) { // Updated the check for large file size
                  $message[] = 'File size is too large';
               } else {
                  move_uploaded_file($_FILES['file']['tmp_name'], $image_folder . $file);
                  $message[] = 'Blog added successfully!';
               }
            } else {
               $message[] = 'Blog could not be added!';
            }
         } else {
            $message[] = 'Invalid file type. Allowed types: jpg, jpeg, png, mp4';
         }
      }
   }
}

function deleteBlog($conn) {
   if (isset($_GET['delete'])) {
      $delete_id = $_GET['delete'];
      $delete_image_query = mysqli_query($conn, "SELECT file FROM `blogs` WHERE id = '$delete_id'") or die('query failed');
      $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
      unlink('uploaded_img/' . $fetch_delete_image['file']);
      mysqli_query($conn, "DELETE FROM `blogs` WHERE id = '$delete_id'") or die('query failed');
      header('location:admin_blogs.php');
      exit(); // Added exit after header to stop further execution
   }
}

function updateBlog($conn) {
   global $message; // Make $message variable accessible inside the function

   if (isset($_POST['update_info'])) {
      $update_p_id = $_POST['update_p_id'];
      $update_title = mysqli_real_escape_string($conn, $_POST['update_blogtitle']);

      mysqli_query($conn, "UPDATE `blogs` SET blogtitle = '$update_title' WHERE id = '$update_p_id'") or die('query failed');

      $update_file = $_FILES['update_file']['name'];
      $update_file_size = $_FILES['update_file']['size'];
      $update_folder = 'uploaded_img/';
      $update_old_file = $_POST['update_old_file'];

      if (!empty($update_file)) {
         if ($update_file_size > 2000000000) {
            $message[] = 'File size is too large';
         } else {
            unlink('uploaded_img/' . $update_old_file);
            move_uploaded_file($_FILES['update_file']['tmp_name'], $update_folder . $update_file);
            mysqli_query($conn, "UPDATE `blogs` SET file = '$update_file' WHERE id = '$update_p_id'") or die('query failed');
         }
      }

      header('location:admin_blogs.php');
      exit(); // Added exit after header to stop further execution
   }
}

addBlog($conn);
deleteBlog($conn);
updateBlog($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta title="viewport" content="width=device-width, initial-scale=1.0">
   <title>blogs</title>
   <style>
      /* Your CSS styles remain unchanged */

      .message-container {
         margin-top: 10px;
      }

      .message {
         color: green; /* Choose a color that suits your design */
         font-weight: bold;
      }
   </style>
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

   <?php include 'admin_header.php'; ?>

   <!-- blogs CRUD section starts  -->

   <section class="add-blogs">

      <h1 class="title">BLOGS</h1>

      <!-- Display messages -->
      <?php if (!empty($message) && is_array($message)): ?>
   <div class="message-container">
      <?php foreach ($message as $msg): ?>
         <p class="message"><?php echo $msg; ?></p>
      <?php endforeach; ?>
   </div>
<?php endif; ?>

         </div>

      <form action="" method="post" enctype="multipart/form-data">
         <input type="text" name="title" class="box" placeholder="Enter blog title" required>
         <input type="text" name="addedby" class="box" placeholder="Added by" required>
         <input type="file" name="file" accept="image/jpg, video/mp4, image/jpeg, image/png" class="box">
         <input type="submit" value="add blog" name="add_info" class="btn">
      </form>

   </section>

   <!-- blogs CRUD section ends -->

   <!-- show blogs  -->

   <section class="show-blogs">

      <div class="box-container">

         <?php
         $select_blogs = mysqli_query($conn, "SELECT * FROM `blogs`") or die('query failed');
         if (mysqli_num_rows($select_blogs) > 0) {
            while ($fetch_blogs = mysqli_fetch_assoc($select_blogs)) {
         ?>
               <div class="box">
                  <?php if (strpos($fetch_blogs['file'], '.mp4') !== false) : ?>
                     <video width="100%" height="auto" controls>
                        <source src="uploaded_img/<?php echo $fetch_blogs['file']; ?>" type="video/mp4">
                     </video>
                  <?php else : ?>
                     <img src="uploaded_img/<?php echo $fetch_blogs['file']; ?>" alt="">
                  <?php endif; ?>
                  <div class="title"><?php echo $fetch_blogs['blogtitle']; ?></div>
                  <div class="added-by">Added by: <?php echo $fetch_blogs['addedby']; ?></div>
                  <a href="admin_blogs.php?update=<?php echo $fetch_blogs['id']; ?>" class="option-btn">update</a>
                  <a href="admin_blogs.php?delete=<?php echo $fetch_blogs['id']; ?>" class="delete-btn" onclick="return confirm('delete this blog?');">delete</a>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty">No blogs added yet!</p>';
         }
         ?>
      </div>

   </section>

   <section class="edit-blogs-form">

      <?php
      if (isset($_GET['update'])) {
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `blogs` WHERE id = '$update_id'") or die('query failed');
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
                  <input type="text" name="update_blogtitle" value="<?php echo $fetch_update['blogtitle']; ?>" class="box" required placeholder="Enter title">
                  <input type="file" name="update_file" class="box" accept="image/jpg, image/jpeg, image/png, video/mp4">
                  <input type="submit" value="update" name="update_info" class="btn">
                  <input type="button" value="cancel" onclick="location.href='admin_blogs.php';" class="option-btn">
               </form>
      <?php
            }
         }
      } else {
         echo '<script>document.querySelector(".edit-blogs-form").style.display = "none";</script>';
      }
      ?>

   </section>

   <!-- custom admin js file link  -->
   <script src="js/admin_script.js"></script>

</body>

</html>
