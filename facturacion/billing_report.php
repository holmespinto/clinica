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

 function total_medicamento($id_medicamentos) {		
			 
		foreach (explode(',',$id_medicamentos, -1) as $keys => $value) :
				$query4 = "SELECT * FROM codes_medicamentos where drug_id=?";
				$bres4 = sqlStatement($query4, array($value));
				while ($row4 = sqlFetchArray($bres4)) {
					$precio[]=$row4['precio'];
					}
		 endforeach;
		 
		 if (!is_null($precio)) {
		$valor_medicamento=formatMoney(array_sum($precio));	
		return $valor_medicamento;
		 }
	 
 }
$form_begin_date = DateTimeToYYYYMMDDHHMMSS($_POST['form_begin_date'] ?? '');
$form_end_date = DateTimeToYYYYMMDDHHMMSS($_POST['form_end_date'] ?? ''); 

//$sql_date_from = (!empty($_POST['date_from'])) ? DateTimeToYYYYMMDDHHMMSS($_POST['date_from']) : date('Y-01-01 H:i:s');
//$sql_date_to = (!empty($_POST['date_to'])) ? DateTimeToYYYYMMDDHHMMSS($_POST['date_to']) : date('Y-m-d H:i:s');
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
						  

if($_GET["mode"]=='search'){
	
				if (strlen($_GET["form_begin_date"])>1 AND strlen($_GET["form_end_date"]) >1) {						  
												  $query = "SELECT * " .
														"ambosFROM  form_encounter" .
														" WHERE date BETWEEN '".$_GET["form_end_date"]."' AND '".$_GET["form_end_date"]."'" .
														" ORDER BY encounter LIMIT ".(($compag-1)*$CantidadMostrar)." , ".$CantidadMostrar;		
				 
				
				}elseif((strlen($_GET["form_begin_date"]) > 1) AND strlen($_GET["form_end_date"])== 0){
												  $query = "bSELECT * " .
														"FROM  form_encounter" .
														" WHERE date BETWEEN '".$_GET["form_begin_date"]."' AND '".$_GET["form_begin_date"]."'" .
														" ORDER BY encounter LIMIT ".(($compag-1)*$CantidadMostrar)." , ".$CantidadMostrar;		
							
			   }elseif((strlen($_GET["form_end_date"]) > 1) AND strlen($_GET["form_begin_date"])==0){
							
												  $query = "enSELECT * " .
														"FROM  form_encounter" .
														" WHERE date BETWEEN '".$_GET["form_end_date"]."' AND '".$_GET["form_end_date"]."'" .
														" ORDER BY encounter LIMIT ".(($compag-1)*$CantidadMostrar)." , ".$CantidadMostrar;		
										 
				}elseif(strlen($_GET["Especialista"]) > 0){
												  $query = "SELECT * " .
												" FROM  form_encounter" .
												" WHERE provider_id='".$_GET["Especialista"]."'" .
												" ORDER BY encounter LIMIT ".(($compag-1)*$CantidadMostrar)." , ".$CantidadMostrar;	
			
				}elseif(strlen($_GET["Paciente"]) > 0){
					
										  $query = "SELECT * " .
										"FROM  form_encounter" .
										" WHERE pid='".$_GET["Paciente"]."'" .
										" ORDER BY encounter LIMIT ".(($compag-1)*$CantidadMostrar)." , ".$CantidadMostrar;		

				}else{
					
									  $query = "SELECT * " .
											"FROM  form_encounter" .
											" ORDER BY encounter LIMIT ".(($compag-1)*$CantidadMostrar)." , ".$CantidadMostrar;							
					
				}
				
				
	}else{
				
									  $query = "SELECT * " .
											"FROM  form_encounter" .
											" ORDER BY encounter LIMIT ".(($compag-1)*$CantidadMostrar)." , ".$CantidadMostrar;		

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
    </script>
 <form name="theform" id="theform" method="get" action="billing_report.php">
<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
<input type="hidden" name="search_form" value="1" />
<input type="hidden" name="mode" value="search" />
 
<p>
  <a class="btn btn-primary" data-toggle="collapse" href="#report_parameters" role="button" aria-expanded="false" aria-controls="report_parameters">
    Buscar
  </a>
<div class="row">
  <div class="col">  
<div class="collapse" id="report_parameters" >

<table>

 <tr>
  <td width='470px'>
    <div class="float-left">

    <table class='text'>
                   <tr>
                      <td class='col-form-label'>
                            <?php echo xlt('Especialista'); ?>:
					    </td>
						<td>	
						<select id="Especialista" name="Especialista" class='form-control'>
						<option value="">- <?php echo xlt('Choose'); ?> -</option>
							<?php
							$qfres = "SELECT id,CONCAT(fname, ' ', lname) As Nombre " .
                                "FROM  users";	
							$query_pac = sqlStatement($qfres);	
							while ($doc = sqlFetchArray($query_pac)) {
							echo "<option value='" . attr($doc['id']) . "'";
							echo ">";
							echo text($doc['Nombre']);
							echo "</option>";
							}
						?>
						</select>
                      </td>
                   </tr>
                   <tr>
                      <td class='col-form-label'>
                            <?php echo xlt('Paciente'); ?>:
					    </td>
						<td>	
						<select id="Paciente" name="Paciente" class='form-control'>
						<option value="">- <?php echo xlt('Choose'); ?> -</option>
							<?php
							$pac_sql = "SELECT pid,CONCAT(fname, ' ', lname) As Nombre " .
                                "FROM  patient_data";
							$query_pac = sqlStatement($pac_sql);							
							while ($doc = sqlFetchArray($query_pac)) {
							echo "<option value='" . attr($doc['pid']) . "'";
							echo ">";
							echo text($doc['Nombre']);
							echo "</option>";
							}
						?>						
						</select>
                      </td>
                   </tr>				   
                   <tr>
                      <td class='col-form-label'>
                            <?php echo xlt('Begin Date'); ?>:
                      </td>
                      <td>
                         <input type='text' name='form_begin_date' id='form_begin_date' size='20' value='<?php echo attr(oeFormatDateTime($form_begin_date, 0, true)); ?>'
                            class='datepicker form-control' />
                      </td>
                   </tr>

                <tr>
                        <td class='col-form-label'>
                                <?php echo xlt('End Date'); ?>:
                        </td>
                        <td>
                           <input type='text' name='form_end_date' id='form_end_date' size='20' value='<?php echo attr(oeFormatDateTime($form_end_date, 0, true)); ?>'
                                class='datepicker form-control' />
                        </td>
                </tr>
                <tr>
                        <td class='col-form-label'>
                                <?php echo xlt('Numero de Filas'); ?>:
                        </td>
                        <td>
                           <input type='text' name='form_CantidadMostrar' id='form_CantidadMostrar' size='20' value='<?php echo attr($CantidadMostrar); ?>'
                                class='form-control' />
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
            <a href='#' id='search_button' class='btn btn-secondary btn-search' onclick='top.restoreSession(); $("#theform").submit()'>
                <?php echo xlt('Search'); ?>
            </a>
            <a href='#' id='refresh_button' class='btn btn-secondary btn-refresh' onclick='top.restoreSession(); $("#theform").submit()'>
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


<!-- TODO: Use BS4 classes here !-->
<div id="searchspinner" style="display: inline; visibility: hidden;"><img src="<?php echo $GLOBALS['webroot'] ?>/interface/pic/ajax-loader.gif"></div>

</form>
</div>  <!-- end of search parameters -->
 </div>
 </div>
<?php
if (isset($_POST["mode"]) && $_POST["mode"] == "search_form") {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }

    echo "<div id='resultsummary bg-success'>";
    echo "Enter search criteria above</div>";
}
    if ($res = sqlStatement($query)) {
        for ($iter = 0; $row = sqlFetchArray($res); $iter++) {
            $result[$iter] = $row;
        }

        echo "<div id='resultsummary bg-success'>";
        if (count($result) > $CantidadMostrar) {
            echo "Número de Registros  " . text($CantidadMostrar) . " encontrados";
        } elseif (count($result) == 0) {
            echo "No Registro encontrados";
        } else {
            echo "Número de Registros " . text(count($result)) . " encontrados";
        }

        echo "</div>";
    }
 
?>

</td>
</tr>
</table>		
<br/>
						<div class="row">
							<div class="col-12">
								<div class="table-responsive">
                            <table class="table table-bordered" id="table_display">
							   <thead>
									<tr scope="row" style="border: 1px solid rgba(238, 87, 85, 0.3);">
									  <th class="font-weight-bold text-center" colspan="12" style='border:white 1px solid;background:rgba(238, 87, 85, 0.3);'>REPORTE DE FACTURACION DE LOS ESPECIALISTAS</th>
									</tr>  							   
									<tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
									  <th class="font-weight-bold text-center" colspan="4" style='border:white 1px solid;background: gray;'>Datos de la Consulta</th>
									  <th class="font-weight-bold text-center" colspan="4" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'>Detalles de la Factura del Paciente</th>
									  <th class="font-weight-bold text-center" colspan="2" style='border:white 1px solid;background:#CCA2AE;'>Detalles de la Factura del Centro</th>
									  <th class="font-weight-bold text-center" colspan="3" style='border:white 1px solid;background: #DEF0F6;color:black;'>Accion</th>
									</tr>
								   <tr class="table-active" id="tr_head">
                                        <td class="font-weight-bold"  style="background: gray;"><?php echo xlt('No.'); ?></td>
										<td class="font-weight-bold"  style="background: gray;"><?php echo xlt('Fecha'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_responsable"  style="background: gray;"><?php echo xlt('Responsable'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_paciente"  style="background: gray;"><?php echo xlt('Paciente'); ?></td>
										<td class="font-weight-bold text-center" id="td_head_tipo_pago" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'><?php echo xlt('Tipo de Pago'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_valor_consulta" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'><?php echo xlt('Valor Consulta'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_medicamento" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'><?php echo xlt('Valor Medicamento(s)'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_descuento" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'><?php echo xlt('Descuento'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_descuento" style='border:white 1px solid;background: #CCA2AE;'><?php echo xlt('Total Especialista'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_descuento" style='border:white 1px solid;background: #CCA2AE;'><?php echo xlt('Total Centro'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_accion" style="background: #DEF0F6;"><?php echo xlt('Imprimir'); ?></td>
                                    </tr>
                                    
<?php 							
							$consulta=array();
							$medicamento=array();
							$descuento=array();
							$profesional=array();
							$centro=array();
							$ids=array();

							

								
								
                                $bres = sqlStatement($query);
                                while ($brow = sqlFetchArray($bres)) {									
									$key =$brow['encounter'];
									$provider_id =$brow['provider_id'];
									$pid =$brow['pid'];
									$date=DateToYYYYMMDD($brow['date'] ?? null);
									
									// obtain EL DOCTOR QUE ATENDIO
							 	 
							$q = "SELECT id,CONCAT(fname, ' ', lname) As Nombre " .
                                "FROM  users" .
                                " WHERE id =? ";									
									$query_doc = sqlStatement($q, array($provider_id));
									 while ($doc = sqlFetchArray($query_doc)) {	
									$nom_doctor= $doc['Nombre'];	
									 	
									 }
							$p = "SELECT id,CONCAT(fname, ' ', lname) As Nombre " .
                                "FROM  patient_data" .
                                " WHERE pid =? ";									
									$query_pac = sqlStatement($p, array($pid));
									 while ($pac = sqlFetchArray($query_pac)) {	
									$nom_paciente= $pac['Nombre'];	
									 	
									 }									 
								 $query3 = "SELECT * " .
									"FROM  form_facturas" .
									" WHERE  pid = ?" .
									" AND encounter = ? ";
									$bres3 = sqlStatement($query3, array($pid,$key));	
									while ($row3 = sqlFetchArray($bres3)) {
									$tipo_pago=$row3['tipo_pago'];
									$valor_consulta=formatMoney($row3['valor_consulta']);
									$valor_descuento=formatMoney($row3['descuento']);
									$total=formatMoney($row3['total']);
									$valor_medicamento=total_medicamento($row3["id_medicamentos"]);
									$id_factura=$row3["id"];
									$tipo_consulta=$row3["tipo_consulta"];
									
									}
						$t = "SELECT * " .
                                "FROM  tarifas" .
                                " WHERE id =? ";									
									$query_pac = sqlStatement($t, array($tipo_consulta));
									 while ($tar = sqlFetchArray($query_pac)) {	
									$tprofesional= $tar['tprofesional'];	
									$tcentro= $tar['tcentro'];	
									 	
									 }										 
									 
									 //$csrf_token_form=CsrfUtils::collectCsrfToken(); 
									 
?>									
									<tr class="table">
									<input type="hidden" name="key" value="<?php echo attr($key); ?>" />
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $key;$ids[]=$key; ?></td>
									<td class="font-weight-bold" style="background: #EAEAEA;"><?php echo $date; ?></td>
									<td class="font-weight-bold" style="background: #EAEAEA;"><?php echo $nom_doctor; ?></td>
									<td class="font-weight-bold" style="background: #EAEAEA;"><?php echo $nom_paciente; ?></td>
									<td class="font-weight-bold" style="background: #C1D4C2;"><?php echo $tipo_pago; ?></td>
									<td class="font-weight-bold" style="background: #C1D4C2;"><?php 
									echo $valor_consulta; 
									$consulta[]=number_format($valor_consulta, 3, '.', '');
									?></td>
									<td class="font-weight-bold" style="background: #C1D4C2;"><?php 
									echo $valor_medicamento; 
									$medicamento[]=number_format($valor_medicamento, 3, '.', '');
									?></td>
									<td class="font-weight-bold" style="background: #C1D4C2;"><?php 
									echo $valor_descuento;
										$descuento[]=number_format($valor_descuento, 3, '.', '');									
									?></td>
									<td class="font-weight-bold" style="background: #CCA2AE;"><?php 
									echo $tprofesional;
										$profesional[]=number_format($tprofesional, 3, '.', '');									
									?></td>
									<td class="font-weight-bold" style="background: #CCA2AE;"><?php 
									echo $tcentro;
										$centro[]=number_format($tcentro, 3, '.', '');	
									?></td>
									<td class="font-weight-bold" style="background: #DEF0F6;">
									 
									<?php  
											  echo "<button class='btn btn-primary btn-sm editenc btn-print' value='" . xla('Imprimir') . "' onclick='imprimir".$key."(" . attr_js($str) . ")'></button>\n";
											?>
									</td>
									</tr>
										<script>	
										function imprimir<?php echo attr($key); ?>(id) {
											dlgopen('<?php echo $GLOBALS['webroot'] . "/interface/facturacion/" ?>factura_pdf.php?csrf_token_form=<?php echo attr(CsrfUtils::collectCsrfToken()); ?>&ids=<?php echo attr($key); ?>', '_blank',900,1200);
										} 
										</script>							
								<?php 
																	 								
									unset($key);
									unset($date);
									unset($nom_doctor);
									unset($nom_paciente);
									//unset($tipo_pago);
									//unset($valor_consulta);
									//unset($valor_medicamento);
									//unset($descuento);
									unset($total);							 
								}
										 
										 if(!is_null($consulta))
										  {
											  $Tconsulta=array_sum($consulta);
											  $Tconsulta=number_format($Tconsulta, 3, '.', '');
										   }else{
											$Tconsulta=0;
										   };
										   
										   if(!is_null($medicamento))
										  {
											  $Tmedicamento=array_sum($medicamento);
											  $Tmedicamento=number_format($Tmedicamento, 3, '.', '');
										   }else{
											$Tmedicamento=0;
										   };
										   
										   if(!is_null($descuento))
										  {
											  $Tdescuento=array_sum($descuento);
											  $Tdescuento=number_format($Tdescuento, 3, '.', '');
										   }else{
											$Tdescuento=0;
										   }; 
										   
										   if(!is_null($profesional))
										  {
											  $Tprofesional=array_sum($profesional);
											  $Tprofesional=number_format($Tprofesional, 3, '.', '');
										   }else{
											$Tprofesional=0;
										   }; 
										   
										   if(!is_null($centro))
										  {
											  $Tcentro=array_sum($centro);
											  $Tcentro=number_format($Tcentro, 3, '.', '');
										   }else{
											$Tcentro=0;
										   }; 
										   
																				
								?>	
										</thead>
											<tfoot>
										 <tr>
									 
											<td class="font-weight-bold" style="background:#FFF2CC; text-align:left;" colspan="5" > TOTALES</td>
											<td class="font-weight-bold" style="background: #FFF2CC;"><?php echo '$'.formatMoney($Tconsulta); ?></td>
											<td class="font-weight-bold" style="background: #FFF2CC;"><?php echo $Tmedicamento; ?></td>
											<td class="font-weight-bold" style="background: #FFF2CC;"><?php echo $Tdescuento; ?></td>
											<td class="font-weight-bold" style="background: #FFF2CC;"><?php echo $Tprofesional; ?></td>
											<td class="font-weight-bold" style="background: #FFF2CC;"><?php echo $Tcentro; ?></td>
											<td class="font-weight-bold" style="background: #FFF2CC;">
											<?php $str=implode(",",$ids); 
											  echo "<button class='btn btn-primary btn-sm editenc btn-print' value='" . xla('Imprimir') . "' onclick='imprimir(" . attr_js($str) . ")'>" . xla('Imprimir resultados') . "</button>\n";
											?>
											 
											 
										<script>	
										function imprimir(id) {
											dlgopen('<?php echo $GLOBALS['webroot'] . "/interface/facturacion/" ?>factura_pdf.php?csrf_token_form=<?php echo attr(CsrfUtils::collectCsrfToken()); ?>&ids='+id+'', '_blank',900,1200);
										} 
										</script>									 
										 
										 </tr>
										 
										 
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
					 

					 
				</body>
			</html>
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