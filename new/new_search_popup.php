<?php

/**
 * new_search_popup.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Rod Roark <rod@sunsetsystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @author    Tyler Wrenn <tyler@tylerwrenn.com>
 * @copyright Copyright (c) 2010-2017 Rod Roark <rod@sunsetsystems.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2020 Tyler Wrenn <tyler@tylerwrenn.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once("../globals.php");
require_once("$srcdir/patient.inc");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

if (!empty($_POST)) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
}

$fstart = isset($_REQUEST['fstart']) ? $_REQUEST['fstart'] + 0 : 0;

$searchcolor = empty($GLOBALS['layout_search_color']) ? 'var(--yellow)' : $GLOBALS['layout_search_color'];
?>
<html>
<head>

<?php Header::setupHeader('opener'); ?>

<style>
    form {
        padding: 0;
        margin: 0;
    }

    #searchCriteria {
        text-align: center;
        width: 100%;
        font-size: 0.8rem;
        background-color: var(--gray300);
        font-weight: bold;
        padding: 3px;
    }

    #searchResultsHeader th {
        font-size: 0.7rem;
    }

    #searchResults {
        width: 100%;
        height: 80%;
        overflow: auto;
    }

    #searchResults table {
        width: 100%;
        border-collapse: collapse;
        background-color: var(--white);
    }

    #searchResults tr {
        cursor: pointer;
    }

    #searchResults td {
        font-size: 0.7rem;
        border-bottom: 1px solid var(--gray200);
    }

    .topResult {
        background-color: <?php echo attr($searchcolor); ?>;
    }

    .billing {
        color: var(--danger);
        font-weight: bold;
    }

    .highlight {
        background-color: var(--info);
        color: var(--white);
    }
</style>

<script>

// This is called when forward or backward paging is done.
//
function submitList(offset) {
 var f = document.forms[0];
 var i = parseInt(f.fstart.value) + offset;
 if (i < 0) i = 0;
 f.fstart.value = i;
 f.submit();
}

</script>
<center>
<?php if ($pubpid_matched) { ?>
<input class='btn btn-primary' type='button' value='<?php echo xla('Cancel'); ?>'
 onclick='dlgclose();' />
<?php } else { ?>
<input class='btn btn-primary' type='button' value='<?php echo xla('Confirm Create New Patient'); ?>' onclick='dlgclose("srcConfirmSave", false);' />
<?php } ?>
</center>

<script>

// jQuery stuff to make the page a little easier to use

$(function () {
  $(".oneresult").mouseover(function() { $(this).addClass("highlight"); });
  $(".oneresult").mouseout(function() { $(this).removeClass("highlight"); });
  $(".oneresult").click(function() { SelectPatient(this); });
});

var SelectPatient = function (eObj) {
<?php
// The layout loads just the demographics frame here, which in turn
// will set the pid and load all the other frames.
  $newPage = "../patient_file/summary/demographics.php?set_pid=";
  $target = "document";

?>
  objID = eObj.id;
  var parts = objID.split("~");
  opener.<?php echo $target; ?>.location.href = '<?php echo $newPage; ?>' + parts[0];
  dlgclose();
  return true;
}

var f = opener.document.forms[0];
<?php if ($pubpid_matched) { ?>
alert(<?php echo xlj('A patient with this ID already exists.'); ?>);
<?php } else { ?>
    // unclear if still needed.
    if (typeof f.create !== 'undefined') {
f.create.value = <?php echo xlj('Confirm Create New Patient'); ?>;
    }
<?php } ?>

</script>
</head>
<body class="body_top">

<div id="searchResultsHeader" class="table-responsive">


<?php

 
 // PARA LA SECCION UNO
 
$Results1BindArray = array();
$Arrayresques_key1=array();
$Arrayresques_value1=array();
$currvalue=1;
 function clear_prefijos($strin){
	$str=str_replace("_uno",'',$strin);
	$str=str_replace("_dos",'',$str);
	return $str;
	 
 }
$tres1 = sqlStatement("SELECT field_id, title FROM layout_options " .
  "WHERE form_id = 'DEM' AND group_id=? AND uor >0 AND" .
  "( edit_options LIKE '%D%' )" .
  "ORDER BY seq",array($currvalue));


		foreach ($_REQUEST as $key => $value) {
					if (strpos($key, '_uno')!== false) {
						array_push($Arrayresques_key1,$key); 
						array_push($Arrayresques_value1,$value); 
					}
		 }
 
if (!is_null($Arrayresques_value1) or(count($Arrayresques_value1)!=0) or(!empty($Arrayresques_value1))) {
	
	$i=0;
		echo '
      <div class="row m-t-3">
        <div class="col-lg-12">
          <div class="card ">
            <div class="card-header bg-blue">
              <h5 class="text-red m-b-0"><strong>Datos Personales del Pacinte</strong></h5>
            </div>
		<table class="table table-condensed table-bordered table-hover"><tr>';
	
		while ($trow1 = sqlFetchArray($tres1)) {
			///$extracols[$trow1['field_id']] = $trow1['title'];
		  
			 if (str_replace("mf_",'',$Arrayresques_key1[$i])==$trow1['field_id'])
			 {
				 array_push($Results1BindArray,$Arrayresques_value1[$i]); 
				 echo "<td class='srMisc'  COLSPAN=1 style='background-color:#E8F0FE;'>" . text(xl_layout_label($trow1['title'])) . "</td>\n";
			 }
			$i++;
		}

echo "</tr><tr id='searchResults'>";	 
	if ($Results1BindArray) {
			foreach ($Results1BindArray as $key => $value) {
				echo "<td class='".$value."' COLSPAN=1><strong>" . text($value) . "</strong></td>\n";
			    
			}
	 
	}
			echo "</tr>
			    </table>
			</div>
		</div>
	</div>";
}	 
 // PARA LA SECCION DOS
$extracols = array();
$Results2BindArray = array();
$Arrayresques_key2=array();
$Arrayresques_value2=array();
$currvalue=2;
$tres2 = sqlStatement("SELECT field_id, title FROM layout_options " .
  "WHERE form_id = 'DEM' AND group_id=? AND uor >0 AND" .
  "( edit_options LIKE '%D%' )" .
  "ORDER BY seq",array($currvalue));

		$value2=0;
		foreach ($_REQUEST as $key => $value) {
					if (strpos($key, '_dos')!== false) {
						array_push($Arrayresques_key2,$key); 
						array_push($Arrayresques_value2,$value); 
					$value2++;
					}
		 }

	if ($value2>0) {
	$i=0;
	echo '
      <div class="row m-t-3">
        <div class="col-lg-12">
          <div class="card ">
            <div class="card-header bg-blue">
              <h5 class="text-red m-b-0"><strong>Datos del Contacto</strong></h5>
            </div>
		<table class="table table-condensed table-bordered table-hover"><tr>';
		while ($trow2 = sqlFetchArray($tres2)) {
		
		if (str_replace("mf_",'',$Arrayresques_key2[$i])==$trow2['field_id'])
			 {
			array_push($Results2BindArray,$Arrayresques_value2[$i]); 		
			
			if($i==0){
					echo "<td class='".$trow2['title']."'  COLSPAN=3 style='background-color:#E8F0FE;'>" . text(xl_layout_label($trow2['title'])) . "</td>\n";
				}else{
					echo "<td class='".$trow2['title']."'  COLSPAN=1 style='background-color:#E8F0FE;'>" . text(xl_layout_label($trow2['title'])) . "</td>\n";
				}

			 }
			
			 $i++;
		}

			echo "</tr><tr>";	 
				if ($Results2BindArray) {
						$j=0;
						foreach ($Results2BindArray as $key => $value) {
							if($j==0){
								echo "<td class='".$value."' COLSPAN=3><strong>" . text($value) . "</strong></td>\n";
							}else{
								echo "<td class='".$value."' COLSPAN=1><strong>" . text($value) . "</strong></td>\n";
								}
						$j++;
						}
				 
				}
			echo "</tr>
			    </table>
			</div>
		</div>
	</div>";
	} 
	
 // PARA LA SECCION CUATRO
$extracols = array();
$Results4BindArray = array();
$Arrayresques_key4=array();
$Arrayresques_value4=array();
$Arraylabels_value4=array();
$currvalue=4;
$tres4 = sqlStatement("SELECT field_id, title FROM layout_options " .
  "WHERE form_id = 'DEM' AND group_id=? AND uor >0 AND" .
  "( edit_options LIKE '%D%' )" .
  "ORDER BY seq",array($currvalue));

		$dataempr=0;
		foreach ($_REQUEST as $key4 => $value4) {
					if (strpos($key4, 'em_')!== false) {
						array_push($Arrayresques_key4,$key4); 
						array_push($Arrayresques_value4,$value4);
						$dataempr++;						
					}
		 }
 
	if ($dataempr>0) {
	
	$i=0;
		echo '
      <div class="row m-t-3">
        <div class="col-lg-12">
          <div class="card ">
            <div class="card-header bg-blue">
              <h5 class="text-red m-b-0"><strong>Datos de la Empresa donde labora</strong></h5>
            </div>
		<table class="table table-condensed table-bordered table-hover"><tr>';
	
		while ($trow3 = sqlFetchArray($tres4)) {
			 if (str_replace("mf_",'',$Arrayresques_key4[$i])==$trow3['field_id'])
			 {
			echo "<td class='srMisc'  COLSPAN=3 style='background-color:#E8F0FE;'>" . text(xl_layout_label($trow3['title'])) . "</td>\n";
			 
				 array_push($Results4BindArray,$Arrayresques_value4[$i]); 
				  
			 }
			$i++;
		}

			echo "</tr><tr id='searchResults'>";	 
				if ($Results4BindArray) {
						foreach ($Results4BindArray as $k => $value) {
							//occupation_cuatro
							 
							if($k==0){$keys='occupation_uno';}else{$keys=$Arrayresques_key4[$k];}
							echo "<td class='".$value."' COLSPAN=3><strong>" . text($value) . "</strong></td>\n";
							 
						}
				 
				}
				 
			echo "</tr>
			    </table>
			</div>
		</div>
	</div>";
		}
		
		// PARA LA SECCION OCHO
$extracols = array();
$Results8BindArray = array();
$Arrayresques_key8=array();
$Arrayresques_value8=array();
$currvalue=8;
$tres8 = sqlStatement("SELECT field_id, title FROM layout_options " .
  "WHERE form_id = 'DEM' AND group_id=? AND uor >0 AND" .
  "( edit_options LIKE '%D%' )" .
  "ORDER BY seq",array($currvalue));	

		$ocho=0;
		foreach ($_REQUEST as $key => $value) {
					if (strpos($key,'guardian')!== false) {
						array_push($Arrayresques_key8,$key); 
						array_push($Arrayresques_value8,$value);
						$ocho++;						
					}
		 }
 
if ($ocho>0) {
	
	$i=0;
		echo '<div class="row m-t-3">
        <div class="col-lg-12">
          <div class="card ">
            <div class="card-header bg-blue">
              <h5 class="text-red m-b-0"><strong>Datos del Acompañante</strong></h5>
            </div>
		<table class="table table-condensed table-bordered table-hover"><tr>';
		while ($trow8 = sqlFetchArray($tres8)) {
		 if (str_replace("mf_",'',$Arrayresques_key8[$i])==$trow8['field_id'])
			 {		
			echo "<tr><td style='background-color:#E8F0FE;'>" . text(xl_layout_label($trow8['title'])) . "</td><td class='".$value."' COLSPAN=1><strong>" .$Arrayresques_value8[$i] . "</strong></td></tr>\n";
			 }	 
			$i++;
		}
			echo "</tr>
			    </table>
			</div>
		</div>
	</div>";
		}

		
?>
 
</table>
</div>

<center>
<?php if ($pubpid_matched) { ?>
<input class='btn btn-primary' type='button' value='<?php echo xla('Cancel'); ?>'
 onclick='dlgclose();' />
<?php } else { ?>
<input class='btn btn-primary' type='button' value='<?php echo xla('Confirm Create New Patient'); ?>' onclick='dlgclose("srcConfirmSave", false);' />
<?php } ?>
</center>

<script>

// jQuery stuff to make the page a little easier to use

$(function () {
  $(".oneresult").mouseover(function() { $(this).addClass("highlight"); });
  $(".oneresult").mouseout(function() { $(this).removeClass("highlight"); });
  $(".oneresult").click(function() { SelectPatient(this); });
});

</script>

</body>
</html>
