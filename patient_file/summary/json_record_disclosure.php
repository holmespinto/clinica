<?php

/**
 * Ajax interface for popup of multi select patient.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Amiel Elboim <amielel@matrix.co.il>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2017 Amiel Elboim <amielel@matrix.co.il
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once('../../globals.php');
require_once("$srcdir/patient.inc");

use OpenEMR\Common\Csrf\CsrfUtils;

if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}
$results=array();
$tipo = $_GET['type'];
$search = $_GET['search'];

switch ($tipo) {
       case 'medication':
							if(!empty($search)){
							$gres = sqlStatement("SELECT * FROM codes_medicamentos WHERE descripcion LIKE '%%%".$search."%%%'"); 							
								while ($grow = sqlFetchArray($gres)) {
									$results[]='{"id":"'.$grow['cod_principal'].'","text":"'.$grow['cod_principal'].'-'.$grow['descripcion'].'/'.$grow['unidad'].'/'.$grow['concentracion'].'"}';								
								}
							}else{
								$results[]='{"id":"0","text":"sin registros"}';	
								
							}
	   break;
       case 'surgery':
							if(!empty($search)){
							$gres = sqlStatement("SELECT * FROM codes_rips WHERE descripcion LIKE '%".$search."%'"); 							
								while ($grow = sqlFetchArray($gres)) {
									$results[]='{"id":"'.$grow['cod_principal'].'","text":"'.$grow['cod_principal'].'-'.$grow['descripcion'].'"}';								
								}
							}else{
								$results[]='{"id":"0","text":"sin registros"}';	
								
							}
	   break;
	   case 'allergy':
							if(!empty($search)){
							$gres = sqlStatement("SELECT * FROM codes_rips WHERE descripcion LIKE '%".$search."%'"); 							
								while ($grow = sqlFetchArray($gres)) {
									$results[]='{"id":"'.$grow['cod_principal'].'","text":"'.$grow['cod_principal'].'-'.$grow['descripcion'].'"}';								
								}
							}else{
								$results[]='{"id":"0","text":"sin registros"}';	
								
							}
	   break;
	   case 'dental':
							if(!empty($search)){
							$gres = sqlStatement("SELECT * FROM codes_rips WHERE descripcion LIKE '%".$search."%'"); 							
								while ($grow = sqlFetchArray($gres)) {
									$results[]='{"id":"'.$grow['cod_principal'].'","text":"'.$grow['cod_principal'].'-'.$grow['descripcion'].'"}';								
								}
							}else{
								$results[]='{"id":"0","text":"sin registros"}';	
								
							}
	   break;
	   case 'medical_problem':
							if(!empty($search)){
							$gres = sqlStatement("SELECT * FROM codes_rips WHERE descripcion LIKE '%".$search."%'"); 							
								while ($grow = sqlFetchArray($gres)) {
									$results[]='{"id":"'.$grow['cod_principal'].'","text":"'.$grow['cod_principal'].'-'.$grow['descripcion'].'"}';								
								}
							}else{
								$results[]='{"id":"0","text":"sin registros"}';	
								
							}
	   break;	   
}
$results = implode(",", $results); 

 echo'{
  "results": [';
  echo $results ; 
 echo']}'; 
die;
 

?>
 