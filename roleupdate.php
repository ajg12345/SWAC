<?php
// Include config file
require_once "includes/header.php";
require_once "includes/dbh.inc.php";

// Define variables and initialize with empty values
$input_role_count = $role_count = $role_count_err = "";
$role = $input_role_description = $role_description_err =  "";

// Processing form data when form is submitted
if(isset($_POST["role_id"]) && !empty($_POST["role_id"])){
	
    // Get hidden input value
    $role_id = $_POST["role_id"];
    //get production name for DHTML titling in content div.
	$sql_production = "SELECT r.description as role, r.role_id, p.description as production, r.role_count FROM roles as r join productions as p on r.prod_id = p.prod_id WHERE role_id = ".$role_id.";";
	$production_result = mysqli_query($conn, $sql_production);
	$prod_row = mysqli_fetch_array($production_result);
	$production = $prod_row['production'];
	mysqli_free_result($production_result);
	
    // Validate role_count
    $input_role_count = trim($_POST["role_count"]);
    if(empty($input_role_count)){
        $role_count_err = "Please enter a number of people who might share this role in a performance (e.g. snowflake would be 12).";
    } elseif(!is_numeric($input_role_count)){
        $role_count_err = "Please a number from 0-60.";
    } else{
        $role_count = $input_role_count;
    }
    
    // Validate description
    $input_role_description = trim($_POST["role"]);
    if(empty($input_role_description)){
        $role_description_err = "Please enter a description.";     
    } else{
        $role = $input_role_description;
    }
    
    
    // Check input errors before inserting in database
    if(empty($role_description_err) && empty($role_count_err)){
        // Prepare an update statement
        $sql = "UPDATE roles SET description=?, role_count=? WHERE role_id=?";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sii", $param_description, $param_role_count, $param_id);
            
            // Set parameters
            $param_description = $role;
			$param_role_count = $role_count;
            $param_id = $role_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: roles.php#itwassucessful");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
				header("location: error.php?error_code=3");
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    // Close connection
    mysqli_close($conn);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["role_id"]) && !empty(trim($_GET["role_id"]))){
        // Get URL parameter
        $role_id =  trim($_GET["role_id"]);
        
        // Prepare a select statement
        $sql = "SELECT r.description as role, r.role_id, p.description as production, r.role_count FROM roles as r join productions as p on r.prod_id = p.prod_id WHERE role_id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $role_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $production = $row["production"];
                    $role = $row["role"];
                    $role_id = $row["role_id"];
					$role_count = $row["role_count"];
					mysqli_free_result($row);
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php?error_code=4");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
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
		<h1><?php echo $production; ?> Role Update</h1>
	</div>
	<p>Please edit the input values and submit to update the record.</p>
	<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="POST"> 
		<div class="form-group <?php echo (!empty($role_description_err)) ? 'has-error' : ''; ?>">
			<label>Role Description</label>
			<input type="text" name="role" class="form-control" value="<?php echo $role; ?>">
			<span class="help-block"><?php echo $role_description_err;?></span>
		</div>
		<div class="form-group <?php echo (!empty($role_count_err)) ? 'has-error' : ''; ?>">
			<label>Role Count</label>
			<input type="text" name="role_count" class="form-control" value="<?php echo $role_count; ?>">
			<span class="help-block"><?php echo $role_count_err;?></span>
		</div>
		<input type="hidden" name="role_id" value="<?php echo $role_id; ?>"/>
		<input name="submit" type="submit" class="btn btn-primary" value="Submit">
		<a href="roles.php" class="btn btn-default">Cancel</a>
	</form>
</div>
<?php
include_once "includes/footer.php";
?>