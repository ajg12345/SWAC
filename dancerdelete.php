<?php
require_once "includes/header.php";
require_once "includes/dbh.inc.php";

// Process delete operation after confirmation
if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_POST["dancer_id"]) && !empty($_POST["dancer_id"])){
		// Prepare a delete statement
		$sql = "Update dancers set is_active = 0 WHERE dancer_id = ?";
		
		if($stmt = mysqli_prepare($conn, $sql)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "i", $param_id);
			
			// Set parameters
			$param_id = trim($_POST["dancer_id"]);
			
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records deleted successfully. Redirect to landing page
				header("location: dancers.php");
				exit();
			} else{
				echo "Oops! Something went wrong. Please try again later.";
			}
		}
		 
		// Close statement
		mysqli_stmt_close($stmt);
		
		// Close connection
		mysqli_close($conn);
	} else{
		// Check existence of id parameter
		if(empty(trim($_GET["dancer_id"]))){
			// URL doesn't contain id parameter. Redirect to error page
			header("location: error.php");
			exit();
		}
	}
}
include_once "includes/crudheader.php";
?>
 <div class="grid">
	<div class="title">
		<h1>Inactivate Dancer</h1>
	</div>
	<div class="content">
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="alert alert-danger fade in">
			<input type="hidden" name="dancer_id" value="<?php echo trim($_GET["dancer_id"]); ?>"/>
			<p>This will mark the dancer as inactive and unable to be cast in future shows, but still present in old records.</p><br>
			<p>
				<input type="submit" value="Yes" class="btn btn-danger">
				<a href="dancers.php" class="btn btn-default">No</a>
			</p>
		</div>
	</form>
	</div>
</div>        
<?php
include_once "includes/footer.php";
?>