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
 include('header.php');
 
require_once("./funciones_crud.php");

	$conexion=conectarse();	
	$Proveedor= NombresProveedores($_GET['proverdor_id'],$conexion);
	$color= ColorProveedores($_GET['proverdor_id'],$conexion);
?>

 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Compras</title>

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row sm-12">
          <div class="col-sm-12">
               <a class="btn btn-app" href="compras.php">
                  <i class="far fa-arrow-alt-circle-left"></i>&nbsp;&nbsp;Regresar
               </a>			   
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
                    <div class="row">
                        <div class="col col-sm-2">
                            <div class="card shadow mb-2">
                                <div class="card-header py-3">Proveedores</div>
	
                            <?php
							
				                              	
									$conexion=conectarse();
									$sql="SELECT * FROM proveedores_medicamentos WHERE status='Enable' ORDER BY id ASC";	
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($table = $rows->fetch_assoc()) {
									$html .= '
									<button type="button" style="background-color:'.$table["color"].'; color:#000" name="table_button" id="table_'.$table["id"].'" class="btn  mb-4 table_button" data-index="'.$table["id"].'" data-order_id="'.$table["id"].'" data-table_name="Table '.utf8_encode($table["nombre"]).'">'.utf8_encode($table["nombre"]).' </button>';										
									}
								}
		//					
					 echo $html;
			$conexion->close();
			
                                ?>								
                                
                            </div>
                        </div>
                        <div class="col col-sm-10">
                            <div class="card shadow mb-4">
                                <div style="background-color:<?php echo $color;?>;color:#000;" class="card-header py-3">Registrar compra del proveedor <?php echo utf8_encode($Proveedor); ?></div>
                                <div class="card-body">
                                    
                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Lista de Medicamentos</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_category" id="add_category" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered display" id="order_status" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Descripcion</th>
                                            <th>Precio Real</th>
                                            <th>Precio Comercial</th>
                                            <th>Precio Unidad</th>
                                            <th>Unidades</th>
                                            <th>Fecha Registro</th>
                                            <th>Status</th>
                                            <th>Action</th>
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
<div id="orderModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="order_form">
            <div class="modal-content">
                <div class="modal-header" style="background-color:<?php echo $color;?>;color:#000;">
                    <h4 class="modal-title" id="modal_title">Registrar Compra</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    
                    <div class="form-group">
                        <span id="form_message"></span>
                        <select name="drug_id" id="drug_id" class="form-control" required data-parsley-trigger="change">
                            <option value="">Select Medicamento</option>
                            <?php
							
				                              	
									$conexion=conectarse();
									$sql="SELECT * FROM farmacia_medicamentos WHERE proverdor_id='".$_GET['proverdor_id']."' AND status='Enable' ORDER BY drug_id ASC";	
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($row = $rows->fetch_assoc()) {
									$html .= '
									<option value="'.$row["drug_id"].'">'.utf8_encode($row['descripcion']).'</option>';										
									}
								}
		//					
								echo $html;
							$conexion->close();
			
                                ?>	                             
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Precio Real</label>
						<input type="text" name="precio_real" class="form-control" id="precio_real" required data-parsley-type="number" data-parsley-trigger="keyup"value=""/>
                    </div>
                    <div class="form-group">
                        <label>Precio Comercial</label>
						<input type="text" name="precio_com" class="form-control" id="precio_com" value="" required data-parsley-type="number" data-parsley-trigger="keyup"/>
                    </div>
					<div class="form-group">
                        <label>Precio Unidad</label>
						<input type="text" name="precio_unidad" class="form-control" id="precio_unidad" value="" required data-parsley-type="number" data-parsley-trigger="keyup"/>
                    </div>
					<div class="form-group">
                        <label>Unidades</label>
						<input type="text" name="total_ingreso" class="form-control" id="total_ingreso" value="" required data-parsley-type="number" data-parsley-trigger="keyup"/>
                    </div>					
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="proverdor_id" id="proverdor_id" value="<?php echo $_GET['proverdor_id']; ?>"/>
                    <input type="hidden" name="action" id="action" value="Add" />
                    <input type="hidden" name="hidden_id_precio" id="hidden_id_precio" value="" />
                    <input type="hidden" name="hidden_drug_id" id="hidden_drug_id" value="" />
                    <input type="hidden" name="hidden_id_inventario" id="hidden_id_inventario" value="" />
                    <input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
                <?php
                include('footer.php');
                ?>

 
</body>
</html>
<script>

$(document).ready(function(){


	var dataTable = $('#order_status').DataTable({
		"processing" : true,
		"serverSide" : true,
        "scrollY":    "200px",
        "scrollCollapse": true,
        "paging":     true,
		"order" : [],
		"ajax" : {
			url: "crud_precios.php",
			type:"POST",
			data:{action:'fetch',order_id:$("#proverdor_id").val()}
		},
		"columnDefs":[
			{
				"targets":[2],
				"orderable":false,
			},
		],
	});	
 
 
 
    var button_id = $(this).attr('id');

    $(document).on('click', '.table_button', function(){        
        var table_id = $(this).data('index');
        $('#hidden_table_id').val(table_id);
        $('#hidden_table_name').val($(this).data('table_name'));
        $('#orderModal').modal('show');
        $('#order_form')[0].reset();
        $('#order_form').parsley().reset();
        $('#submit_button').attr('disabled', false);
        $('#submit_button').val('Add');
        var order_id = $(this).data('order_id');
        $('#hidden_order_id').val(order_id);
        //fetch_order_data(order_id);
		 window.location.href = './add_compra.php?proverdor_id='+order_id+'';
    });	
	 
	$('#add_category').click(function(){
		
		$('#order_form')[0].reset();
		$('#order_form').parsley().reset();
    	$('#modal_title').text('Add Data');
    	$('#action').val('Add');
    	$('#submit_button').val('Add');
    	$('#orderModal').modal('show');
    	$('#form_message').html('');

	});

	$('#order_form').parsley();

	$('#order_form').on('submit', function(event){
		event.preventDefault();
		if($('#order_form').parsley().isValid())
		{		
			$.ajax({
				url:"crud_precios.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:'json',
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#orderModal').modal('hide');
					$('#submit_button').attr('disabled', false);
          			$('#message').html(data);
          			setTimeout(function(){
            			$('#message').html('');
          			}, 5000);
        		  window.location.href = './add_compra.php?proverdor_id='+$("#proverdor_id").val()+'';
				}
			})
		}
	});

    $(document).on('click', '.edit_button', function(){

		var drug_id = $(this).data('id');
		$('#order_form').parsley().reset();
		$('#order_form')[0].reset();
		$('#form_message').html('');
		$.ajax({
	      	url:"crud_precios.php",
	      	method:"POST",
	      	data:{drug_id:drug_id, action:'fetch_single'},
	      	dataType:'JSON',
	      	success:function(data)
	      	{
	        	$('#id_precio').val(data.id_precio);
                $('#precio_real').val(data.precio_real);
                $('#precio_com').val(data.precio_com);
                $('#precio_unidad').val(data.precio_unidad);
                $('#total_ingreso').val(data.total_ingreso);
	        	$('#modal_title').text('Edit Data');
	        	$('#action').val('Edit');
	        	$('#submit_button').val('Edit');
	        	$('#orderModal').modal('show');
	        	$('#hidden_id_precio').val(data.id_precio);
	        	$('#hidden_drug_id').val(data.drug_id);
	        	$('#hidden_id_inventario').val(data.id_inventario);
				$('#form_message').html(data.nom_medicamento);
	        	 
	      	}
	    })
	});

	$(document).on('click', '.status_button', function(){
		var id = $(this).data('id');
    	var status = $(this).data('status');
		var next_status = 'Enable';
		if(status == 'Enable')
		{
			next_status = 'Disable';
		}
		if(confirm("Are you sure you want to "+next_status+" it?"))
    	{
      		$.ajax({
        		url:"crud_precios.php",
        		method:"POST",
        		data:{id:id, action:'change_status', status:status, next_status:next_status},
        		success:function(data)
        		{
          			$('#message').html(data);
          			dataTable.ajax.reload();
          			setTimeout(function(){
            			$('#message').html('');
          			}, 5000);
					 window.location.href = './add_compra.php?proverdor_id='+$("#proverdor_id").val()+'';
        		}
      		})
    	}
	});

	$(document).on('click', '.delete_button', function(){
    	var id = $(this).data('id');
    	if(confirm("Are you sure you want to remove it?"))
    	{
      		$.ajax({
        		url:"crud_precios.php",
        		method:"POST",
        		data:{id:id, action:'delete'},
        		success:function(data)
        		{
          			$('#message').html(data);
          			//dataTable.ajax.reload();
          			setTimeout(function(){
            			$('#message').html('');
          			}, 5000);
					window.location.href = './add_compra.php?proverdor_id='+$("#proverdor_id").val()+'';
        		}
      		})
    	}
  	});

});

</script>
 
