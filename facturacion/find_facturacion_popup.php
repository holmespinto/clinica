<?php

/**
 * find_code_popup.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Rod Roark <rod@sunsetsystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2008-2014 Rod Roark <rod@sunsetsystems.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once("../globals.php");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
use OpenEMR\OeUI\OemrUI;



if (!empty($_POST)) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo xlt('Adjuntar valor'); ?></title>
    <?php Header::setupHeader('opener'); ?>
    <script>
        // Standard function
        function selcode(codetype, code, selector, codedesc) {
            if (opener.closed || !opener.set_related) {
                alert(<?php echo xlj('The destination form was closed; I cannot act on your selection.'); ?>);
            } else {
                var msg = opener.set_related(codetype, code, selector, codedesc);
                if (msg) alert(msg);
                dlgclose();
                return false;
            }
        }

        // TBD: The following function is not necessary. See
        // interface/forms/LBF/new.php for an alternative method that does not require it.
        // Rod 2014-04-15

        // Standard function with additional parameter to select which
        // element on the target page to place the selected code into.
        function selcode_target(codetype, code, selector, codedesc, target_element) {
            if (opener.closed || !opener.set_related_target)
                alert(<?php echo xlj('The destination form was closed; I cannot act on your selection.'); ?>);
            else
                opener.set_related_target(codetype, code, selector, codedesc, target_element);
            dlgclose();
            return false;
        }
    </script>
</head>
<body>
    <div class="container-fluid">
	 <form class="form-inline" method='post' name='theform' action='find_facturacion_popup.php'>
                <div class="table-responsive">
                <table class='table table-striped table-responsive-sm'>
                    <thead>
                        <th class='font-weight-bold'></th>
                        <th class='font-weight-bold'><?php echo xlt('Profesional'); ?></th>
                        <th class='font-weight-bold'><?php echo xlt('Especialidad'); ?></th>
                        <th class='font-weight-bold'><?php echo xlt('Valsor'); ?></th>
                        <th class='font-weight-bold'></th>
                    </thead>
                    <tbody>
						<?php 
                          $query = "SELECT * " .
                                "FROM  tarifas" .
                                " ORDER BY id";
                                $bres = sqlStatement($query);
                                
                                while ($brow = sqlFetchArray($bres)) {									
									$key =$brow['id'];
									$code =$brow['code'];
									$tprofesional =$brow['tprofesional'];
									$tcentro =$brow['tcentro'];
?>									
									<tr class="table"> 
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $key; ?></td>					
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $code; ?></td>					
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $tprofesional; ?></td>					
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $tcentro; ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"></td>
									</tr>
								<?php }?>								
									<script>
									/*
									var $radios = $('input[name=opt]').change(function () {
												var value = $radios.filter(':checked').val();
													
												
												   if (opener.closed)
														alert(<?php echo xlj('The destination form was closed; I cannot act on your selection.'); ?>);
													else
													var codetype="<?php echo $code; ?>";	
													var code="<?php echo $code; ?>";	
													var selector="<?php echo $tprofesional; ?>";	
													var codedesc="<?php echo $tprofesional; ?>";	
													//var msg = opener.set_related(codetype, code, selector, codedesc);
													 var msg = opener.document.getElementById('#valor_consulta_35');
													 alert(msg);
													dlgclose();
													return false;												
												
												});
												*/
									</script>	
                    </tbody>
                </table>
                </div>
					
	</div>
	</form>
</body>
</html>
