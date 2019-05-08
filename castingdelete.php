<?php
require_once "includes/header.php";
require_once "includes/dbh.inc.php";

// Process delete operation after confirmation
if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_POST["casting_id"]) && !empty($_POST["casting_id"]) && isset($_POST["re_id"]) && !empty($_POST["re_id"])){    
		$re_id = $_POST["re_id"];
		// Prepare a delete statement
		$sql = "DELETE FROM castings WHERE casting_id = ?";
		
		if($stmt = mysqli_prepare($conn, $sql)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "i", $param_id);
			
			// Set parameters
			$param_id = trim($_POST["casting_id"]);
			
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records deleted successfully. Redirect to landing page
				
				$header_dest = "location: castings.php?re_id=".$re_id;
				header($header_dest);
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
		if(empty(trim($_GET["re_id"]))){
			// URL doesn't contain id parameter. Redirect to error page
			header("location: error.php#no_re_id_to_get_in_casting_delete");
			exit();
		}else{
			header("location: error.php#unknown_error_in_casting_delete");
			exit();
		}
	}
}
include_once "includes/crudheader.php";
?>
 <div class="grid">
	<div class="title">
		<h1>Delete Casting</h1>
	</div>
	<div class="content">
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="alert alert-danger fade in">
			<input type="hidden" name="casting_id" value="<?php echo trim($_GET["casting_id"]); ?>"/>
			<input type="hidden" name="re_id" value="<?php echo trim($_GET["re_id"]); ?>"/>
			<p>Are you sure you want to delete this casting?</p><br>
			<p>
				<input type="submit" value="Yes" class="btn btn-danger">
				<a href="castings.php<?php echo "?re_id=".trim($_GET["re_id"]); ?>" class="btn btn-default">No</a>
			</p>
		</div>
	</form>
	</div>
</div>        
<?php
include_once "includes/footer.php";
?>