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
		
 

		
								 
								 if($_POST["actualiza"]==1){
 
									  
													$sets = "
														tipo_pago     	= ?,
														tipo_consulta 	= ?,
														valor_consulta 	= ?,
														id_medicamentos = ?,
														descuento 		= ?,
														total       	= ?";
													if($_POST["valor_consulta"]==1){
														$valor_consulta =$_POST["tipo_consulta2"];
													}else{														
														 
												$query = "SELECT * " .
														"FROM  tarifas WHERE id='".$_POST["valor_consulta"]."'";
														$bres = sqlStatement($query);
														while ($brow = sqlFetchArray($bres)) {									
															$valor_consulta =$brow['tprofesional'];
														}												
													
													}	

									sqlQuery("UPDATE form_facturas SET $sets WHERE id = ?", array(
									$_POST["tipo_pago"],
									$_POST["valor_consulta"],
									$valor_consulta,
									$_POST["ids_medicamentos"],
									$_POST["valor_descuento"],
									$_POST["valor_pago"],
									$_POST["id_factura"]));
										 					
									$query3 = "SELECT * " .
										"FROM  form_facturas" .
										" WHERE  id = ?";
										$bres3 = sqlStatement($query3, array($_POST["id_factura"]));	
										while ($row3 = sqlFetchArray($bres3)) {
											$encounter=$row3['encounter'];
											$ids_medicamentos=$row3['id_medicamentos'];
											$tipo_consulta=$row3['tipo_consulta'];
											$valor_consulta=$row3['valor_consulta'];
											$pid=$row3['pid'];
											$tipo_pago=$row3['tipo_pago'];
											$valor_descuento=$row3['descuento'];
											$valor_pago=$row3['total'];
										}								 
								 }else{
									 $query3 = "SELECT * " .
										"FROM  form_facturas" .
										" WHERE  id = ?";
										$bres3 = sqlStatement($query3, array($_GET["id_factura"]));	
										while ($row3 = sqlFetchArray($bres3)) {
											$encounter=$row3['encounter'];
											$ids_medicamentos=$row3['id_medicamentos'];
											$tipo_consulta=$row3['tipo_consulta'];
											$valor_consulta=$row3['valor_consulta'];
											$pid=$row3['pid'];
											$tipo_pago=$row3['tipo_pago'];
											$valor_descuento=$row3['descuento'];
											$valor_pago=$row3['total'];
										}
								}
									
									
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
?> 
 <script>
sessionStorage.clear();

 </script>
<head>
    <title><?php echo xlt("Facturación"); ?></title>
    <?php Header::setupHeader('common'); ?>
 
</head>
<html>
<body>
<form class="form-horizontal" name='theform' id='theform' method="post"  action="facturacion_update.php">
	<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
	<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
	<input type="hidden" name="encounter" value="<?php echo $encounter; ?>" />	
	<input type="hidden" name="id_factura" value="<?php echo $_GET["id_factura"]; ?>" />	
	<input type="hidden" name="actualiza" value="1" />	
	
 <div class="row">
        <div class="col-12">
                        <div class="table-responsive">
                            <table class="table" id="table_display">
                                <thead>
									 <tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
										<th class="font-weight-bold text-center" colspan="2" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'>FORMULARIO PARA EL REGISTRO DE PAGO</th>
									 </tr>
								 </thead>	 
									</br>
								   <tr class="table-active" id="tr_head">
                                        <td class="font-weight-bold text-white"  style="background: gray;">Tipo de Pago:</td>
										<td class="font-weight-bold"  style="background: gray;">
												<select class="form-control" id="tipo_pago" name="tipo_pago">
													<?php
													echo "<option value='0'>Forma de Pago</option>";
													$query1112 = "SELECT * FROM list_options where list_id=? AND activity=1 ORDER BY seq, title ";
													$bres1112 = sqlStatement($query1112, array('payment_method'));
													while ($brow1112 = sqlFetchArray($bres1112)) {
														echo "<option value='" .text(xl_list_label($brow1112['option_id'])). "'>" . text(xl_list_label($brow1112['title'])) . "</option>";
													}
													?>
												</select>    										
										</td>
                                    </tr>
										<tr class="table-active" id="tr_consulta">
                                        <td class="font-weight-bold text-white"  style="background: gray;">Valor de la Consulta:</td>
										<td class="font-weight-bold"  style="background: gray;">
										
										 	  										
												<select class="form-control" id="valor_consulta" name="valor_consulta">
													<?php
													
													//echo "<option value='0'>Seleccione el valor de la consulta</option>";
														$query22 = "SELECT * FROM tarifas where id=?";
																$bres22 = sqlStatement($query22, array($tipo_consulta));
																while ($row22 = sqlFetchArray($bres22)) {
																echo "<option value='" . attr($row22['id']) . "'>" . text(xl_list_label($row22['tprofesional'])) . "-" . text(xl_list_label($row22['code'])) . "</option>";
																}												
												
												$query1 = "SELECT * FROM tarifas_users where user_id=? ";
														$bres1 = sqlStatement($query1, array($provider_id));
														while ($row1 = sqlFetchArray($bres1)) {
															$tarifa_id=$row1['tarifa_id']; 
														}												
												
														if($tarifa_id>0){
														$query2 = "SELECT * FROM tarifas where id=?";
																$bres2 = sqlStatement($query2, array($tarifa_id));
																while ($row2 = sqlFetchArray($bres2)) {
																echo "<option value='" . attr($row2['id']) . "'>Por defecto: " . text(xl_list_label($row2['tprofesional'])) . "-" . text(xl_list_label($row2['code'])) . "</option>";
																}
														}else{
															echo "<option value='0'>Aun no se ha asignado un profesional a la tarifa</option>";	
														}
												
												$query = "SELECT * " .
														"FROM  tarifas" .
														" ORDER BY id";
														$bres = sqlStatement($query);
														while ($brow = sqlFetchArray($bres)) {									
															$key =$brow['id'];
															
															$code =$brow['code'];
															$tprofesional =$brow['tprofesional'];
															$tcentro =$brow['tcentro'];
														
														echo "<option value='" . attr($key) . "'>" . text(xl_list_label($brow['tprofesional'])) . "-" . text(xl_list_label($brow['code'])) . "</option>";
													}
													?>
												</select> 										
										</td>
										</tr>
										<tr class="table-active" id="tr_consulta2">
										<td class="font-weight-bold text-white"  style="background: gray;">Digite el valor de la  Consulta de Ginecologia:</td>
										<td class="font-weight-bold"  style="background: gray;">
										<input type="text" id="valor_consulta2"  name="valor_consulta2" class="form-control validanumericos" value="<?php echo $valor_consulta; ?>"  />		  										
										<script>
										$("#valor_consulta2").keypress(function(e) { 
										var code = (e.keyCode ? e.keyCode : e.which); 
										if(code == 13){ 
											var variableAcumuladora = sessionStorage.getItem("variableAcumuladora");
											var datoAcumular=$(this).val(); 
											sessionStorage.setItem("variableAcumuladora", Number(datoAcumular) + Number(variableAcumuladora)); 
											variableAcumuladora = sessionStorage.getItem("variableAcumuladora");
											capturaAcumulable = Number(variableAcumuladora)
											var f = document.forms[0];
											f.valor_pago.value = Number(capturaAcumulable);												
											 
												return false; 
										} 
										});										
										</script>
										</td>
										</tr>
										
										<tr class="table-active" id="tr_medicamento">
                                        <td class="font-weight-bold text-white"  style="background: gray;">Medicamento(s) apicados al paciente:</td>
										<td class="font-weight-bold"  style="background: gray;">
										 <input type="hidden" id="ids_medicamentos"  name="ids_medicamentos" value="0"  />	
										 <input type="hidden" id="id_medicamento"  name="id_medicamento" value="0"  />	
										 <input type="hidden" id="ids"  name="ids" value="0"  />	
										 <select class="form-control" id="lista_medicamento" name="lista_medicamento[]" multiple >
													<?php
												  echo "<option value='0'>Borrar sumatoria</option>"; 
													$query3 = "SELECT * FROM prescriptions where encounter=? AND patient_id=? ORDER BY encounter";
													$bres3 = sqlStatement($query3, array($encounter,$pid));
													while ($row3 = sqlFetchArray($bres3)) {
														 
														list($drug_id,$pass) = explode("-",$row3['rxnorm_drugcode']);
													 
														$query4 = "SELECT * FROM codes_medicamentos where drug_id=?";
														$bres4 = sqlStatement($query4, array($drug_id));
														while ($row4 = sqlFetchArray($bres4)) {
														$precio=$row4['precio'];
														}
														echo "<option value='" . attr($drug) . "'>" . text(xl_list_label($row3['drug'])) . "</option>";
													}											 
			
													?>
												</select> 										
									
									<script>
									$("#lista_medicamento").on('change', function () {
										var lista = $(this);
										var medicamento = $("#valor_medicamento");
									$("#lista_medicamento option:selected").each(function () {	
										document.getElementById("tr_consulta3").style.display = ''; 
										var f = document.forms[0];
													obj = {};		
													$.ajax({
													url: 'json_medicamentos.php?sSearch='+$("#lista_medicamento option:selected").text().substring(0,10)+'&csrf_token_form='+ $("#csrf_token_form").val(),
													type: 'POST',
													beforeSend: function( xhr ) {
													xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
												   },
													cache: false,
													contentType: 'application/json; charset=utf-8',
													datatype: "application/json",
													error: function()
													{
														alert('Ocurrio un error en el servidor ..');
														lista.prop('disabled', false);
													}
													}).done(function( data ) {
													//medicamento.find('option').remove();
													//f.id_medicamento.value='';													
													var obj = $.parseJSON(data);
													for (var i = 0; i < data.length; i++) {
														var texto=obj.data[i].text;
														var id=obj.data[i].id;
														f.id_medicamento.value=id;
														$("#valor_medicamento").append('<option value="'+ texto +'">'+ texto +'</option>');														
														};														

												});
											});
											});
											
										 

										</script>
										</td>
										</tr>
										<tr class="table-active">
										<td class="font-weight-bold text-white" colspan='2' style="background: gray;">Resultado de la Busqueda</td>
										</tr>
										<tr class="table-active" id="tr_consulta3">
										<td colspan='2'>
										<select class="form-control" id="valor_medicamento" name="valor_medicamento" multiple >
										 <option value='0'></option> 
										</select>
										</td>										
										
										</tr>
										<tr class="table-active" id="tr_descuento">
                                        <td class="font-weight-bold text-white"  style="background: gray;">Descuento:</td>
										<td class="font-weight-bold"  style="background: gray;">
										<input type="text" id="valor_descuento"  name="valor_descuento" class="form-control validanumericos" value="<?php echo $valor_descuento; ?>"  />		  										
										<script>
										$("#valor_descuento").keypress(function(e) { 
										var code = (e.keyCode ? e.keyCode : e.which); 
										if(code == 13){ 
											var variableAcumuladora = sessionStorage.getItem("variableAcumuladora");
											var datoAcumular=$(this).val(); 
											sessionStorage.setItem("variableAcumuladora", Number(datoAcumular) - Number(variableAcumuladora)); 
											variableAcumuladora = sessionStorage.getItem("variableAcumuladora");
											capturaAcumulable = Number(variableAcumuladora)
											var f = document.forms[0];
										   f.valor_pago.value = Math.abs(Number(capturaAcumulable));												
											 
										return false; 
										} 
										});	
										</script>
										</td>
										</tr>
										<tr class="table-active" id="tr_pago">
                                        <td class="font-weight-bold text-white"  style="background: gray;">Total a Pagar:</td>
										<td class="font-weight-bold"  style="background: gray;">
										<input type="text" id="valor_pago"  name="valor_pago" class="form-control" value="<?php echo $valor_pago; ?>"  required />		  										
										</td>
										</tr>
										<tr class="table-active" id="tr_Guardar">
                                        <td class="font-weight-bold text-white"  style="background: gray;"></td>
										<td class="font-weight-bold"  style="background: gray;">
										<a class="oe-pull-away oe-help-redirect"  href="#" style="color:#676666">
										<button type="submit" onclick='top.restoreSession()' value='btn' class='btn btn-primary btn-sm editenc btn-save'>Actualizar Pago</button></a>										
										</td>
										</tr>
									</br>
                                <thead>
									  <tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
									   <th class="font-weight-bold text-center" colspan="2" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'></th>
									 </tr>
								 </thead>										
							</table>			
						</div>
		<script>	
			
												$(function(){

												  $('.validanumericos').keypress(function(e) {
													if(isNaN(this.value + String.fromCharCode(e.charCode))) 
													 return false;
												  }).on("cut copy paste",function(e){
													e.preventDefault();
												  });

												});								

										if ($("#tr_consulta2").is(":visible")) {
												document.getElementById("tr_consulta2").style.display = 'none';
											}else {
												document.getElementById("tr_consulta2").style.display = '';
											}
											
											if ($("#tr_consulta3").is(":visible")) {
												document.getElementById("tr_consulta3").style.display = 'none';
											}
											else {
												document.getElementById("tr_consulta3").style.display = '';
											}
						// Set up the Select2 control
					$("#valor_consulta").on('change', function () {
						
						var variableAcumuladora = sessionStorage.getItem("variableAcumuladora");
						$("#valor_consulta option:selected").each(function () {
						var elegido=$(this).val(); 
						
							if (elegido==1) {
								document.getElementById("tr_consulta2").style.display = '';
								
							}else {
									
									document.getElementById("tr_consulta2").style.display = 'none';
									var res = $(this).text().split("-")[0];
									var datoAcumular = res.split(".")[0];
									sessionStorage.setItem("variableAcumuladora", Number(datoAcumular) + Number(variableAcumuladora)); 
									variableAcumuladora = sessionStorage.getItem("variableAcumuladora");
									capturaAcumulable = Number(variableAcumuladora)
									var f = document.forms[0];
								   f.valor_pago.value = Number(capturaAcumulable).toFixed(2);	
							}

							
						});
				   });								 
							
						$("#valor_medicamento").on('change', function () {
					    var f = document.forms[0];
					    var cadena1 = '';
						//DECLARE LAS VARIABLES DE SESSIONS
						var variableAcumuladora = sessionStorage.getItem("variableAcumuladora");
						var variableMedicamento = sessionStorage.getItem("variableMedicamento");
						var variableValue = sessionStorage.getItem("variableValue");
						var variableValores = sessionStorage.getItem("variableValores");
						var variableMedi = sessionStorage.getItem("variableMedi");
						
						//DEFINA LA CIFRA NUMERICA DEL PRECIO
						var cadena1 = $("#valor_medicamento").val();
						var res = cadena1.toString();
						var tmp = res.split("-$")[1]; //retorna un array
							
						//CADA VEZ QUE SELECCIONE EL MEDICAMENTO	
								$("#valor_medicamento option:selected").each(function () {
										datoAcumular = isNaN(tmp) ? 0 : tmp;
										sessionStorage.setItem("variableAcumuladora", Number(datoAcumular) + Number(variableAcumuladora));
										sessionStorage.setItem("variableMedicamento", $('#valor_medicamento').val()+","+variableMedicamento);
										sessionStorage.setItem("variableValue", $('#id_medicamento').val()+","+variableValue);
								
								});


						
						//CANTURE EL TEXTO DE LA LISTA
						sessionStorage.setItem("variableMedi", sessionStorage.getItem("variableMedicamento"));
						f.ids_medicamentos.value =sessionStorage.getItem("variableMedi");
						//CACTURE EL ID DE LA LISTA
						sessionStorage.setItem("variableValores", sessionStorage.getItem("variableValue"));
						f.ids.value =sessionStorage.getItem("variableValores");	
							    
						//TOTAL DE SUMA ACUMULADA
						variableAcumuladora = sessionStorage.getItem("variableAcumuladora");
						capturaAcumulable = Number(variableAcumuladora);						
						f.valor_pago.value = Number(capturaAcumulable).toFixed(2);
					});	
				</script>
			</div>			
		</div>			
	</form>		
</body>
</html>