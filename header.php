<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   

   <div class="header-2">
      <div class="flex">
      <div class="icons"> <div id="menu-btn" class="fas fa-bars"></div>  </div>
      
         <a href="home.php" class="logo">ANTI-DRUGS RWANDA</a>

         <nav class="navbar">
            <a href="home.php">Home</a>
            <a href="Blog.php">Blogs</a>
            <a href="Drug Info.php">Drug Info</a>
            <a href="testimonials.php">Testimonials</a>
            <a href="contact.php">Contact Us</a>
            <a href="Consequences.php">Consequences</a>
            
         </nav>
         <div class="icons">
         <div class="icons">
            
            <a href="search_page.php" class="fas fa-search"></a>
            <div id="user-btn" class="fas fa-user"></div>
         </div>
         <div class="user-box">
            <p>username : <span><?php echo $_SESSION['user_name']; ?></span></p>
            <p>email : <span><?php echo $_SESSION['user_email']; ?></span></p>
            <a href="logout.php" class="delete-btn">logout</a>
         </div>
      </div>
   </div>

</header>