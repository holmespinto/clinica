<?php
require_once("./funciones_crud.php");
header('Content-Type: application/json; charset=utf8');

		$results=array();
 
		switch ($_POST["action"]) {

			case 'fetch':
				
				$search_query .= 'WHERE m.descripcion LIKE "%'.$_POST["search"]["value"].'%"
									AND m.proverdor_id="'.$_POST["order_id"].'"
									AND m.drug_id=i.drug_id
									AND p.id=i.proverdor_id
									AND p.id=m.proverdor_id';

					 
								$conexion=conectarse();
							    $sql="SELECT COUNT(*) AS num FROM farmacia_medicamentos";	
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($mat = $rows->fetch_assoc()) {			
									$total_rows=$mat["num"];
									}
								}			
				
				$order_column = array('descripcion', 'status');
				$output = array();
				
				$main_query="SELECT m.drug_id,m.proverdor_id,i.total_ingreso,i.total_vendidos,m.descripcion,p.nombre,i.mes  
											FROM farmacia_medicamentos_precios_inventario AS i,
											farmacia_medicamentos AS m,
											proveedores_medicamentos AS p ";

	
					$order_query = ' ORDER BY m.drug_id ASC';

				$limit_query = '';

				if($_POST["start"]==0)
				{
					$limit_query = '';
					
				}else{
					$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
									
				}
				
			
					
				$sql=$main_query . $search_query . $order_query . $limit_query ;
				 
				
				$data = array();
				$sub_array = array();
				$descripcion = array();
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($row = $rows->fetch_assoc()) {
												
												
												list($id_precio,$precio_real,$precio_com,$precio_unidad,$status)=precio_medicamentos($row['drug_id'],$_POST["order_id"],$conexion);
												if($status == 'Enable'){
												$class='primary';
												
												}else{
												$class='danger';	
												}
												
												$cantidad=UnidadesCompradas($row['drug_id'],$row['proverdor_id'],$id_precio,$conexion);
												$data[] = '["'.utf8_encode($row["descripcion"]).'","'.$precio_real.'","'.$precio_com.'","'.$precio_unidad.'","'.$cantidad.'","'.$row['mes'].'","<button type=\"button\" name=\"status_button\" class=\"btn btn-'.$class.' btn-sm status_button\" data-id=\"'.$id_precio.'\" data-status=\"'.$status.'\">'.$status.'<\/button>","\n\t\t\t<div align=\"center\">\n\t\t\t<button type=\"button\" name=\"edit_button\" class=\"btn btn-warning btn-circle btn-sm edit_button\" data-id=\"'.$row["drug_id"].'\"><i class=\"fas fa-edit\"><\/i><\/button>\n\t\t\t&nbsp;\n\t\t\t<button type=\"button\" name=\"delete_button\" class=\"btn btn-danger btn-circle btn-sm delete_button\" data-id=\"'.$id_precio.'\" data-status=\"'.$status.'\"><i class=\"fas fa-times\"><\/i><\/button>\n\t\t\t<\/div>\n\t\t\t"]';								
											}
										}
		 	
									$num=count($descripcion);
									$filtered_rows = $rows->num_rows;
			
									
									$output = array(
										"draw"    			=> 	intval($_POST["draw"]),
										"recordsTotal"  	=>  $total_rows,
										"recordsFiltered" 	=> 	$filtered_rows,
										"data"    			=> 	$data
									);
						
						echo '{"draw":'.intval($_POST["draw"]).',"recordsTotal":'.$total_rows.',"recordsFiltered":'.$filtered_rows.',"data":[';
						echo implode(",",$data);
						echo ']}';
						$conexion->close();	
						
			break;	
			case 'Add':
			$html = '';			
			$conexion=conectarse();	
			$campos='drug_id, proverdor_id, precio_real, precio_com, precio_unidad,mes,status';
			$valores="'".clean_input($_POST['drug_id'])."','".clean_input($_POST['proverdor_id'])."','".clean_input($_POST['precio_real'])."','".clean_input($_POST['precio_com'])."','".clean_input($_POST['precio_unidad'])."','".date('Y-m-d')."','Enable'";
			$num=crear($campos,$valores,'farmacia_medicamentos_precios',$conexion);
			
			$precio_id=Genere_id_creado('id','farmacia_medicamentos_precios',$conexion);
			
			
			if($precio_id>1){
				$campos='drug_id, proverdor_id, precio_id, total_ingreso, total_vendidos';
				$valores="'".clean_input($_POST['drug_id'])."','".clean_input($_POST['proverdor_id'])."','".clean_input($precio_id)."','".clean_input($_POST['total_ingreso'])."','0'";
				$nums=crear($campos,$valores,'farmacia_medicamentos_precios_inventario',$conexion);
				$success = '<div class="alert alert-success">Precio regitrado con exito!!</div>';
			}else{
				$error = '<div class="alert alert-danger">Error/div>';
			}
				

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);
			$conexion->close();								
		
			break;	
			case 'fetch_single':
			$data = array();
										
										$conexion=conectarse();	
										$sqlm="SELECT * FROM farmacia_medicamentos WHERE drug_id='".clean_input($_POST['drug_id'])."'";	
										$rowsm = $conexion->query($sqlm);
										if ($rowsm->num_rows > 0) {
										while($mat = $rowsm->fetch_assoc()) {
											 
												list($id_precio,$precio_real,$precio_com,$precio_unidad,$status)=precio_medicamentos($mat['drug_id'],$mat['proverdor_id'],$conexion);
												$total_ingreso=UnidadesCompradas($mat['drug_id'],$mat['proverdor_id'],$id_precio,$conexion);
												$id_inventario=IDUnidadesCompradas($mat['drug_id'],$mat['proverdor_id'],$id_precio,$conexion);
												
												 	
														$data['id_precio'] = $id_precio;
														$data['precio_real'] =number_format($precio_real,2,'.','');
														$data['precio_com'] =number_format ($precio_com,2,'.','');
														$data['precio_unidad'] =number_format ($precio_unidad,2,'.','');
														$data['total_ingreso'] =number_format ($total_ingreso,2,'.','');
														$data['drug_id'] =$mat['drug_id'];
														$data['id_inventario'] =$id_inventario;
														$data['nom_medicamento'] ='Medicamento seleccionado: '.$mat['descripcion'];
											}
										}
			
			echo json_encode($data);
			$conexion->close();
			break;	
			case 'Edit':
			$conexion=conectarse();
			
			$sets_medi = "total_ingreso = '".$_POST['total_ingreso']."'";
			$id_inventario=$_POST['hidden_id_inventario'];
			actualizar('id',$id_inventario,$sets_medi,'farmacia_medicamentos_precios_inventario',$conexion);				
			//actualiza precios
			
			$sets_precio = "
            precio_real     = '".$_POST['precio_real']."',
            precio_com  	= '".$_POST['precio_com']."',
            precio_unidad   = '".$_POST['precio_unidad']."'";
			$id_precio=$_POST['hidden_id_precio'];
			
			actualizar('id',$id_precio,$sets_precio,'farmacia_medicamentos_precios',$conexion);			
			$success = '<div class="alert alert-success">Registro Actualizado con exito!!</div>';
			
			$output = array(
				'error'		=>	$error,
				'success'	=>	$success
			);			
			echo json_encode($output);
			break;	
			case 'change_status':
			$conexion=conectarse();
			
			$sets_medi = "status = '".$_POST['next_status']."'";
			$id=$_POST['id'];
			actualizar('id',$id,$sets_medi,'farmacia_medicamentos_precios',$conexion);			
			$success = '<div class="alert alert-success">Registro Actualizado con exito!!</div>';
			
			$output = array(
				'error'		=>	$error,
				'success'	=>	$success
			);			
			echo json_encode($output);			
			
			break;
		case 'delete':	
			$conexion=conectarse();		   
			

										$sqli="SELECT * FROM farmacia_medicamentos_precios_inventario WHERE precio_id='".$_POST["id"]."'";	
										$row = $conexion->query($sqli);
									if ($row->num_rows > 0) {
										// Salida de la consulta mediante el ciclo WHILE
										while($p = $row->fetch_assoc()) {
										$inventario_id=$p['id'];
											borrar('id',$inventario_id,'farmacia_medicamentos_precios_inventario',$conexion);	
										}
									}								
	
			
			borrar('id',$_POST["id"],'farmacia_medicamentos_precios',$conexion);
			$conexion->close();	
			$success = '<div class="alert alert-success">Registro ELIMINADO con exito!!</div>';
			
			$output = array(
				'error'		=>	$error,
				'success'	=>	$success
			);			
			echo json_encode($output);		
		break;
		 
		}


 
	 
 
?>