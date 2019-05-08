<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
include_once 'includes/dbh.inc.php';
//get casting labels lke location, time, prod etc.
$casting_count = 0;
$building = "";
$production = "";
$prod_id = "";
$re_id = "";
$room = "";
$perf_dt = "";
$start_time = "";
$end_time = "";
$type = "Rehearsal";

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
	//add code to check the number of castings in this show, set $casting_count
	$sql = "SELECT  
			c.casting_id,
			p.description as production,
			p.prod_id as prod_id,
			r.description as role,
			d.dancer_fullname as dancer
			FROM castings as c 
			join roles r on c.role_id = r.role_id
			join dancers as d on c.dancer_id = d.dancer_id
			join rehearsals as re on c.re_id = re.re_id
			join productions as p on re.prod_id = p.prod_id
			where c.re_id = " . $re_id . " order by c.role_id asc;";
	if($result = mysqli_query($conn, $sql)){
		$casting_count = mysqli_num_rows($result) ;
	}
}
?>

<div class= "grid">
	<div class="title">
		<h1><?php echo $type; ?> Casting</h1>
		<?php echo "<h2>".$production." on ".date("m-d-Y", strtotime($perf_dt))." from ".date("g:i a", strtotime($start_time))." to ".date("g:i a", strtotime($end_time))."</h2>"; ?>
	</div>
	<div class="content"> 
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
					<?php if($casting_count > 0){echo '<div><a href="castingCopy.php?re_id='. $re_id .'" class="btn btn-success pull-right">Copy Casting to another performance or rehearsal of "'.$production.'"</a></div>';}?>
					<div class="page-header clearfix">		
						<?php echo '<a href="castingcreate.php?re_id='. $re_id .'" class="btn btn-success pull-right">Cast Additional Role</a>' ?>
					</div>
					<?php
					
					// Attempt select query execution
					if(isset($_GET["re_id"]) && !empty(trim($_GET["re_id"]))){
						// Get URL parameter
						if($result = mysqli_query($conn, $sql)){
							if($casting_count > 0){
								echo "<table class='table table-bordered table-striped'>";
									echo "<thead>";
										echo "<tr>";
											echo "<th>Role</th>";
											echo "<th>Dancer</th>";
										echo "</tr>";
									echo "</thead>";
									echo "<tbody>";
									while($row = mysqli_fetch_array($result)){
										echo "<tr>";
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
								echo "<p class='lead'><em>No casting was found.</em></p>";
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

