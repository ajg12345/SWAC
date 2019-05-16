<?php
require_once "includes/header.php";
require_once "includes/dbh.inc.php";

// Process delete operation after confirmation
if(isset($_POST["location_id"]) && !empty($_POST["location_id"])){    
    // Prepare a delete statement
    $sql = "update locations set is_active = 0 WHERE location_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        $param_id = trim($_POST["location_id"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            // Records deleted successfully. Redirect to landing page
            header("location: locations.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
    
} else{
    // Check existence of id parameter
    if(empty(trim($_GET["location_id"]))){
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
include_once "includes/crudheader.php";
?>
 <div class="grid">
	<div class="title">
		<h1>Delete Location</h1>
	</div>
	<div class="content">
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="alert alert-danger fade in">
			<input type="hidden" name="location_id" value="<?php echo trim($_GET["location_id"]); ?>"/>
			<p>Are you sure you want to inactivate this location? <br>
			It will no longer be an option to select from when making performances or rehearsals.<br>
			It will still be seen in historical performance and rehearsal records.</p>
			<p>
				<input type="submit" value="Yes" class="btn btn-danger">
				<a href="locations.php" class="btn btn-default">No</a>
			</p>
		</div>
	</form>
	</div>
</div>        
<?php
include_once "includes/footer.php";
?>