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
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate first namename
    $input_dancer_first = trim($_POST["dancer_first"]);
    if(empty($input_dancer_first)){
        $dancer_first_err = "Please enter a first name.";
    } elseif(!preg_match("/^[a-zA-Z]*$/", $input_dancer_first) || !preg_match("/^[a-zA-Z]*$/", $input_dancer_first)){
        $dancer_first_err = "Please enter a valid first name."; 
    } else{
        $dancer_first = $input_dancer_first;
    }
    
    // Validate last name
    $input_dancer_last = trim($_POST["dancer_last"]);
    if(empty($input_dancer_last)){
        $dancer_last_err = "Please enter an last name.";     
    } else{
        $dancer_last = $input_dancer_last;
    }
    
    // Validate email
    $input_dancer_email = trim($_POST["dancer_email"]);
    if(empty($input_dancer_email)){
        $dancer_email_err = "Please enter the email.";     
    } elseif(!strpos($input_dancer_email, '@')){
        $dancer_email_err = "Please enter a valid email.";
    } else{
        $dancer_email = $input_dancer_email;
    }
    
    // Check input errors before inserting in database
    if(empty($dancer_first_err) && empty($dancer_last_err) && empty($dancer_email_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO dancers (dancer_first, dancer_last, dancer_email) VALUES (?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_first, $param_last, $param_email);
            
            // Set parameters
            $param_first = $dancer_first;
            $param_last = $dancer_last;
            $param_email = $dancer_email;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
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
}
include_once "includes/crudheader.php";
?>
 
<div class="grid">
    
    <div class="title">Create Dancer</div>
    
    <div class="content">
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
			<input type="submit" class="btn btn-primary" value="Submit">
			<a href="dancers.php" class="btn btn-default">Cancel</a>
		</form>
    </div>
</div>
<?php
include_once "includes/footer.php";
?>