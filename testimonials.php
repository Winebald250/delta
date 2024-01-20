<?php
require_once 'config.php';

ini_set('upload_max_filesize', '1000M');
ini_set('post_max_size', '1000M');

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit(); // Stop further execution
}

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    
    if (isset($_POST['testimonials_title'], $_POST['testimonials_file'])) {
        
        $testimonials_title = mysqli_real_escape_string($conn, $_POST['testimonials_title']);
        $testimonials_testimony = mysqli_real_escape_string($conn, $_POST['testimonials_testimony']);
        $testimonials_file = mysqli_real_escape_string($conn, $_POST['testimonials_file']);
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Testimonials</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      /* Your existing styles... */

      /* Styles for the testimonial boxes */
      .testimonials .box-container {
         display: flex;
         flex-wrap: wrap;
         justify-content: space-around;
      }

      .testimonials .box {
         position: relative;
         width: 48%; /* Adjust the width as needed */
         margin-bottom: 20px;
         overflow: hidden;
         border: 1px solid #ddd; /* Add border as desired */
         border-radius: 8px;
      }

      .testimonials .file {
         width: 100%;
         height: auto;
         border-bottom: 1px solid #ddd; /* Add border as desired */
      }

      .testimonials .name {
         font-size: 18px;
         font-weight: bold;
         margin: 10px;
      }

      .testimonials .testimony {
         margin: 10px;
      }

      /* Additional styles to ensure responsiveness */
      @media (max-width: 768px) {
         .testimonials .box {
            width: 100%;
         }
      }

   </style>
</head>

<body>

   <?php include 'header.php'; ?>

   <!-- sub header -->

   <div class="heading">
      <h3>Testimonials</h3>
   </div>

   <section class="testimonials">
      <div class="box-container">

         <?php
         $select_testimonials = mysqli_query($conn, "SELECT * FROM `testimonials`") or die('query failed');
         if (mysqli_num_rows($select_testimonials) > 0) {
            while ($fetch_testimonials = mysqli_fetch_assoc($select_testimonials)) {
         ?>
               <form action="" method="post" class="box">
                  <?php
                  if (strpos($fetch_testimonials['file'], '.mp4') !== false) {
                  ?>
                     <video class="file" controls>
                        <source src="uploaded_img/<?php echo $fetch_testimonials['file']; ?>" type="video/mp4">
                        Your browser does not support the video tag.
                     </video>
                  <?php
                  } else {
                  ?>
                     <img class="file" src="uploaded_img/<?php echo $fetch_testimonials['file']; ?>" alt="">
                  <?php
                  }
                  ?>
                  <div class="name"><?php echo $fetch_testimonials['name']; ?></div>
                  <div class="testimony"><?php echo $fetch_testimonials['testimony']; ?></div>

                  <?php
                  // Check if the user is an admin
                  if (isset($_SESSION['admin_id'])) {
                     // Display additional information for admins
                     echo '<div>Added by Admin</div>';
                  }
                  ?>

                  <input type="hidden" name="testimonials_name" value="<?php echo $fetch_testimonials['name']; ?>">
                  <input type="hidden" name="testimonials_testimony" value="<?php echo $fetch_testimonials['testimony']; ?>">
                  <input type="hidden" name="testimonials_file" value="<?php echo $fetch_testimonials['file']; ?>">
               </form>
         <?php
            }
         } else {
            echo '<p class="empty">No testimonials added yet!</p>';
         }
         ?>
      </div>
   </section>

   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>
