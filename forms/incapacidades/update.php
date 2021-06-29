<?php

/**
 * Patient disclosures main screen.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Visolve <vicareplus_engg@visolve.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) Visolve <vicareplus_engg@visolve.com>
 * @copyright Copyright (c) 2017-2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once("../../globals.php");
require_once("$srcdir/options.inc.php");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

//if the edit button for editing disclosure is set.
if (isset($_GET['editlid'])) {
    $editlid = $_GET['editlid'];
}
if (isset($_GET['id'])) {
								$p = "SELECT * " .
                                "FROM  form_incapacidades WHERE id = ? ";									
									 $query = sqlStatement($p, array($_GET["id"]));
									 while ($doc = sqlFetchArray($query)) {	
										$incapacidad= $doc['incapacidad']; 	
										$fecha_inicio= $doc['fecha_inicio']; 	
										$fecha_final= $doc['fecha_final'];
									 }

}									 
?>
<!DOCTYPE html>
<html>
<head>
<?php Header::setupHeader(['datetime-picker', 'opener']); ?>

<script>
//function to validate fields in record disclosure page
 
     
$(function () {
    $("#disclosure_form").submit(function (event) {
        event.preventDefault(); //prevent default action
        var post_url = $(this).attr("action");
		 
        var request_method = $(this).attr("method");
        var form_data = $(this).serialize();

        $.ajax({
            url: post_url,
            type: request_method,
            data: form_data
        }).done(function (r) { //
            dlgclose('refreshme', false);
        });
    });

    $('.datepicker').datetimepicker({
        <?php $datetimepicker_timepicker = true; ?>
        <?php $datetimepicker_showseconds = false; ?>
        <?php $datetimepicker_formatInput = false; ?>
        <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
        <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
    });
});
 
</script>
</head>
<body>
    <div class="container" id="record-disclosure">
        <div class="row">
            <div class="col-12">
               
                    <span class="title"><?php echo xlt('Registrar Incapacidad'); ?></span>

            </div>
            <div class="col-12">
                <form name="disclosure_form" id="disclosure_form" method="POST" action="save.php">
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="id" value="<?php echo attr($_GET['id']); ?>" />
                    <input type="hidden" name="save" value="1" />
                    <div class="btn-group">
                        <button class='btn btn-primary btn-save' name='form_save' id='form_save'>
                            <?php echo xlt('Save'); ?>
                        </button>
                        <button class="btn btn-secondary btn-cancel" id='cancel' onclick='top.restoreSession();dlgclose()'>
                            <?php echo xlt('Cancel'); ?>
                        </button>
                    </div>

                     <div class="form-group mt-3">
                        <label><?php echo xlt('Fecha de Inicio'); ?>:</label>
                         <input type='entry' size='20' class='datepicker form-control' name='fecha_inicio' id='fecha_inicio' value='<?php echo xlt($fecha_inicio); ?>'/>&nbsp;
                    </div>
                     <div class="form-group mt-3">
                        <label><?php echo xlt('Fecha de Final'); ?>:</label>
                         <input type='entry' size='20' class='datepicker form-control' name='fecha_final' id='fecha_final' value='<?php echo xlt($fecha_final); ?>'/>&nbsp;
                    </div>
                    <div class="form-group mt-3">
                        <label><?php echo xlt('Descripción de la Incapacidad'); ?>:</label>
                     
                            <textarea class="form-control" name="incapacidad" wrap="auto" rows="4" cols="30"><?php echo xlt($incapacidad); ?></textarea>
                      
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
