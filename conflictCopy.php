<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
include_once 'includes/dbh.inc.php';
//get casting labels lke location, time, prod etc.
$production = "";
$prod_id = "";

if(isset($_GET["prod_id"]) && !empty(trim($_GET["prod_id"]))){
	// Get header text from GET and mysql
	$prod_id =  trim($_GET["prod_id"]);					
	
	$sql_title = "SELECT  pro.prod_id,
				pro.description as production
				FROM productions as pro
				where pro.prod_id = " . $prod_id . ";";
	if($result_title = mysqli_query($conn, $sql_title)){
		while($row_title = mysqli_fetch_array($result_title)){
			$production = $row_title['production'];
			$prod_id = $row_title['prod_id'];
		}
	}
	
	//find list of prods without role_conflicts to potentially select
	$sql_copy_dest = "SELECT  pro.prod_id as new_prod_id,
				pro.description as production
				FROM productions as pro
				where pro.prod_id not in (select prod_id from role_conflicts);";
				
	$result_destinations = mysqli_query($conn, $sql_copy_dest);
}
//insert check for POST code to insert new casting records into the castings table
if(isset($_POST["prod_id"]) && !empty($_POST["prod_id"]) && isset($_POST["new_prod_id"]) && !empty($_POST["new_prod_id"])){
	
	$input_prod_id = trim($_POST["prod_id"]);
	$input_new_prod_id = trim($_POST["new_prod_id"]);
	
	$matches = 0;
	$r1_roles = 1;
	$r2_roles = 2;
	
	$sql_role_matches = "select 
					count(distinct r1.description) as matches
					from (select description, role_count from roles where prod_id = ".$input_prod_id.") as r1 
					join (select description, role_count from roles where prod_id = ".$input_new_prod_id.") as r2 on 
					r1.description = r2.description and r1.role_count = r2.role_count ";
	$result_matches = mysqli_query($conn, $sql_role_matches);
	$matches_row = mysqli_fetch_array($result_matches);
	$matches = 	$matches_row['matches'];
	mysqli_free_result($matches_row);
	
	$sql_r1_roles = "select count(distinct r1.description) as r1_roles
							from (select description, role_count from roles where prod_id = ".$input_prod_id.") as r1 ";
	$result_r1_roles = mysqli_query($conn, $sql_r1_roles);
	$r1_roles_row = mysqli_fetch_array($result_r1_roles);
	$r1_roles = $r1_roles_row['r1_roles'];
	mysqli_free_result($r1_roles_row);
	
	$sql_r2_roles = "select count(distinct r2.description) as r2_roles
							from (select description, role_count from roles where prod_id = ".$input_new_prod_id.") as r2 ";
	$result_r2_roles = mysqli_query($conn, $sql_r2_roles);
	$r2_roles_row = mysqli_fetch_array($result_r2_roles);
	$r2_roles = $r2_roles_row['r2_roles'];
	mysqli_free_result($r2_roles_row);
	//be sure to find and exclude possible productions which already have conflicts
	
	
	//find highest conflict_pair_id and set a next highest to use.
	$sql_max_pair_id = "select max(conflict_pair_id) as max_conflict_pair_id from role_conflicts;";
	$max_pair_id = mysqli_query($conn, $sql_max_pair_id);
	$max_pair_id_row = mysqli_fetch_array($max_pair_id);
	$insert_pair_id = $max_pair_id_row['max_conflict_pair_id'];
	mysqli_free_result($max_pair_id_row);

	//THS WOULD BE BEST TESTED WITH A SHOW WITH 3 ROLES AND EVERY POSSIBLE COMBINATION OF ROLE_CONFLICT, THEN PROPER CONFLICT PAIR ID ASSIGNMENT
	$conflict_insert_p1 ="insert into role_conflicts(conflict_pair_id, prod_id, role_id1, role_id2)
	select 
	@rownum:= @rownum + 1 as rank,
	".$input_new_prod_id.", 
	rnew1.role_id as role_id1, 
	rnew2.role_id as role_id2 
	from (select distinct 
				case when role_id1 > role_id2 then role_id1 else role_id2 end as role_id1, 
				case when role_id1 > role_id2 then role_id2 else role_id1 end as role_id2, 
				prod_id from role_conflicts where prod_id = ".$input_prod_id.") as rc 
	join (select description, role_count, role_id from roles where prod_id = ".$input_prod_id.") as r1 on rc.role_id1 = r1.role_id 
	join (select description, role_count, role_id from roles where prod_id = ".$input_prod_id.") as r2 on rc.role_id2 = r2.role_id 
	join (select description, role_count, role_id from roles where prod_id = ".$input_new_prod_id.") as rnew1 on r1.description = rnew1.description and r1.role_count = rnew1.role_count 
	join (select description, role_count, role_id from roles where prod_id = ".$input_new_prod_id.") as rnew2 on r2.description = rnew2.description and r2.role_count = rnew2.role_count
	,(select @rownum := ".$insert_pair_id.")	rwn;";
	
	//USE THIS SAME QUERY TWICE BUT WITH JUST REVERSING THE ROLE_ID1/2 insert statement ORDER. THAT WILL ENSURE THAT THE ROWNUMBER/ROLE_CONFLICT ASSIGNS PROPERLY.
	$conflict_insert_p2 ="insert into role_conflicts(conflict_pair_id, prod_id, role_id2, role_id1)
	select 
	@rownum:= @rownum + 1 as rank,
	".$input_new_prod_id.", 
	rnew1.role_id as role_id1, 
	rnew2.role_id as role_id2 
	from (select distinct 
				case when role_id1 > role_id2 then role_id1 else role_id2 end as role_id1, 
				case when role_id1 > role_id2 then role_id2 else role_id1 end as role_id2, 
				prod_id from role_conflicts where prod_id = ".$input_prod_id.") as rc 
	join (select description, role_count, role_id from roles where prod_id = ".$input_prod_id.") as r1 on rc.role_id1 = r1.role_id 
	join (select description, role_count, role_id from roles where prod_id = ".$input_prod_id.") as r2 on rc.role_id2 = r2.role_id 
	join (select description, role_count, role_id from roles where prod_id = ".$input_new_prod_id.") as rnew1 on r1.description = rnew1.description and r1.role_count = rnew1.role_count 
	join (select description, role_count, role_id from roles where prod_id = ".$input_new_prod_id.") as rnew2 on r2.description = rnew2.description and r2.role_count = rnew2.role_count
	,(select @rownum := ".$insert_pair_id.")	rwn;";
	
	//$location_dest = "location: conflicts.php?matches=".$matches."&r1_roles=".$r1_roles."&r2_roles=".$r2_roles."&new_prod_id=".$input_new_prod_id;
	//header($location_dest);
	//exit();
	
	if(($matches == $r1_roles) && ($r1_roles == $r2_roles)){
		if(mysqli_query($conn,$conflict_insert_p1)){			
			$location_dest = "location: conflicts.php#conflicts_copied_into_production_id_".$input_new_prod_id;
		}else{                
			$header_target = "location: error.php?error_code=9&part=1";
			header($header_target);
			exit();
		}			
		if(mysqli_query($conn,$conflict_insert_p2)){			
			$location_dest = "location: conflicts.php#conflicts_copied_into_production_id_".$input_new_prod_id;
		}else{                
			$header_target = "location: error.php?error_code=9&part=2";
			header($header_target);
			exit();
		}		
		header($location_dest);
		exit();
	}else{
		$header_target = "location: error.php?error_code=10";
		header($header_target);
		exit();
	}
}
?>

<div class= "grid">
	<div class="title">
		<h1>Copy the Role Conflicts of:</h1>
		<?php echo "<h2>".$production."</h2>"; ?>
		<h2>to ...</h2>
	</div>
	<div class="content"> 
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($prod_id_err)) ? 'has-error' : ''; ?>">
				<label>Productions without Role Conflicts</label>
				<select name="new_prod_id" class="form-control">
					<?php 
					while($dest_row = mysqli_fetch_array($result_destinations)){
						$option_string = '<option value="' . $dest_row['new_prod_id'] . '">';	//open tag
						$option_string = $option_string . $dest_row['production']; //interior
						$option_string = $option_string . '</option>'; //close tag
						echo $option_string;
					}
					?>
				</select>
			</div>
			<input type="submit" class="btn btn-primary" value="Submit">
			<input type="hidden" name="prod_id" value="<?php echo $prod_id; ?>"/>
			<a href="roles.php?prod_id=<?php echo $prod_id;?>" class="btn btn-default">Cancel</a>
		</form>	
	</div>	
</div>	
	
	

<?php
include_once "includes/footer.php";
?>

