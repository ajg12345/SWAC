<?php
	if (isset($_POST['submit'])) {	//this condition makes sure that people can't navigate to this page.
		include_once 'dbh.inc.php';
		
		$first = mysqli_real_escape_string($conn, $_POST['first']);
		$last = mysqli_real_escape_string($conn, $_POST['last']);
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$uid = mysqli_real_escape_string($conn, $_POST['uid']);
		$pwd = mysqli_real_escape_string($conn, $_POST['pwd']);		
		$pwd2 = mysqli_real_escape_string($conn, $_POST['pwd2']);		
	
		if (empty($first) || empty($last) || empty($email)|| empty($uid)|| empty($pwd) || empty($pwd2)) {
			header("Location: ../signup.php?signup=empty&first=".$first."&last=".$last."&uid=".$uid);		//by including a signup=empty we are creating a get method for signup in the index page
			exit();
		} else {
			if (!preg_match("/^[a-zA-Z]*$/", $first) || !preg_match("/^[a-zA-Z]*$/", $last)) {
				header("Location: ../signup.php?signup=name_char_error&uid=".$uid);
				exit();
			} else {
				if (!filter_var($email, 
				FILTER_VALIDATE_EMAIL)) {	//there are a lot of error handlers to consider here, and these are just a few.
					header("Location: ../signup.php?signup=invalid_email&first=".$first."&last=".$last."&uid=".$uid);
					exit();
				} else {
					if ($pwd2 !== $pwd) {	//there are a lot of error handlers to consider here, and these are just a few.
						header("Location: ../signup.php?signup=passwordcheck&first=".$first."&last=".$last."&uid=".$uid);
						exit();
					} else {		//all error tests are passed! but is it already present?
						$sql_user_present = "SELECT * FROM users WHERE user_uid=?";
						$stmt_user_present = mysqli_stmt_init($conn);
						if (!mysqli_stmt_prepare($stmt_user_present, $sql_user_present)){
							header("Location: ../signup.php?signup=SQLERRORUID&first=".$first."&last=".$last."&uid=".$uid);
							exit();
						} else {
							mysqli_stmt_bind_param($stmt_user_present, "s", $uid);
							mysqli_stmt_execute($stmt_user_present);
							mysqli_stmt_store_result($stmt_user_present);
							$resultCheck_user_present = mysqli_num_rows($stmt_user_present);
							if ($resultCheck_user_present > 0){
								header("Location: ../signup.php?signup=userTaken&first=".$first."&last=".$last);
								exit();
							} else {
								$sql_insert = "insert into users (user_first, user_last, user_email, user_uid, user_pwd)
												values(?, ?, ?, ?, ?);";
								$stmt_insert = mysqli_stmt_init($conn);
								if (!mysqli_stmt_prepare($stmt_insert, $sql_insert)){
									header("Location: ../signup.php?signup=SQLERRORINSERT");
									exit();
								} else {
									$hashedPwdInDb = password_hash($pwd,  PASSWORD_DEFAULT );	//uses bcrypt always up to date dont use sha256 or md5
									mysqli_stmt_bind_param($stmt_insert, "sssss", $first, $last, $email, $uid, $hashedPwdInDb);
									mysqli_stmt_execute($stmt_insert);
									header("Location: ../signup.php?signup=success");	//redirect to a login page
									exit();
									//echo password_verify($INPUT, $hashedPwdInDb);
									
								}
							}
						
						
						}
					}
				}
			}	
		}
	} else {	//this is to make sure this page is not accessible by URL traversal.
		header("Location: ../signup.php");
		exit();
	}
?>