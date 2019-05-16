<?php
// Include config file
require_once "includes/header.php";
require_once "includes/dbh.inc.php";
// Define variables and initialize with empty values

$description = $description_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	 // Validate description
    $input_description = trim($_POST["description"]);
    if(empty($input_description)){
        $description_err = 'Please enter a value.';
    }else{
        $description = $input_description;
	}
	
    // Check input errors before inserting in database
    if(empty($description_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO productions(description, create_dt) VALUES (?, date(NOW()))";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_description);
            
            // Set parameters
            $param_description = $description;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: productions.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
}
include_once "includes/crudheader.php";
?>
 
<div class="grid">
    <div class="Title">
		<h2>Create Production</h2>
		<h3>please use a detailed description including year (e.g. 2019 Nutcracker)</h3>
	</div>
    <div class="content">
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($description_err)) ? 'has-error' : ''; ?>">
				<label>Description</label>
				<input type="text" name="description" class="form-control" value="<?php echo $description; ?>">
				<span class="help-block"><?php echo $description_err;?></span>
			</div>
			<input type="submit" class="btn btn-primary" value="Submit">
			<a href="productions.php" class="btn btn-default">Cancel</a>
		</form>
    </div>
</div>
<?php
include_once "includes/footer.php";
?>