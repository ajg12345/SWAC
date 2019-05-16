<?php
// Include config file
require_once "includes/header.php";
require_once "includes/dbh.inc.php";
// Define variables and initialize with empty values
$dancer_email_or_phone = "";
$dancer_fullname = $dancer_phone = $dancer_email =  "";
$dancer_fullname_err = $dancer_phone_err = $dancer_email_err = $dancer_email_or_phone_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	 // Validate email_or_phone
    $input_dancer_email_or_phone = trim($_POST["dancer_email_or_phone"]);
    if((strcmp($input_dancer_email_or_phone, 'email') !== 0) AND (strcmp($input_dancer_email_or_phone, 'phone') !== 0)){
        $dancer_email_or_phone_err = 'Please enter the value "phone" or "email".';
    }else{
        $dancer_email_or_phone = $input_dancer_email_or_phone;
	}
	
    // Validate fullname
    $input_dancer_full = trim($_POST["dancer_fullname"]);
    if(empty($input_dancer_full)){
        $dancer_first_err = "Please enter a full name.";
    } elseif(preg_match('~[0-9]~', $input_dancer_full)){
        $dancer_fullname_err = "Please enter a valid full name."; 
    } else{
        $dancer_fullname = $input_dancer_full;
    }
	
    // Validate phone
    $input_dancer_phone = trim($_POST["dancer_phone"]);
    if(!preg_match('~[0-9]~', $input_dancer_phone)){
        $dancer_phone_err = "Please enter a phone number that contains numbers.";
    } else{
        $dancer_phone = $input_dancer_phone;
    }
	
    // Validate email
    $input_dancer_email = trim($_POST["dancer_email"]);
    if(!strpos($input_dancer_email, '@')){
        $dancer_email_err = "Please enter a valid email.";
    } else{
        $dancer_email = $input_dancer_email;
    }
    
    // Check input errors before inserting in database
    if(empty($dancer_fullname_err) && empty($dancer_email_err) && empty($dancer_email_or_phone_err) && empty($dancer_phone_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO dancers(dancer_fullname, dancer_phone, dancer_email, dancer_email_or_phone) VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_fullname, $param_phone, $param_email, $param_email_or_phone);
            
            // Set parameters
            $param_fullname = $dancer_fullname;
            $param_phone = $dancer_phone;
            $param_email = $dancer_email;
			$param_email_or_phone = $dancer_email_or_phone;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: dancers.php");
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
		<h2>Create Dancer</h2>
	</div>
    <div class="content">
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($dancer_fullname_err)) ? 'has-error' : ''; ?>">
				<label>Full Name</label>
				<input type="text" name="dancer_fullname" class="form-control" >
				<span class="help-block"><?php echo $dancer_fullname_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($dancer_phone_err)) ? 'has-error' : ''; ?>">
				<label>Contact Phone</label>
				<input type="text" name="dancer_phone" class="form-control" >
				<span class="help-block"><?php echo $dancer_phone_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($dancer_email_err)) ? 'has-error' : ''; ?>">
				<label>Contact Email Address</label>
				<input type="text" name="dancer_email" class="form-control">
				<span class="help-block"><?php echo $dancer_email_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($dancer_email_or_phone_err)) ? 'has-error' : ''; ?>">
				<label>Preferred Contact Method</label>
				<select name="dancer_email_or_phone" class="form-control">
					<option value="phone">phone</option>
					<option value="email">email</option>
				</select>
				<span class="help-block"><?php echo $dancer_email_or_phone_err;?></span>
			</div>
			<input type="submit" class="btn btn-primary" value="Submit">
			<a href="dancers.php" class="btn btn-default">Cancel</a>
		</form>
    </div>
</div>
<?php
include_once "includes/footer.php";
?>