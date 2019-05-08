<?php
// Include config file
require_once "includes/header.php";

require_once "includes/dbh.inc.php";
// Define variables and initialize with empty values
$production_description = $production_description_err = "";

// Processing form data when form is submitted
if(isset($_POST["prod_id"]) && !empty($_POST["prod_id"])){
	
    // Get hidden input value
    $prod_id = $_POST["prod_id"];
    
    // Validate description
    $input_production_description = trim($_POST["production_description"]);
    if(empty($input_production_description)){
        $production_description_err = "Please enter a production name.";
    } else{
        $production_description = $input_production_description;
    }
    
    // Check input errors before inserting in database
    if(empty($production_description_err)){
        // Prepare an update statement
        $sql = "UPDATE productions SET description=? WHERE prod_id=?";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_description, $param_id);
            
            // Set parameters
            $param_description = $production_description;
            $param_id = $prod_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: productions.php#itwassucessful");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
				header("location: error.php#43");
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
        $sql = "SELECT description, prod_id FROM productions WHERE prod_id = ?";
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
                    $production_description = $row["description"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php#80");
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
        header("location: error.php#95");
        exit();
    }
}
include_once "includes/crudheader.php";
?>
<div class="grid">
	<div class="Title">
		<h2>Update Production Record</h2>
	</div>
	<p>Please edit the production title and submit to update the record.</p>
	<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="POST"> 
		<div class="form-group <?php echo (!empty($production_description_err)) ? 'has-error' : ''; ?>">
			<label>Full Name</label>
			<input type="text" name="production_description" class="form-control" value="<?php echo $production_description; ?>">
			<span class="help-block"><?php echo $production_description_err;?></span>
		</div>
		<input type="hidden" name="prod_id" value="<?php echo $prod_id; ?>"/>
		<input name="submit" type="submit" class="btn btn-primary" value="Submit">
		<a href="productions.php" class="btn btn-default">Cancel</a>
	</form>
</div>
<?php
include_once "includes/footer.php";
?>