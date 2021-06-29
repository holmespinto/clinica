<?php

require_once("./funciones_crud.php");
header('Content-Type: application/json; charset=utf8');


		$method = $_POST['action'];
		switch ($method) {
		case 'fetch':
						$conexion=conectarse();
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}								
								//$sql="SELECT * FROM proveedores_medicamentos WHERE  proverdor_id='".$_POST['proverdor_id']."' ORDER BY id ASC";	
								$sql="SELECT * FROM proveedores_medicamentos ORDER BY id ASC";	
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($row = $rows->fetch_assoc()) {
										
										$Total=NProductosProveedores($row['id'],$conexion);
										$data[] = array(
											'proveedor'		=>	utf8_encode($row['nombre']),
											'total'			=>	$Total,
											'color'			=>	'#' . rand(100000, 999999) . ''
										);
		
									}
								}
			echo json_encode($data);					
			$conexion->close();
			break;
			case 'fetch_repor1':
								$mes = $_POST['mes'];
								$conexion=conectarse();
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}								
								
								$sql="SELECT DISTINCT(f.provider_id), CONCAT(u.fname, ' ', u.lname) AS full_name
										FROM form_facturas AS f, users AS u
										WHERE u.id=f.provider_id
										ORDER BY f.provider_id ASC";	
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									while($row = $rows->fetch_assoc()) {
										$Total=TFacturadoMes($row['provider_id'],$mes,$conexion);
										$output[] = array(
											'proveedor'		=>	utf8_encode($row['full_name']),
											'Total'			=>	floatval($Total),
											'color'			=>	'#' . rand(100000, 999999) . ''
										);
		
									}
								}

 
				 
				 echo json_encode($output);
			$conexion->close();	 
			break;
			case 'table_repor1':
								$mes = $_POST['mes'];
								/*
								$data=array();
								$sub_array=array();
								
								/*
								$query = "SELECT DISTINCT(f.provider_id), CONCAT(u.fname, ' ', u.lname) AS full_name,f.maj
										FROM form_facturas AS f, users AS u";
								if(isset($_GET['search']['value']))
								{
								 $query .= '
								 WHERE u.id=f.provider_id AND  LIKE u.fname "%'.$_GET['search']['value'].'%"';
								}							
								
								if(isset($_GET['order']))
								{
								 $query .= 'ORDER BY '.$column[$_GET['order']['0']['column']].' '.$_GET['order']['0']['dir'].' ';
								}
								else
								{
								 $query .= 'ORDER BY f.provider_id DESC ';
								}

								$query1 = '';

								if($_GET['length'] != -1)
								{
								 $query1 = 'LIMIT ' . $_GET['start'] . ', ' . $_GET['length'];
								}
								 
								
								$conexion=conectarse();
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}
								
								 
								$query="SELECT DISTINCT(f.provider_id), CONCAT(u.fname, ' ', u.lname) AS full_name
										FROM form_facturas AS f, users AS u
										WHERE u.id=f.provider_id
										ORDER BY f.provider_id ASC";
								 		
								$rows = $conexion->query($query);
								if ($rows->num_rows > 0) {
									while($row = $rows->fetch_assoc()) {
									   $total= TFacturadoMes($row['provider_id'],$mes,$conexion);
										$data[] = '["'.utf8_encode($row["full_name"]).'","'.$total.'","'.$total.'"]';										
	
									}
								}
								*/
								
echo '{
  "data": [
    {
      "id": "1",
      "name": "Tiger Nixon",
      "position": "System Architect",
      "salary": "$320,800",
      "start_date": "2011/04/25",
      "office": "Edinburgh",
      "extn": "5421"
    },
    {
      "id": "2",
      "name": "Garrett Winters",
      "position": "Accountant",
      "salary": "$170,750",
      "start_date": "2011/07/25",
      "office": "Tokyo",
      "extn": "8422"
    }
	]
}';								

				
				/*	
						//echo '{"draw":'.intval($_POST["draw"]).',"recordsTotal":'.$total_rows.',"recordsFiltered":'.$row['maj'].',"data":[';
						echo '"data":[';
						echo implode(",",$data);
						echo ']}';				
				$output = array(
				 'draw'    => intval($_GET['draw']),
				 'recordsTotal'  => count_all_data($conexion),
				 'recordsFiltered' => $number_filter_row,
				 'data'    => $sub_array
				);
				
				*/
			//echo json_encode($output);
			//$conexion->close();	 										
			break;
		
		}
		
				 

		
		

 
?>