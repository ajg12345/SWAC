<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
include_once 'includes/dbh.inc.php';
//$prod_id = 1;
//get full rehearsal list:
$sql = "SELECT  re.re_id as re_id,
				loc.building as building, 
				loc.room as room, 
				pro.description as production, 
				re.perf_dt as perf_dt, 
				re.start_time as start_time, 
				re.end_time as end_time,
				re.is_performance
				FROM rehearsals as re
				join productions as pro on re.prod_id = pro.prod_id
				join locations as loc on re.location_id = loc.location_id
				order by re.perf_dt, re.start_time asc";
$result = mysqli_query($conn, $sql);
	
?>

<div class= "grid">
	<div class="title"><h1>Joffrey Ballet Rehearsal and Performance Calendar</h1></div>
	<div class="content"> 
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
					<div class="page-header clearfix">
						<h3>Select Month and Year</h3>
						<select id="month_year_select" class="form-control">
							<?php //the add new conflict button below will have to pass a get to the conflict create page link.
							$sql_month_year_list = "SELECT 
													distinct
													concat(month(perf_dt),' ',year(perf_dt)) as date_id
													from rehearsals
													order by month(perf_dt), year(perf_dt) asc";
							$result_month_year_list = mysqli_query($conn, $sql_month_year_list);
							echo '<option value=0>all</option>';
							while($date_row = mysqli_fetch_array($result_month_year_list)){
								echo '<option value="' . $date_row['date_id'] . '">' . $date_row['date_id'] .'</option>';
							}
							mysqli_free_result($result_month_year_list);	
							?>
						</select>
						<br>
					</div>
					<?php
					// Attempt select query execution
						echo "<table class='table table-bordered table-striped'>";
							echo "<thead>";
								echo "<tr>";
									echo "<th>ID</th>";
									echo "<th>P. or R.</th>";
									echo "<th>Building</th>";
									echo "<th>Room</th>";
									echo "<th>Production</th>";
									echo "<th>Date</th>";
									echo "<th>Start Time</th>";
									echo "<th>End Time</th>";
									echo "<th>Casting</th>";
								echo "</tr>";
							echo "</thead>";
							echo "<tbody id='rehearsal_table'>";
							while($row = mysqli_fetch_array($result)){
								echo "<tr>";
									echo "<td>" . $row['re_id'] . "</td>";
									if(strcmp($row['is_performance'],1)==0){echo "<td><img src='img/Performancesmall.png'></td>";}else{echo "<td><img src='img/Rehearsalsmall.png'></td>";}
									echo "<td>" . $row['building'] . "</td>";
									echo "<td>" . $row['room'] . "</td>";
									echo "<td>" . $row['production'] . "</td>";
									echo "<td>" . date("m-d-Y", strtotime($row['perf_dt'])) . "</td>";
									echo "<td>" . date("g:i a", strtotime($row['start_time'])) . "</td>";
									echo "<td>" . date("g:i a", strtotime($row['end_time'])) . "</td>";
									echo "<td>";
										echo "<a href='castings.php?re_id=". $row['re_id'] ."' title='View Casting' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
									echo "</td>";
								echo "</tr>";
							}	
							mysqli_free_result($result);
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

    $("#month_year_select").change(function(){
        var date_id = $(this).val();
		
        $.ajax({
            url: 'getcalendar.php',
            type: 'post',
            data: {date_id:date_id},
            dataType: 'json',
            success:function(response){

                var len = response.length;

                $("#rehearsal_table").empty();
				
                for( var i = 0; i<len; i++){
					var re_id = response[i]['re_id'] ;
					if(response[i]['is_performance'] == 1){
						var is_performance = "<img src='img/Performancesmall.png'>";
					}else{
						var is_performance = "<img src='img/Rehearsalsmall.png'>";
					}
					
					var building = response[i]['building'];
					var room = response[i]['room'];
					var production = response[i]['production'];
					var perf_dt = response[i]['perf_dt'];
					var start_time = response[i]['start_time'];
					var end_time = response[i]['end_time'];
					
					
					
					var table_contents = "<tr><td>"+re_id+"</td><td>"+is_performance+"</td><td>"+building+"</td><td>"+room+"</td>"+
											"<td>"+production+"</td><td>"+perf_dt+"</td><td>"+start_time+"</td><td>"+end_time+"</td><td>"+
											"<a href='castings.php?re_id="+re_id+"' title='View Casting' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>"+
											"</td></tr>";
                    $("#rehearsal_table").append(table_contents);
	
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