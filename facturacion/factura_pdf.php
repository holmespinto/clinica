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
use Mpdf\Mpdf;
use OpenEMR\Common\Acl\AclMain;
use OpenEMR\OeUI\OemrUI; 
 
// -------------------------------------------------- //
//                  PROPERTIES
// -------------------------------------------------- //
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

	 $ids= array();
     $config_mpdf = array(
        'tempDir' => $GLOBALS['MPDF_WRITE_DIR'],
        'mode' => $GLOBALS['pdf_language'],
        'format' => 'Letter',
        'default_font_size' => '9',
        'default_font' => 'dejavusans',
        'margin_left' => $GLOBALS['pdf_left_margin'],
        'margin_right' => $GLOBALS['pdf_right_margin'],
        'margin_top' => $GLOBALS['pdf_top_margin'],
        'margin_bottom' => $GLOBALS['pdf_bottom_margin'],
        'margin_header' => '',
        'margin_footer' => '',
        'orientation' => 'P',
        'shrink_tables_to_fit' => 1,
        'use_kwt' => true,
        'autoScriptToLang' => true,
        'keep_table_proportions' => true
    );
    $pdf = new mPDF($config_mpdf);
 

// -------------------------------------------------- //
//            ALLOCATE FPDF RESSOURCE
// -------------------------------------------------- //

//$pdf = new eFPDF('P', 'mm', array(102,252)); // set the orentation, unit of measure and size of the page
$pdf->SetFont('Arial', 'B', $fontSize);
$pdf->SetTextColor(0, 0, 0);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetY(65);
$pdf->Image('logo.jpg',10,6,110);
$pdf->SetFont('Arial','B',14);
$pdf->Ln();
$pdf->Cell(90);
$pdf->Cell(30,10,'REPORTE DE FACTURACION',0,0,'C');
$pdf->Ln(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(15);
$pdf->Cell(30,10,'Datos de la Consulta',0,0,'C');
$pdf->Ln();
$pdf->SetFont('Arial','B',10);
 
    $pdf->Cell(10,10,'No.',1,0,'C');
    $pdf->Cell(40,10,'Fecha',1,0,'C');
    $pdf->Cell(50,10,'Responsable',1,0,'C');
    $pdf->Cell(50,10,'Paciente',1,0,'C');
    $pdf->Cell(50,10,'Identificación',1,0,'C');
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
 
 

 
 
 $query = "SELECT * " .
	"FROM  form_encounter" .
	" WHERE encounter IN (".$_GET['ids'].")";
      $bres = sqlStatement($query);
      while ($brow = sqlFetchArray($bres)) {
		$i=1;  
		$key =$brow['encounter'];
		$provider_id =$brow['provider_id'];
		$pid =$brow['pid'];
		$date=DateToYYYYMMDD($brow['date'] ?? null);
		
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
				$ident_paciente= $pac['pubpid'];	
									 	
			 }			 
		$pdf->Cell(10,10,$key,1,0,'C');
		$pdf->Cell(40,10,$date,1,0,'C');
		$pdf->Cell(50,10,$nom_doctor,1,0,'C');	
		$pdf->Cell(50,10,$nom_paciente,1,0,'C');	
		$pdf->Cell(50,10,$ident_paciente,1,0,'C');	
		$pdf->Ln();   
	 }
		$pdf->Ln(5); 
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(10);
		$pdf->Cell(30,5,'Detalles de la Consulta',0,0,'C');
		$pdf->Ln(10);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(10,10,'No.',1,0,'C');
		$pdf->Cell(40,10,'Tipo de Pago',1,0,'C');
		$pdf->Cell(50,10,'Valor de la Consulta',1,0,'C');
		$pdf->Cell(50,10,'Valor del Medicamento',1,0,'C');
		$pdf->Cell(50,10,'Descuento',1,0,'C');	 
		$pdf->Ln();	 
 $query = "SELECT * " .
	"FROM  form_encounter" .
	" WHERE encounter IN (".$_GET['ids'].")";
      $bres = sqlStatement($query);
      while ($brow = sqlFetchArray($bres)) {
		$key =$brow['encounter'];
		$pid =$brow['pid'];		  
								 $query3 = "SELECT * " .
									"FROM  form_facturas" .
									" WHERE  pid = ?" .
									" AND encounter = ? ";
									$bres3 = sqlStatement($query3, array($pid,$key));	
									while ($row3 = sqlFetchArray($bres3)) {
									$tipo_pago=$row3['tipo_pago'];
									$valor_consulta=formatMoney($row3['valor_consulta']);
									$consulta[]=number_format($valor_consulta, 3, '.', '');
									
									$valor_descuento=formatMoney($row3['descuento']);
									$descuento[]=number_format($valor_descuento, 3, '.', '');
									
									$total=formatMoney($row3['total']);
									$valor_medicamento=total_medicamento($row3["id_medicamentos"]);
									$medicamento[]=number_format($valor_descuento, 3, '.', '');
									
									
									$id_factura=$row3["id"];
									$tipo_consulta=$row3["tipo_consulta"];
									
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
		$pdf->Cell(10,10,$key,1,0,'C');
		$pdf->Cell(40,10,$tipo_pago,1,0,'C');
		$pdf->Cell(50,10,$valor_consulta,1,0,'C');	
		$pdf->Cell(50,10,$valor_medicamento,1,0,'C');	
		$pdf->Cell(50,10,$valor_descuento,1,0,'C');	
		$pdf->Ln();   
	 }   
 		
		$pdf->Ln(5); 
		$pdf->Cell(10,10,'',1,0,'C');
		$pdf->Cell(40,10,'TOTALES',1,0,'C');
		$pdf->Cell(50,10,$Tconsulta,1,0,'C');	
		$pdf->Cell(50,10,$Tmedicamento,1,0,'C');	
		$pdf->Cell(50,10,$Tdescuento,1,0,'C');
		$pdf->Ln(10); 
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(80,10,'Detalles de la Factura del Centro',0,0,'C');
		$pdf->Ln(10);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(10,10,'No.',1,0,'C');
		$pdf->Cell(90,10,'Total Especialista',1,0,'C');
		$pdf->Cell(100,10,'Total Centro',1,0,'C');		
		$pdf->Ln();  						
 $query = "SELECT * " .
	"FROM  form_encounter" .
	" WHERE encounter IN (".$_GET['ids'].")";
      $bres = sqlStatement($query);
      while ($brow = sqlFetchArray($bres)) {
		$key =$brow['encounter'];
		$pid =$brow['pid'];									
								
								$query3 = "SELECT * " .
									"FROM  form_facturas" .
									" WHERE  pid = ?" .
									" AND encounter = ? ";
									$bres3 = sqlStatement($query3, array($pid,$key));	
									while ($row3 = sqlFetchArray($bres3)) {
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
									 $profesional[]=number_format($tprofesional, 3, '.', '');
									 $centro[]=number_format($tprofesional, 3, '.', '');
									 
									 }
									 
		$pdf->Cell(10,10,$key,1,0,'C');
		$pdf->Cell(90,10,$tprofesional,1,0,'C');
		$pdf->Cell(100,10,$tcentro,1,0,'C');
		$pdf->Ln();  		
	  }
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
		$pdf->Ln(5); 
		$pdf->Cell(40,10,'TOTALES',1,0,'C');
		$pdf->Cell(60,10,$Tprofesional,1,0,'C');
		$pdf->Cell(100,10,$Tcentro,1,0,'C');  
	  
$pdf->Ln();     
$pdf->Footer();
$pdf->Output();

?>