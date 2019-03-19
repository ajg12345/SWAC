<?php
// Include config file
require_once "includes/header.php";
if (!isset($_SESSION['useruid']) || $_SESSION['can_create'] == 0)  {
	header("Location: index.php");
}
require_once "includes/dbh.inc.php";
// Define variables and initialize with empty values
$dancer_first = $dancer_last = $dancer_email = "";
$dancer_first_err = $dancer_last_err = $dancer_email_err = "";

// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
	
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate name
    $input_first = trim($_POST["dancer_first"]);
    if(empty($input_first)){
        $dancer_first_err = "Please enter a name.";
    } elseif(!filter_var($input_first, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $dancer_first_err = "Please enter a valid name.";
    } else{
        $dancer_first = $input_first;
    }
    
    // Validate last name
    $input_last = trim($_POST["dancer_last"]);
    if(empty($input_last)){
        $dancer_last_err = "Please enter a last name.";     
    } else{
        $dancer_last = $input_last;
    }
    
    // Validate email address
    $input_email = trim($_POST["dancer_email"]);
    if(empty($input_email)){
        $dancer_email_err = "Please enter the email address.";     
    } else{
        $dancer_email = $input_email;
    }
    
    // Check input errors before inserting in database
    if(empty($dancer_first) && empty($dancer_last) && empty($dancer_email)){
        // Prepare an update statement
        $sql = "UPDATE dancers SET dancer_first=?, dancer_last=?, dancer_email=? WHERE id=?";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssi", $param_first, $param_last, $param_email, $param_id);
            
            // Set parameters
            $param_first = $dancer_first;
            $param_last = $dancer_last;
            $param_email = $dancer_email;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($conn);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM dancers WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
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
                    // URL doesn't contain valid id. Redirect to error page
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
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
include_once "includes/crudheader.php";
?>
<div class="grid">
	<div class="Title">
		<h2>Update Dancer Record</h2>
	</div>
	<p>Please edit the input values and submit to update the record.</p>
	<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
		<div class="form-group <?php echo (!empty($dancer_first_err)) ? 'has-error' : ''; ?>">
			<label>First Name</label>
			<input type="text" name="dancer_first" class="form-control" value="<?php echo $dancer_first; ?>">
			<span class="help-block"><?php echo $dancer_first_err;?></span>
		</div>
		<div class="form-group <?php echo (!empty($dancer_last_err)) ? 'has-error' : ''; ?>">
			<label>Last Name</label>
			<textarea name="dancer_last" class="form-control"><?php echo $dancer_last; ?></textarea>
			<span class="help-block"><?php echo $dancer_last_err;?></span>
		</div>
		<div class="form-group <?php echo (!empty($dancer_email_err)) ? 'has-error' : ''; ?>">
			<label>Email Address</label>
			<input type="text" name="dancer_email" class="form-control" value="<?php echo $dancer_email; ?>">
			<span class="help-block"><?php echo $dancer_email_err;?></span>
		</div>
		<input type="hidden" name="id" value="<?php echo $id; ?>"/>
		<input type="submit" class="btn btn-primary" value="Submit">
		<a href="index.php" class="btn btn-default">Cancel</a>
	</form>
</div>