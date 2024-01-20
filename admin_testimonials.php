<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
   exit();
}

if (isset($_POST['add_info'])) {

   $title = mysqli_real_escape_string($conn, $_POST['title']);
   $info = mysqli_real_escape_string($conn, $_POST['message']);
   $file = $_FILES['file']['name'];
   $file_type = $_FILES['file']['type'];
   $file_size = $_FILES['file']['size'];
   $file_tmp = $_FILES['file']['tmp_name'];
   $allowed_extensions = array("jpg", "jpeg", "png", "mp4");

   $image_folder = 'uploaded_img/';

   $select_info_title = mysqli_query($conn, "SELECT name FROM `testimonials` WHERE name = '$title'") or die('query failed');

   if (mysqli_num_rows($select_info_title) > 0) {
      $message[] = 'testimony already added';
   } else {
      if (in_array(pathinfo($file, PATHINFO_EXTENSION), $allowed_extensions)) {
         $add_info_query = mysqli_query($conn, "INSERT INTO `testimonials` (name, testimony, file) VALUES ('$title', '$info', '$file')") or die('query failed');

         if ($add_info_query) {
            if ($file_size > 2000000000) {
               $message[] = 'File size is too large';
            } else {
               move_uploaded_file($file_tmp, $image_folder . $file);
               $message[] = 'testimony added successfully!';
            }
         } else {
            $message[] = 'testimony could not be added!';
         }
      } else {
         $message[] = 'Invalid file type. Allowed types: jpg, jpeg, png, mp4';
      }
   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_image_query = mysqli_query($conn, "SELECT file FROM `testimonials` WHERE id = '$delete_id'") or die('query failed');
   $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
   unlink('uploaded_img/' . $fetch_delete_image['file']);
   mysqli_query($conn, "DELETE FROM `testimonials` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_testimonials.php');
   exit();
}

if (isset($_POST['update_info'])) {

   $update_p_id = $_POST['update_p_id'];
   $update_title = mysqli_real_escape_string($conn, $_POST['update_title']);
   $update_info = mysqli_real_escape_string($conn, $_POST['update_message']);

   mysqli_query($conn, "UPDATE `testimonials` SET name = '$update_title'WHERE id = '$update_p_id'") or die('query failed');

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
         mysqli_query($conn, "UPDATE `testimonials` SET file = '$update_file' WHERE id = '$update_p_id'") or die('query failed');
      }
   }

   header('location:admin_testimonials.php?updated=true');
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>testimonials</title>
<style>
   .add-testimonials form{
   background-color: var(--white);
   border-radius: .5rem;
   padding:2rem;
   text-align: center;
   box-shadow: var(--box-shadow);
   border:var(--border);
   max-width: 50rem;
   margin:0 auto;
}

.add-testimonials form h3{
   font-size: 2.5rem;
   text-transform: uppercase;
   color:var(--black);
   margin-bottom: 1.5rem;
}

.add-testimonials form .box{
   width: 100%;
   background-color: var(--light-bg);
   border-radius: .5rem;
   margin:1rem 0;
   padding:1.2rem 1.4rem;
   color:var(--black);
   font-size: 1.8rem;
   border:var(--border);
}


.show-testimonials .box-container{
   display: grid;
   grid-template-columns: repeat(auto-fit, 30rem);
   justify-content: center;
   gap:1.5rem;
   max-width: 1200px;
   margin:0 auto;
   align-items: flex-start;
}

.show-testimonials{
   padding-top: 0;
}

.show-testimonials .box-container .box{
   text-align: center;
   padding:2rem;
   border-radius: .5rem;
   border:var(--border);
   box-shadow: var(--box-shadow);
   background-color: var(--white);
}

.show-testimonials .box-container .box img{
   width: 100%;
}

.show-testimonials .box-container .box .name{
   padding:1rem 0;
   font-size: 2rem;
   color:var(--black);
}

.show-testimonials .box-container .box .price{
   padding:1rem 0;
   font-size: 2.5rem;
   color:var(--red);
}

.edit-testimonials-form{
   min-height: 100vh;
   background-color: rgba(0,0,0,.7);
   display: flex;
   align-items: center;
   justify-content: center;
   padding:2rem;
   overflow-y: scroll;
   position: fixed;
   top:0; left:0; 
   z-index: 1200;
   width: 100%;
}

.edit-testimonials-form form{
   width: 50rem;
   padding:2rem;
   text-align: center;
   border-radius: .5rem;
   background-color: var(--white);
}

.edit-testimonials-form form img{
   height: 25rem;
   margin-bottom: 1rem;
}

.edit-testimonials-form form .box{
   margin:1rem 0;
   padding:1.2rem 1.4rem;
   border:var(--border);
   border-radius: .5rem;
   background-color: var(--light-bg);
   font-size: 1.8rem;
   color:var(--black);
   width: 100%;
}
</style>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom admin CSS file link -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

   <?php include 'admin_header.php'; ?>

   <!-- Testimonials CRUD section starts -->

   <section class="add-testimonials">

      <h1 class="title">Testimonials</h1>

      <form action="" method="post" enctype="multipart/form-data">
         <input type="text" name="title" class="box" placeholder="Enter name" required>
         <input type="file" name="file" accept="image/jpg, video/mp4, image/jpeg, image/png" class="box">
         <textarea name="message" class="box" placeholder="Enter testimony" id="" cols="30" rows="10" required></textarea>
         <input type="submit" value="Add Testimony" name="add_info" class="btn">
      </form>

   </section>

   <!-- Testimonials CRUD section ends -->

   <!-- Show Testimonials -->

   <section class="show-testimonials">
      <div class="box-container">
         <?php
            $select_testimonials = mysqli_query($conn, "SELECT * FROM `testimonials`") or die('query failed');
            if (mysqli_num_rows($select_testimonials) > 0) {
               while ($fetch_testimonials = mysqli_fetch_assoc($select_testimonials)) {
         ?>
                  <div class="box">
                     <?php if (strpos($fetch_testimonials['file'], '.mp4') !== false) : ?>
                        <video width="100%" height="auto" controls>
                           <source src="uploaded_img/<?php echo $fetch_testimonials['file']; ?>" type="video/mp4">
                           Your browser does not support the video tag.
                        </video>
                     <?php else : ?>
                        <img src="uploaded_img/<?php echo $fetch_testimonials['file']; ?>" alt="">
                     <?php endif; ?>
                     <div class="title"><?php echo $fetch_testimonials['name']; ?></div>
                     <div class="info"><?php echo $fetch_testimonials['testimony']; ?></div>
                     <a href="admin_testimonials.php?update=<?php echo $fetch_testimonials['id']; ?>" class="option-btn">update</a>
                     <a href="admin_testimonials.php?delete=<?php echo $fetch_testimonials['id']; ?>" class="delete-btn" onclick="return confirm('Delete this testimonial?');">delete</a>
                  </div>
<?php
               }
            } else {
               echo '<p class="empty">No testimonials added yet!</p>';
            }
         ?>
      </div>
   </section>

   <section class="edit-testimonials-form">
      <?php
         if (isset($_GET['update'])) {
            $update_id = $_GET['update'];
            $update_query = mysqli_query($conn, "SELECT * FROM `testimonials` WHERE id = '$update_id'") or die('query failed');
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
                     <input type="text" name="update_title" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Enter name">
                     <input type="file" name="update_file" class="box" accept="image/jpg, image/jpeg, image/png, video/mp4">
                     <input type="submit" value="Update" name="update_info" class="btn">
                     <input type="button" value="Cancel" onclick="location.href='admin_testimonials.php';" class="option-btn">
                  </form>
      <?php
               }
            }
         } else {
            echo '<script>document.querySelector(".edit-testimonials-form").style.display = "none";</script>';
         }
      ?>
   </section>

   <!-- Custom admin JS file link -->
   <script src="js/admin_script.js"></script>
</body>

</html>
