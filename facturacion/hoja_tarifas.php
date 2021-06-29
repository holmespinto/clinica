<?php

/**
 * new.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once("../globals.php");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
use OpenEMR\OeUI\OemrUI;


?>
<html>
<head>
    <title><?php echo xlt("Administrador de Facturación"); ?></title>
    <?php Header::setupHeader('common'); ?>
 
</head>
<?php
 function formatMoney($number, $fractional=false) {
    if ($fractional) {
        $number = sprintf('%.2f', $number);
    }
    while (true) {
        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
        if ($replaced != $number) {
            $number = $replaced;
        } else {
            break;
        }
    }
    return $number;
}	

 							if (empty($_GET['search_form'])) {
							$CantidadMostrar=5;
							}else{
								$CantidadMostrar=$_GET['form_CantidadMostrar'];
								
							}
							$queryp = "SELECT COUNT(*) AS TotalReg " .
                                "FROM  form_encounter" .
                                " ORDER BY encounter";
								
                                $bres = sqlStatement($queryp);
                                while ($prow = sqlFetchArray($bres)) {						
									$TotalReg=$prow['TotalReg'];	
								}
								$compag=(int)(!isset($_GET['pag'])) ? 1 : $_GET['pag']; 
								$TotalRegistro  =ceil($TotalReg/$CantidadMostrar);    
 
 
 if ($_GET['mode']=='search') {
				$sets = "
				code_type     	= ?,
				code 	= ?,
				tprofesional 	= ?,
				tcentro = ?,
				porcentaje 		= ?";
			sqlStatement(
				"INSERT INTO tarifas SET $sets",
				[
					$_GET["code_type"],
					$_GET["code"],
					$_GET["tprofesional"],
					$_GET["tcentro"],
					$_GET["porcentaje"]
				]
			);					 
}
 
 ?>
<body class="body_top">
<script>
 $(function () {
  var win = top.printLogSetup ? top : opener.top;
  win.printLogSetup(document.getElementById('printbutton'));
$("#printbutton").click(function() { SaveTemplate(this); });
     // save the template, overwriting the older version
    var SaveTemplate = function(btnObj) {
        if (! confirm(<?php echo xlj('You are about to permanently replace the existing template. Are you sure you wish to continue?'); ?>)) {
            return false;
        }
        //$("#formaction").val("printbutton");
        $("#theform").submit();
    }
 });
</script>           
 
 
<?php Header::setupHeader('datetime-picker'); ?>
    <script>
        $(function () {
            $('.datepicker').datetimepicker({
                <?php $datetimepicker_timepicker = true; ?>
                <?php $datetimepicker_showseconds = true; ?>
                <?php $datetimepicker_formatInput = true; ?>
                <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
                <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
            });
    $("#theform").on("submit", function() { SubmitForm(this); });
 

			// show the 'searching...' status and submit the form
			var SubmitForm = function(eObj) {
				$("#search_button").attr("disabled", "true");
				$("#refresh_button").css("disabled", "true");
				$("#searchspinner").css("visibility", "visible");
				return top.restoreSession();
			}								       
		
		});

												$(function(){

												  $('.validanumericos').keypress(function(e) {
													if(isNaN(this.value + String.fromCharCode(e.charCode))) 
													 return false;
												  }).on("cut copy paste",function(e){
													e.preventDefault();
												  });

												});		
    </script>
 
 
<p>
  <a class="btn btn-primary" data-toggle="collapse" href="#report_parameters" role="button" aria-expanded="false" aria-controls="report_parameters">
    Registrar Tarifa
  </a>
 
<div class="collapse" id="report_parameters" > 
<div class="row">
<div class="col"> 

<form name="theform" id="theform" method="get" action="hoja_tarifas.php">
<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
<input type="hidden" name="mode" value="search" />
<input type='hidden' name='form_CantidadMostrar' id='form_CantidadMostrar' size='20' value='<?php echo attr($CantidadMostrar); ?>'/>
<table>

 <tr>
  <td width='470px'>
    <div class="float-left">

    <table class='text'>
                   <tr>
                      <td class='col-form-label'>
                            <?php echo xlt('Nombre Especializacion'); ?>:
                      </td>
                      <td>
                         <input type='text' name='code' id='code' size='20' value=''
                            class='form-control' required />
                      </td>
                   </tr>
                   <tr>
                      <td class='col-form-label'>
                            <?php echo xlt('Código Especializacion'); ?>:
                      </td>
                      <td>
                         <input type='text' name='code_type' id='code_type' size='20' value=''
                            class='form-control' required />
                      </td>
                   </tr>
                <tr>
                        <td class='col-form-label'>
                                <?php echo xlt('Valor Tarifa Profesional'); ?>:
                        </td>
                        <td>
                           <input type='text' name='tprofesional' id='tprofesional' size='20' value=''
                                class='form-control validanumericos' required />
                        </td>
                </tr>
                <tr>
                        <td class='col-form-label'>
                                <?php echo xlt('Valor Tarifa Centro'); ?>:
                        </td>
                        <td>
                           <input type='text' name='tcentro' id='tcentro' size='20' value=''
                                class='form-control validanumericos' required />
                        </td>
                </tr>
               <tr>
                        <td class='col-form-label'>
                                <?php echo xlt('Porcentaje'); ?>:
                        </td>
                        <td>
                           <input type='text' name='porcentaje' id='porcentaje' size='20' value=''
                                class='form-control validanumericos' required />
                        </td>
                </tr>
        <tr>
            <td>
                <div class="text-center">
          <div class="btn-group" role="group">
            <a href='#' id='search_button' class='btn btn-secondary btn-save' onclick='top.restoreSession(); $("#theform").submit()'>
                <?php echo xlt('Guardar'); ?>
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
 </div>
 <br/>
 <br/>
 <br/>
 
 
						<div class="row">
							<div class="col-12">
								<div class="table-responsive">
                            <table class="table table-bordered" id="table_display">
							   <thead>
									<tr scope="row" style="border: 1px solid rgba(238, 87, 85, 0.3);">
									  <th class="font-weight-bold text-center" colspan="10" style='border:white 1px solid;background:rgba(238, 87, 85, 0.3);'>REGISTRO DE TARIFAS PARA EL CENTRO</th>
									</tr>  							   
									<tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: gray;'>NID</th>
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: gray;'><?php echo xlt('Nombre'); ?></br><?php echo xlt('Especializacion')?></th>
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: gray;'><?php echo xlt('código'); ?></th>
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'><?php echo xlt('Valor'); ?></br><?php echo xlt('Tarifa')?></br><?php echo xlt('Profesional')?></th>
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background:#CCA2AE;'><?php echo xlt('Valor'); ?></br><?php echo xlt('Tarifa')?></br><?php echo xlt('Centro')?></th>
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background:#CCA2AE;'><?php echo xlt('%'); ?></br><?php echo xlt('Tarifa')?></br><?php echo xlt('Centro')?></th>
									    <td class="font-weight-bold text-center" style='width:10px;' id="td_head_accion"></td>										
                                        <td class="font-weight-bold text-center" style='width:10px;' id="td_head_accion"></td>											
									</tr>
								<?php
								$q = "SELECT * " .
                                "FROM  tarifas";									
									$query_doc = sqlStatement($q);
									 while ($doc = sqlFetchArray($query_doc)) {	
									$key= $doc['id'];	
									$nom_code= $doc['code'];	
									$code_type= $doc['code_type'];	
									$tprofesional= $doc['tprofesional'];	
									$tcentro= $doc['tcentro'];	
									$porcentaje= $doc['porcentaje'];	
									 	
									 
								?>	 
									<tr class="table">								
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $key; ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $nom_code; ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo strtoupper($code_type); ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo formatMoney($tprofesional); ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo formatMoney($tcentro); ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $porcentaje; ?></td>
									<td class="font-weight-bold">
									<a class="oe-pull-away oe-help-redirect"  href="#" onclick="sel_update<?php echo attr($key); ?>(this.parentElement.parentElement.parentElement.id);" id="help-href" name="help-href" style="color:#676666">
									<button value='update<?php echo attr($key); ?>' class='btn btn-primary btn-sm editenc btn-update'></button>
									</a>
									</td>
									<td class="font-weight-bold">
									<button value='delete<?php echo attr($key); ?>' onclick="deleteRow<?php echo attr($key); ?>(<?php echo attr($key); ?>);" class='btn btn-primary btn-sm editenc btn-delete'></button>
									</td>
									<script>
									function sel_update<?php echo attr($key); ?>(id) {
											dlgopen('<?php echo $GLOBALS['webroot'] . "/interface/facturacion/" ?>update_hoja_tarifas.php?id_tarifa=<?php echo attr($key); ?>&csrf_token_form=<?php echo attr(CsrfUtils::collectCsrfToken()); ?>', '_blank', 700, 400, '', '', {onClosed: 'refreshme'});
										}
									function deleteRow<?php echo attr($key); ?>(idfactura) {
											//alert();
												// Check checkbox checked or not
												  if(idfactura > 0){

													 // Confirm alert
													 var confirmdelete = confirm("Esta seguro que desea eliminar el rewgistro?");
													 if (confirmdelete == true) {
														$.ajax({
														   url: 'tarifas_delete.php',
														   type: 'post',
														   data: {id:idfactura,csrf_token_form:'<?php echo attr(CsrfUtils::collectCsrfToken()); ?>'},
														   success: function(response){
															 top.restoreSession(); 
															  window.location.href = '<?php echo "$rootdir/facturacion/hoja_tarifas.php"; ?>';
														   }
														});
													 } 
												  }
										}										
									</script>
									</tr>
									 <?php } ?>
									<tfoot>
										<tr>
										 <td class="font-weight-bold text-center" colspan="26" style="background: #E1E4F0;">
												<?php 

										//echo "<b>La cantidad de resgistro se dividio a: </b>".$TotalRegistro." para mostrar 5 en 5<br>";
										 /*Sector de Paginacion */
										
										//Operacion matematica para boton siguiente y atras 
										$IncrimentNum =(($compag +1)<=$TotalRegistro)?($compag +1):1;
										$DecrementNum =(($compag -1))<1?1:($compag -1);
									  
										echo "<ul><li class=\"btn\"><a href=\"?pag=".$DecrementNum."\">◀</a></li>";
										//Se resta y suma con el numero de pag actual con el cantidad de 
										//numeros  a mostrar
										 $Desde=$compag-(ceil($CantidadMostrar/2)-1);
										 $Hasta=$compag+(ceil($CantidadMostrar/2)-1);
										 
										 //Se valida
										 $Desde=($Desde<1)?1: $Desde;
										 $Hasta=($Hasta<$CantidadMostrar)?$CantidadMostrar:$Hasta;
										 //Se muestra los numeros de paginas
										 for($i=$Desde; $i<=$Hasta;$i++){
											//Se valida la paginacion total
											//de registros
											if($i<=$TotalRegistro){
												//Validamos la pag activo
											  if($i==$compag){
											   echo "<li class=\"active\"><a href=\"?pag=".$i."\">".$i."</a></li>";
											  }else {
												echo "<li><a href=\"?pag=".$i."\">".$i."</a></li>";
											  }     		
											}
										 }
										echo "<li class=\"btn\"><a href=\"?pag=".$IncrimentNum."\">▶</a></li></ul>";
										 
												 
												 ?>										 
										 </td>
										 </tr>
									</tfoot>										
								</table>
								
						</div>
					</div>
				</div>
 <br/>
 <style>
        /* specifically include & exclude from printing */
        @media print {
            #report_parameters {
                visibility: hidden;
                display: none;
            }
            #report_parameters_daterange {
                visibility: visible;
                display: inline;
            }
            #report_results table {
               margin-top: 0;
            }
        }

        /* specifically exclude some from the screen */
        @media screen {
            #report_parameters_daterange {
                visibility: hidden;
                display: none;
            }
        }
		table{
				width: 450px;
			}
	table th{
		/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#1e5799+0,2989d8+50,7db9e8+100 */
		background: rgb(30,87,153); /* Old browsers */
		background: -moz-linear-gradient(top,  rgba(30,87,153,1) 0%, rgba(41,137,216,1) 50%, rgba(125,185,232,1) 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  rgba(30,87,153,1) 0%,rgba(41,137,216,1) 50%,rgba(125,185,232,1) 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  rgba(30,87,153,1) 0%,rgba(41,137,216,1) 50%,rgba(125,185,232,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1e5799', endColorstr='#7db9e8',GradientType=0 ); /* IE6-9 */
		color: #FFFFFF;
		height: 30px;
		}
	   .active > a{
	   	background: rgb(255,116,0); 
	   }
	  ul{
	  	margin-left: 0px;
	  	    padding: 0px;
	  } 
      ul > li{
      	list-style: none;
      	display: inline-block;
      	margin-right:7px;
      }
      ul > li > a {
      	color: #FFFFFF;
      	text-decoration: none;
      	padding: 5px 10px 5px 10px;
        display: block;
		background: #1e5799; /* Old browsers */
		border-radius: 20px;

      }
      .btn > a{
      	padding: 2px;
		background: #1e5799; /* Old browsers */
		 border-radius: 2px;
		 text-align: center;
		 width:30px;
      }
      table{
      	border-collapse: collapse;
      }
		td , th{
      	padding: 2px;
      	text-align: center;
      }		
    </style> 