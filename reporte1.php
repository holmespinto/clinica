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
 <link href="./vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
 <link href="./css/sb-admin-2.min.css" rel="stylesheet">
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
               <a class="btn btn-app" href="reporte.php">
                  <i class="fas fa-home"></i>Inicio
               </a><a class="btn btn-app" href="reporte1.php">
                  <i class="fas fa-chart-pie"></i>Total venta por Meses 
               </a><a class="btn btn-app" href="reporte2.php">
                  <i class="fas fa-chart-line"></i>Total venta por Dia 
               </a>
               
               <?php
               /*
                <a class="btn btn-app" href="view.php">
                  <i class="fas fa-chart-line"></i>Total venta por Dia 
               </a><a class="btn btn-app" href="view.php">
                 <i class="fas fa-chart-area"></i>Productos Entregados 
               </a><a class="btn btn-app" href="view.php">
                 <i class="fas fa-chart-bar"></i>Venta por Unidad 
               </a>
			          */
                ?>
			   
          </div>
        </div>
      </div><!-- <i class="fad fa-chart-scatter"></i> -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="card">
        <div class="card-header">
          
                    <div class="row">
                        <div class="col-md-3">
<h3 class="card-title">Total venta por Meses</h3>
                        </div>
						<div class="col-md-3">

                        </div>
                        <div class="col-md-3">
                            <select name="mes" class="form-control" id="mes">
                                <option value="">Seleccione el mes</option>
                            <?php
                                echo '<option value="1">Enero</option>';
                                echo '<option value="2">Febrero</option>';
                                echo '<option value="3">Marzo</option>';
                                echo '<option value="4">Abril</option>';
                                echo '<option value="5">Mayo</option>';
                                echo '<option value="6">Junio</option>';
                                echo '<option value="7">Julio</option>';
                                echo '<option value="8">Agosto</option>';
                                echo '<option value="9">Septiembre</option>';
                                echo '<option value="10">Octubre</option>';
                                echo '<option value="11">Noviembbre</option>';
                                echo '<option value="12">Diciembre</option>';
                            ?>
                            </select>
                        </div>
                    </div>		  
        </div>
        <!-- /.card-header -->
        <div class="card-body">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-6">
					<div class="card mt-6">
						<div class="card-header"></div>
						<div class="card-body">			
							<div class="panel-body">
								<div id="chart_area" style="width: 1000px; height: 620px;"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="card mt-6">
						<div class="card-header"></div>
						<div class="card-body">
						  <div class="container box">
						   <br />
							<div class="table-responsive">
                                <table class="table table-bordered display" id="order_status" width="100%" cellspacing="0">
									<thead>
										<tr>
											<th></th>
											<th>Especialista</th>
											<th>Total</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th></th>
											<th></th>
											<th></th>
										</tr>
									</tfoot>
								</table>						
	 
						   </div>
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

/*
$id_desc=explode(',',$_POST["ids"], -1);
foreach ($id_desc as $key => $value) :
*/
?>

</body>
</html>
<link href="./vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="./vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="./vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script	src="./js/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback();

function load_monthwise_data(mes, title)
{
    var temp_title = title + ' '+mes+'';
    $.ajax({
        url:"crud_reporte.php",
        method:"POST",
        data:{mes:mes,action:'fetch_repor1'},
        dataType:"JSON",
        success:function(data)
        {
            drawMonthwiseChart(data, temp_title);
        }
    });
}

function drawMonthwiseChart(chart_data, chart_main_title)
{
    
	var jsonData = chart_data;
    var data = new google.visualization.DataTable();
     data.addColumn('string', 'Proveedor');  
     data.addColumn('number', 'Total');
    $.each(jsonData, function(i, jsonData){
        var proveedor = jsonData.proveedor;
        var Total = parseFloat($.trim(jsonData.Total));
        data.addRows([[proveedor,Total]]);
    });
    var options = {
        title:chart_main_title,
        hAxis: {
            title: "Especialistas"
        },
        vAxis: {
            title: 'Total'
        }
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_area'));
    chart.draw(data, options);
}

</script>
<script>
  function consulta_tabla(mes)
{ 
 
 var table = $('#order_status').DataTable( {
           "ajax" : {
			url:"crud_reporte1.php",
			data:{mes:mes,action:'table_repor1'},
			type:"GET"
		   },
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            { "data": "nombre" },
            { "data": "total" }
        ],
        "order": [[1, 'asc']]
    } );
	
} 
/*	
  function consulta_tabla(mes)
{ 
  $('#customer_data').DataTable({
   "processing" : true,
   "serverSide" : true,
   "ajax" : {
    url:"crud_reporte.php",
	data:{mes:mes,action:'table_repor1'},
    type:"GET"
   },
   dom: 'lBfrtip',
   buttons: [
    'excel', 'csv', 'pdf', 'copy'
   ],
   "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
  });
}  
*/  
$(document).ready(function(){

    $('#mes').change(function(){
        var mes = $(this).val();
        if(mes != '')
        {
            load_monthwise_data(mes, 'Estadisticos del mes');
			consulta_tabla(mes);
        }
    });

});

</script>

	 

