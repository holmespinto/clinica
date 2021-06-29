<?php

/**
 * Upload and install a designated code set to the codes table.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Rod Roark <rod@sunsetsystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2014 Rod Roark <rod@sunsetsystems.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

set_time_limit(0);

require_once("../globals.php");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

if (!empty($_POST)) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
}
 

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Farmacia</title>
<?php
require_once("./funciones_crud.php");
require_once("./encabezado.php");
?>
  <!-- jsGrid -->
  <link rel="stylesheet" href="./plugins/jsgrid/jsgrid.min.css">
  <link rel="stylesheet" href="./plugins/jsgrid/jsgrid-theme.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="./dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="./dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row sm-12">
          <div class="col-sm-12">
               <a class="btn btn-app" href="view.php">
                  <i class="fas fa-home"></i>Inicio
               </a><a class="btn btn-app" href="reporte1.php">
                  <i class="fas fa-chart-pie"></i>Total venta por Meses 
               </a><a class="btn btn-app" href="view.php">
                  <i class="fas fa-chart-line"></i>Total venta por Dia 
               </a><a class="btn btn-app" href="view.php">
                 <i class="fas fa-chart-area"></i>Productos Entregados 
               </a><a class="btn btn-app" href="view.php">
                 <i class="fas fa-chart-bar"></i>Venta por Unidad 
               </a>
			   
          </div>
        </div>
      </div><!-- <i class="fad fa-chart-scatter"></i> -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Reportes</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-4">
					<div class="card mt-4">
						<div class="card-header">Total de Productos</div>
						<div class="card-body">
							<div class="chart-container pie-chart">
								<canvas id="pie_chart"></canvas>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card mt-4">
						<div class="card-header"></div>
						<div class="card-body">
							<div class="chart-container pie-chart">
								<canvas id="doughnut_chart"></canvas>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card mt-4 mb-4">
						<div class="card-header"></div>
						<div class="card-body">
							<div class="chart-container pie-chart">
								<canvas id="bar_chart"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </section>
    <!-- /.content -->
  </div>
</div>
<?php
require_once("./pie.php");
?>

</body>
</html>
<script	src="./js/Chart.bundle.min.js.js"></script>
	<script>
	
$(document).ready(function(){

 

	makechart();

	function makechart()
	{
		$.ajax({
			url:"crud_reporte.php",
			method:"POST",
			data:{action:'fetch'},
			dataType:"JSON",
			success:function(data)
			{
				var proveedor = [];
				var total = [];
				var color = [];

				for(var count = 0; count < data.length; count++)
				{
					proveedor.push(data[count].proveedor);
					total.push(data[count].total);
					color.push(data[count].color);
				}

				var chart_data = {
					labels:proveedor,
					datasets:[
						{
							label:'Vote',
							backgroundColor:color,
							color:'#fff',
							data:total
						}
					]
				};

				var options = {
					responsive:true,
					scales:{
						yAxes:[{
							ticks:{
								min:0
							}
						}]
					}
				};

				var group_chart1 = $('#pie_chart');

				var graph1 = new Chart(group_chart1, {
					type:"pie",
					data:chart_data
				});

				var group_chart2 = $('#doughnut_chart');

				var graph2 = new Chart(group_chart2, {
					type:"doughnut",
					data:chart_data
				});

				var group_chart3 = $('#bar_chart');

				var graph3 = new Chart(group_chart3, {
					type:'bar',
					data:chart_data,
					options:options
				});
			}
		})
	}

});

</script>	

