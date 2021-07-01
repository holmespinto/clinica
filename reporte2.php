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
use OpenEMR\OeUI\OemrUI;

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
  <?php Header::setupHeader('common'); ?>
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

    <section>
    <div class="card">
          <div class="card-header">
            <div class="col-md-12"><h3 class="card-title"></h3>Estadísticos de ventas diarias</div>
          </div>
        </div>
    </section>

    <section class="content">
      <div class="row sm-12">
      <div class="card">
        <div class="card-header">
          
                    <div class="row">
                        <div class="col-md-4"><h3 class="card-title">Consultar por Gráficas</h3></div>
                        <div class="col-md-8">
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
              </div>
        </div>
        </section>
        <section>
        <div class="card-body">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12">
                  <div class="card mt-12">
                    <div class="card-header"></div>
                    <div class="card-body">			
                      <div class="panel-body">
                        <div id="chart_area" style="width: 1000px; height: 620px;"></div>
                      </div>
                    </div>
                  </div>
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
      </div>

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
        data:{mes:mes,action:'fetch_repor2'},
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
     data.addColumn('string', 'Nombre');  
     data.addColumn('number', 'Total');
    $.each(jsonData, function(i, jsonData){
        var Nombre = jsonData.Nombre;
        var Total = parseFloat($.trim(jsonData.Total));
        data.addRows([[Nombre,Total,]]);
    });
    var options = {
        title:chart_main_title,
        hAxis: {
            title: "Nombre"
        },
        vAxis: {
            title: 'Total'
        }
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_area'));
    chart.draw(data, options);
}

 
 
    $('#mes').change(function(){
        var mes = $(this).val();
        if(mes != '')
        {
        load_monthwise_data(mes, 'Estadisticos del mes');
        dlgopen('<?php echo $GLOBALS['webroot'] . "/interface/farmacia/" ?>reporte_ventas_dias.php?mes='+ mes +'&csrf_token_form=<?php echo attr(CsrfUtils::collectCsrfToken()); ?>', '_blank',900, 1024);
        }
    });
 
 
</script>

	 

