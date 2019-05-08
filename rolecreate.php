<?php
// Include config file
require_once "includes/header.php";
require_once "includes/dbh.inc.php";

// Define variables and initialize with empty values
$prod_id = '';
$input_role_count = $role_count = $role_count_err = "";
$role = $input_role_description = $role_description_err =  "";
$production = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	//Collect the prod_id
	 $prod_id = trim($_POST["prod_id"]);
	
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
        $sql = "INSERT INTO roles(prod_id, description, role_count) VALUES (?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isi", $param_id, $param_description, $param_role_count);

			// Set parameters
			$param_id = $prod_id;
            $param_description = $role;
			$param_role_count = $role_count;            
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: roles.php#itwassucessful");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
				header("location: error.php?error_code=7");
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    // Close connection
    mysqli_close($conn);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["prod_id"]) && !empty(trim($_GET["prod_id"]))){
        // Get URL parameter
        $prod_id =  trim($_GET["prod_id"]);
        
        // Prepare a select statement
        $sql = "SELECT description from productions WHERE prod_id=?";
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $prod_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $production = $row["description"];
					mysqli_free_result($row);
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php?error_code=8");
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
		<h1><?php echo $production; ?> Role Create</h1>
	</div>
	<p>Please Provide a role description and role count of the number of dancers in that role in one production (e.g. snowflake: 16)</p>
	<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="POST"> 
		<div class="form-group <?php echo (!empty($role_description_err)) ? 'has-error' : ''; ?>">
			<label>Role Description</label>
			<input type="text" name="role" class="form-control" >
			<span class="help-block"><?php echo $role_description_err;?></span>
		</div>
		<div class="form-group <?php echo (!empty($role_count_err)) ? 'has-error' : ''; ?>">
			<label>Role Count</label>
			<input type="text" name="role_count" class="form-control">
			<span class="help-block"><?php echo $role_count_err;?></span>
		</div>
		<input type="hidden" name="prod_id" value="<?php echo $prod_id; ?>"/>
		<input name="submit" type="submit" class="btn btn-primary" value="Submit">
		<a href="roles.php" class="btn btn-default">Cancel</a>
	</form>
</div>
<?php
include_once "includes/footer.php";
?>