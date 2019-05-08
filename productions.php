<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
?>

<div class= "grid">
	<div class="title"><h1>Joffrey Ballet Production</h1></div>
	<div class="content"> 
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
					<div class="page-header clearfix">
						<a href="productioncreate.php" class="btn btn-success pull-right">Add New Production</a>
					</div>
					<?php
					include_once 'includes/dbh.inc.php';
					// Attempt select query execution
					$sql = "SELECT * FROM productions";
					if($result = mysqli_query($conn, $sql)){
						if(mysqli_num_rows($result) > 0){
							echo "<table class='table table-bordered table-striped'>";
								echo "<thead>";
									echo "<tr>";
										echo "<th>prod_id</th>";
										echo "<th>description</th>";
										echo "<th>create_dt</th>";
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
								while($row = mysqli_fetch_array($result)){
									echo "<tr>";
										echo "<td>" . $row['prod_id'] . "</td>";
										echo "<td>" . $row['description'] . "</td>";
										echo "<td>" . $row['create_dt'] . "</td>";
										echo "<td>";
											echo "<a href='productiondelete.php?prod_id=". $row['prod_id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
											echo "<a href='productionupdate.php?prod_id=". $row['prod_id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
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

