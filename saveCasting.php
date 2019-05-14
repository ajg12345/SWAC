<?php
include_once 'includes/dbh.inc.php';
//title variables
$building = '';
$production = '';
$prod_id = '';
$room = '';
$perf_dt = '';
$start_time = '';
$end_time = '';
$type = '';
//casting variables
$role = '';
$role_count = '';
$dancer = '';

//gather information for top of file
$re_id = $_POST['save_as_file'];	
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
									
$title_result = mysqli_query($conn,$sql_title);
while($row_title = mysqli_fetch_array($title_result)){	//this should only return one result
	$building = $row_title['building'];
	$production = $row_title['production'];
	$prod_id = $row_title['prod_id'];
	$room = $row_title['room'];
	$perf_dt = $row_title['perf_dt'];
	$start_time = $row_title['start_time'];
	$end_time = $row_title['end_time'];
	if ($row_title['is_performance'] == 1){$type = "Performance";}
}

//create csv file name for user with title and randomizer
$output_file = str_replace(' ','_',$production) . '_' . $type . '_' . date("m-d-Y", strtotime($perf_dt)) . '_' . date("gia", strtotime($start_time)) . '_' . mt_rand() . '.csv';
$of_handler = fopen($output_file, 'w');			//readying the write
$new_line_text = "Casting for ".$production." ".$type." on ".date("l M j Y", strtotime($perf_dt))." at ". date("g:ia", strtotime($start_time)). ' to '.date("g:ia", strtotime($end_time)) . ' in '. $room . ' of the ' . $building;
fwrite($of_handler, $new_line_text);
$sql_casting = 	"SELECT  
			c.casting_id,
			p.description as production,
			r.description as role,
			r.role_count,
			d.dancer_fullname as dancer
			FROM castings as c 
			join roles r on c.role_id = r.role_id
			join dancers as d on c.dancer_id = d.dancer_id
			join rehearsals as re on c.re_id = re.re_id
			join productions as p on re.prod_id = p.prod_id
			where c.re_id = " . $re_id . " order by c.role_id asc;";
						
						
$casting_result = mysqli_query($conn,$sql_casting);
$of_handler = fopen($output_file, 'a');			//readying the append
fwrite($of_handler, "\n".'Role, Dancer'); 			//readying the append//input the header row of the document
$old_role = null;		//make sure that duplicated roles are not repeated again and again.
while($role_row = mysqli_fetch_array($casting_result)){
	if(is_null($old_role)){		//first loop
		$old_role = $role_row['role'];
		$role = $role_row['role'];
		$dancer = $role_row['dancer'];
	}elseif(strcmp($old_role, $role_row['role']) == 0){
		$role = '';
		$dancer = $role_row['dancer'];
	}else{
		$old_role = $role_row['role'];
		$role = $role_row['role'];
		$dancer = $role_row['dancer'];
	}
	
	$new_line_text = $role. ','.$dancer;
	fwrite($of_handler, "\n".$new_line_text);
}

fclose($of_handler);
mysqli_free_result($title_result);
mysqli_free_result($casting_result);
mysqli_close($conn);
$destination = "location: castings.php?re_id=".$re_id."#file_saved_as#".$output_file;
header($destination);
exit();

?>