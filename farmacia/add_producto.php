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

 
require_once("./funciones_crud.php");
?>

 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>add Medicamentos</title>
<?php
require_once("./encabezado.php");
?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="card-header card-secondary">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row sm-12">
          <div class="col-sm-12">
               <a class="btn btn-app" href="productos.php">
                  <i class="far fa-arrow-alt-circle-left"></i>&nbsp;&nbsp;Regresar
               </a>			   
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
	</nav>
	</div>
	</br>
	</br>
 <div class="container">
    	<span id="message"></span>
    	<form id="sample_form">
		   	<div class="card card-info">
		    	<div class="card-header">Formulario para el registro de medicamentos</div>
		    	<div class="card-body">
		    		<div class="form-group">
	    				<label>Proveedor <span class="text-danger">*</span></label>
                        <select name="proverdor_id" id="proverdor_id" class="form-control form_data" style="width:90%" >
                            <option value="">Seleccione Proveedor</option>
                            <?php
							
				                              	
									$conexion=conectarse();
									$sql="SELECT * FROM proveedores_medicamentos WHERE status='Enable' ORDER BY id ASC";	
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($row = $rows->fetch_assoc()) {
									$html .= '
									<option value="'.$row["id"].'">'.utf8_encode($row['nombre']).'</option>';										
									}
								}
		//					
								echo $html;
							$conexion->close();
			
                                ?>	                             
                        </select>						
	    				<span id="proverdor_error" class="text-danger"></span>
	    			</div>
	    			<div class="form-group">
	    				<label>Descripcion <span class="text-danger">*</span></label>
	    				<textarea name="descripcion" id="descripcion" cols="40" rows="5" class="form-control form_data"></textarea>
	    				<span id="descripcion_error" class="text-danger"></span>
	    			</div>
 
		    	</div>
		    	<div class="card-footer">
		    		<button type="button" name="submit" id="submit" class="btn btn-primary" onclick="save_data(); return false;">Registrar Medicamento</button>
<script>
  function save_data()
{
	var form_element = document.getElementsByClassName('form_data');
	var form_data = new FormData();
	for(var count = 0; count < form_element.length; count++)
	{
		form_data.append(form_element[count].name, form_element[count].value);
	}
	document.getElementById('submit').disabled = true;
	var ajax_request = new XMLHttpRequest();
	ajax_request.open('POST', 'process_data_med.php');
	ajax_request.send(form_data);
	ajax_request.onreadystatechange = function()
	{
		if(ajax_request.readyState == 4 && ajax_request.status == 200)
		{
			document.getElementById('submit').disabled = false;
			var response = ajax_request.responseText;
			  var obj = JSON.stringify(response);
			 
			if(obj.success != '')
			{
				document.getElementById('sample_form').reset();
				document.getElementById('message').innerHTML ='REGISTRO GUARDADO CON EXITO!!';
				setTimeout(function(){
					document.getElementById('message').innerHTML = '';
				}, 5000);
				document.getElementById('proverdor_error').innerHTML = '';
				document.getElementById('descripcion_error').innerHTML = '';
			}else{
				//display validation errorsuccess
				document.getElementById('proverdor_error').innerHTML = obj.proveedor_error;
				document.getElementById('descripcion_error').innerHTML = obj.descripcion_error;
			}
		}
	}
}
</script>		    	
				</div>
		    </div>
		</form>
		<br />
		<br />
    </div>
    </div>
    </div>
    </div>
 
                <?php
                include('footer.php');
                ?>

 
</body>
</html>
 

