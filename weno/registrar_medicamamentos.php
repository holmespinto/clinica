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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Farmacia</title>

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
	<h1 class="h3 mb-4 text-gray-800">Registrar Medicamento</h1>

                    <div class="row">
                        <div class="col col-sm-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">Proveedores</div>
                                <div class="card-body" id="table_status"></div>
                            <?php
 function conectarse() {
								$ServerName = "localhost";
								$Username = "autoeval_siat";
								$Password = "78+E75RDgC[jqm";
								$NameBD = "autoeval_openemr";

								// Creamos la conexión con MySQL
								$conexion = new mysqli($ServerName, $Username, $Password, $NameBD);
return $conexion ;
}                               	
									$conexion=conectarse();
									$sql="SELECT * FROM proveedores_medicamentos WHERE status='Enable' ORDER BY id ASC";	
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($table = $rows->fetch_assoc()) {
									$html .= '
									<button type="button" name="table_button" id="table_'.$table["id"].'" class="btn btn-warning mb-4 table_button" data-index="'.$table["id"].'" data-order_id="'.$table["id"].'" data-table_name="Table '.utf8_decode($table["nombre"]).'">'.utf8_decode($table["nombre"]).'<br />Proveedor '.utf8_decode($table["nombre"]).' </button>';										
									}
								}
		//					
					 echo $html;
			$conexion->close();
                                ?>								
                                
                            </div>
                        </div>
                        <div class="col col-sm-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">Medicamentos</div>
                                <div class="card-body">
                                    <div class="table-responsive" id="order_status">

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
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">Add Item</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <span id="form_message"></span>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_name" id="category_name" class="form-control" required data-parsley-trigger="change">
                            <option value="">Select Category</option>
                             
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product Name</label>
                        <select name="product_name" id="product_name" class="form-control" required>
                            <option value="">Select Product</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <select name="product_quantity" id="product_quantity" class="form-control" required>
                            <?php
                            for($i = 1; $i < 25; $i++)
                            {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="hidden_table_id" id="hidden_table_id" />
                    <input type="hidden" name="hidden_order_id" id="hidden_order_id" />
                    <input type="hidden" name="hidden_product_rate" id="hidden_product_rate" />
                    <input type="hidden" name="hidden_table_name" id="hidden_table_name" />
                    <input type="hidden" name="action" id="action" value="Add" />
                    <input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
                <?php
                include('footer.php');
                ?>

</body>
</html>
<script>

$(document).ready(function(){

    reset_table_status();

    setInterval(function(){
        reset_table_status();
    }, 10000);

    function reset_table_status()
    {
        $.ajax({
            url:"consulta_medicamentos.php",
            method:"POST",
            data:{action:'reset'},
            success:function(data){
				 //$('#table_status').replaceWith($('#table_status',data));
				 $('#table_status').html(data);
				  
            }
        });
    }

    function fetch_order_data(order_id)
    {
        $.ajax({
            url:"consulta_medicamentos.php",
            method:"POST",
            data:{action:'fetch_order', order_id:order_id},
            success:function(data)
            {
                $('#order_status').html(data);
            }
        });
    }
    

    $(document).on('change', '#category_name', function(){
        var category_name = $('#category_name').val();
        if(category_name != '')
        {
            $.ajax({
                url:"order_action.php",
                method:"POST",
                data:{action:'load_product', category_name:category_name},
                success:function(data)
                {
                    $('#product_name').html(data);
                }
            });
        }
    });

    $(document).on('change', '#product_name', function(){
        var rate = $('#product_name').find(':selected').data('price');
        $('#hidden_product_rate').val(rate);
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
        fetch_order_data(order_id);
    });

    $('#product_form').parsley();

    $('#order_form').on('submit', function(event){
        event.preventDefault();
        if($('#order_form').parsley().isValid())
        {
            $.ajax({
                url:"order_action.php",
                method:"POST",
                data:$(this).serialize(),
                beforeSend:function(){
                    $('#submit_button').attr('disabled', 'disabled');
                    $('#submit_button').val('Wait...');
                },
                success:function(data)
                {
                    $('#submit_button').attr('disabled', false);
                    $('#submit_button').val('Add');
                    $('#'+button_id).addClass('btn-primary');
                    $('#'+button_id).removeClass('btn-secondary');
                    $('#order_form')[0].reset();
                    $('#orderModal').modal('hide');
                    fetch_order_data(data);
                }
            }); 
        }
    });

    $(document).on('change', '.product_quantity', function(){
        var quantity = $(this).val();
        var item_id = $(this).data('item_id');
        var order_id = $(this).data('order_id');
        var rate = $(this).data('rate');
        $.ajax({
            url:"order_action.php",
            method:"POST",
            data:{order_id:order_id, item_id:item_id, quantity:quantity, rate:rate, action:'change_quantity'},
            success:function(data)
            {
                fetch_order_data(order_id);
            }
        });
    });

    $(document).on('click', '.remove_item', function(){
        if(confirm("Are you sure you want to remove it?"))
        {
            var item_id = $(this).data('item_id');
            var order_id = $(this).data('order_id');
            $.ajax({
                url:"order_action.php",
                method:"POST",
                data:{order_id:order_id, item_id:item_id, action:'remove_item'},
                success:function(data)
                {
                    fetch_order_data(order_id);
                }
            });
        }
    });

});

</script>
 
