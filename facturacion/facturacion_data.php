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
    <title><?php echo xlt("Facturación para Paciente"); ?></title>
    <?php Header::setupHeader('common'); ?>
 
</head>
<?php
//$focus = "document.theform.search_term.select();";
?>
<body onload="<?php //echo $focus; ?>">
<?php
    $arrOeUiSettings = array(
    'heading_title' => xl('Aceptar pago'),
    'include_patient_name' => true,// use only in appropriate pages
    'expandable' => true,
    'expandable_files' => array(),//all file names need suffix _xpd
    'action' => "",//conceal, reveal, search, reset, link or back
    'action_title' => "",
    'action_href' => "",//only for actions - reset, link or back
    'show_help_icon' => true,
    'help_file_name' => ""
    );
    $oemr_ui = new OemrUI($arrOeUiSettings);
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
    ?></legend>
        <div class="row">
          <div class="col-lg-6 m-t-3">
          <div class="card">
            <div class="card-body">			
               <h4> <?php echo  $oemr_ui->pageHeading() . "\r\n"; ?></h4>
            </div>
        </div>
        </div>
        </div>
 <script>		
function refreshme() {
        top.restoreSession();
        location.reload();
    }
	 </script>
	 
	 
 <div class="row">
        <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table_display">
                                <thead>
									<tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
									  <th class="font-weight-bold text-center" colspan="3" style='border:white 1px solid;background: gray;'>Datos de la Consulta</th>
									  <th class="font-weight-bold text-center" colspan="5" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'>Datos de la Factura</th>
									  <th class="font-weight-bold text-center" colspan="4" style='border:white 1px solid;'>Accion</th>
									</tr>
									
								   <tr class="table-active" id="tr_head">
                                        <td class="font-weight-bold"  style="background: gray;"><?php echo xlt('IDConsulta'); ?></td>
										<td class="font-weight-bold"  style="background: gray;"><?php echo xlt('Fecha Visita'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_responsable"  style="background: gray;"><?php echo xlt('Responsable'); ?></td>
                                        
										
										<td class="font-weight-bold text-center" id="td_head_tipo_pago" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'><?php echo xlt('Tipo de Pago'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_valor_consulta" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'><?php echo xlt('Valor Consulta'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_medicamento" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'><?php echo xlt('Valor Medicamento(s)'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_descuento" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'><?php echo xlt('Descuento'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_total" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'><?php echo xlt('Total a Pagar'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_accion"><?php echo xlt('Guardar'); ?></td>
                                        <td class="font-weight-bold text-center" id="td_head_accion"><?php echo xlt('Actualizar'); ?></td>										
                                        <td class="font-weight-bold text-center" id="td_head_accion"><?php echo xlt('Eliminar'); ?></td>										
                                        <td class="font-weight-bold text-center" id="td_head_accion"><?php echo xlt('Imprimir'); ?></td>

                                    </tr>
                                    
<?php 
                          $query = "SELECT * " .
                                "FROM  form_encounter" .
                                " WHERE pid = ? " . //AND b.billed = 0
                                " ORDER BY encounter";
                                $bres = sqlStatement($query, array($pid));
                                
                                while ($brow = sqlFetchArray($bres)) {									
									$key =$brow['encounter'];
									$provider_id =$brow['provider_id'];
									$date=DateToYYYYMMDD($brow['date'] ?? null);
									
									// obtain EL DOCTOR QUE ATENDIO
							 	 
							$q = "SELECT id,CONCAT(fname, ' ', lname) As Nombre " .
                                "FROM  users" .
                                " WHERE id =? ";									
									$query_doc = sqlStatement($q, array($provider_id));
									 while ($doc = sqlFetchArray($query_doc)) {	
									$nom_doctor= $doc['Nombre'];	
									 	
									 }
									 
								 $query3 = "SELECT * " .
									"FROM  form_facturas" .
									" WHERE  pid = ?" .
									" AND encounter = ? ";
									$bres3 = sqlStatement($query3, array($pid,$key));	
									while ($row3 = sqlFetchArray($bres3)) {
									$tipo_pago=$row3['tipo_pago'];
									$valor_consulta=formatMoney($row3['valor_consulta']);
									$descuento=formatMoney($row3['descuento']);
									$total=formatMoney($row3['total']);
									$valor_medicamento=total_medicamento($row3["id_medicamentos"]);
									$id_factura=$row3["id"];
									
									}
									 
									 $csrf_token_form=CsrfUtils::collectCsrfToken(); 
									 
?>									

									<tr class="table">
									<div class="tb_row" id="tb_row_1<?php echo attr($key); ?>">
									<input type="hidden" name="key" value="<?php echo attr($key); ?>" />
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $key; ?></td>
									<td class="font-weight-bold" style="background: #EAEAEA;"><?php echo $date; ?></td>
									<td class="font-weight-bold" style="background: #EAEAEA;"><?php echo $nom_doctor; ?></td>
									
									
									<td class="font-weight-bold" style="background: #C1D4C2;"><?php echo $tipo_pago; ?></td>
									<td class="font-weight-bold" style="background: #C1D4C2;"><?php echo "$ ".$valor_consulta; ?></td>
									<td class="font-weight-bold" style="background: #C1D4C2;"><?php echo "$ ".$valor_medicamento; ?></td>
									<td class="font-weight-bold" style="background: #C1D4C2;"><?php echo "$ ".$descuento; ?></td>
									<td class="font-weight-bold" style="background: #C1D4C2;"><?php echo "$ ".$total; ?></td>
									<td class="font-weight-bold"><a class="oe-pull-away oe-help-redirect"  href="#" onclick="sel_consulta<?php echo attr($key); ?>(this.parentElement.parentElement.parentElement.id);" id="help-href" name="help-href" style="color:#676666">
									<button value='btn<?php echo attr($key); ?>' class='btn btn-primary btn-sm editenc btn-save'></button></a>
									</td>
									<script>	
										function sel_consulta<?php echo attr($key); ?>(id) {
											dlgopen('<?php echo $GLOBALS['webroot'] . "/interface/facturacion/" ?>facturacion_data_guardar.php?key=<?php echo attr($key); ?>&pid=<?php echo attr($pid); ?>&form_create=true&pid=<?php echo attr($pid); ?>&form_create=true&csrf_token_form=<?php echo attr(CsrfUtils::collectCsrfToken()); ?>', '_blank', 700, 400, '', '', {onClosed: 'refreshme'});
										}
										function sel_imprimir<?php echo attr($key); ?>(id) {
											dlgopen('<?php echo $GLOBALS['webroot'] . "/interface/facturacion/" ?>facturacion_print.php?id_factura=<?php echo attr($id_factura); ?>&csrf_token_form=<?php echo attr(CsrfUtils::collectCsrfToken()); ?>', '_blank', 700, 400);
										}
										function sel_update<?php echo attr($key); ?>(id) {
											dlgopen('<?php echo $GLOBALS['webroot'] . "/interface/facturacion/" ?>facturacion_update.php?id_factura=<?php echo attr($id_factura); ?>&csrf_token_form=<?php echo attr(CsrfUtils::collectCsrfToken()); ?>', '_blank', 700, 400, '', '', {onClosed: 'refreshme'});
										}
										 										
									 
										function deleteRow<?php echo attr($key); ?>(idfactura) {
											//alert();
												// Check checkbox checked or not
												  if(idfactura > 0){

													 // Confirm alert
													 var confirmdelete = confirm("Esta seguro que desea eliminar el rewgistro?");
													 if (confirmdelete == true) {
														$.ajax({
														   url: 'facturacion_delete.php',
														   type: 'post',
														   data: {id:idfactura,csrf_token_form:'<?php echo attr(CsrfUtils::collectCsrfToken()); ?>'},
														   success: function(response){
															 top.restoreSession(); 
															  window.location.href = '<?php echo "$rootdir/facturacion/facturacion_data.php"; ?>';
														   }
														});
													 } 
												  }
										}										
									</script>
									<td class="font-weight-bold">
									<a class="oe-pull-away oe-help-redirect"  href="#" onclick="sel_update<?php echo attr($key); ?>(this.parentElement.parentElement.parentElement.id);" id="help-href" name="help-href" style="color:#676666">
									<button value='update<?php echo attr($key); ?>' class='btn btn-primary btn-sm editenc btn-update'></button>
									</a>
									</td>
									<td class="font-weight-bold">
									<button value='delete<?php echo attr($key); ?>' onclick="deleteRow<?php echo attr($key); ?>(<?php echo attr($id_factura); ?>);" class='btn btn-primary btn-sm editenc btn-delete'></button>
									</td>
									<td class="font-weight-bold">
									<a class="oe-pull-away oe-help-redirect"  href="#" onclick="sel_imprimir<?php echo attr($key); ?>(this.parentElement.parentElement.parentElement.id);" id="help-href" name="help-href" style="color:#676666">
									<button value='print<?php echo attr($key); ?>' class='btn btn-primary btn-sm editenc btn-print'></button>
									</a>
									</td>
										</div>
									</tr>
									
								<?php 
									unset($key);
									unset($date);
									unset($nom_doctor);
									unset($tipo_pago);
									unset($valor_consulta);
									unset($valor_medicamento);
									unset($descuento);
									unset($total);							 
								}
								?>	
                                </thead>
								
                                </table>
                                </div>
								
								
        </div>
    </div>

</body>
</html>
