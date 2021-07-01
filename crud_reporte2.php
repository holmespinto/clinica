<?php

require_once("./funciones_crud.php");
header('Content-Type: application/json; charset=utf8');
		switch ($_GET["action"]) {

            case 'table_repor2':
                $mes = $_POST['mes'];
    
                $drog=array();
                $arra_id=array();
                $arraUno=array();
                $arramaj=array();
                $mes=$_POST['mes'];
                /*
                if($mes<10){$meses='0'.$mes;}else{$meses=$mes;}
                
                $finicial=date('Y').'-'.$meses.'-'.'01';
                $ffinal=date('Y').'-'.$meses.'-'.'31';
    
                $conexion=conectarse();
                        if ($conexion->connect_error) {
                            die("Ha fallado la conexión: " . $conexion->connect_error);
                        }	
                        $arra_id=cargeIdsMedicmentoMes($finicial,$ffinal,$conexion);								
                        //$sql="SELECT * FROM proveedores_medicamentos WHERE  proverdor_id='".$_POST['proverdor_id']."' ORDER BY id ASC";	
                        $sql="SELECT * FROM  form_facturas AS f WHERE maj BETWEEN '".$finicial."' AND '".$ffinal."' ORDER BY id ASC";	
                        $rows = $conexion->query($sql);
                        if ($rows->num_rows > 0) {
                            // Salida de la consulta mediante el ciclo WHILE
                            while($row = $rows->fetch_assoc()) {
                                $id= $row['id'];
                                $id_medicamentos= $row['id_medicamentos'];
                                $Fecha= $row['maj'];
                                $idss=implode(",", $id_medicamentos);
                                $arraUno=array_unique(array_filter(explode(",", $idss)));
                                foreach ($arraUno as &$value) {
                                    $Nombre=NombresMedicamento($value,$conexion);
                                    $Total=TotalMedicamentoVendidoMes($value,$arra_id);
                                    $data[] = '{"Nombre":"'.utf8_encode($Nombre).'","Total":"'.$Total.'","Fecha":"'.$Fecha.'"}';	
    
                                }
                                
                            }
                        }
                        $conexion->close();
                       
                        echo '{
                            "data": [
                              {
                                "name": "Tiger Nixon",
                                "total": "1",
                                "fecha": "2011/04/25",
                              },
                              {
                                "name": "Tiger Nixon",
                                "total": "1",
                                "fecha": "2011/04/25",
                              }    
                              ]
                            } ';                      
        				 */
                    
                break;
		 
		}
 
?>