<?php
// Include config file
require_once "includes/header.php";

require_once "includes/dbh.inc.php";
// Define variables and initialize with empty values
$prod_id = 0;
$prod_id_err = "";
$perf_dt = $perf_dt_err = "";
$location_id = 0;
$location_id_err = "";
$start_time = $start_time_err = $end_time = $end_time_err = "";

// Processing form data when form is submitted
if(isset($_POST["re_id"]) && !empty($_POST["re_id"])){
	
    // Get hidden input value
    $re_id = $_POST["re_id"];
     // Validate production_id
    $input_prod_id = trim($_POST["prod_id"]);
    if(empty($input_prod_id)){
        $prod_id_err = 'Please select a production.';
    }else{
        $prod_id = $input_prod_id;
	}
	
	// Validate location_id
    $input_location_id = trim($_POST["location_id"]);
    if(empty($input_location_id)){
        $location_id_err = 'Please select a location.';
    }else{
        $location_id = $input_location_id;
	}
	
    // Validate perf_dt
    $input_perf_dt = trim($_POST["perf_dt"]);
    if(empty($input_perf_dt)){
        $perf_dt_err = "Please select a performance date.";
    } else{
        $perf_dt = $input_perf_dt;
    }
	
    // Validate start_time
    $input_start_time = trim($_POST["start_time"]);
    if(empty($input_start_time)){
        $start_time_err = "Please enter a start time";
    } else{
        $start_time = $input_start_time;
    }
	
    // Validate start_time
    $input_end_time = trim($_POST["end_time"]);
    if(empty($input_end_time)){
        $end_time_err = "Please enter a end time";
    } else{
        $end_time = $input_end_time;
    }
    // Check input errors before inserting in database
    if(empty($prod_id_err) && empty($perf_dt_err) && empty($location_id_err) && empty($start_time_err) && empty($end_time_err)){
        // Prepare an update statement
        $sql = "UPDATE rehearsals SET prod_id=?, location_id=?, perf_dt=?, start_time =?, end_time=? WHERE re_id=?";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iisssi", $param_prod_id, $param_location_id, $param_perf_dt, $param_start_time, $param_end_time, $param_re_id);
            
            // Set parameters
            $param_prod_id = $prod_id;
            $param_location_id = $location_id;
            $param_perf_dt = $perf_dt;
			$param_start_time = $start_time;
			$param_end_time = $end_time;
			$param_re_id = $re_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: performances.php#itwassucessful");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
				header("location: error.php#73");
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    // Close connection
    mysqli_close($conn);
	} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["re_id"]) && !empty(trim($_GET["re_id"]))){
        // Get URL parameter
        $re_id =  trim($_GET["re_id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM rehearsals WHERE re_id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $re_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
					$prod_id = $row["prod_id"];
                    $location_id = $row["location_id"];
                    $perf_dt = $row["perf_dt"];
                    $start_time = $row["start_time"];
					$end_time = $row["end_time"];
					
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php#112");
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
        header("location: error.php#128");
        exit();
    }
}
include_once "includes/crudheader.php";
?>
<div class="grid">
	<div class="Title">
		<h2>Update Performance</h2>
	</div>
	<p>Please edit the input values and submit to update the record.</p>
	<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="POST"> 
		<div class="form-group <?php echo (!empty($prod_id_err)) ? 'has-error' : ''; ?>">
				<label>Production</label>
				<input type="text" name="prod_id" class="form-control" value="<?php echo $prod_id; ?>">
				<span class="help-block"><?php echo $prod_id_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($location_id_err)) ? 'has-error' : ''; ?>">
				<label>Location</label>
				<input type="text" name="location_id" class="form-control" value="<?php echo $location_id; ?>">
				<span class="help-block"><?php echo $location_id_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($perf_dt_err)) ? 'has-error' : ''; ?>">
				<label>Performance Date</label>
				<input type="text" name="perf_dt" class="form-control" value="<?php echo $perf_dt; ?>">
				<span class="help-block"><?php echo $perf_dt_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($start_time_err)) ? 'has-error' : ''; ?>">
				<label>Start Time</label>
				<input type="text" name="start_time" class="form-control" value="<?php echo $start_time; ?>">
				<span class="help-block"><?php echo $start_time_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($end_time_err)) ? 'has-error' : ''; ?>">
				<label>End Time</label>
				<input type="text" name="end_time" class="form-control" value="<?php echo $end_time; ?>">
				<span class="help-block"><?php echo $end_time_err;?></span>
			</div>
		<input type="hidden" name="re_id" value="<?php echo $re_id; ?>"/>
		<input name="submit" type="submit" class="btn btn-primary" value="Submit">
		<a href="performances.php" class="btn btn-default">Cancel</a>
	</form>
</div>
<?php
include_once "includes/footer.php";
?>