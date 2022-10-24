<?php
get_header();
?>
<h1>Your BaseCamp API Token is: <?php echo $_GET['r']; ?></h1>    
<?php 
$_SESSION['has_token'] = true;     

get_footer();
