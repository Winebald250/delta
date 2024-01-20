<?php
require_once 'config.php';

// Set the maximum allowed size for file uploads
ini_set('upload_max_filesize', '1000M');

// Set the maximum size of POST data
ini_set('post_max_size', '1000M');

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit(); // Stop further execution
}

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    
    if (isset($_POST['blogs_blogtitle'], $_POST['blogs_file'])) {
        
        $blogs_blogtitle = mysqli_real_escape_string($conn, $_POST['blogs_blogtitle']);
        $blogs_file = mysqli_real_escape_string($conn, $_POST['blogs_file']);
       
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>blogs</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
    .blogs .box-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
}

.blogs .box {
    position: relative;
    width: 48%; /* Adjust the width as needed */
    margin-bottom: 20px;
    overflow: hidden;
    border: 1px solid #ddd; /* Add border as desired */
    border-radius: 8px;
}



.blogs .blogtitle {
    font-size: 18px;
    font-weight: bold;
    margin: 10px;
}
.name {
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
}

/* Additional styles to ensure responsiveness */
@media (max-width: 768px) {
    .blogs .box {
        width: 100%;
    }
}
   </style>
</head>
<body>
   
<?php require_once 'header.php'; ?>

<!-- sub header -->

<div class="heading">
   <h3>blogs</h3>
</div>

<section class="blogs">
   <div class="box-container">

      <?php  
         $select_blogs = mysqli_query($conn, "SELECT * FROM `blogs`") or die('query failed');
         if(mysqli_num_rows($select_blogs) > 0){
            while($fetch_blogs = mysqli_fetch_assoc($select_blogs)){
      ?>
     <form action="" method="post" class="box">
      <?php
         if (strpos($fetch_blogs['file'], '.mp4') !== false) {
            // Display video player for .mp4 files
      ?>
      <div class="video-container">
         <video controls>
            <source src="uploaded_img/<?php echo $fetch_blogs['file']; ?>" type="video/mp4">
            Your browser does not support the video tag.
         </video>
      </div>
      <?php
         } else {
            // Display image for other file types
      ?>
      <img class="file" src="uploaded_img/<?php echo $fetch_blogs['file']; ?>" alt="">
      <?php
         }
      ?>
      <div class="name">
          <?php 
          echo htmlspecialchars($fetch_blogs['blogtitle']) . '
          <br>
          <br>
           added by ' . htmlspecialchars($fetch_blogs['addedby']) . ' on ' . date('Y-m-d H:i:s', strtotime($fetch_blogs['time']));
          ?>
      </div>
      <input type="hidden" name="blogs_blogtitle" value="<?php echo htmlspecialchars($fetch_blogs['blogtitle']); ?>">
      <input type="hidden" name="blogs_file" value="<?php echo htmlspecialchars($fetch_blogs['file']); ?>">
     </form>
      <?php
         }
      }else{
         echo '<p class="empty">no blogs added yet!</p>';
      }
      ?>
   </div>
</section>

<?php require_once 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
