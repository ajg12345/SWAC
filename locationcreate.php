<?php
// Include config file
require_once "includes/header.php";
require_once "includes/dbh.inc.php";
// Define variables and initialize with empty values

$room = $building = "";
$room_err = $building_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	 // Validate room
    $input_room = trim($_POST["room"]);
    if(empty($input_room)){
        $room_err = 'Please enter a value.';
    }else{
        $room = $input_room;
	}
	
    // Validate building
    $input_building = trim($_POST["building"]);
    if(empty($input_building)){
        $building_err = "Please enter a value.";
    } else{
        $building = $input_building;
    }
    
    // Check input errors before inserting in database
    if(empty($room_err) && empty($building_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO locations(room, building) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_room, $param_building);
            
            // Set parameters
            $param_room = $room;
            $param_building = $building;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: locations.php");
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
		<h2>Create Location</h2>
	</div>
    <div class="content">
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($room_err)) ? 'has-error' : ''; ?>">
				<label>Room</label>
				<input type="text" name="room" class="form-control" value="<?php echo $room; ?>">
				<span class="help-block"><?php echo $room_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($building_err)) ? 'has-error' : ''; ?>">
				<label>Building</label>
				<input type="text" name="building" class="form-control" value="<?php echo $building; ?>">
				<span class="help-block"><?php echo $building_err;?></span>
			</div>
			<input type="submit" class="btn btn-primary" value="Submit">
			<a href="locations.php" class="btn btn-default">Cancel</a>
		</form>
    </div>
</div>
<?php
include_once "includes/footer.php";
?>