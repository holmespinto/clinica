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

require_once("../globals.php");
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
 
if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}
Header::setupHeader('common');  
$id_desc=array();			
$descip=array();			
			
			if(($_POST["valor_consulta"]==1) OR($_POST["valor_consulta"]==10)){
				$valor_consulta =$_POST["valor_consulta2"];
			}else{
				$query = "SELECT * " .
				"FROM  tarifas WHERE id='".$_POST["valor_consulta"]."'";
				$bres = sqlStatement($query);
				while ($brow = sqlFetchArray($bres)) {									
					$valor_consulta =formatMoney($brow['tprofesional']);
				}
			}
			
			
	
	$query1 = "SELECT * " .
			"FROM  patient_data WHERE pid='".$_POST["pid"]."'";
			$bres1 = sqlStatement($query1);
			while ($row1 = sqlFetchArray($bres1)) {									
				$fname =$row1['fname'];
				$lname =$row1['lname'];
				$pubpid =$row1['pubpid'];
				$email =$row1['email'];
				$street =$row1['street'];
			} 
			
     
	 $query2 = "SELECT * " .
        "FROM  form_encounter" .
        " WHERE encounter = ? ";
          $bres2 = sqlStatement($query2, array($_POST["encounter"]));
           while ($row2 = sqlFetchArray($bres2)) {									
			$provider_id =$row2['provider_id'];
		    $q = "SELECT id,CONCAT(fname, ' ', lname) As Nombre " .
              "FROM  users" .
              " WHERE id =? ";									
				$query_doc = sqlStatement($q, array($provider_id));
				while ($doc = sqlFetchArray($query_doc)) {	
				$nom_doctor= $doc['Nombre'];	
				}
			}
	$query3 = "SELECT * FROM tarifas where id=?";
		$bres3 = sqlStatement($query3, array($_POST["valor_consulta"]));
		while ($row3 = sqlFetchArray($bres3)) {
			$Tipo_Consulta= text(xl_list_label($row3['code']));	
		}
$precio=array();			
$descip=array();			
$id_desc=explode(',',$_POST["ids"], -1);
foreach ($id_desc as $key => $value) :
		
	 
$query4 = "SELECT m.drug_id,m.descripcion,p.nombre,i.precio_com  
											FROM farmacia_medicamentos_precios AS i,
											farmacia_medicamentos AS m,
											proveedores_medicamentos AS p
									WHERE m.drug_id=?
									AND m.drug_id=i.drug_id
									AND p.id=i.proverdor_id
									AND p.id=m.proverdor_id
									ORDER BY  m.drug_id ASC";		
		
		$bres4 = sqlStatement($query4, array($value));
		while ($row4 = sqlFetchArray($bres4)) {
		  $precio[]=$row4['precio_com'];
		  $descip[]="".$row4['nombre'].'/'.$row4['descripcion'].'/ $ '.$row4['precio_com']."";
		}
 endforeach;
 
 
$valor_medicamento=formatMoney(array_sum($precio));
  
  
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
<html>
<head>
    <title><?php echo xlt("Facturación"); ?></title>
    <?php Header::setupHeader('common'); ?>
</head>
<body>
 <div class="row">
        <div class="col-12">
           <div class="table-responsive">
           <table class="table table-bordered" id="table_display">
                 <thead>
				<tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
					<th class="font-weight-bold text-center" colspan="5" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'>Datos del Paciente</th>
				</tr>
				</thead>
				<tr class="table-active" id="tr_head">
                 <td class="font-weight-bold text-white"  style="background: gray;"><?php echo xlt('Identificación'); ?></td>
				 <td class="font-weight-bold text-white"  style="background: gray;" ><?php echo xlt('Nombre'); ?></td>
                 <td class="font-weight-bold text-white"  style="background: gray;" ><?php echo xlt('Apellidos'); ?></td>
                 <td class="font-weight-bold text-white"  style="background: gray;" ><?php echo xlt('Email'); ?></td>
                 <td class="font-weight-bold text-white"  style="background: gray;" ><?php echo xlt('Dirección'); ?></td>
                 
				</tr>
				<tr class="table-active" id="tr_head">
				 <td class="font-weight-bold" ><?php echo $pubpid; ?></td>
                 <td class="font-weight-bold" ><?php echo $fname; ?></td>
                 <td class="font-weight-bold" ><?php echo $lname; ?></td>
                 <td class="font-weight-bold" ><?php echo $email; ?></td>
                 <td class="font-weight-bold" ><?php echo $street; ?></td>
                 
				</tr>				
                 <thead>
				<tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
					<th class="font-weight-bold text-center" colspan="5" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'>Registro de la Factura</th>
				</tr>
				</thead>
				<tr class="table-active" id="tr_head">
                 <td class="font-weight-bold text-white"  style="background: gray;"><?php echo xlt('Tipo de Pago'); ?></td>
                 <td class="font-weight-bold text-white"  style="background: gray;"><?php echo xlt('Valor Consulta'); ?></td>
                 <td class="font-weight-bold text-white"  style="background: gray;"><?php echo xlt('Valor Medicamento(s)'); ?></td>
                 <td class="font-weight-bold text-white"  style="background: gray;"><?php echo xlt('Descuento'); ?></td>
                 <td class="font-weight-bold text-white"  style="background: gray;"><?php echo xlt('Total a Pagar'); ?></td>
				</tr>
				
				<tr class="table-active" id="tr_head">
                 <td class="font-weight-bold" ><?php echo "".$_POST["tipo_pago"]; ?></td>
                 <td class="font-weight-bold" ><?php echo "$ ".$valor_consulta; ?></td>
                 <td class="font-weight-bold" ><?php echo "$ ".$valor_medicamento; ?></td>
                 <td class="font-weight-bold" ><?php echo "$ ".formatMoney($_POST['valor_descuento']); ?></td>
                 <td class="font-weight-bold" ><?php echo "$ ".formatMoney($_POST['valor_pago']); ?></td>
				</tr>
                 <thead>
				<tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
					<th class="font-weight-bold text-center" colspan="5" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'>Medicamentos Facturados</th>
				</tr>
				</thead>
				<tr class="table-active" id="tr_head">
                 <td class="font-weight-bold text-white"  style="background: gray;"><?php echo xlt('Nombre del Especialista'); ?></td>
				 <td class="font-weight-bold text-white"  style="background: gray;" colspan="2"><?php echo xlt('Tipo de Consulta'); ?></td>
                 <td class="font-weight-bold text-white"  style="background: gray;" colspan="2"><?php echo xlt('Medicamentos'); ?></td>
				</tr>
				<tr class="table-active" id="tr_head">
				 <td class="font-weight-bold" ><?php echo $nom_doctor; ?></td>
                 <td class="font-weight-bold" colspan="2" ><?php echo $Tipo_Consulta; ?></td>
                 <td class="font-weight-bold" colspan="2" >
					<?php 
						echo "<ul>";
					foreach ($descip as $key => $value) :
						echo "<li type='circle'>$value</li>";
					endforeach;
					echo "</ul>";
					?>
				 </td>
                 
				</tr>
                <thead>
				<tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
					<th class="font-weight-bold text-center" colspan="5" style='border:white 1px solid;background: rgba(128, 255, 0, 0.3);'></th>
				</tr>
				</thead>				
			</table>						
		</div>						
	</div>						
</div>						
</body>						
</html>	
<?php
 
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
					str_replace("null","",$_POST["ids"]),
					$_POST['valor_descuento'],
					$_POST['valor_pago']
				]
			);
		}
 	}
	 
?>		