<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
include_once 'includes/dbh.inc.php';
//get casting labels lke location, time, prod etc.
$building = "";
$production = "";
$room = "";
$perf_dt = "";
$start_time = "";
$end_time = "";
$type = "Rehearsal";

if(isset($_GET["re_id"]) && !empty(trim($_GET["re_id"]))){
	// Get URL parameter
	$re_id =  trim($_GET["re_id"]);					
	
	$sql = "SELECT  re.re_id as re_id,
				re.is_performance as is_performance,
				loc.building as building, 
				loc.room as room, 
				pro.description as production, 
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
			$room = $row['room'];
			$perf_dt = $row['perf_dt'];
			$start_time = $row['start_time'];
			$end_time = $row['end_time'];
			if ($row['is_performance'] == 1){$type = "Performance";}
		}
	}
}
?>

<div class= "grid">
	<div class="title">
		<h1><?php echo $type; ?> Casting</h1>
		<?php echo "<h2>".$production." on ".$perf_dt." from ".$start_time." to ".$end_time."</h2>"; ?>
	</div>
	<div class="content"> 
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
					<div class="page-header clearfix">
						<a href="castingcreate.php" class="btn btn-success pull-right">Cast Additional Role</a>
					</div>
					<?php
					
					// Attempt select query execution
					if(isset($_GET["re_id"]) && !empty(trim($_GET["re_id"]))){
						// Get URL parameter
						$re_id =  trim($_GET["re_id"]);					
						
						$sql = "SELECT  
								c.casting_id,
								p.description as production,
								r.description as role,
								d.dancer_fullname as dancer
								FROM castings as c 
								join roles r on c.role_id = r.role_id
								join dancers as d on c.dancer_id = d.dancer_id
								join rehearsals as re on c.re_id = re.re_id
								join productions as p on re.prod_id = p.prod_id
								where c.re_id = " . $re_id . " order by c.role_id asc;";
						if($result = mysqli_query($conn, $sql)){
							if(mysqli_num_rows($result) > 0){
								echo "<table class='table table-bordered table-striped'>";
									echo "<thead>";
										echo "<tr>";
											//echo "<th>casting_id</th>";
											//echo "<th>Production</th>";
											echo "<th>Role</th>";
											echo "<th>Dancer</th>";
										echo "</tr>";
									echo "</thead>";
									echo "<tbody>";
									while($row = mysqli_fetch_array($result)){
										echo "<tr>";
											//echo "<td>" . $row['casting_id'] . "</td>";
											//echo "<td>" . $row['production'] . "</td>";
											echo "<td>" . $row['role'] . "</td>";
											echo "<td>" . $row['dancer'] . "</td>";
											echo "<td>";
												echo "<a href='castingupdate.php?casting_id=". $row['casting_id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
												echo "<a href='castingdelete.php?casting_id=". $row['casting_id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
											echo "</td>";
										echo "</tr>";
									}
									echo "</tbody>";                            
								echo "</table>";
								// Free result set
								mysqli_free_result($result);
							} else{
								echo "<p class='lead'><em>No records were found.</em></p>";
							}
						} else{
							echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
						}
					} else{
							echo "<p>ERROR: missing valid rehearsal/performance id </p>";
					}

					// Close connection
					mysqli_close($conn);
					?>
				</div>	
			</div>
		</div>	
	</div>	
</div>	
	
	

<?php
include_once "includes/footer.php";
?>
