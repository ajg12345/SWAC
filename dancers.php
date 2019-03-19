<?php
include_once "includes/header.php";

?>

<div class= "grid">
	<div class="title"><h1>Welcome the Joffrey Ballet's Web Application for Casting! </h1></div>
	<div class="header">
		<?php
			if (isset($_SESSION['useruid']))  {
				$uname = str_replace('@joffrey.org', '', $_SESSION['user_email']);
				echo '<h2>WELCOME, '.$uname.'!</h2>';
			} else {
				echo '<h2>Please log-in at the top right for content.</h2>';
			}
		?>
	</div>	
	<div class="content">Content goes here</div>
</div>	

<?php
include_once "includes/footer.php";
?>

