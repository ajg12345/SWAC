<?php
// Include config file
require_once "includes/header.php";
require_once "includes/dbh.inc.php";
// Define variables and initialize with empty values
//this will get a prod_id, perf_dt, location_id, start_time, and end_time
$prod_id = null;
$prod_id_err = "";
$perf_dt = $perf_dt_err = "";
$location_id = null;
$location_id_err = "";
$start_time = $start_time_err = $end_time = $end_time_err = "";

//gather all options for selecting a new production
$sql_loc_list = "select building, room, location_id from locations where is_active = 1 order by building, room;";
$loc_list = mysqli_query($conn, $sql_loc_list);
$sql_prod_list = "select description as production, prod_id, create_dt from productions where is_active = 1 order by create_dt;";
$prod_list = mysqli_query($conn, $sql_prod_list);

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
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
        // Prepare an insert statement
        $sql = "INSERT INTO rehearsals(is_performance, prod_id, location_id, perf_dt, start_time, end_time) VALUES (0, ?, ?, ?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iisss", $param_prod_id, $param_location_id, $param_perf_dt, $param_start_time, $param_end_time);
            // Set parameters
            $param_prod_id = $prod_id;
            $param_location_id = $location_id;
            $param_perf_dt = $perf_dt;
			$param_start_time = $start_time;
			$param_end_time = $end_time;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: rehearsals.php");
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
    <div class="Title">
		<h2>Create Rehearsal</h2>
	</div>
    <div class="content">
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($prod_id_err)) ? 'has-error' : ''; ?>">
				<label>Production</label>
				<select name="prod_id" class="form-control">
					<?php 
					while($prod_row = mysqli_fetch_array($prod_list)){
						echo '<option value="' . $prod_row['prod_id'] . '">' . $prod_row['production'] . '</option>';
					}
					?>
				</select>
				<span class="help-block"><?php echo $prod_id_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($location_id_err)) ? 'has-error' : ''; ?>">
				<label>Location</label>
				<select name="location_id" class="form-control">
					<?php 
					while($loc_row = mysqli_fetch_array($loc_list)){
						echo '<option value="' . $loc_row['location_id'] . '">' . $loc_row['building'] . ' - ' . $loc_row['room'] .'</option>';
					}
					?>
				</select>
				<span class="help-block"><?php echo $location_id_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($perf_dt_err)) ? 'has-error' : ''; ?>">
				<label>Performance Date</label>
				<input type="date" name="perf_dt" class="form-control" >
				<span class="help-block"><?php echo $perf_dt_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($start_time_err)) ? 'has-error' : ''; ?>">
				<label>Start Time</label>
				<input type="time" name="start_time" class="form-control" >
				<span class="help-block"><?php echo $start_time_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($end_time_err)) ? 'has-error' : ''; ?>">
				<label>End Time</label>
				<input type="time" name="end_time" class="form-control" >
				<span class="help-block"><?php echo $end_time_err;?></span>
			</div>
			<input type="submit" class="btn btn-primary" value="Submit">
			<a href="rehearsals.php" class="btn btn-default">Cancel</a>
		</form>
    </div>
</div>
<?php
//free memory assoc with prod and locs
mysqli_free_result($prod_list);	
mysqli_free_result($loc_list);
include_once "includes/footer.php";
?>