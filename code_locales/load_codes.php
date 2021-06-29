<?php

/**
 * Upload and install a designated code set to the codes table.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Rod Roark <rod@sunsetsystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2014 Rod Roark <rod@sunsetsystems.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

set_time_limit(0);

require_once('../globals.php');
//require_once($GLOBALS['fileroot'] . '/custom/code_types.inc.php');
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Common\Logging\EventAuditLogger;
use OpenEMR\Core\Header;

if (!empty($_POST)) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
}
//retrieve the user name
$res = sqlQuery("select username from users where username=?", array($_SESSION["authUser"]));
$user = $res["username"];


if (isset($_POST["mode"]) and $_POST["mode"] == "guardar") {
        $event ='codes-select';
		 $form_cod_principal = $_POST['form_cod_principal'] ?? '';
		 $form_cod_sub = trim($_POST['form_cod_sub']);
		 $form_cod_descrip = $_POST['form_cod_descrip'] ?? '';
        EventAuditLogger::instance()->recordCodigoLocal($event, $user, $form_cod_principal,$form_cod_sub, $form_cod_descrip);
 }
 
 if (isset($_POST["mode"]) and $_POST["mode"] == "delete") {
		$deletelid =$_POST['deletelid'];
         EventAuditLogger::instance()->deleteCodigoLocal($deletelid);
 } 
 
 if (isset($_POST["mode"]) and $_POST["mode"] == "editcode") {
								
								$editcode =$_POST['editcode'];
								$gres = sqlStatement("SELECT CL.id,P.descripcion AS cod_principal,CL.codigo,CL.descripcion FROM code_local_principales AS P, codes_locales AS CL WHERE P.id=CL.cod_principal AND CL.id='".$editcode."'");
                                while ($row = sqlFetchArray($gres)) {
                                     $form_cod_sub = $row['codigo'];
                                     $form_cod_descrip = $row['descripcion'];
                                }
 }
?>
<html>
<script>
   $("#formcode").submit(function (event) {
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
	
 
</script>
<head>
<title><?php echo xlt('Editor de Códigos'); ?></title>
<?php Header::setupHeader(); ?>

</head>

<body class="body_top">

<!-- Required for the popup date selectors -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<span class='title'><?php echo xlt('Registro de Códigos para procedimientos'); ?></span>
 
<form method='post' name='formcode' id='formcode' action='load_codes.php' onsubmit='return top.restoreSession()'>
<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
<input type="hidden" name="mode" value="guardar" />

<div id="report_parameters">

<table>
 <tr>
  <td width='470px'>
    <div class="float-left">

    <table class='text'>

                   <tr>
                      <td class='col-form-label'>
                           Código Principal:
                      </td>
                      <td>
					  <select name='form_cod_principal' id='form_cod_principal' class="form-control">
                        <?php
                                $gres = sqlStatement("SELECT id, descripcion FROM code_local_principales " .
                                "ORDER BY id");
                                while ($grow = sqlFetchArray($gres)) {
                                    echo "<option value='" . attr($grow['id']) . "'>" .
                                    text($grow['descripcion']) . "</option>";
                                }?>
					   </select>
                      </td>
                   </tr>
                <tr>
                        <td class='col-form-label'>
                                Código:
                        </td>
                        <td>
                           <input type='text' name='form_cod_sub' id='form_cod_sub' size='20' value='<?php echo attr($form_cod_sub); ?>'
                                class='form-control' />
                        </td>
                        <td class='col-form-label'>
                                Descripción del procedimiento:
                        </td>
                        <td> 
                           <textarea name='form_cod_descrip' id='form_cod_descrip' wrap="auto" rows="4" cols="30"><?php echo attr($form_cod_descrip); ?></textarea>
                        </td>						
                </tr>
    </table>
    </div>

  </td>
  <td class='h-100' align='left' valign='middle'>
    <table class='w-100 h-100' style='border-left: 1px solid;'>
        <tr>
            <td>
                <div class="text-center">
          <div class="btn-group" role="group">
            <a href='#' id='search_button' class='btn btn-secondary btn-save' onclick='top.restoreSession(); $("#formcode").submit()'>
                <?php echo xlt('Save'); ?>
            </a>
            <a href='#' id='refresh_button' class='btn btn-secondary btn-refresh' onclick='top.restoreSession(); $("#formcode").submit()'>
                <?php echo xlt('Refresh'); ?>
            </a>
          </div>
                </div>
            </td>
        </tr>
    </table>
  </td>
 </tr>
</table>

</div>  <!-- end of search parameters -->
</form>
<br />
    <?php
    // Setup the GET string to append when switching between billing and clinical views.


 

    $pagestart = 0;
    if (isset($_GET['pagesize'])) {
        $pagesize = $_GET['pagesize'];
    } else {
        if (array_key_exists('encounter_page_size', $GLOBALS)) {
            $pagesize = $GLOBALS['encounter_page_size'];
        } else {
            $pagesize = 0;
        }
    }
    if (isset($_GET['pagestart'])) {
        $pagestart = $_GET['pagestart'];
    } else {
        $pagestart = 0;
    }
    $getStringForPage = "&pagesize=" . attr_url($pagesize) . "&pagestart=" . attr_url($pagestart);

    ?>
<script>	
 function changePageSize() {
 
   pagestart = $("#pagestart").val();
    pagesize = $("#selPagesize").val(); 
    top.restoreSession();
    window.location.href = "load_codes.php?pagestart=" + encodeURIComponent(pagestart) + "&pagesize=" + encodeURIComponent(pagesize);
}

function BuscarCodigos() {
 
   
    cod_principal = $("#cod_principal").val(); 
    top.restoreSession();
    window.location.href = "load_codes.php?cod_principal=" + encodeURIComponent(cod_principal);
}

 
</script>
<div class="row m-t-3"> 					  
 <div class="col-lg-4">	
<?php echo xlt('Buscar por Código'); ?>: 
					 <select name='cod_principal' id='cod_principal' onchange="BuscarCodigos();" class="form-control">
                        <?php
                                $gres = sqlStatement("SELECT id, descripcion FROM code_local_principales " .
                                "ORDER BY id");
								 echo "<option value='0'>Seleccione el código principal</option>";
                                while ($grow = sqlFetchArray($gres)) {
                                    echo "<option value='" . attr($grow['id']) . "'>" .
                                    text($grow['descripcion']) . "</option>";
                                }?>
					   </select>
 </div>
 <div class="col-lg-4">	
 </div>					   
<div class="col-lg-4">					   
    <span class="float-right">
	<input type="hidden" name="pagestart" id="pagestart" value="<?php echo $pagestart; ?>" />
        <?php echo xlt('Results per page'); ?>:
        <select id="selPagesize" name="selPagesize" class="form-control" onchange="changePageSize();">
            <?php
            $pagesizes = array(5, 10, 15, 20, 25, 50, 0);
            for ($idx = 0; $idx < count($pagesizes); $idx++) {
                echo "<option value='" . attr($pagesizes[$idx]) . "'";
                if ($pagesize == $pagesizes[$idx]) {
                    echo " selected='true'>";
                } else {
                    echo ">";
                }
                if ($pagesizes[$idx] == 0) {
                    echo xlt('ALL');
                } else {
                    echo text($pagesizes[$idx]);
                }
                echo "</option>";
            }
            ?>
        </select>
    </span>
    </div>
    </div>

    <br />
    <br />
<div id="report_results">
<script> 
$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#example tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
	
        var oTable = $('#example').dataTable({
            "processing": true,
            "lengthMenu": [10, 25, 50, 100]
        });
 
} );
</script> 
<table class='table' id="example">

 <thead class='thead-light'>
  <th class='text-center'>
    Cod Principal
  </th>

  <th class='text-center'>
   Codigo
  </th>

  <th class='text-center'>
    Descripción
  </th>
  <th class='text-center'>
    Eliminar
  </th>
    <th class='text-center'>
    Editar
  </th>
 </thead>
 <tbody>  <!-- added for better print-ability -->
 
                    <?php
                                 if (!isset($_GET['cod_principal'])) {
							 
									$gres = sqlStatement("SELECT CL.id,P.descripcion AS cod_principal,CL.codigo,CL.descripcion FROM code_local_principales AS P, codes_locales AS CL WHERE P.id=CL.cod_principal LIMIT 0,".$pagesize."");
								 }else{
									$gres = sqlStatement("SELECT CL.id,P.descripcion AS cod_principal,CL.codigo,CL.descripcion FROM code_local_principales AS P, codes_locales AS CL WHERE P.id=CL.cod_principal AND CL.cod_principal='".$_GET['cod_principal']."'"); 
								 }
								
								while ($grow = sqlFetchArray($gres)) {
                                   ?><tr class="amendmentrow" id="<?php echo attr($grow['id']); ?>">
								
									<?php 
									$urlparms = "deletelid=".attr_url($grow['id']);
								    echo "<td class='text-center'>".text($grow['cod_principal']) . "</td>";
                                    echo "<td class='text-center'>".text($grow['codigo']) . "</td>";
                                    echo "<td class='text-center'>".text($grow['descripcion']) . "</td>";
                                    
                                   ?>
								   <td class='text-center'>
								   <form method='post' name='formdelete<?php echo $grow['id'];?>' id="formdelete<?php echo $grow['id'];?>" action='load_codes.php?<?php echo $urlparms; ?>'>
									
									<a class='btn btn-secondary btn-cancel' id='deletecode<?php echo $grow['id'];?>' href='#'></a>
										<input type="hidden" name="mode" id="mode" value="delete" />
										<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
										<input type="hidden" name="deletelid" value="<?php echo $grow['id'];?>" />
									<script> 
										 $("#deletecode<?php echo $grow['id'];?>").click(function() { DeleteCode<?php echo $grow['id'];?>(this); });
										var DeleteCode<?php echo $grow['id'];?> = function(note) {
											if (confirm(<?php echo xlj('Are you sure you want to delete this message?'); ?> + '\n ' + <?php echo xlj('This action CANNOT be undone.'); ?>)) {
												top.restoreSession();
												$("#formdelete<?php echo $grow['id'];?>").submit();
											}
										}
									</script>								   
									</form>
									</td>
									<td class='text-center'>
								   <form method='post' name='formdedit<?php echo $grow['id'];?>' id="formdedit<?php echo $grow['id'];?>" action='load_codes.php?<?php echo $urlparms; ?>'>
									
									<a class='btn btn-secondary btn-edit' id='editcode<?php echo $grow['id'];?>' href='#'></a>
										<input type="hidden" name="mode" id="mode" value="editcode" />
										<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
										<input type="hidden" name="editcode" value="<?php echo $grow['id'];?>" />
									<script> 
										 $("#editcode<?php echo $grow['id'];?>").click(function() { EditCode<?php echo $grow['id'];?>(this); });
										var EditCode<?php echo $grow['id'];?> = function(note) {
											top.restoreSession();
											$("#formdedit<?php echo $grow['id'];?>").submit();
											 
										}
									</script>								   
									</form></td>
										</tr>									
								   <?php } ?>
								<tfoot>
									<tr>
										<th>Cod Principal</th>
										<th>Codigo</th>
										<th>Descripción</th>
										<th>Eliminar</th>
										<th>Editar</th>
									</tr>
								</tfoot>
</tbody>
</table>
</div>  <!-- end of search results -->



</body>

</html>
