
<?php
include_once 'includes/dbh.inc.php';
//$prod_id = 1;

$production_id = $_POST['prod_id'];		

$sql_conflict = "SELECT  distinct
									rc.conflict_pair_id,
									case when r1.description > r2.description then r1.description else r2.description end as role1,
									case when r1.description > r2.description then r2.description else r1.description end as role2
									from role_conflicts as rc
									join roles as r1 on rc.role_id1 = r1.role_id
									join roles as r2 on rc.role_id2 = r2.role_id
									where rc.prod_id =". $production_id ." ;";
$conflict_result = mysqli_query($conn,$sql_conflict);
$conflict_array = array();
while($conflict_row = mysqli_fetch_array($conflict_result)){
	$conflict_pair_id = $conflict_row['conflict_pair_id'] ;
	$role1 = $conflict_row['role1'];
	$role2 = $conflict_row['role2'];
	$conflict_array[] = array("conflict_pair_id" => $conflict_pair_id, "role1" => $role1, "role2" => $role2);
}
echo json_encode($conflict_array);
mysqli_free_result($conflict_result);
mysqli_close($conn);
?>