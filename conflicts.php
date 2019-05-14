<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
include_once 'includes/dbh.inc.php';
//$prod_id = 1;

?>

<div class= "grid">
	<div class="title"><h1>Joffrey Ballet Role Conflicts</h1></div>
	<div class="content"> 
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
					<div class="page-header clearfix">
						<h3>First select a Production</h3>
						<select id="prod_select" class="form-control">
							<?php //the add new conflict button below will have to pass a get to the conflict create page link.
							$sql_prod_list = "SELECT * FROM productions where is_active = 1 order by create_dt desc";
							$result_prod_list = mysqli_query($conn, $sql_prod_list);
							echo '<option id="default_option" value="null">-</option>';
							while($prod_row = mysqli_fetch_array($result_prod_list)){
								echo '<option value="' . $prod_row['prod_id'] . '">' . $prod_row['description'] .'</option>';
							}
							mysqli_free_result($result_prod_list);	
							?>
						</select>
						<br>
						<a id="copy_conflict_button" href="conflictCopy.php" class="btn btn-success pull-right" style="display: none;">Copy Conflicts to Production without Conflicts</a>
						<a id="create_conflict_button" href="conflictcreate.php" class="btn btn-success pull-right" style="display: none;">Add New Conflict to this Production</a>
					</div>
					<?php
					// Attempt select query execution
						echo "<table class='table table-bordered table-striped'>";
							echo "<thead>";
								echo "<tr>";
									echo "<th>Conflict pair id</th>";
									echo "<th>Role 1</th>";
									echo "<th>Role 2</th>";
									echo "<th></th>";
								echo "</tr>";
							echo "</thead>";
							echo "<tbody id='conflict_table'>";
							
								echo "<tr>";
									echo "<td>None</td>";
									echo "<td>Nothing selected</td>";
									echo "<td>Nothing selected</td>";
									echo "<td>Nothing selected</td>";
								echo "</tr>";
								
							
							echo "</tbody>";                            
						echo "</table>";					
					?>
				</div>	
			</div>
		</div>	
	</div>	
</div>	
<!-- Script -->
<script type="text/javascript">
	$(document).ready(function(){

    $("#prod_select").change(function(){
        var prod_id = $(this).val();
		
		var conflict_copy_target = "conflictCopy.php?prod_id=" + prod_id;
		var conflict_create_target = "conflictcreate.php?prod_id=" + prod_id;
		$("#create_conflict_button").attr("href", conflict_create_target);
		$("#create_conflict_button").attr("style", "display:initial;");
		$("#default_option").attr("style", "display:none;");
        $.ajax({
            url: 'getconflicts.php',
            type: 'post',
            data: {prod_id:prod_id},
            dataType: 'json',
            success:function(response){

                var len = response.length;
				if(len > 0){
					$("#copy_conflict_button").attr("style", "display:initial;");
					$("#copy_conflict_button").attr("href", conflict_copy_target);
				}else{
					$("#copy_conflict_button").attr("style", "display:none;");
				}
                $("#conflict_table").empty();
				
                for( var i = 0; i<len; i++){
					
                    var pair_id = response[i]['conflict_pair_id'];
                    var name1 = response[i]['role1'];
					var name2 = response[i]['role2'];
                    $("#conflict_table").append("<tr><td>"+pair_id+"</td><td>"+name1+"</td><td>"+name2+"</td><td><a href='conflictdelete.php?conflict_pair_id="+pair_id+"' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
	
                }
            }
        });
    });

});
</script>
	

<?php
//close connection
mysqli_close($conn);
include_once "includes/footer.php";
?>