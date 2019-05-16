<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
include_once 'includes/dbh.inc.php';
//get casting labels lke location, time, prod etc.
$building = "";
$production = "";
$prod_id = "";
$re_id = "";
$room = "";
$perf_dt = "";
$start_time = "";
$end_time = "";
$type = "Rehearsal";
$input_overbook_err = '';

if(isset($_GET["re_id"]) && !empty(trim($_GET["re_id"]))){
	// Get URL parameter
	$re_id =  trim($_GET["re_id"]);					
	
	$sql_title = "SELECT  re.re_id as re_id,
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
				
	if($result_title = mysqli_query($conn, $sql_title)){

		while($row_title = mysqli_fetch_array($result_title)){
			$building = $row_title['building'];
			$production = $row_title['production'];
			$prod_id = $row_title['prod_id'];
			$room = $row_title['room'];
			$perf_dt = $row_title['perf_dt'];
			$start_time = $row_title['start_time'];
			$end_time = $row_title['end_time'];
			if ($row_title['is_performance'] == 1){$type = "Performance";}
		}
	}

	//find list of uncast performances/rehearsals of this rehearsals production to populate the drop down select list.
	$sql_copy_dest = "SELECT  re.re_id as new_re_id,
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
				where pro.prod_id = " . $prod_id . " and re.re_id not in (select re_id from castings) order by perf_dt asc;";
				
	$result_destinations = mysqli_query($conn, $sql_copy_dest);
	
}
//insert check for POST code to insert new casting records into the castings table
if(isset($_POST["re_id"]) && !empty($_POST["re_id"]) && isset($_POST["new_re_id"]) && !empty($_POST["new_re_id"])){
	$input_re_id = trim($_POST["re_id"]);
	$input_new_re_id = trim($_POST["new_re_id"]);
	
		$sql_title = "SELECT  re.re_id as re_id,
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
				where re.re_id = " . $input_re_id . ";";
				
	if($result_title = mysqli_query($conn, $sql_title)){

		while($row_title = mysqli_fetch_array($result_title)){
			$building = $row_title['building'];
			$production = $row_title['production'];
			$prod_id = $row_title['prod_id'];
			$room = $row_title['room'];
			$perf_dt = $row_title['perf_dt'];
			$start_time = $row_title['start_time'];
			$end_time = $row_title['end_time'];
			if ($row_title['is_performance'] == 1){$type = "Performance";}
		}
	}

	//find list of uncast performances/rehearsals of this rehearsals production to populate the drop down select list.
	$sql_copy_dest = "SELECT  re.re_id as new_re_id,
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
				where pro.prod_id = " . $prod_id . " and re.re_id not in (select re_id from castings) order by perf_dt asc;";
				
	$result_destinations = mysqli_query($conn, $sql_copy_dest);
	
	//check that the dancer isn't overbooked that day (more than 6 hours of rehearsal)
	$sql_overbook_check = "select 	dancer_id, 
									sum(end_time - start_time)/10000 as total_hours_in_day
								from (
									select 
									distinct c.dancer_id,
									start_time, 
									end_time 
									from rehearsals as re
									join castings as c on re.re_id = c.re_id
									where re.is_performance = 0
									and re.perf_dt in (SELECT perf_dt FROM rehearsals where re_id = ".$input_re_id.")
									union 
									 select 
									c.dancer_id, 
									start_time, 
									end_time 
									from (select start_time, end_time from rehearsals where re_id = ".$input_new_re_id.") as re
									cross join (select dancer_id from castings where re_id = ".$input_re_id.") as c 
									) as a
								group by dancer_id";
    if( (strcmp($type,"Rehearsal") == 0)){							
		if($over_book_duration = mysqli_query($conn, $sql_overbook_check)){
			while($row_hour_check = mysqli_fetch_array($over_book_duration)){
				if ($row_hour_check['total_hours_in_day'] > 6){$input_overbook_err = "Some of the dancers in the selected casting would be be scheduled OVER 6 HOURS if they were in this rehearsal/production. The casting was not copied";}
			}
		}
	mysqli_free_result($over_book_duration);	
	}
	
	if(empty($input_overbook_err)){
		
		$sql_insert_statment = "insert into castings(dancer_id, re_id, role_id)
								select dancer_id, ?, role_id
								from castings where re_id = ?;";
		if($stmt = mysqli_prepare($conn, $sql_insert_statment)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "ii", $param_new_re_id, $param_re_id);
			// Set parameters
			$param_re_id = $input_re_id;
			$param_new_re_id = $input_new_re_id;
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records created successfully. Redirect to landing page which now has proper castings.
				$location_dest = "location: castings.php?re_id=". $input_new_re_id;
				header($location_dest);
				exit();
			} else{                
				$header_target = "location: error.php?error_code=6";
				header($header_target);
				exit();
			}		
		}			
	}
	mysqli_close($conn);
}
?>

<div class= "grid">
	<div class="title">
		<h1>Copy the Casting of:</h1>
		<?php echo "<h2>".$production." on ".date("m-d-Y", strtotime($perf_dt))." from ".date("g:i a", strtotime($start_time))." to ".date("g:i a", strtotime($end_time))." ".$type."</h2>"; ?>
		<h2>to ...</h2>
	</div>
	<div class="content"> 
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($prod_id_err) || !empty($input_overbook_err)) ? 'has-error' : ''; ?>">
				<label>Uncast Rehearsals and Performances of <?php echo $production; ?></label>
				<select name="new_re_id" class="form-control">
					<?php 
					while($dest_row = mysqli_fetch_array($result_destinations)){
						if ($dest_row['is_performance'] == 1){$type = "Performance";}else{$type = "Rehearsal";}
						$option_string = '<option value="' . $dest_row['new_re_id'] . '">';	//open tag
						$option_string = $option_string . $type ." on ". date("m-d-Y", strtotime($dest_row['perf_dt']))." from ". date("g:i a", strtotime($dest_row['start_time']))." to ". date("g:i a", strtotime($dest_row['end_time']))." ". $dest_row['building']." ". $dest_row['room'];	//interior
						$option_string = $option_string . '</option>'; //close tag
						echo $option_string;
					}
					?>
				</select>
				<span class="help-block"><?php echo $input_overbook_err;?></span>
			</div>
			<input type="submit" class="btn btn-primary" value="Submit">
			<input type="hidden" name="re_id" value="<?php echo $re_id; ?>"/>
			<a href="castings.php?re_id=<?php echo $re_id;?>" class="btn btn-default">Cancel</a>
		</form>	
	</div>	
</div>	
	
	

<?php
include_once "includes/footer.php";
?>

