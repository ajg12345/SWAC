<?php
include_once 'includes/dbh.inc.php';
//$prod_id = 1;

$date_id = $_POST['date_id'];		

$sql_rehearsals_and_perfs = 	"SELECT  
				re.re_id as re_id,
				loc.building as building, 
				loc.room as room, 
				pro.description as production, 
				re.perf_dt as perf_dt, 
				re.start_time as start_time, 
				re.end_time as end_time,
				re.is_performance
				FROM rehearsals as re
				join productions as pro on re.prod_id = pro.prod_id
				join locations as loc on re.location_id = loc.location_id
				where ((concat(month(re.perf_dt),' ',year(re.perf_dt))) = '".$date_id."' )
				or ('".$date_id."' = '0') order by re.perf_dt, re.start_time asc";
$result = mysqli_query($conn,$sql_rehearsals_and_perfs);
$result_array = array();
while($role_row = mysqli_fetch_array($result)){
	$re_id = $role_row['re_id'] ;
	$building = $role_row['building'];
	$room = $role_row['room'];
	$production = $role_row['production'];
	$perf_dt = $role_row['perf_dt'];
	$start_time = $role_row['start_time'];
	$end_time = $role_row['end_time'];
	$is_performance = $role_row['is_performance'];
	$result_array[] = array("re_id" => $re_id, "building" => $building, "room" => $room, "production" => $production, "perf_dt" => $perf_dt, "start_time" => $start_time, "end_time" => $end_time, "is_performance" => $is_performance);
}
echo json_encode($result_array);
mysqli_free_result($result);
mysqli_close($conn);
?>