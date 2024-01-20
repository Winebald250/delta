<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About Us</title>
   <style>
      .about .flex{
   max-width: 1200px;
   margin:0 auto;
   display: flex;
   align-items: center;
   flex-wrap: wrap;
}

.about .flex .image{
   flex:1 1 40rem;
}

.about .flex .image img{
   width: 100%;
}

.about .flex .content{
   flex:1 1 40rem;
   padding:2rem;
   background-color: var(--light-bg);
}

.about .flex .content h3{
   font-size: 3rem;
   color:var(--black);
   text-transform: uppercase;
}

.about .flex .content p{
   padding:1rem 0;
   line-height: 2;
   font-size: 1.7rem;
   color:var(--light-color);
}
</style>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>About Us</h3>
</div>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="images/about-img.jpg" alt="">
      </div>

      <div class="content">
         <h3>why choose us?</h3>
         <h1><ul>
        <li><b>We are committed to helping people affected by drugs and alcohol. 
        Our goal is to provide comprehensive information and resources to individuals struggling with addiction and their families.
        We believe that by providing our readers with the knowledge and resources that they need to make informed decisions about their health, they can make positive changes in their lives.<br/>
        <br/></li>
        <li>We strive to create a safe space where individuals can come to find the support they need and learn more about the dangers of drugs and alcohol abuse. 
        Our hope is that through our work, individuals can begin to take control of their lives and achieve sobriety.</b></li>
    </ul></h1>
         <a href="contact.php" class="btn">contact us</a>
      </div>

   </div>

</section>








<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>