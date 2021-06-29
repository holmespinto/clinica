<?php

require_once("./funciones_crud.php");
header('Content-Type: application/json; charset=utf8');
		switch ($_GET["action"]) {

			case 'table_repor1':

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
									   $total= TFacturadoMes($row['provider_id'],$_GET["mes"],$conexion);
										$data[] = '{"nombre":"'.utf8_encode($row["full_name"]).'","total":"'.$total.'"}';										
	
									}
								}
						echo '{
						"data": [';
						echo implode(",",$data);
						echo ']}'; 
		break;
		 
		}
 
?>