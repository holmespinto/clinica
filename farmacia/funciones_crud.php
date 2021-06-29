<?php
/**
 * Reciclaje de funcioones para el CRUD de la apliicacion
 *
 * @package   HP
 * @Funciones recicladoras
 * @author   Holmes Pinto Avila

 */

set_time_limit(0);


function conectarse() {
								$ServerName = "localhost";
								$Username = "autoeval_siat";
								$Password = "78+E75RDgC[jqm";
								$NameBD = "autoeval_openemr";

								// Creamos la conexión con MySQL
								$conexion = new mysqli($ServerName, $Username, $Password, $NameBD);
return $conexion ;
}

 function actualizar($id_nom,$id,$sets,$table,$conexion){
 			parse_str(file_get_contents("php://input"), $_PUT);

			$sql = "UPDATE $table SET $sets WHERE $id_nom ='".$id."'";
			$conexion=conectarse();
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}				
            if ($conexion->query($sql)) {		
            }
 }

 function borrar($id_nom,$id,$table,$conexion){
			
			$sql = "DELETE FROM $table WHERE $id_nom = '".$id."'";
           $conexion=conectarse();	
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}		   
            if ($conexion->query($sql)) {
            }
 }
  function crear($campos,$valores,$table,$conexion){
		
								$sql = "INSERT INTO $table ".
								   "($campos) "."VALUES ".
								   "($valores)";
							   $conexion=conectarse();
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}								   
								if ($conexion->query($sql)) {
									 $num=1;
								}else{
									  $num=2;
								}
								return $num;
 }
 
 	function Genere_id_creado($nom_campo,$table,$conexion)
	{
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}		
									$sql="SELECT MAX($nom_campo) AS order_number FROM $table";	
									$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($mat = $rows->fetch_assoc()) {
									$order_number=$mat['order_number'];
									}
								}
						return $order_number;		
		
	}
  function NombresProveedores($proverdor_id,$conexion){
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}									
									$sql="SELECT * FROM proveedores_medicamentos WHERE id='".$proverdor_id."'";	
									$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($mat = $rows->fetch_assoc()) {
									$nombre=$mat['nombre'];
									}
								}	
return $nombre;								
 }
 
 function ColorProveedores($proverdor_id,$conexion){
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}									
									$sql="SELECT * FROM proveedores_medicamentos WHERE id='".$proverdor_id."'";	
									$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($mat = $rows->fetch_assoc()) {
									$nombre=$mat['color'];
									}
								}	
return $nombre;								
 }
 
 function NProductosProveedores($proverdor_id,$conexion){
							  if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}	
									$sql="SELECT COUNT(*) AS num FROM farmacia_medicamentos WHERE proverdor_id='".$proverdor_id."'";	
									$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($mat = $rows->fetch_assoc()) {
									$num=$mat['num'];
									}
								}	
return $num;								
 } 
   function UnidadesCompradas($drug_id,$proverdor_id,$precio_id,$conexion){
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}									
									$sql="SELECT * FROM farmacia_medicamentos_precios_inventario WHERE drug_id='".$drug_id."' AND proverdor_id='".$proverdor_id."' AND precio_id='".$precio_id."'";	
									$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($mat = $rows->fetch_assoc()) {
									$total_ingreso=$mat['total_ingreso'];
									}
								}	
		return $total_ingreso;								
 }
 
   function IDUnidadesCompradas($drug_id,$proverdor_id,$precio_id,$conexion){
									
									$sql="SELECT * FROM farmacia_medicamentos_precios_inventario WHERE drug_id='".$drug_id."' AND proverdor_id='".$proverdor_id."' AND precio_id='".$precio_id."'";	
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}									
									$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($mat = $rows->fetch_assoc()) {
									$id=$mat['id'];
									}
								}	
		return $id;								
 } 
 
  function precio_medicamentos($drug_id,$proverdor_id,$conexion){
									$precio=array();
									$sql="SELECT * FROM farmacia_medicamentos_precios 
									WHERE proverdor_id='".$proverdor_id."' 
									AND drug_id='".$drug_id."'";	
									$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($mat = $rows->fetch_assoc()) {
									$id=$mat['id'];
									$precio_real=$mat['precio_real'];
									$precio_com=$mat['precio_com'];
									$precio_unidad=$mat['precio_unidad'];
									$status=$mat['status'];
									}
								}
								
return array($id,$precio_real,$precio_com,$precio_unidad,$status);								
 }
 
  
 
 
 
 function listar_proveedores($nombre,$conexion,$method){		
								if(($method=='GET') OR ($method=='POST') OR ($method=='DELETE')){
									$sql="SELECT * FROM proveedores_medicamentos WHERE nombre LIKE '%".$nombre."%' ORDER BY id ASC";	
								}else{
									$sql="SELECT * FROM proveedores_medicamentos WHERE id='".$nombre."' ORDER BY id ASC";	
								}
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}								
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($mat = $rows->fetch_assoc()) {
										$output[] ="{\"id\":\"".$mat['id']."\",
										\"nombre\": \"".utf8_encode($mat['nombre'])."\",
										\"nit\": \"".$mat['nit']."\",
										\"celular\": \"".$mat['celular']."\",
										\"direccion\": \"".$mat['direccion']."\"}\n";	
									}
								}
								
								
return $output;								
} 
function listar_medicamentos ($descripcion,$conexion,$method){		
								$precio=array();
								if(($method=='GET') OR ($method=='POST') OR ($method=='DELETE')){
									//$sql="SELECT * FROM farmacia_medicamentos WHERE proverdor_id LIKE '%".$proverdor_id."%' ORDER BY drug_id DESC";	
								
								$sql="SELECT m.drug_id,m.proverdor_id,i.total_ingreso,i.total_vendidos,m.descripcion,p.nombre,i.mes  
											FROM farmacia_medicamentos_precios_inventario AS i,
											farmacia_medicamentos AS m,
											proveedores_medicamentos AS p
									WHERE m.descripcion LIKE '%".$descripcion."%'
									AND m.drug_id=i.drug_id
									AND p.id=i.proverdor_id
									AND p.id=m.proverdor_id
									ORDER BY  m.drug_id ASC";								
								
								}else{
									$sql="SELECT * FROM farmacia_medicamentos WHERE drug_id='".$proverdor_id."' ORDER BY drug_id DESC";	
								
								$sql="SELECT m.drug_id,m.proverdor_id,i.total_ingreso,i.total_vendidos,m.descripcion,p.nombre,i.mes  
											FROM farmacia_medicamentos_precios_inventario AS i,
											farmacia_medicamentos AS m,
											proveedores_medicamentos AS p
									WHERE m.drug_id='".$proverdor_id."'
									AND m.drug_id=i.drug_id
									AND p.id=i.proverdor_id
									AND p.id=m.proverdor_id
									ORDER BY  m.drug_id ASC";									
								
								}
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}								
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($mat = $rows->fetch_assoc()) {
										
										
										 
										
										list($id_precio,$precio_real,$precio_com,$precio_unidad)=precio_medicamentos($mat['drug_id'],$mat['proverdor_id'],$conexion);
										
										$output[] ="{\"drug_id\":\"".$mat['drug_id']."\",
										\"nom_proveedor\": \"".utf8_encode($mat['nombre'])."\",
										\"proverdor_id\": \"".$mat['proverdor_id']."\",
										\"id_precio\": \"".$id_precio."\",
										\"descripcion\": \"".utf8_encode($mat['descripcion'])."\",
										\"precio_real\": \"".$precio_real."\",
										\"precio_com\": \"".$precio_com."\",
										\"precio_unidad\": \"".$precio_unidad."\"}\n";	
									}
								}
								
								
return $output;								
} 

function listar_compras($nombre,$conexion,$method){		
								
								$sql="SELECT m.drug_id,m.proverdor_id,i.total_ingreso,i.total_vendidos,m.descripcion,p.nombre,i.mes  
											FROM farmacia_medicamentos_precios_inventario AS i,
											farmacia_medicamentos AS m,
											proveedores_medicamentos AS p
									WHERE m.descripcion LIKE '%".$nombre."%'
									AND m.drug_id=i.drug_id
									AND p.id=i.proverdor_id
									AND p.id=m.proverdor_id
									ORDER BY  m.drug_id ASC";
										
								 
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}								
								$rows = $conexion->query($sql);
								if ($rows->num_rows > 0) {
									// Salida de la consulta mediante el ciclo WHILE
									while($mat = $rows->fetch_assoc()) {
										$medicamento=$mat['descripcion'];
										$proverdor=$mat['nombre'];
										
										$output[] ="{\"id\":\"".$mat['id']."\",
										\"id\": \"".$mat['id']."\",
										\"proverdor_id\": \"".$mat['proverdor_id']."\",
										\"medicamento\": \"".utf8_encode($medicamento)."\",
										\"proverdor\": \"".utf8_encode($proverdor)."\",
										\"total_ingreso\": \"".$mat['total_ingreso']."\",
										\"total_existencia\": \"".$mat['total_vendidos']."\",
										\"mes\": \"".$mat['mes']."\"}\n";	
									}
								}
								
								
return $output;								
} 
/*
function load_data()
  {
    $.ajax({
      url:"<?php echo base_url(); ?>livetable/load_data",
      dataType:"JSON",
      success:function(data){
        var html = '<tr>';
        html += '<td id="first_name" contenteditable placeholder="Enter First Name"></td>';
        html += '<td id="last_name" contenteditable placeholder="Enter Last Name"></td>';
        html += '<td id="age" contenteditable></td>';
        html += '<td><button type="button" name="btn_add" id="btn_add" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-plus"></span></button></td></tr>';
        for(var count = 0; count < data.length; count++)
        {
          html += '<tr>';
          html += '<td class="table_data" data-row_id="'+data[count].id+'" data-column_name="first_name" contenteditable>'+data[count].first_name+'</td>';
          html += '<td class="table_data" data-row_id="'+data[count].id+'" data-column_name="last_name" contenteditable>'+data[count].last_name+'</td>';
          html += '<td class="table_data" data-row_id="'+data[count].id+'" data-column_name="age" contenteditable>'+data[count].age+'</td>';
          html += '<td><button type="button" name="delete_btn" id="'+data[count].id+'" class="btn btn-xs btn-danger btn_delete"><span class="glyphicon glyphicon-remove"></span></button></td></tr>';
        }
        $('tbody').html(html);
      }
    });
  }
  */
 	function clean_input($string)
	{
	  	$string = trim($string);
	  	$string = stripslashes($string);
	  	$string = htmlspecialchars($string);
	  	return $string;
	}

	function get_datetime()
	{
		return date("Y-m-d H:i:s",  STRTOTIME(date('h:i:sa')));
	}
	
	
	//PARA ENCRIPTAR Y DESENCRIPTAR DATOS
	//$first_name = convert_string('encrypt', $first_name);
	//$id = convert_string('decrypt', $_POST["id"]);
	
function convert_string($action, $string)
{
		 $output = '';
		 $encrypt_method = "AES-256-CBC";
			$secret_key = 'eaiYYkYTysia2lnHiw0N0vx7t7a3kEJVLfbTKoQIx5o=';
			$secret_iv = 'eaiYYkYTysia2lnHiw0N0';
			// hash
			$key = hash('sha256', $secret_key);
		 $initialization_vector = substr(hash('sha256', $secret_iv), 0, 16);
		 if($string != '')
		 {
		  if($action == 'encrypt')
		  {
		   $output = openssl_encrypt($string, $encrypt_method, $key, 0, $initialization_vector);
		   $output = base64_encode($output);
		  } 
		  if($action == 'decrypt') 
		  {
		   $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $initialization_vector);
		  }
		 }
 return $output;
}	

function TFacturadoMes($provider_id,$meses,$conexion) {
								date_default_timezone_set('America/Bogota');	
								
								if($meses<=9){$m='0'.$meses;}else{$m=$meses;}
								
								$dinicia='01';
								$dfinal='31';
								$y=date('Y');								 
								if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}
	
								$consulta= "SELECT SUM(total) AS total FROM form_facturas WHERE provider_id='".$provider_id."'AND maj BETWEEN '".$y."-".$m."-".$dinicia."' AND '".$y."-".$m."-".$dfinal."'";
								$resultados = $conexion->query($consulta);
								if ($resultados->num_rows > 0) {
									while($registro = $resultados->fetch_assoc()) {
										$total=$registro["total"];
									}
								}				
							 
return $total;
}


function count_all_data($table,$conexion)
{
									if ($conexion->connect_error) {
									die("Ha fallado la conexión: " . $conexion->connect_error);
								}	
								$consulta= "SELECT COUNT(*) AS total FROM $table";
								$resultados = $conexion->query($consulta);
								if ($resultados->num_rows > 0) {
									while($registro = $resultados->fetch_assoc()) {
										$total=$registro["total"];
									}
								}
 return $total;
}
 ?>