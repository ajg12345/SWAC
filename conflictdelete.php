<?php
require_once "includes/header.php";
require_once "includes/dbh.inc.php";

$prod_desc = "";
if (isset($_GET["conflict_pair_id"]) && !empty(trim($_GET["conflict_pair_id"]))){
	$sql_prod = "SELECT p.description as production, rc.conflict_pair_id from role_conflicts as rc join productions as p on rc.prod_id = p.prod_id where rc.conflict_pair_id = ".$_GET["conflict_pair_id"]." limit 1;";
	$prod_query = mysqli_query($conn, $sql_prod);
	$prod_row = mysqli_fetch_array($prod_query);
	$prod_desc = $prod_row['production'];
}

// Process delete operation after confirmation
if(isset($_POST["conflict_pair_id"]) && !empty($_POST["conflict_pair_id"])){    
    // Prepare a delete statement
    $sql = "DELETE FROM role_conflicts WHERE conflict_pair_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        $param_id = trim($_POST["conflict_pair_id"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            // Records deleted successfully. Redirect to landing page
            header("location: conflicts.php");
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
    if(empty(trim($_GET["conflict_pair_id"]))){
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}


include_once "includes/crudheader.php";
?>
 <div class="grid">
	<div class="title">
		<h1>Conflict Deletion</h1>
		<?php echo "<h2>For '".$prod_desc."'</h2>"; ?>
	</div>
	<div class="content">
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="alert alert-danger fade in">
			<input type="hidden" name="conflict_pair_id" value="<?php echo trim($_GET["conflict_pair_id"]); ?>"/>
			<p>Are you sure you want to delete this Role Conflict?</p><br>
			<p>
				<input type="submit" value="Yes" class="btn btn-danger">
				<a href="conflicts.php" class="btn btn-default">No</a>
			</p>
		</div>
	</form>
	</div>
</div>        
<?php
include_once "includes/footer.php";
?>