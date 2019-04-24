<?php
include_once "includes/header.php";
// Check existence of dancer_id parameter before processing further
if(isset($_GET["dancer_id"]) && !empty(trim($_GET["dancer_id"]))){
    // Include config file
    require_once "includes/dbh.inc.php";
    
    // Prepare a select statement
    $sql = "SELECT * FROM dancers WHERE dancer_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["dancer_id"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $dancer_fullname = $row["dancer_fullname"];
                $dancer_phone = $row["dancer_phone"];
                $dancer_email = $row["dancer_email"];
				$dancer_email_or_phone = $row["dancer_email_or_phone"];
            } else{
                // URL doesn't contain valid dancer_id parameter. Redirect to error page
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
    // URL doesn't contain dancer_id parameter. Redirect to error page
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
			<label>Full Name</label>
			<p class="form-control-static"><?php echo $dancer_fullname; ?></p>
		</div>
		<div class="form-group">
			<label>Contact Phone Number</label>
			<p class="form-control-static"><?php echo $dancer_phone; ?></p>
		</div>
		<div class="form-group">
			<label>Contact Email Address</label>
			<p class="form-control-static"><?php echo $dancer_email; ?></p>
		</div>
		<div class="form-group">
			<label>Preferred Contact Method</label>
			<p class="form-control-static"><?php echo $dancer_email_or_phone; ?></p>
		</div>
		<p><a href="dancers.php" class="btn btn-primary">Back</a></p>
	</div>
</div>