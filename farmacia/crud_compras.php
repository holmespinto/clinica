<?php

require_once("./funciones_crud.php");
header('Content-Type: application/json; charset=utf8');


		$method = $_SERVER['REQUEST_METHOD'];
		switch ($method) {
		case 'GET':
						$conexion=conectarse();
						$output=listar_compras($_GET['medicamento'],$conexion,$method);
						echo '[';
						if (!is_null($output)) {
						echo implode(",",$output);
						}
							echo ']';			
			$conexion->close();
			break;
			
		case 'PUT':	
			parse_str(file_get_contents("php://input"), $_PUT);
			$conexion=conectarse();	
			$sets = "total_ingreso  = '".$_PUT['total_ingreso']."'";			 
			actualizar('id',$_PUT['id'],$sets,'farmacia_medicamentos_precios_inventario',$conexion);
			$conexion->close();

		break;
		 
		case 'DELETE':	
			 parse_str(file_get_contents("php://input"), $_DELETE);
			$conexion=conectarse();		   
		
										$sqli="SELECT * FROM farmacia_medicamentos_precios_inventario WHERE precio_id='".$_DELETE["id"]."'";	
										$row = $conexion->query($sqli);
									if ($row->num_rows > 0) {
										// Salida de la consulta mediante el ciclo WHILE
										while($p = $row->fetch_assoc()) {
										$inventario_id=$p['id'];
											borrar('id',$inventario_id,'farmacia_medicamentos_precios_inventario',$conexion);	
										}
									}								
				
			borrar('id',$_POST["id"],'farmacia_medicamentos_precios',$conexion);			
			$output=listar_compras($_DELETE['proverdor_id'],$conexion,$method);
				echo '[';
				if (!is_null($output)) {
					echo implode(",",$output);
				}
				echo ']';
		$conexion->close();									
		break;
		 
		}	
		
				 

		
		

 
?>