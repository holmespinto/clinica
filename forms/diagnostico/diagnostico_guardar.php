<?php

/**
 * new_patient_save.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */
require_once(__DIR__ . "/../../globals.php");
 
use OpenEMR\Core\Header;
 
use OpenEMR\Common\Csrf\CsrfUtils;
if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}

									 $query .= "SELECT * FROM codes_rips WHERE " .
											 "id = ? ";
									$query_doc = sqlStatement($query, array("".$_GET['key'].""));							
									 
									 while ($doc = sqlFetchArray($query_doc)) {	
									$title= $doc['descripcion'];	
									$Codigo= $doc['cod_principal'];
									 }									
Header::setupHeader('common');  
$id_desc=array();			
$descip=array();			
?>
<html>
<head>

    <title><?php echo xlt("Clinical Instructions"); ?></title>
    <?php Header::setupHeader(['datetime-picker', 'opener']); ?>
</head>
<script>
//function to validate fields in record disclosure page
function submitform() {
    if (document.forms[0].dates.value.length <= 0) {
        document.forms[0].dates.focus();
        document.forms[0].dates.style.backgroundColor = "red";
    }
    else if (document.forms[0].recipient_name.value.length <= 0) {
        document.forms[0].dates.style.backgroundColor = "white";
        document.forms[0].recipient_name.focus();
        document.forms[0].recipient_name.style.backgroundColor = "red";
    }
    else if (document.forms[0].desc_disc.value.length <= 0) {
        document.forms[0].recipient_name.style.backgroundColor = "white";
        document.forms[0].desc_disc.focus();
        document.forms[0].desc_disc.style.backgroundColor = "red";
    }
    else if (document.forms[0].dates.value.length > 0 && document.forms[0].recipient_name.value.length > 0 && document.forms[0].desc_disc.value.length > 0) {
        top.restoreSession();
        document.forms[0].submit();
    }
}
     
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
<body>
 <div class="container" id="record-disclosure">
        <div class="col-12">
           <div class="table-responsive">
                <form name="disclosure_form" id="disclosure_form" method="POST" action="save.php">
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="codrips" value="<?php echo $Codigo; ?>" />
                    <div class="btn-group">
                        <button class='btn btn-primary btn-save' name='form_save' id='form_save'>
                            <?php echo xlt('Save'); ?>
                        </button>
                        <button class="btn btn-secondary btn-cancel" id='cancel' onclick='top.restoreSession();dlgclose()'>
                            <?php echo xlt('Cancel'); ?>
                        </button>
                    </div>	   
           <table class="table table-bordered" id="table_display">
 
				<tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
					<th class="font-weight-bold text-center" colspan="5" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'>Registro de la Factura</th>
				</tr>
				</thead>
				<tr class="table-active" id="tr_head">
                 <td class="font-weight-bold text-white"  colspan="5" style="background: gray;"><?php echo xlt('Código'); ?>-<?php echo xlt('Describcion'); ?></td>
				</tr>
				
				<tr class="table-active" id="tr_head">
                 <td class="font-weight-bold" colspan="5"><?php echo $Codigo; ?>-<?php echo $title;?></td>
				 </tr>
				<tr class="table-active" id="tr_head">
                 <td class="font-weight-bold text-white"  style="background: gray;"><?php echo xlt('Tipo de Problema'); ?></td>
                      <td>
                          <select class='form-control' name='form_code_type'>
                        <?php
						$allowed_code=array();
						$allowed_codes='medical_problem,allergy';
						$allowed_code=explode(',',$allowed_codes);
                        $k=0;
						foreach ($allowed_code as $code) {
                         ?>
                        <option value='<?php echo attr($code) ?>'><?php if($k==0){echo xlt('Problema Médico');}else{echo xlt('Alergia');} ?></option>
                        <?php 
						$k++;
						} ?>						  
						  </select>
						  
                      </td>				 
				 </tr>				
				<tr class="table-active" id="tr_head">
                 <td class="font-weight-bold text-white"  style="background: gray;"><?php echo xlt('Observaciones:'); ?></td>
				 <td class="font-weight-bold" >
				  <textarea name="instruction" id="instruction" class="form-control" cols="80" rows="5" ><?php echo text($check_res['instruction'] ?? ''); ?></textarea>
				 </td>
				 </tr>
                <thead>
				<tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
					<th class="font-weight-bold text-center" colspan="5" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'></th>
				</tr>
				</thead>				
			</table>						
		</div>						
		</form>						
	</div>						
</div>						
</body>						
</html>	
<?php
 
/*
	 $query3 = "SELECT COUNT(*) AS num " .
        "FROM  form_facturas" .
        " WHERE  pid = ?" .
        " AND encounter = ? ";
		$bres3 = sqlStatement($query3, array($_POST["pid"],$_POST["encounter"]));	
		while ($row3 = sqlFetchArray($bres3)) {									
		
		if($row3['num']==0){	 
 
        $sets = "pid      	= ?,
            encounter     	= ?,
            provider_id   	= ?,
            tipo_pago     	= ?,
            tipo_consulta 	= ?,
            valor_consulta 	= ?,
            id_medicamentos = ?,
            descuento 		= ?,
            total       	= ?";
			sqlStatement(
				"INSERT INTO form_facturas SET $sets",
				[
					$_POST["pid"],
					$_POST["encounter"],
					$provider_id,
					$_POST["tipo_pago"],
					$_POST["valor_consulta"],
					$valor_consulta,
					$_POST["ids_medicamentos"],
					$_POST['valor_descuento'],
					$_POST['valor_pago']
				]
			);
		}
 	}
	*/
?>		