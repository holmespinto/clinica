<?php
require_once("./funciones_crud.php");
header('Content-Type: application/json; charset=utf8');
		

		$method = $_SERVER['REQUEST_METHOD'];
		switch ($method) {
		case 'GET':
						$conexion=conectarse();
						$output=listar_proveedores($_GET['proverdor_id'],$conexion,$method);
							echo '[';
						if (!is_null($output)) {
						echo implode(",",$output);
						}
							echo ']';			
			$conexion->close();
			break;
			
		case 'POST':
			
			$conexion=conectarse();	
			$campos='nombre, nit, celular, direccion';
			$valores="'".$_POST['nombre']."','".$_POST['nit']."','".$_POST['celular']."','".$_POST['direccion']."'";
			crear($campos,$valores,'proveedores_medicamentos',$conexion);
					  
					  $output=listar_proveedores($_POST['proverdor_id'],$conexion,$method);
					   echo '[';
						if (!is_null($output)) {
						echo implode(",",$output);
						}
						echo ']';

			$conexion->close();
			break;				
		case 'PUT':	
			parse_str(file_get_contents("php://input"), $_PUT);
       
	   $sets = "
			nombre    = '".$_PUT['nombre']."',
            nit     = '".$_PUT['nit']."',
            celular     = '".$_PUT['celular']."',
            direccion  	= '".$_PUT['direccion']."'";
			
			actualizar('id',$_PUT['id'],$sets,'proveedores_medicamentos',$conexion);
					  
					  $output=listar_proveedores($_PUT['proverdor_id'],$conexion,$method);
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
			borrar('id',$_DELETE["id"],'proveedores_medicamentos',$conexion);		
			$output=listar_proveedores($_DELETE['proverdor_id'],$conexion,$method);
				echo '[';
				if (!is_null($output)) {
					echo implode(",",$output);
				}
				echo ']';
		$conexion->close();									
		break;
		 
		}	
		
				 

		
		

 
?>