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
    
    if (isset($_POST['consequences_title'], $_POST['consequences_file'])) {
        
        $consequences_title = mysqli_real_escape_string($conn, $_POST['consequences_title']);
        $consequences_info = mysqli_real_escape_string($conn, $_POST['consequences_info']);
        $consequences_file = mysqli_real_escape_string($conn, $_POST['consequences_file']);
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>consequences</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

   <style>
     .consequences .box-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
    }

    .consequences .box {
        position: relative;
        width: 48%;
        margin-bottom: 20px;
        overflow: hidden;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .consequences .file {
        width: 100%;
        height: auto;
        max-height: 400px; /* Set a max-height for images */
        border-bottom: 1px solid #ddd;
        border-radius: 8px 8px 0 0;
    }

    .consequences .video-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        overflow: hidden;
        border-radius: 8px 8px 0 0;
    }

    .consequences .file video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 8px 8px 0 0;
    }

    .consequences .title {
        font-size: 18px;
        font-weight: bold;
        margin: 10px;
    }

    .consequences .info {
        margin: 10px;
    }

    /* Additional styles for smaller screens (max-width: 768px) */
    @media (max-width: 768px) {
        .consequences .box {
            width: 100%;
        }
    }
    
   </style>
</head>

<body>

   <?php include 'header.php'; ?>

   <!-- sub header -->

   <div class="heading">
      <h3>consequences</h3>
   </div>

   <section class="consequences">
      <div class="box-container">

         <?php
         $select_consequences = mysqli_query($conn, "SELECT * FROM `consequences`") or die('query failed');
         if (mysqli_num_rows($select_consequences) > 0) {
            while ($fetch_consequences = mysqli_fetch_assoc($select_consequences)) {
         ?>
               <form action="" method="post" class="box">
                  <?php
                  if (strpos($fetch_consequences['file'], '.mp4') !== false) {
                  ?>
                     <div class="video-container">
                        <video class="file" controls>
                           <source src="uploaded_img/<?php echo $fetch_consequences['file']; ?>" type="video/mp4">
                           Your browser does not support the video tag.
                        </video>
                     </div>
                  <?php
                  } else {
                  ?>
                     <img class="file" src="uploaded_img/<?php echo $fetch_consequences['file']; ?>" alt="">
                  <?php
                  }
                  ?>
                  <div class="title"><?php echo $fetch_consequences['title']; ?></div>
                  <div class="info"><?php echo $fetch_consequences['info']; ?></div>

                  <?php
                  // Check if the user is an admin
                  if (isset($_SESSION['admin_id'])) {
                     // Display additional information for admins
                     echo '<div>Added by Admin</div>';
                  }
                  ?>

                  <input type="hidden" name="consequences_title" value="<?php echo $fetch_consequences['title']; ?>">
                  <input type="hidden" name="consequences_info" value="<?php echo $fetch_consequences['info']; ?>">
                  <input type="hidden" name="consequences_file" value="<?php echo $fetch_consequences['file']; ?>">
               </form>
         <?php
            }
         } else {
            echo '<p class="empty">No consequences added yet!</p>';
         }
         ?>
      </div>
   </section>

   <?php include 'footer.php'; ?>

   <!-- Custom JS file link -->
   <script src="js/script.js"></script>

</body>

</html>
