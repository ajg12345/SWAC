<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
include_once 'includes/dbh.inc.php';
//$prod_id = 1;

?>

<div class= "grid">
	<div class="title"><h1>Joffrey Ballet Roles</h1></div>
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
						<a id="copy_roles_button" href="rolesCopy.php" class="btn btn-success pull-right" style="display: none;">Copy Roles to Production without Roles</a>
						<a id="create_role_button" href="rolecreate.php" class="btn btn-success pull-right" style="display: none;">Add New Role to this Production</a>
					</div>
					<?php
					// Attempt select query execution
						echo "<table class='table table-bordered table-striped'>";
							echo "<thead>";
								echo "<tr>";
									echo "<th>Prod ID</th>";
									echo "<th>Production</th>";
									echo "<th>Role ID</th>";
									echo "<th>Role</th>";
									echo "<th>Role Count</th>";
									echo "<th></th>";
								echo "</tr>";
							echo "</thead>";
							echo "<tbody id='role_table'>";
							
								echo "<tr>";
									echo "<td>Nothing selected</td>";
									echo "<td>Nothing selected</td>";
									echo "<td>Nothing selected</td>";
									echo "<td>Nothing selected</td>";
									echo "<td>Nothing selected</td>";
									echo "<td></td>";
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
		
		var create_role_target = "rolecreate.php?prod_id=" + prod_id;
		var copy_role_target = "rolesCopy.php?prod_id=" + prod_id;
		$("#create_role_button").attr("href", create_role_target);
		$("#create_role_button").attr("style", "display:initial;");
		$("#default_option").attr("style", "display:none;");
        $.ajax({
            url: 'getroles.php',
            type: 'post',
            data: {prod_id:prod_id},
            dataType: 'json',
            success:function(response){

                var len = response.length;
				if(len > 0){
					$("#copy_roles_button").attr("style", "display:initial;");
					$("#copy_roles_button").attr("href", copy_role_target);
				}else{
					$("#copy_roles_button").attr("style", "display:none;");
				}
                $("#role_table").empty();
				
                for( var i = 0; i<len; i++){
					var prod_id = response[i]['prod_id'];
					var production = response[i]['production'];
                    var role_id = response[i]['role_id'];
                    var role = response[i]['role'];
					var role_count = response[i]['role_count'];
                    $("#role_table").append("<tr><td>"+prod_id+"</td><td>"+production+"</td><td>"+role_id+"</td><td>"+role+"</td><td>"+role_count+"</td><td><a href='roledelete.php?role_id="+role_id+"' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a><a href='roleupdate.php?role_id="+role_id+"' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a></td></tr>");
	
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