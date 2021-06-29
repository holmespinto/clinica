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

require_once("../globals.php");
require_once("$srcdir/patient.inc");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
use OpenEMR\OeUI\OemrUI;
 $results=array();
 header('Content-Type: application/json; charset=utf8');
function charset_decode_utf_8($string)
    {
        /* Only do the slow convert if there are 8-bit characters */
        if ( !preg_match("/[\200-\237]/", $string) && !preg_match("/[\241-\377]/", $string) )
               return $string;

        // decode three byte unicode characters
          $string = preg_replace_callback("/([\340-\357])([\200-\277])([\200-\277])/",
                    create_function ('$matches', 'return \'&#\'.((ord($matches[1])-224)*4096+(ord($matches[2])-128)*64+(ord($matches[3])-128)).\';\';'),
                    $string);

        // decode two byte unicode characters
          $string = preg_replace_callback("/([\300-\337])([\200-\277])/",
                    create_function ('$matches', 'return \'&#\'.((ord($matches[1])-192)*64+(ord($matches[2])-128)).\';\';'),
                    $string);

        return $string;
    }
if (!empty($_POST)) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
}
 $results=array();	
$searchTerm =$_GET['sSearch'];
 $sSearch = add_escape_custom($searchTerm);
	
						$query="SELECT m.drug_id,m.descripcion,p.nombre,i.precio_com  
											FROM farmacia_medicamentos_precios AS i,
											farmacia_medicamentos AS m,
											proveedores_medicamentos AS p
									WHERE m.descripcion LIKE '%".$sSearch."%'
									AND m.drug_id=i.drug_id
									AND p.id=i.proverdor_id
									AND p.id=m.proverdor_id
									ORDER BY  m.drug_id ASC";		
	
	$bres = sqlStatement($query);	
	while ($grow = sqlFetchArray($bres)) {
		$results[]='{"id":"'.$grow['drug_id'].'","text":"'.charset_decode_utf_8($grow['descripcion']).'-$'.$grow['precio_com'].'"}';								
	}
$results = implode(",", $results); 
 echo'{
  "data": [';
  echo $results ; 
 echo']}'; 
die;
 

?>
 