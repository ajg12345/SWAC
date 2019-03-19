<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name=viewport content="width=device-width, initial-scale=1">
<title>Joffrey Ballet - SWAC</title>
<script src="js/main.js"></script>
<link rel="stylesheet" href="resetstyle.css">
<link rel="stylesheet" href="style.css">

</head>
<body>
	<nav class="nav-main">
		<div class="btn-toggle-nav" onclick="toggleNav()"></div>
		<ul>
			<?php
				if (isset($_SESSION['useruid']))  {
					echo '	<li><a href="#">calendar</a></li>';
				}
			?>
		</ul>
		<div class="nav-login">
			<a href="signup.php">Sign Up</a>	
			<?php
				if (isset($_SESSION['useruid']))  {
					$uid = $_SESSION['useruid'];
					echo '	<form action="includes/logout.inc.php" method="POST">
							<button type="submit" name="logout-submit">LOGOUT</BUTTON>
							</form>';
				} else {
					echo '<form action="includes/login.inc.php" method="POST">
							<input type="text" name="uid" placeholder="username/e-mail">
							<input type="password" name="pwd" placeholder="password">
							<button type="submit" name="login-submit">LOGIN</BUTTON>
						  </FORM>';
				}
			?>
		</div>
		</ul>
	</nav>

	<aside class="nav-sidebar"> 
		<ul>
			<li><span>admin create menu:</span></li>
			<?php 
			if (isset($_SESSION['useruid']) && $_SESSION['can_create'] === 1)  {
			echo '<li><a href="#">performances</a></li>
			<li><a href="#">rehearsals</a></li>
			<li><a href="#">roles</a></li>
			<li><a href="#">role conflicts</a></li>
			<li><a href="dancers.php">dancers</a></li>
			<li><a href="#">notification templates</a></li>';
			}
			?>
		</ul>
	</aside>
	
	