<?php
require_once "includes/header.php";
require_once "includes/dbh.inc.php";

$conflict_role_id = '';
$casting_role_id = '';

// Process delete operation after confirmation
if(isset($_POST["role_id"]) && !empty($_POST["role_id"])){    
    // Prepare a delete statement
	$param_id = trim($_POST["role_id"]);
	$conflict_check = "select role_id1 from role_conflicts where role_id1=".$param_id." limit 1;";
	$casting_check = "select role_id from castings where role_id=".$param_id." limit 1;";
	
	$conflict_result = mysqli_query($conn,$conflict_check);
	$conflict_row = mysqli_fetch_array($conflict_result);
	$conflict_role_id = $conflict_row['role_id1'];
	mysqli_free_result($conflict_result);
	
	$casting_result = mysqli_query($conn,$casting_check);
	$casting_row = mysqli_fetch_array($casting_result);
	$casting_role_id = $casting_row['role_id'];
	mysqli_free_result($casting_result);
	
    $sql = "DELETE FROM roles WHERE role_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        
		//check that this role is not referenced in role_conflicts or castings
        if (empty($casting_role_id) && empty($conflict_role_id)){
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records deleted successfully. Redirect to landing page
				header("location: roles.php");
				exit();
			} else{
				echo "Oops! Something went wrong. Please try again later.";
			}
		} else {
			$error_code = 1;
			$header_target = "location: error.php?error_code=".$error_code;
			header($header_target);
			exit();
		}
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
    
} else{
    // Check existence of id parameter
    if(empty(trim($_GET["role_id"]))){
        // URL doesn't contain id parameter. Redirect to error page
		$error_code = 2;
		$header_target = "location: error.php?error_code=".$error_code;
        header($header_target);
        exit();
    }
}
include_once "includes/crudheader.php";
?>
 <div class="grid">
	<div class="title">
		<h1>Delete Role</h1>
	</div>
	<div class="content">
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="alert alert-danger fade in">
			<input type="hidden" name="role_id" value="<?php echo trim($_GET["role_id"]); ?>"/>
			<p>Are you sure you want to delete this role?</p><br>
			<p>
				<input type="submit" value="Yes" class="btn btn-danger">
				<a href="roles.php" class="btn btn-default">No</a>
			</p>
		</div>
	</form>
	</div>
</div>        
<?php
include_once "includes/footer.php";
?>