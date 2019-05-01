<?php
// Include config file
require_once "includes/header.php";
require_once "includes/dbh.inc.php";
// Define variables and initialize with empty values
//this will get a prod_id, perf_dt, location_id, start_time, and end_time
$prod_id = null;
$re_id = null;
$building = "";
$production = "";
$room = "";
$perf_dt = "";
$start_time = "";
$end_time = "";
$type = "Rehearsal";
$input_dancer_err = "";
$input_role_err = "";

if(isset($_GET["re_id"]) && !empty(trim($_GET["re_id"]))){
	// Get URL parameter
	$re_id =  trim($_GET["re_id"]);					
	
	$sql = "SELECT  re.re_id as re_id,
				re.is_performance as is_performance,
				loc.building as building, 
				loc.room as room, 
				pro.description as production, 
				pro.prod_id as prod_id,
				re.perf_dt as perf_dt, 
				re.start_time as start_time, 
				re.end_time as end_time 
				FROM rehearsals as re
				join productions as pro on re.prod_id = pro.prod_id
				join locations as loc on re.location_id = loc.location_id
				where re.re_id = " . $re_id . ";";
				
	if($result = mysqli_query($conn, $sql)){
		while($row = mysqli_fetch_array($result)){
			$building = $row['building'];
			$production = $row['production'];
			$prod_id = $row['prod_id'];
			$room = $row['room'];
			$perf_dt = $row['perf_dt'];
			$start_time = $row['start_time'];
			$end_time = $row['end_time'];
			if ($row['is_performance'] == 1){$type = "Performance";}
		}
	}
}

//gather all options for selecting a new casting
$sql_dancer_list = "select dancer_fullname, dancer_id from dancers order by dancer_fullname desc;";
$dancer_list = mysqli_query($conn, $sql_dancer_list);
$sql_role_list = "select role_id, description from roles where prod_id = ". $prod_id . " order by description desc;";
$role_list = mysqli_query($conn, $sql_role_list);

$input_dancer_id = null;
$dancer_id = null;
$input_role_id = null;
$role_id = null;
$input_conflict_err = "";	//an explanation regarding possible role-conflicts


// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	// Validate role does not conflict with dancer choice ONLY FOR PERFORMANCES, not rehearsals
    $input_dancer_id = trim($_POST["dancer_id"]);
	$input_role_id = trim($_POST["role_id"]);
	$input_re_id = trim($_POST["re_id"]);
	
	$sql_conflict_list ="select r1.description as role1,
								r2.description as role2
								from castings as c
								join role_conflicts as rc on c.role_id = rc.role_id1 or c.role_id = rc.role_id2
								join roles as r1 on rc.role_id1 = r1.role_id
								join roles as r2 on rc.role_id2 = r2.role_id
								where c.re_id=". $input_re_id ."
								and c.dancer_id=". $input_dancer_id ."
								and (	(rc.role_id1 = c.role_id and rc.role_id2 =". $input_role_id .") 
									or  (rc.role_id2 = c.role_id and rc.role_id1 =". $input_role_id ."));";
	//select r1.description as role1, r2.description as role2 
	//from castings as c join role_conflicts as rc on c.role_id = rc.role_id1 or c.role_id = rc.role_id2 
	//join roles as r1 on rc.role_id1 = r1.role_id join roles as r2 on rc.role_id2 = r2.role_id 
	//where c.re_id=2 and c.dancer_id=1 and ( (rc.role_id1 = c.role_id and rc.role_id2 =1) or (rc.role_id2 = c.role_id and rc.role_id1 =2))
								 
	$conflict_list = mysqli_query($conn, $sql_conflict_list);
	
    if(empty($input_dancer_id) || empty($input_role_id)){
        $input_dancer_err = 'Please select a dancer and role to cast.';
		$input_role_err = 'Please select a dancer and role to cast.';
    }elseif((strcmp($type,"Rehearsal") <> 0)){	//a performnace, so apply the conflict rules
		while($conflict_row = mysqli_fetch_array($conflict_list)){
			$input_dancer_err = 'This dancer cannot dance both ' . $conflict_row['role1'] .' and '. $conflict_row['role2'] . ' in this production.';
			$input_role_err = 'This dancer cannot dance both ' . $conflict_row['role1'] .' and '. $conflict_row['role2'] . ' in this production.';
		}
	}else{
        $dancer_id = $input_dancer_id;
		$role_id = $input_role_id;
	}
	
    // Check input errors before inserting in database
    if(empty($input_dancer_err) && empty($input_role_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO castings(dancer_id, role_id, re_id) VALUES (?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iii", $param_dancer_id, $param_role_id, $param_re_id);
            // Set parameters
            $param_dancer_id = $dancer_id;
            $param_role_id = $role_id;
            $param_re_id = $input_re_id;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
				$landing_page = "location: castings.php?re_id=" . $param_re_id;
                header($landing_page);
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
		<?php echo "<h1>Create ".$type." Casting</h1>"; ?>
		<?php echo "<h2>".$production." on ".$perf_dt." from ".$start_time." to ".$end_time."</h2>"; ?>
	</div>
    <div class="content">
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($input_dancer_err)) ? 'has-error' : ''; ?>">
				<label>Dancer</label>
				<select name="dancer_id" class="form-control">
					<?php 
					while($dancer_row = mysqli_fetch_array($dancer_list)){
						echo '<option value="' . $dancer_row['dancer_id'] . '">' . $dancer_row['dancer_fullname'] . '</option>';
					}
					?>
				</select>
				<span class="help-block"><?php echo $input_dancer_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($input_role_err)) ? 'has-error' : ''; ?>">
				<label>Role</label>
				<select name="role_id" class="form-control">
					<?php 
					while($role_row = mysqli_fetch_array($role_list)){
						echo '<option value="' . $role_row['role_id'] . '">' . $role_row['description'] .'</option>';
					}
					?>
				</select>
				<span class="help-block"><?php echo $input_role_err;?></span>
			</div>
			<input type="hidden" name="re_id" value="<?php echo $re_id ?>">
			<input type="submit" class="btn btn-primary" value="Submit">
			<a href="castings.php" class="btn btn-default">Cancel</a>
		</form>
    </div>
</div>
<?php
//free memory assoc with prod and locs
mysqli_free_result($dancer_list);	
mysqli_free_result($role_list);
include_once "includes/footer.php";
?>