<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
?>

<div class= "grid">
	<div class="title"><h1>Joffrey Ballet Dancer Listing Landing Page</h1></div>
	<div class="content"> 
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
					<div class="page-header clearfix">
						<a href="dancercreate.php" class="btn btn-success pull-right">Add New Dancer</a>
					</div>
					<?php
					include_once 'includes/dbh.inc.php';
					// Attempt select query execution
					$sql = "SELECT * FROM dancers";
					if($result = mysqli_query($conn, $sql)){
						if(mysqli_num_rows($result) > 0){
							echo "<table class='table table-bordered table-striped'>";
								echo "<thead>";
									echo "<tr>";
										echo "<th>id</th>";
										echo "<th>Full Name</th>";
										echo "<th>Phone Number</th>";
										echo "<th>Email Address</th>";
										echo "<th>Preferred Contact Method</th>";
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
								while($row = mysqli_fetch_array($result)){
									echo "<tr>";
										echo "<td>" . $row['dancer_id'] . "</td>";
										echo "<td>" . $row['dancer_fullname'] . "</td>";
										echo "<td>" . $row['dancer_phone'] . "</td>";
										echo "<td>" . $row['dancer_email'] . "</td>";
										echo "<td>" . $row['dancer_email_or_phone'] . "</td>";
										echo "<td>";
											echo "<a href='dancerread.php?dancer_id=". $row['dancer_id'] ."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
											echo "<a href='dancerupdate.php?dancer_id=". $row['dancer_id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
											echo "<a href='dancerdelete.php?dancer_id=". $row['dancer_id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
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

