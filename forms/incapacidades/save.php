<?php

/**
 * Functional cognitive status form.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Jacob T Paul <jacob@zhservices.com>
 * @author    Vinish K <vinish@zhservices.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2015 Z&H Consultancy Services Private Limited <sam@zhservices.com>
 * @copyright Copyright (c) 2017-2019 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once(__DIR__ . "/../../globals.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");

use OpenEMR\Common\Csrf\CsrfUtils;

if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}
$encounter = $_SESSION['encounter'];
$pid = $_SESSION['pid'];
if (!$encounter) { // comes from globals.php
    die(xlt("Internal error: we do not seem to be in an encounter!"));
}

$save           = isset($_POST['save']) ? $_POST['save'] : '';
$fecha_inicio   = $_POST["fecha_inicio"];
$fecha_final    = $_POST["fecha_final"];
$incapacidad    = $_POST["incapacidad"];
$id    			= $_POST["id"];


if ($save == 3) {
    sqlStatement("DELETE FROM `form_incapacidades` WHERE id=?", array($id));
    $newid = $id;
} 
if ($save == 2) {
    $res2 = sqlStatement("SELECT MAX(id) as largestId FROM `form_incapacidades`");
    $getMaxid = sqlFetchArray($res2);
    if ($getMaxid['largestId']) {
        $newid = $getMaxid['largestId'] + 1;
    } else {
        $newid = 1;
    }
				$sets = "
				pid     	= ?,
				encounter 	= ?,
				provider_id 	= ?,
				incapacidad = ?,
				fecha_inicio = ?,
				fecha_final 		= ?";
			sqlStatement(
				"INSERT INTO form_incapacidades SET $sets",
				[
					$_POST["pid"],
					$_POST["encounter"],
					$_POST["provider_id"],
					$_POST["incapacidad"],
					DateTimeToYYYYMMDDHHMMSS($_POST['fecha_inicio'] ?? ''),
					DateTimeToYYYYMMDDHHMMSS($_POST['fecha_final'] ?? '')
				]
			);		

    addForm($encounter, "Incapacidades Form", $newid, "incapacidades", $_SESSION["pid"], $userauthorized);
}


if($save == 1) {
	
														$sets = "
														incapacidad    = ?,
														fecha_inicio    = ?,
														fecha_final 	= ?";	

									sqlQuery("UPDATE form_incapacidades SET $sets WHERE id = ".$_POST["id"]."", array(
									$incapacidad,
									$fecha_inicio,
									$fecha_final));
				
	
}


 
 

formHeader("Redirecting....");
formJump();
formFooter();
