<?php
include_once 'includes/dbh.inc.php';
//$prod_id = 1;

$production_id = $_POST['prod_id'];		

$sql_role = 	"SELECT r.role_id,
						r.description as role,
						r.role_count,
						p.prod_id,
						p.description as production
						from roles as r
						join productions as p on r.prod_id = p.prod_id
						where r.prod_id =". $production_id ." ;";
$role_result = mysqli_query($conn,$sql_role);
$role_array = array();
while($role_row = mysqli_fetch_array($role_result)){
	$role_id = $role_row['role_id'] ;
	$role = $role_row['role'];
	$role_count = $role_row['role_count'];
	$production = $role_row['production'];
	$prod_id = $role_row['prod_id'];
	$role_array[] = array("role_id" => $role_id, "role" => $role, "production" => $production, "prod_id" => $prod_id, "role_count" => $role_count);
}
echo json_encode($role_array);
mysqli_free_result($role_result);
mysqli_close($conn);
?>