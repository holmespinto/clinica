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



if (!empty($_GET)) {
    if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
}
	 							 
								 
								 if($_GET["mode"]=='search'){
 
									  
													$sets = "
														code_type     	= ?,
														code 	= ?,
														tprofesional 	= ?,
														tcentro = ?,
														porcentaje 		= ?";

									sqlQuery("UPDATE tarifas SET $sets WHERE id = ".$_GET["id_tarifa"]."", array(
									$_GET["code_type"],
									$_GET["code"],
									$_GET["tprofesional"],
									$_GET["tcentro"],
									$_GET["porcentaje"]));
								 
										}								 
								  
									$query3 = "SELECT * " .
										"FROM  tarifas" .
										" WHERE  id = ?";
										$bres3 = sqlStatement($query3, array($_GET["id_tarifa"]));	
										while ($doc = sqlFetchArray($bres3)) {
											$key= $doc['id'];	
											$nom_code= $doc['code'];	
											$code_type= $doc['code_type'];	
											$tprofesional= $doc['tprofesional'];	
											$tcentro= $doc['tcentro'];	
											$porcentaje= $doc['porcentaje']; 
										}
 										
									
  //echo "----->>>>".$_GET["id_tarifa"]."";
?> 

<head>
    <title><?php echo attr("Tarifas"); ?></title>
    <?php Header::setupHeader('common'); ?>
 
</head>
<html>
<body>

<div class="row">
<div class="col"> 

<form name="theform" id="theform" method="get" action="update_hoja_tarifas.php">
<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
<input type="hidden" name="id_tarifa" id="id_tarifa" value="<?php echo $_GET["id_tarifa"]; ?>" />
<input type="hidden" name="mode" value="search" />

<table>

 <tr>
  <td width='470px'>
    <div class="float-left">

    <table class='text'>
                   <tr>
                      <td class='col-form-label'>
                            <?php echo attr('Nombre Especializacion'); ?>:
                      </td>
                      <td>
                         <input type='text' name='code' id='code' size='20' value='<?php echo $nom_code; ?>'
                            class='form-control' required />
                      </td>
                   </tr>
                   <tr>
                      <td class='col-form-label'>
                            <?php echo attr('Codigo'); ?>:
                      </td>
                      <td>
                         <input type='text' name='code_type' id='code_type' size='20' value='<?php echo attr($code_type); ?>'
                            class='form-control' required />
                      </td>
                   </tr>
                <tr>
                        <td class='col-form-label'>
                                <?php echo attr('Valor Tarifa Profesional'); ?>:
                        </td>
                        <td>
                           <input type='text' name='tprofesional' id='tprofesional' size='20' value='<?php echo attr($tprofesional); ?>'
                                class='form-control validanumericos' required />
                        </td>
                </tr>
                <tr>
                        <td class='col-form-label'>
                                <?php echo attr('Valor Tarifa Centro'); ?>:
                        </td>
                        <td>
                           <input type='text' name='tcentro' id='tcentro' size='20' value='<?php echo attr($tcentro); ?>'
                                class='form-control validanumericos' required />
                        </td>
                </tr>
               <tr>
                        <td class='col-form-label'>
                                <?php echo attr('Porcentaje'); ?>:
                        </td>
                        <td>
                           <input type='text' name='porcentaje' id='porcentaje' size='20' value='<?php echo attr($porcentaje); ?>'
                                class='form-control validanumericos' required />
                        </td>
                </tr>
        <tr>
            <td>
                <div class="text-center">
          <div class="btn-group" role="group">
            <a href='#' id='search_button' class='btn btn-secondary btn-save' onclick='top.restoreSession(); $("#theform").submit()'>
                <?php echo attr('Actualizar'); ?>
            </a>
          </div>
                </div>
            </td>
        </tr>				
    </table>
    </div>
 
  </td>

 </tr>
</table>


<!-- TODO: Use BS4 classes here !-->
<div id="searchspinner" style="display: inline; visibility: hidden;"><img src="<?php echo $GLOBALS['webroot'] ?>/interface/pic/ajax-loader.gif"></div>

</form>
</div>  <!-- end of search parameters -->
 </div>
 
</body>
</html>