<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>search page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>search page</h3>
</div>

<section class="search-form">
   <form action="" method="post">
      <input type="text" name="search" placeholder="search drug info" class="box">
      <input type="submit" name="submit" value="search" class="btn">
   </form>
</section>

<section class="druginfo" style="padding-top: 0;">

   <div class="box-container">
   <?php
      if(isset($_POST['submit'])){
         $search_item = $_POST['search'];
         $select_druginfo = mysqli_query($conn, "SELECT * FROM `druginfo` WHERE title LIKE '%{$search_item}%'") or die('query failed');
         if(mysqli_num_rows($select_druginfo) > 0){
         while($fetch_druginfo = mysqli_fetch_assoc($select_druginfo)){
   ?>
   <form action="" method="post" class="box">
      <img src="uploaded_img/<?php echo $fetch_druginfo['file']; ?>" alt="" class="file">
      <div class="name"><?php echo $fetch_druginfo['title']; ?></div>
      <input type="hidden" name="druginfo_title" value="<?php echo $fetch_druginfo['title']; ?>">
      <input type="hidden" name="druginfo_file" value="<?php echo $fetch_druginfo['file']; ?>">
   </form>
   <?php
            }
         }else{
            echo '<p class="empty">no result found!</p>';
         }
      }else{
         echo '<p class="empty">search something!</p>';
      }
   ?>
   </div>
  

</section>









<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>