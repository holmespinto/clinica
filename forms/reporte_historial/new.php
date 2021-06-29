<?php

/**
 * forms.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once("../../globals.php");
require_once("$srcdir/encounter.inc");
require_once("$srcdir/group.inc");
require_once("$srcdir/calendar.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/amc.php");
require_once("$srcdir/../controllers/C_Document.class.php");
require_once("$srcdir/lab.inc");

use ESign\Api;
use OpenEMR\Common\Acl\AclMain;
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
//index.php
//include autoloader
 require_once("dompdf/autoload.inc.php");
// reference the Dompdf namespace
use Dompdf\Dompdf;
//initialize dompdf class
$document = new Dompdf();
 //ob_start()
$expand_default = (int)$GLOBALS['expand_form'] ? 'show' : 'hide';
$reviewMode = false;
if (!empty($_REQUEST['review_id'])) {
    $reviewMode = true;
    $encounter = sanitizeNumber($_REQUEST['review_id']);
}

$is_group = ($attendant_type == 'gid') ? true : false;
if ($attendant_type == 'gid') {
    $groupId = $therapy_group;
}
$attendant_id = $attendant_type == 'pid' ? $pid : $therapy_group;
if ($is_group && !AclMain::aclCheckCore("groups", "glog", false, array('view','write'))) {
    echo xlt("access not allowed");
    exit();
}


if (isset($_GET["set_encounter"])) {
    // The billing page might also be setting a new pid.
    if (isset($_GET["set_pid"])) {
        $set_pid = $_GET["set_pid"];
    } elseif (isset($_GET["pid"])) {
        $set_pid = $_GET["pid"];
    } else {
        $set_pid = false;
    }

    if ($set_pid && $set_pid != $_SESSION["pid"]) {
        setpid($set_pid);
    }

    setencounter($_GET["set_encounter"]);
}

    $facility = getFacility();
 
$patdata = getPatientData($pid, "fname,lname,squad,pubpid,sex,ss");	
 $eres = sqlStatement("SELECT * FROM form_encounter WHERE encounter=? AND pid = ? " .
"ORDER BY date DESC", array($encounter,$pid)); 

$clinical = sqlStatement("SELECT * FROM form_clinical_instructions WHERE encounter=? AND pid = ? " .
"ORDER BY date DESC", array($encounter,$pid));

$medical_problem = sqlStatement("SELECT * FROM lists WHERE type='medical_problem' AND pid = ? " .
"ORDER BY date DESC", array($pid));

$allergy = sqlStatement("SELECT * FROM lists WHERE type='allergy' AND pid = ? " .
"ORDER BY date DESC", array($pid));

$medication = sqlStatement("SELECT * FROM lists WHERE type='medication' AND pid = ? " .
"ORDER BY date DESC", array($pid));

$incapacidad = sqlStatement("SELECT * FROM form_incapacidades WHERE pid = ? " .
"ORDER BY maj DESC", array($pid));
 function validar_dias($id_inca){
	 
								$queryp = "SELECT DATEDIFF(fecha_inicio,fecha_final) AS TotalDias " .
                                "FROM  form_incapacidades WHERE id=? " .
                                " ORDER BY encounter";
								
                                $bres = sqlStatement($queryp,$id_inca);
                                while ($prow = sqlFetchArray($bres)) {						
									$TotalDias=$prow['TotalDias'];	
								} 
	 return $TotalDias;
 }

function US_weight($pounds, $mode = 1)
{

    if ($mode == 1) {
        return $pounds . " " . xl('lb') ;
    } else {
        $pounds_int = floor($pounds);
        $ounces = round(($pounds - $pounds_int) * 16);
        return $pounds_int . " " . xl('lb') . " " . $ounces . " " . xl('oz');
    }
}

function vitals_report($pid, $encounter, $cols, $id, $print = true)
{
    $count = 0;
    $data = formFetch("form_vitals", $id);
    $patient_data = getPatientData($pid);
    $patient_age = getPatientAge($patient_data['DOB']);

    $vitals = "";
    if ($data) {
        $vitals .= "<table><tr>";

        foreach ($data as $key => $value) {
            if (
                $key == "id" || $key == "pid" ||
                $key == "user" || $key == "groupname" ||
                $key == "authorized" || $key == "activity" ||
                $key == "date" || $value == "" ||
                $value == "0000-00-00 00:00:00" || $value == "0.0"
            ) {
                // skip certain data
                continue;
            }

            if ($value == "on") {
                $value = "yes";
            }

            $key = ucwords(str_replace("_", " ", $key));

            //modified by BM 06-2009 for required translation
            if ($key == "Temp Method" || $key == "BMI Status") {
                if ($key == "BMI Status") {
                    if ($patient_age <= 20 || (preg_match('/month/', $patient_age))) {
                        $value = "See Growth-Chart";
                    }
                }

                $vitals .= '<td><div class="bold" style="display:inline-block">' . xlt($key) . ': </div><div class="text" style="display:inline-block">' . xlt($value) . "</div></td>";
            } elseif ($key == "Bps") {
                $bps = $value;
                if (!empty($bpd)) {
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt('Blood Pressure') . ": </div><div class='text' style='display:inline-block'>" . text($bps) . "/" . text($bpd)  . "</div></td>";
                } else {
                    continue;
                }
            } elseif ($key == "Bpd") {
                $bpd = $value;
                if ($bps) {
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt('Blood Pressure') . ": </div><div class='text' style='display:inline-block'>" . text($bps) . "/" . text($bpd)  . "</div></td>";
                } else {
                    continue;
                }
            } elseif ($key == "Weight") {
                $convValue = number_format($value * 0.45359237, 2);
                $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>";
                // show appropriate units
                $mode = $GLOBALS['us_weight_format'];
                if ($GLOBALS['units_of_measurement'] == 2) {
                    $vitals .=  text($convValue) . " " . xlt('kg') . " (" . text(US_weight($value, $mode)) . ")";
                } elseif ($GLOBALS['units_of_measurement'] == 3) {
                    $vitals .=  text(US_weight($value, $mode));
                } elseif ($GLOBALS['units_of_measurement'] == 4) {
                    $vitals .= text($convValue) . " " . xlt('kg');
                } else { // = 1 or not set
                    $vitals .= text(US_weight($value, $mode)) . " (" . text($convValue) . " " . xlt('kg')  . ")";
                }

                $vitals .= "</div></td>";
            } elseif ($key == "Height" || $key == "Waist Circ"  || $key == "Head Circ") {
                $convValue = round(number_format($value * 2.54, 2), 1);
                // show appropriate units
                if ($GLOBALS['units_of_measurement'] == 2) {
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($convValue) . " " . xlt('cm') . " (" . text($value) . " " . xlt('in')  . ")</div></td>";
                } elseif ($GLOBALS['units_of_measurement'] == 3) {
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($value) . " " . xlt('in') . "</div></td>";
                } elseif ($GLOBALS['units_of_measurement'] == 4) {
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($convValue) . " " . xlt('cm') . "</div></td>";
                } else { // = 1 or not set
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($value) . " " . xlt('in') . " (" . text($convValue) . " " . xlt('cm')  . ")</div></td>";
                }
            } elseif ($key == "Temperature") {
                $convValue = number_format((($value - 32) * 0.5556), 2);
                // show appropriate units
                if ($GLOBALS['units_of_measurement'] == 2) {
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($convValue) . " " . xlt('C') . " (" . text($value) . " " . xlt('F')  . ")</div></td>";
                } elseif ($GLOBALS['units_of_measurement'] == 3) {
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($value) . " " . xlt('F') . "</div></td>";
                } elseif ($GLOBALS['units_of_measurement'] == 4) {
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($convValue) . " " . xlt('C') . "</div></td>";
                } else { // = 1 or not set
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($value) . " " . xlt('F') . " (" . text($convValue) . " " . xlt('C')  . ")</div></td>";
                }
            } elseif ($key == "Pulse" || $key == "Respiration"  || $key == "Oxygen Saturation" || $key == "BMI") {
                $value = number_format($value, 0);
                if ($key == "Oxygen Saturation") {
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($value) . " " . xlt('%') . "</div></td>";
                } elseif ($key == "BMI") {
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($value) . " " . xlt('kg/m^2') . "</div></td>";
                } else { //pulse and respirations
                    $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($value) . " " . xlt('per min') . "</div></td>";
                }
            } else {
                $vitals .= "<td><div class='font-weight-bold d-inline-block'>" . xlt($key) . ": </div><div class='text' style='display:inline-block'>" . text($value) . "</div></td>";
            }

            $count++;

            if ($count == $cols) {
                $count = 0;
                $vitals .= "</tr><tr>\n";
            }
        }

        $vitals .= "</tr></table>";
		}
		return $vitals;
    }
	
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
    <th colspan="4" style="text-align:center">Datos Personales</th>
  </tr>
  <tr>
    <th colspan="2">Nombres y Apellidos</th>
    <th>Identificación</th>
    <th>Sexo</th>
  </tr>
  <tr>
    <td colspan="2">' . text($patdata['fname']) . ' ' . text($patdata['lname']) . '</td>
    <td>' . text($patdata['pubpid']) . '</td>
    <td>' . text($patdata['sex']) . '</td>
  </tr>
  <tr>
    <th colspan="4" style="text-align:center">Visitas y Formularios:</th>
  </tr>
  <tr>
    <td colspan="3">Descripción de la Consulta</td>
    <td colspan="1">'.xlt('Date').'</td>
  </tr>';
	 while ($row = sqlFetchArray($eres)) { 
				$output .= '<tr>
				<td colspan="3">' . text($row['reason']) . '</td>
				<td colspan="1">' . text(substr($row['date'], 0, 10)) . '</td>
				</tr>'; 
		}

 $output .= '
  <tr>
    <th colspan="4" style="text-align:center">Instruciones clínicas</th>
  </tr>
   <tr>
   <td  style="width:10%;">Código</td>
   <td style="width:5%;">Tipo de Problema</td>
   <td style="width:50%;">Descripción </br>de la instruciones</td>
   <td style="width:20%;">Fecha</td>
  </tr>';
 	 while ($row = sqlFetchArray($clinical)) { 
				$output .= '<tr>
				<td style="width:5%;">' . text($row['codrips']) . '</td>
				<td style="width:5%;">' . text($row['code_type']) . '</td>
				<td style="width:50%;">' . text($row['instruction']) . '</td>
				<td style="width:10%;">' . text(substr($row['date'], 0, 10)) . '</td>
				</tr>'; 
		} 
 $output .= '  
  <tr>
    <td colspan="4" style="text-align:center">Problemas Médicos</td>
  </tr>
  <tr>
    <td style="width:10%;">Código</td>
    <td>Titulo</td>
    <td>Comentarios</td>
    <td>Fecha</td>
  </tr>';
 	 while ($row = sqlFetchArray($medical_problem)) { 
				$output .= '<tr>
				<td>' . text($row['diagnosis']) . '</td>
				<td>' . text($row['title']) . '</td>
				<td>' . text($row['comments']) . '</td>
				<td>' . text(substr($row['date'], 0, 10)) . '</td>
				</tr>'; 
		}   
 $output .= '  
  <tr>
    <th colspan="4" style="text-align:center">Alergias</th>
  </tr>
  <tr>
    <td style="width:10%;">Código</td>
    <td>Titulo</td>
    <td>Comentarios</td>
    <td>Fecha</td>
  </tr>'; 
 	 while ($row = sqlFetchArray($allergy)) { 
				$output .= '<tr>
				<td>' . text($row['diagnosis']) . '</td>
				<td>' . text($row['title']) . '</td>
				<td>' . text($row['comments']) . '</td>
				<td>' . text(substr($row['date'], 0, 10)) . '</td>
				</tr>'; 
		}    

 $output .= '  
  <tr>
    <th colspan="4" style="text-align:center">Medicamentos</th>
  </tr>
  <tr>
    <td style="width:10%;">Código</td>
    <td style="width:30%;">Titulo</td>
    <td style="width:30%;">Comentarios</td>
    <td style="width:20%;">Fecha</td>
  </tr>'; 
 	 while ($row = sqlFetchArray($medication)) { 
				$output .= '<tr>
				<td>' . text($row['diagnosis']) . '</td>
				<td>' . text($row['title']) . '</td>
				<td>' . text($row['comments']) . '</td>
				<td>' . text(substr($row['date'], 0, 10)) . '</td>
				</tr>'; 
		} 

 $output .= '  
  <tr>
    <th colspan="4" style="text-align:center">Incapacidades</th>
  </tr>
  <tr>
    <td style="width:10%;">&nbsp;</td>
    <td style="width:10%;">Fecha de Inicio</td>
    <td style="width:10%;">Fecha Final</td>
    <td style="width:20%;">Días</td>
  </tr>'; 
	$k=1;
  	 while ($row = sqlFetchArray($incapacidad)) { 
				$output .= '<tr>
				<td>'.$k.'</td>
				<td>' . text(substr($row['fecha_inicio'], 0, 10)) . '</td>
				<td>' . text(substr($row['fecha_final'], 0, 10)) . '</td>
				<td>' . text(validar_dias($row['id'])) . '</td>
				</tr>'; 
				$output .= '<tr>
				<td colspan="4">Concepto:&nbsp;&nbsp;' . text($row['incapacidad']) . '</td>
				</tr>'; 				
		$k++;
		} 
		
$output .= ' </table>';

	 while ($row = sqlFetchArray($eres)) {
				$output .= vitals_report($pid, $row['encounter'],2, $row['id'],false);		 

		}

/*
$output .= ' <table>';
$output .= '<tr>
			<th colspan="4" style="text-align:center">Vitales</th>
			</tr>';
		 $output .= '<tr>
			<th >Nombre</th>
			<th >Unidad</th>
			<th >Valor</th>
		  </tr>'; 
				
				
				
				
		$output .= '<tr>
			<td ></td>
			<td ></td>
			<td></td>
			<th>x</th>
		  </tr>';

 
$output .= ' </table>';

$output .= ' <table>';
$output .= '<tr>
    <th colspan="2" style="text-align:center">Revisión de chequeos de sistemas</th>
  </tr>';
 $output .= '<tr>
    <th colspan="2" style="text-align:center">General</th>
  </tr>'; 
  
$output .= '<tr>
    <td colspan="2">Nombre</td>
    <th>x</th>
  </tr>';

$output .= ' </table>';
*/
$document->loadHtml($output);
//set page size and orientation
$document->setPaper('A4', 'landscape');
//Render the HTML as PDF
$document->render();
//Get output of generated pdf in Browser
$document->stream("historico", array("Attachment"=>0));
 $document->loadHtml($output);
 //$output = ob_get_clean();
?>

 
