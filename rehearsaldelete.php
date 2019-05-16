<?php
require_once "includes/header.php";
require_once "includes/dbh.inc.php";

// Process delete operation after confirmation
if(isset($_POST["re_id"]) && !empty($_POST["re_id"])){    
    // Prepare a delete statement
    $sql = "DELETE FROM rehearsals WHERE re_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        $param_id = trim($_POST["re_id"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            // Records deleted successfully. Redirect to landing page
            header("location: rehearsals.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);

} else{
    // Check existence of id parameter
    if(empty(trim($_GET["re_id"]))){
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
include_once "includes/crudheader.php";
?>
 <div class="grid">
	<div class="title">
		<h1>Delete Rehearsal</h1>
	</div>
	<div class="content">
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="alert alert-danger fade in">
			<input type="hidden" name="re_id" value="<?php echo trim($_GET["re_id"]); ?>"/>
			<p>Are you sure you want to delete this rehearsal?</p><br>
			<p>
				<input type="submit" value="Yes" class="btn btn-danger">
				<a href="rehearsals.php" class="btn btn-default">No</a>
			</p>
		</div>
	</form>
	</div>
</div>        
<?php
include_once "includes/footer.php";
?>