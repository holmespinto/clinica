<?php

/**
 * Upload and install a designated code set to the codes table.
 *
 * @package   OpenEMR
 * @link      HOLMES PINTO
 * @author    Rod Roark <rod@sunsetsystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2014 Rod Roark <rod@sunsetsystems.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

set_time_limit(0);

require_once("../globals.php");
require_once("./funciones_crud.php");
 require_once("dompdf/autoload.inc.php");
// reference the Dompdf namespace
use Dompdf\Dompdf;
//initialize dompdf class
$document = new Dompdf();
$mes = $_GET['mes'];
    
$drog=array();
$arra_id=array();
$arraUno=array();
$arramaj=array();
 
 


if($mes<10){$meses='0'.$mes;}else{$meses=$mes;}

$finicial=date('Y').'-'.$meses.'-'.'01';
$ffinal=date('Y').'-'.$meses.'-'.'31';
$conexion=conectarse();
$facility = DatosEmpresa($conexion);

$output = '
	<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
</style>
 <div class="container">
	 <div class="reqHeader">
            <font size="9" text-align:center ><i class="far fa-heart"></i><u>UNIDAD MÉDICA D & D</u></font>
           <div class="cinfo">
           <font size="4">
                '.text($facility["name"]) .'<br /> '. text($facility["street"]) .'<br />
				'.text($facility["city"]) .','. text($facility["state"]) .','. text($facility["postal_code"]) .' <br />
                          '.text($facility["phone"]).'
                          </font>
           </div>
   </div>
<br />
<br />
<table>
   <tr>
    <th colspan="4" style="text-align:center">Registro diarios de ventas</th>
  </tr>
  <tr>
    <td colspan="2">Medivcamento</td>
    <td colspan="1">Total</td>
    <td colspan="1">'.xlt('Date').'</td>
  </tr>';

        
        if ($conexion->connect_error) {
            die("Ha fallado la conexión: " . $conexion->connect_error);
        }	
       // $arra_id=cargeIdsMedicmentoMes($finicial,$ffinal,$conexion);								
        //$sql="SELECT * FROM proveedores_medicamentos WHERE  proverdor_id='".$_POST['proverdor_id']."' ORDER BY id ASC";	
        $sql="SELECT * FROM  form_facturas AS f WHERE maj BETWEEN '".$finicial."' AND '".$ffinal."' ORDER BY id ASC";	
        $rows = $conexion->query($sql);
        if ($rows->num_rows > 0) {
            // Salida de la consulta mediante el ciclo WHILE
            while($row = $rows->fetch_assoc()) {
                $id= $row['id'];
                $Fecha= $row['maj'];
                $arraUno=array_unique(array_filter(explode(",",$row['id_medicamentos'])));
                foreach ($arraUno as &$value) {
                    $Nombre=NombresMedicamento($value,$conexion);
                    $Total=TotalMedicamentoVendidoMes($value,$arraUno);
                   $output .= '<tr>
                   <td colspan="2">' . $Nombre . '</td>
                   <td colspan="1">' . $Total . '</td>
                   <td colspan="1">' . substr($Fecha, 0, 10) . '</td>
                   </tr>'; 
                        }
                }
                
            }
           $conexion->close();
        $output .= ' </table>';
        $document->loadHtml($output);
        //set page size and orientation
        $document->setPaper('A4', 'landscape');
        $document->render();
        $document->stream("historico", array("Attachment"=>0));
        $document->loadHtml($output);
          

?>