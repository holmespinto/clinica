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

require_once(__DIR__ . "/../../globals.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once($GLOBALS['srcdir'] . '/csv_like_join.php');
require_once($GLOBALS['fileroot'] . '/custom/code_types.inc.php');

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;



?>
<html>
<head>
    <title><?php echo xlt("Incapacidades"); ?></title>
    <?php Header::setupHeader('common'); ?>
 
</head>
<?php
$encounter = $_SESSION['encounter'];
$pid = $_SESSION['pid'];

 							if (empty($_GET['search_form'])) {
							$CantidadMostrar=5;
							}else{
								$CantidadMostrar=$_GET['form_CantidadMostrar'];
								
							}
							$queryp = "SELECT COUNT(*) AS TotalReg " .
                                "FROM  form_incapacidades" .
                                " ORDER BY encounter";
								
                                $bres = sqlStatement($queryp);
                                while ($prow = sqlFetchArray($bres)) {						
									$TotalReg=$prow['TotalReg'];	
								}
								$compag=(int)(!isset($_GET['pag'])) ? 1 : $_GET['pag']; 
								$TotalRegistro  =ceil($TotalReg/$CantidadMostrar);    
 
 							$p = "SELECT * " .
                                "FROM  form_encounter" .
                                " WHERE encounter = ? ";
                                $bp = sqlStatement($p, array($_GET["encounter"]));
								while ($r = sqlFetchArray($bp)) {
									$provider_id =$r['provider_id'];
								}	
 
 
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
 
 
 $patdata = getPatientData($pid, "fname,lname,squad");
 /*
 $patdata = sqlQuery("SELECT " .
  "p.fname, p.mname, p.lname, p.pubpid, p.DOB, " .
  "p.street, p.city, p.state, p.postal_code, p.pid " .
  "FROM patient_data AS p " .
  "WHERE p.pid = ? LIMIT 1", array($pid));
  
  */
 ?>
 <style>
tr.head   { font-size:10pt; background-color:#cccccc; text-align:center; }
tr.detail { font-size:10pt; background-color:#eeeeee; }
</style>

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
 
			
$("#theform").submit(function (event) {
        event.preventDefault(); //prevent default action
        var post_url = '<?php echo $GLOBALS["webroot"]."/interface/forms/incapacidades/save.php" ?>',
		 
        request_method = $(this).attr("method"),
        form_data = $(this).serialize();

        $.ajax({
            url: post_url,
            type: request_method,
            data: form_data
        }).done(function (r) { //
            dlgclose('refreshme', false);
		top.restoreSession(); 
		window.location.href = '<?php echo $GLOBALS["webroot"]."/interface/forms/incapacidades/new.php" ?>';
														   		
        });
    });
    $("#theform").on("submit", function() { SubmitForm(this); });
 

			// show the 'searching...' status and submit the form
			var SubmitForm = function(eObj) {
				$("#search_button").attr("disabled", "true");
				$("#refresh_button").css("disabled", "true");
				$("#searchspinner").css("visibility", "visible");
				return top.restoreSession();
			}								       
    $('.datepicker').datetimepicker({
        <?php $datetimepicker_timepicker = true; ?>
        <?php $datetimepicker_showseconds = false; ?>
        <?php $datetimepicker_formatInput = false; ?>
        <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
        <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
    });		
		});
    </script>
 
   <div class="container" id="record-incapacidades">
<p>
  <a class="btn btn-primary" data-toggle="collapse" href="#report_parameters" role="button" aria-expanded="false" aria-controls="report_parameters">
    Registrar Incapacidad
  </a>
 
<div class="collapse" id="report_parameters" > 
<div class="row">
<div class="col"> 

<form name="theform" id="theform" method="POST" action="<?php echo $GLOBALS['webroot']; ?>/interface/forms/incapacidades/save.php">
<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
<input type="hidden" name="pid" value="<?php echo $_SESSION['pid']; ?>" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="save" value="2" />
<input type="hidden" name="encounter" value="<?php echo $_SESSION['encounter']; ?>" />
<input type="hidden" name="provider_id" value="<?php echo $provider_id; ?>" />
<input type='hidden' name='form_CantidadMostrar' id='form_CantidadMostrar' size='20' value='<?php echo attr($CantidadMostrar); ?>'/>
<table>

 <tr>
  <td width='470px'>
    <div class="float-left">
            <div class="col-12">
               
                    <span class="title"><?php echo xlt('Registrar Incapacidad'); ?></span>

            </div>
    <table class='text'>
                   <tr>
                      <td class='col-form-label'>
                            <?php echo xlt('Fecha Inicio'); ?>:
                      </td>
                      <td>
                         <input type='entry' size='20' class='datepicker form-control' name='fecha_inicio' id='fecha_inicio' value='' required />
                      </td>
                   </tr>
                   <tr>
                      <td class='col-form-label'>
                            <?php echo xlt('Fecha Final'); ?>:
                      </td>
                      <td>
                         <input type='entry' size='20' class='datepicker form-control' name='fecha_final' id='fecha_final' value='' required />
                      </td>
                   </tr>
                <tr>
                        <td class='col-form-label'>
                                <?php echo xlt('Descripcion'); ?>:
                        </td>
                        <td>
                           <textarea class="form-control" name="incapacidad" wrap="auto" rows="4" cols="30" required></textarea>
                        </td>
                </tr>

        <tr>
            <td>
                <div class="text-center">
          <div class="btn-group" role="group">
             <button class='btn btn-primary btn-save' name='form_save' id='form_save'>
                <?php echo xlt('Guardar'); ?>
             </button>
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
				<table class="table">
                    <tr>
                        <td class="text-center align-top p-0 pl-3">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr class='head'>
                                        <td>Paciente: </td>
                                        <td><?php echo "" . text($patdata['fname']) . " " . text($patdata['lname']) . ""  ?></td>
                                        <td><?php echo ""  ?></td>
                                        <td><?php echo "" . text($patdata['email']) . " "  ?></td>
                                    </tr>
			                   </table>
                            </div>
                        </td>
                    </tr>
                </table>
                            <table class="table table-bordered" id="table_display">
							   <thead>
									<tr scope="row" style="border: 1px solid rgba(238, 87, 85, 0.3);">
									  <th class="font-weight-bold text-center" colspan="10" style='border:white 1px solid;background:rgba(238, 87, 85, 0.3);'>REGISTRO DE INCAPACIDADES</th>
									</tr>  							   
									<tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: gray;'>NID</th>
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: gray;'><?php echo xlt('Fecha Inicio'); ?></th>
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: gray;'><?php echo xlt('Fecha Final'); ?></th>
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: gray;'><?php echo xlt('Días'); ?></th>
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'><?php echo xlt('Concepto')?></th>
									  <td class="font-weight-bold text-center" style='width:10px;' id="td_head_accion"></td>										
                                        <td class="font-weight-bold text-center" style='width:10px;' id="td_head_accion"></td>											
                                        <td class="font-weight-bold text-center" style='width:10px;' id="td_head_accion"></td>											
									</tr>
								<?php
								$q = "SELECT * " .
                                "FROM  form_incapacidades WHERE encounter=?";									
									 
									$query_doc = sqlStatement($q, array($_SESSION["encounter"]));
									 while ($doc = sqlFetchArray($query_doc)) {	
									$key= $doc['id']; 	
									$incapacidad= $doc['incapacidad']; 	
									$fecha_inicio= $doc['fecha_inicio']; 	
									$fecha_final= $doc['fecha_final']; 
									$dias=validar_dias($key);
									 	
									 
								?>	 
									<tr class="table">								
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $key; ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo DateTimeToYYYYMMDDHHMMSS($fecha_inicio); ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo DateTimeToYYYYMMDDHHMMSS($fecha_final); ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $dias; ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $incapacidad; ?></td>
									
									<td class="font-weight-bold">
									<a class="oe-pull-away oe-help-redirect"  href="#" onclick="sel_update<?php echo attr($key); ?>(this.parentElement.parentElement.parentElement.id);" id="help-href" name="help-href" style="color:#676666">
									<button value='update<?php echo attr($key); ?>' class='btn btn-primary btn-sm editenc btn-update'></button>
									</a>
									</td>
									<td class="font-weight-bold">
									<a class="oe-pull-away oe-help-redirect"  href="#" onclick="sel_print<?php echo attr($key); ?>(this.parentElement.parentElement.parentElement.id);" id="help-href" name="help-href" style="color:#676666">
									<button value='print<?php echo attr($key); ?>' class='btn btn-primary btn-sm editenc btn-print'></button>
									</a>
									</td>
									<td class="font-weight-bold">
									<button value='delete<?php echo attr($key); ?>' onclick="deleteRow<?php echo attr($key); ?>(<?php echo attr($key); ?>);" class='btn btn-primary btn-sm editenc btn-delete'></button>
									</td>
									<script>
									function sel_update<?php echo attr($key); ?>(id) {
											dlgopen('<?php echo $GLOBALS["webroot"] . "/interface/forms/incapacidades/" ?>update.php?id=<?php echo attr($key);?>&csrf_token_form=<?php echo attr(CsrfUtils::collectCsrfToken()); ?>', '_blank', 700, 400, '', '', {onClosed: 'refreshme'});
										}
										
										function sel_print<?php echo attr($key); ?>(id) {
											dlgopen('<?php echo $GLOBALS["webroot"] . "/interface/forms/incapacidades/" ?>report.php?id=<?php echo attr($key);?>&csrf_token_form=<?php echo attr(CsrfUtils::collectCsrfToken()); ?>', '_blank', 700, 400, '', '', {onClosed: 'refreshme'});
										}
									function deleteRow<?php echo attr($key); ?>(idfactura) {
											//alert();
												// Check checkbox checked or not
												  if(idfactura > 0){

													 // Confirm alert
													 var confirmdelete = confirm("Esta seguro que desea eliminar el rewgistro?");
													 if (confirmdelete == true) {
														$.ajax({
														   url: '<?php echo $GLOBALS["webroot"]."/interface/forms/incapacidades/save.php" ?>',
														   type: 'post',
														   data: {id:idfactura,save:'3',csrf_token_form:'<?php echo attr(CsrfUtils::collectCsrfToken()); ?>'},
														   success: function(response){
															 top.restoreSession(); 
															  window.location.href = '<?php echo $GLOBALS["webroot"]."/interface/forms/incapacidades/new.php" ?>';
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