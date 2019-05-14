<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
?>

<div class= "grid">
	<div class="title"><h1>Joffrey Ballet Rehearsals</h1></div>
	<div class="content"> 
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
					<div class="page-header clearfix">
						<a href="rehearsalcreate.php" class="btn btn-success pull-right">Add New Rehearsal</a>
					</div>
					<?php
					include_once 'includes/dbh.inc.php';
					// Attempt select query execution
					$sql = "SELECT  re.re_id as re_id,
									loc.building as building, 
									loc.room as room, 
									pro.description as production, 
									re.perf_dt as perf_dt, 
									re.start_time as start_time, 
									re.end_time as end_time 
									FROM rehearsals as re
									join productions as pro on re.prod_id = pro.prod_id
									join locations as loc on re.location_id = loc.location_id
									where re.is_performance = 0
									order by re.perf_dt, re.start_time asc";
					if($result = mysqli_query($conn, $sql)){
						if(mysqli_num_rows($result) > 0){
							echo "<table class='table table-bordered table-striped'>";
								echo "<thead>";
									echo "<tr>";
										echo "<th>ID</th>";
										echo "<th>Building</th>";
										echo "<th>Room</th>";
										echo "<th>Production</th>";
										echo "<th>Date</th>";
										echo "<th>Start Time</th>";
										echo "<th>End Time</th>";
										echo "<th>Casting</th>";
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
								while($row = mysqli_fetch_array($result)){
									echo "<tr>";
										echo "<td>" . $row['re_id'] . "</td>";
										echo "<td>" . $row['building'] . "</td>";
										echo "<td>" . $row['room'] . "</td>";
										echo "<td>" . $row['production'] . "</td>";
										echo "<td>" . date("m-d-Y", strtotime($row['perf_dt'])) . "</td>";
										echo "<td>" . date("g:i a", strtotime($row['start_time'])) . "</td>";
										echo "<td>" . date("g:i a", strtotime($row['end_time'])) . "</td>";
										echo "<td>";
											echo "<a href='castings.php?re_id=". $row['re_id'] ."' title='View Casting' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
											echo "<a href='rehearsalupdate.php?re_id=". $row['re_id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
											echo "<a href='rehearsaldelete.php?re_id=". $row['re_id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
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
						echo "ERROR: Could not execute $sql. " . mysqli_error($conn);
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
