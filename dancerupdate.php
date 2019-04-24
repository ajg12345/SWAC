<?php
// Include config file
require_once "includes/header.php";

require_once "includes/dbh.inc.php";
// Define variables and initialize with empty values
$dancer_email_or_phone = "";
$dancer_fullname = $dancer_phone = $dancer_email =  "";
$dancer_fullname_err = $dancer_phone_err = $dancer_email_err = $dancer_email_or_phone_err = "";

// Processing form data when form is submitted
if(isset($_POST["dancer_id"]) && !empty($_POST["dancer_id"])){
	
    // Get hidden input value
    $dancer_id = $_POST["dancer_id"];
    
    // Validate fullname
    $input_fullname = trim($_POST["dancer_fullname"]);
    if(empty($input_fullname)){
        $dancer_fullname_err = "Please enter a full name.";
    } elseif(!filter_var($input_fullname, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $dancer_fullname_err = "Please enter a valid full name.";
    } else{
        $dancer_fullname = $input_fullname;
    }
    
    // Validate phone
    $input_phone = trim($_POST["dancer_phone"]);
    if(empty($input_phone)){
        $dancer_phone_err = "Please enter a last name.";     
    } else{
        $dancer_phone = $input_phone;
    }
    
    // Validate email address
    $input_email = trim($_POST["dancer_email"]);
    if(empty($input_email)){
        $dancer_email_err = "Please enter the email address.";     
    } else{
        $dancer_email = $input_email;
    }
	
	$input_dancer_email_or_phone = trim($_POST["dancer_email_or_phone"]);
    if($input_dancer_email_or_phone !== "email" OR $input_dancer_email_or_phone !== "phone"){
        $dancer_email_or_phone_err = 'Please enter the value "phone" or "email".';
    }else{
        $dancer_email_or_phone = $input_dancer_email_or_phone;
	}
    
    // Check input errors before inserting in database
    if(empty($dancer_full) && empty($dancer_phone) && empty($dancer_email) && empty($dancer_email_or_phone)){
        // Prepare an update statement
        $sql = "UPDATE dancers SET dancer_fullname=?, dancer_phone=?, dancer_email=?, dancer_email_or_phone=? WHERE dancer_id=?";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssi", $param_fullname, $param_phone, $param_email, $param_email_or_phone, $param_id);
            
            // Set parameters
            $param_fullname = $dancer_fullname;
            $param_phone = $dancer_phone;
            $param_email = $dancer_email;
			$param_email_or_phone = $dancer_email_or_phone;
            $param_id = $dancer_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: dancers.php");
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
    if(isset($_GET["dancer_id"]) && !empty(trim($_GET["dancer_id"]))){
        // Get URL parameter
        $dancer_id =  trim($_GET["dancer_id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM dancers WHERE dancer_id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $dancer_id;
            
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
		<div class="form-group <?php echo (!empty($dancer_fullname_err)) ? 'has-error' : ''; ?>">
			<label>Full Name</label>
			<input type="text" name="dancer_fullname" class="form-control" value="<?php echo $dancer_fullname; ?>">
			<span class="help-block"><?php echo $dancer_fullname_err;?></span>
		</div>
		<div class="form-group <?php echo (!empty($dancer_phone_err)) ? 'has-error' : ''; ?>">
			<label>Contact Phone</label>
			<input type="text" name="dancer_phone" class="form-control" value="<?php echo $dancer_phone; ?>">
			<span class="help-block"><?php echo $dancer_phone_err;?></span>
		</div>
		<div class="form-group <?php echo (!empty($dancer_email_err)) ? 'has-error' : ''; ?>">
			<label>Contact Email Address</label>
			<input type="text" name="dancer_email" class="form-control" value="<?php echo $dancer_email; ?>">
			<span class="help-block"><?php echo $dancer_email_err;?></span>
		</div>
		<div class="form-group <?php echo (!empty($dancer_email_or_phone_err)) ? 'has-error' : ''; ?>">
			<label>Preferred Contact Method</label>
			<input type="text" name="dancer_email_or_phone" class="form-control" value="<?php echo $dancer_email_or_phone; ?>">
			<span class="help-block"><?php echo $dancer_email_or_phone_err;?></span>
		</div>
		<input type="hidden" name="id" value="<?php echo $dancer_id; ?>"/>
		<input type="submit" class="btn btn-primary" value="Submit">
		<a href="dancers.php" class="btn btn-default">Cancel</a>
	</form>
</div>
<?php
include_once "includes/footer.php";
?>