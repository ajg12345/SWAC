<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
?>

<div class= "grid">
	<div class="title"><h1>Joffrey Ballet Rehearsal and Performance Locations</h1></div>
	<div class="content"> 
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
					<div class="page-header clearfix">
						<a href="locationcreate.php" class="btn btn-success pull-right">Add New Location</a>
					</div>
					<?php
					include_once 'includes/dbh.inc.php';
					// Attempt select query execution
					$sql = "SELECT * FROM locations where is_active = 1";
					if($result = mysqli_query($conn, $sql)){
						if(mysqli_num_rows($result) > 0){
							echo "<table class='table table-bordered table-striped'>";
								echo "<thead>";
									echo "<tr>";
										echo "<th>location_id</th>";
										echo "<th>building</th>";
										echo "<th>room</th>";
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
								while($row = mysqli_fetch_array($result)){
									echo "<tr>";
										echo "<td>" . $row['location_id'] . "</td>";
										echo "<td>" . $row['room'] . "</td>";
										echo "<td>" . $row['building'] . "</td>";
										echo "<td>";
											echo "<a href='locationdelete.php?location_id=". $row['location_id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
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
					?>
				</div>	
			</div>
		</div>	
	</div>	
</div>	
	
	

<?php
include_once "includes/footer.php";
?>

