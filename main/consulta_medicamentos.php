<?php

header('Content-Type: application/json; charset=utf8');
		
 
function conectarse() {
								$ServerName = "localhost";
								$Username = "autoeval_siat";
								$Password = "78+E75RDgC[jqm";
								$NameBD = "autoeval_openemr";

								// Creamos la conexión con MySQL
								$conexion = new mysqli($ServerName, $Username, $Password, $NameBD);
return $conexion ;
}


		$html = '';
 
		switch ($_POST["action"]) {
		case 'reset':
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
					 
			$conexion->close();
			echo $html;
			break;
			case 'fetch_order':
		$html = '
		<table class="table table-striped table-bordered">
			<tr>
				<th>Item Name</th>
				<th>Quantity</th>
				<th>Rate</th>
				<th>Amount</th>
				<th>Action</th>
			</tr>
		';			
				$conexion=conectarse();
				$sql="SELECT * FROM farmacia_medicamentos WHERE proverdor_id='".$_POST['order_id']."' ORDER BY drug_id ASC";	
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($table = $rows->fetch_assoc()) {
									$html .= '
										<tr>
											<td>'.$row["descripcion"].'</td>
											<td><input type="number" class="form-control product_quantity" data-item_id="'.$row["precio_real"].'" data-order_id="'.$row["precio_real"].'" data-rate="'.$row["precio_real"].'"  value="'.$row["precio_real"].'" /></td>
											<td><input type="number" class="form-control product_quantity" data-item_id="'.$row["precio_com"].'" data-order_id="'.$row["precio_com"].'" data-rate="'.$row["precio_com"].'"  value="'.$row["precio_com"].'" /></td>
											<td><input type="number" class="form-control product_quantity" data-item_id="'.$row["precio_com"].'" data-order_id="'.$row["precio_unidad"].'" data-rate="'.$row["precio_unidad"].'"  value="'.$row["precio_unidad"].'" /></td>
											<td>'. $row["mes"].'</td>
											<td><button type="button" name="remove" class="btn btn-danger btn-sm remove_item" data-item_id="'.$row["drug_id"].'" data-order_id="'.$row["drug_id"].'"><i class="fas fa-minus-square"></i></button></td>
										</tr>
										';
									}
								}
				$html .= '
				</table>
				';								
			$conexion->close();
			echo $html;			
			break;	
            }

				 

	 
 
?>