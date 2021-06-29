<?php

require_once("./funciones_crud.php");
header('Content-Type: application/json; charset=utf8');

			$success  = '';
			$descripcion_error = '';
			$proverdor_error = '';			
			
			$conexion=conectarse();	
			$descripcion = $_POST["descripcion"];
			$proverdor_id = $_POST["proverdor_id"];
			if(empty($descripcion))
				{
					$descripcion_error = 'Descripcion is Required Field';
				}
			if(empty($proverdor_id))
				{
					$proverdor_error = 'ID is Required Field';
				}				
			
			$campos='proverdor_id, descripcion';
			$valores="'".$proverdor_id."','".$descripcion."'";
			crear($campos,$valores,'farmacia_medicamentos',$conexion);
			
			$success = '<div class="alert alert-success">Data Saved</div>';
			
			$output = array(
				'success'		=>	$success,
				'descripcion_error'	=>	$descripcion_error,
				'proverdor_error'	=>	$proverdor_error
			);			
			
			echo json_encode($output);
			$conexion->close();
			 

 ?>