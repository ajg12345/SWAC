<?php
include_once "includes/header.php";
if (!isset($_SESSION['useruid']) || $_SESSION['can_create'] == 0)  {
	header("Location: index.php");
}
// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Include config file
    require_once "includes/dbh.inc.php";
    
    // Prepare a select statement
    $sql = "SELECT * FROM dancers WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $dancer_first = $row["dancer_first"];
                $dancer_last = $row["dancer_last"];
                $dancer_email = $row["dancer_email"];
            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                header("location: error.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($conn);
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
include_once "includes/crudheader.php";
?>

<div class="grid">
	<div class="Title">
		<h1>View Record</h1>
	</div>
	<div class="content"> 
		<div class="form-group">
			<label>First Name</label>
			<p class="form-control-static"><?php echo $dancer_first; ?></p>
		</div>
		<div class="form-group">
			<label>Last Name</label>
			<p class="form-control-static"><?php echo $dancer_last; ?></p>
		</div>
		<div class="form-group">
			<label>Email Address</label>
			<p class="form-control-static"><?php echo $dancer_email; ?></p>
		</div>
		<p><a href="index.php" class="btn btn-primary">Back</a></p>
	</div>
</div>