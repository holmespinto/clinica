<?php
		require_once("./funciones_crud.php");
		header('Content-Type: application/json; charset=utf8');

		$method = $_SERVER['REQUEST_METHOD'];
		switch ($method) {
		case 'GET':
						$conexion=conectarse();
						$output=listar_medicamentos($_GET['descripcion'],$conexion,$method);
							echo '[';
						if (!is_null($output)) {
						echo implode(",",$output);
						}
							echo ']';			
			$conexion->close();
			break;
			
		case 'POST':
			
///
			
		case 'PUT':	
			parse_str(file_get_contents("php://input"), $_PUT);
        $sets_medi = "
            descripcion = '".$_PUT['descripcion']."'";
			$conexion=conectarse();
			$drug_id=$_PUT['drug_id'];
			actualizar('drug_id',$drug_id,$sets_medi,'farmacia_medicamentos',$conexion);	
        
		
		$sets_precio = "
            precio_real     = '".$_PUT['precio_real']."',
            precio_com  	= '".$_PUT['precio_com']."',
            precio_unidad   = '".$_PUT['precio_unidad']."'";
			$id_precio=$_PUT['id_precio'];
			
			actualizar('id',$id_precio,$sets_precio,'farmacia_medicamentos_precios',$conexion);
					
					
						$output=listar_medicamentos($_PUT['drug_id'],$conexion,$method);
							echo '[';
						if (!is_null($output)) {
						echo implode(",",$output);
						}
						echo ']';			
			$conexion->close();
		break;
		 
		case 'DELETE':	
			 parse_str(file_get_contents("php://input"), $_DELETE);

			$conexion=conectarse();		   
			borrar('drug_id',$_DELETE["drug_id"],'farmacia_medicamentos',$conexion);
			borrar('id',$_DELETE["id_precio"],'farmacia_medicamentos_precios',$conexion);
			
			$output=listar_medicamentos($_DELETE['proverdor_id'],$conexion,$method);
				echo '[';
				if (!is_null($output)) {
					echo implode(",",$output);
				}
				echo ']';
		$conexion->close();									
		break;
 
		}	
		
				 

		
		



 
?>