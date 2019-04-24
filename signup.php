<?php
	include_once "includes/header.php";
?>

<div class="content">
	<h2>Signup</h2>
	<form class="signup-form" action="includes/signup.inc.php" method="POST">
		<?php
			if (isset($_GET['first'])) {
				$first = $_GET['first'];
				echo '<input type="text" name="first" value="'.$first.'"><br>';
			} else {
				echo '<input type="text" name="first" placeholder="FirstName"><br>';
			}
			if (isset($_GET['last'])) {
				$last = $_GET['last'];
				echo '<input type="text" name="last" value="'.$last.'"><br>';
			} else {
				echo '<input type="text" name="last" placeholder="LastName"><br>';
			}
		?>
		<input type="text" name="email" placeholder="Email">
		<br>
		<?php
			if (isset($_GET['uid'])) {
			$uid = $_GET['uid'];
				echo '<input type="text" name="uid" value="'.$uid.'"><br>';
			} else {
				echo '<input type="text" name="uid" placeholder="username"><br>';
			}
		?>
		<input type="password" name="pwd" placeholder="password">
		<br>
		<input type="password" name="pwd2" placeholder="password repeated">
		<br>
		<button type="submit" name="submit"> Sign up </button>
		<?php
			if (!isset($_GET['signup'])) {
				exit();
			} else {
				$signupCheck = $_GET['signup'];
				if ($signupCheck=='empty') {
					echo "<p class='error'>You did not fill in all fields!</p>";
					exit();
				} elseif ($signupCheck=='name_char_error') {
					echo "<p class='error'>You used invalid characters!</p>";
					exit();
				} elseif ($signupCheck=='invalid_email') {
					echo "<p class='error'>You used an invalid e-mail!</p>";
					exit();
				} elseif ($signupCheck=='passwordcheck') {
					echo "<p class='error'>Your Passwords did not agree!</p>";
					exit();	
				} elseif ($signupCheck=='success') {
					echo "<p class='success'>You have been signed up!</p>";
					exit();
				}
			}
		?>
	</form>
</div>



<?php
	include_once "includes/footer.php";
?>


	