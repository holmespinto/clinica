<?php

/**
 * Functional cognitive status form.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Jacob T Paul <jacob@zhservices.com>
 * @author    Vinish K <vinish@zhservices.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2015 Z&H Consultancy Services Private Limited <sam@zhservices.com>
 * @copyright Copyright (c) 2017-2019 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once(__DIR__ . "/../../globals.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/lab.inc");

require_once($GLOBALS['srcdir'] . '/csv_like_join.php');
require_once($GLOBALS['fileroot'] . '/custom/code_types.inc.php');

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
$fileroots=$GLOBALS['srcdir']. '/templates/prescription/';

$encounter = $_SESSION['encounter'];
$pid = $_SESSION['pid'];
//$check_res = $encounter ? formFetch("prescriptions", $encounter) : array();
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
  $sqd = "SELECT COUNT(*) AS num
     FROM issue_encounter
     WHERE encounter= ?";
    $qnum = sqlStatement($sqd, array($encounter));
        while ($numrow = sqlFetchArray($qnum)) {
		$num = $numrow['num'];
		}
 
// get problems 
$pres = sqlStatement("SELECT l.type,l.title,l.comments FROM lists AS l,issue_encounter AS ie WHERE l.pid=ie.pid AND l.id=ie.list_id AND ie.pid = ? AND ie.encounter = ? " .
"ORDER BY type, date", array($pid,$encounter));

// get encounters
$eres = sqlStatement("SELECT * FROM form_encounter WHERE encounter=? AND pid = ? " .
"ORDER BY date DESC", array($encounter,$pid));

// get problem/encounter relations
$peres = sqlStatement("SELECT * FROM issue_encounter WHERE encounter=? AND pid = ?", array($encounter,$pid));
$patdata = getPatientData($pid, "fname,lname,squad,pubpid,sex,ss");

// get DIGNOSTICO 
$diagnost = sqlStatement("SELECT * FROM form_clinical_instructions WHERE encounter=? AND pid = ?", array($encounter,$pid));
$oid = fetchProcedureId($pid, $encounter);
$order = getProceduresInfo($oid, $encounter);

    $patient_id = $pid;
    $pdata = getPatientData($pid);
    $facility = getFacility();
    $ins = getAllinsurances($pid);
    $prov_id   = $order[5];
    $lab       = $order[7];
    $provider  = getLabProviders($prov_id);
    $npi       = getNPI($prov_id);
    $pp        = getProcedureProvider($lab);
    $provLabId = getLabconfig();

 
?>

<html>
    <head>
        <title><?php echo xlt("Resetas"); ?></title>
  
<?php Header::setupHeader('datetime-picker'); ?>
 
<html>
<style>
tr.head   { font-size:10pt; background-color:#cccccc; text-align:center; }
tr.detail { font-size:10pt; background-color:#eeeeee; }
</style>

<script>

// These are the possible colors for table rows.
var trcolors = new Object();
// Colors for:            Foreground Background
trcolors['U'] = new Array('var(--black)', 'var(--gray200)'); // unselected
trcolors['K'] = new Array('var(--black)', 'var(--yellow)'); // selected key
trcolors['V'] = new Array('var(--black)', 'var(--indigo)'); // selected value

var pselected = new Object();
var eselected = new Object();
var keyid = null; // id of currently hilited key, if any

<?php require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>

// callback from add_edit_issue.php:
function refreshIssue(issue, title) {
 top.restoreSession();
 location.reload();
}

// New Issue button is clicked.
function newIssue() {
 var f = document.forms[0];
 var tmp = (keyid && f.form_key[1].checked) ? ('?enclink=' + encodeURIComponent(keyid)) : '';
 dlgopen('summary/add_edit_issue.php' + tmp, '_blank', 600, 625);
}

// Determine if a given problem/encounter pair is currently linked.
// If yes, return the "resolved" character (Y or N), else an empty string.
function isPair(problem, encounter) {
 var pelist = document.forms[0].form_pelist;
 // var frag = '/' + problem + ',' + encounter + ',';
 var frag = '/' + problem + ',' + encounter + '/';
 var i = pelist.value.indexOf(frag);
 if (i < 0) return '';
 // return pelist.value.charAt(i + frag.length);
 return 'V';
}

// Unlink a problem/encounter pair.
function removePair(problem, encounter) {
 var pelist = document.forms[0].form_pelist;
 // var frag = '/' + problem + ',' + encounter + ',';
 var frag = '/' + problem + ',' + encounter + '/';
 var i = pelist.value.indexOf(frag);
 if (i >= 0) {
  // pelist.value = pelist.value.substring(0, i) + pelist.value.substring(i + frag.length + 1);
  pelist.value = pelist.value.substring(0, i) + pelist.value.substring(i + frag.length - 1);
  document.forms[0].form_save.disabled = false;
 }
}

// Link a new or modified problem/encounter pair.
// function addPair(problem, encounter, resolved) {
function addPair(problem, encounter) {
 removePair(problem, encounter);
 var pelist = document.forms[0].form_pelist;
 // pelist.value += '' + problem + ',' + encounter + ',' + resolved + '/';
 pelist.value += '' + problem + ',' + encounter + '/';
 document.forms[0].form_save.disabled = false;
}

// Clear displayed highlights.
function doclearall(pfx) {
 var thisarr = (pfx == 'p') ? pselected : eselected;
 for (var id in thisarr) {
  var thistr = document.getElementById(pfx + '_' + id);
  if (thisarr[id]) {
   thisarr[id] = '';
   thistr.style.color = trcolors['U'][0];
   thistr.style.backgroundColor = trcolors['U'][1];
  }
 }
}

function clearall() {
 doclearall('p');
 doclearall('e');
 keyid = null;
}

// Process clicks on table rows.
function doclick(pfx, id) {
 var thisstyle = document.getElementById(pfx + '_' + id).style;
 var thisarr = (pfx == 'p') ? pselected : eselected;
 var piskey = document.forms[0].form_key[0].checked;
 var thisiskey = (pfx == 'p') ? piskey : !piskey;
 var wasset = thisarr[id];
 if (thisiskey) { // they clicked in the key table
  clearall();
  if (!wasset) { // this item is not already hilited
   keyid = id;
   thisarr[id] = 'K';
   thisstyle.color = trcolors['K'][0];
   thisstyle.backgroundColor = trcolors['K'][1];
   // Now hilite the related value table entries:
   if (pfx == 'p') { // key is problems, values are encounters
    for (key in eselected) {
     var resolved = isPair(id, key);
     if (resolved.length > 0) {
      eselected[key] = resolved;
      var valstyle = document.getElementById('e_' + key).style;
      valstyle.color = trcolors[resolved][0];
      valstyle.backgroundColor = trcolors[resolved][1];
     }
    }
   } else { // key is encounters, values are problems
    for (key in pselected) {
     var resolved = isPair(key, id);
     if (resolved.length > 0) {
      pselected[key] = resolved;
      var valstyle = document.getElementById('p_' + key).style;
      valstyle.color = trcolors[resolved][0];
      valstyle.backgroundColor = trcolors[resolved][1];
     }
    }
   }
  }
 } else { // they clicked in the value table
  if (keyid) {
   var resolved = thisarr[id];
   // if (resolved == 'Y') { // it was hilited and resolved, change to unresolved
   //  thisarr[id] = 'N';
   //  thisstyle.color = trcolors['N'][0];
   //  thisstyle.backgroundColor = trcolors['N'][1];
   //  if (pfx == 'p') addPair(id, keyid, 'N'); else addPair(keyid, id, 'N');
   // } else if (resolved == 'N') { // it was hilited and unresolved, remove it
   if (resolved != '') { // hilited, so remove it
    thisarr[id] = '';
    thisstyle.color = trcolors['U'][0];
    thisstyle.backgroundColor = trcolors['U'][1];
    if (pfx == 'p') removePair(id, keyid); else removePair(keyid, id);
   // } else { // not hilited, change to hilited and resolved
   //  thisarr[id] = 'Y';
   //  thisstyle.color = trcolors['Y'][0];
   //  thisstyle.backgroundColor = trcolors['Y'][1];
   //  if (pfx == 'p') addPair(id, keyid, 'Y'); else addPair(keyid, id, 'Y');
   } else { // not hilited, change to hilited
    thisarr[id] = 'V';
    thisstyle.color = trcolors['V'][0];
    thisstyle.backgroundColor = trcolors['V'][1];
    if (pfx == 'p') addPair(id, keyid); else addPair(keyid, id);
   }
  } else {
   alert(<?php echo xlj('You must first select an item in the section whose radio button is checked.') ;?>);
  }
 }
}

</script>
<head>
 <div class="req" id="printableArea">
	  <div class="container">
 	 
	 <?php require_once("./report_head.php"); ?>
  	  
        <div class="barcode">
        <br />
        <br />
            <?php
            /**
             *  This is to store the requisition bar code number to use again if the form needs to be printed or viewed again
             *  But save it the first time through.
             */
               $lab_id = $order[0];
               $storeBar = getBarId($lab_id, $pid);

            if (!empty($storeBar)) {
                $bar = $storeBar['req_id'];
            } else {
                $bar = rand(1000, 999999);
                saveBarCode($bar, $pid, $order[0]);
            }

            ?>
            <img  src="../../forms/requisition/barcode.php?text=<?php echo attr_url($bar); ?>" alt="barcode" /><br />
        &#160;&#160;&#160;&#160;&#160;  <?php echo text($bar); ?>
        </div>

 <div class="container">
            <div class="table-responsive">   
				
				<table class="table">
                    <tr>
                        <td colspan='2' class="text-center font-weight-bold">
                            <b>Datos personales</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-top p-0 pl-3">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr class='head'>
                                        <td colspan='3' class="text-center">
                                            
                                            <span class="font-weight-bold">Datos del Paciente</span>
                                        </td>
                                    </tr>
                                    <tr class='head'>
                                        <td>Nombres y Apellidos</td>
                                        <td>Identificación</td>
                                        <td>Sexo</td>
                                    </tr>
									 
                                    <?php
                                     
                                        $rowid = $row['id']; 
                                        echo "    <tr class='detail' id='p_" . attr($rowid) . "'>\n";
                                        echo "     <td class='align-top'>" . text($patdata['fname']) . " " . text($patdata['lname']) . "</td>\n";
                                        echo "     <td class='align-top'>" . text($patdata['pubpid']) . "</td>\n";
                                        echo "     <td class='align-top'>" . text($patdata['sex']) . "</td>\n";
                                        echo "    </tr>\n";
										echo "    <tr class='detail' id='p_" . attr($rowid) . "'>\n";
                                        echo "     <td class='align-top'colspan='3'>SS : " . text($patdata['ss']) . "</td>\n";
                                        echo "    </tr>\n";										
                                        $endjs .= "pselected[" . js_escape($rowid) . "] = '';\n";
                                     
                                    ?>
                                </table>
                            </div>
                        </td>
						
                        <td class="text-center align-top p-0 pr-3">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr class='head'>
                                        <td colspan='2' class="text-center">
                                            
                                            <span class="font-weight-bold"><?php echo xlt('Encounters Section'); ?></span>
                                        </td>
                                    </tr>
                                    <tr class='head'>
                                        <td><?php echo xlt('Date'); ?></td>
                                        <td>Descripción de la Consulta</td>
                                    </tr>
                                    <?php
                                    while ($row = sqlFetchArray($eres)) {
                                        $provider_id = $row['provider_id'];
										
																				
                                        $rowid = $row['encounter'];
                                        $provider_id = $row['provider_id'];
                                        echo "    <tr class='detail' id='e_" . attr($rowid) . "' >\n";
                                        echo "     <td class='align-top'>" . text(substr($row['date'], 0, 10)) . "</td>\n";
                                        echo "     <td class='align-top'>" . text($row['reason']) . "</td>\n";
                                        echo "    </tr>\n";
                                        $endjs .= "eselected[" . js_escape($rowid) . "] = '';\n";
                                    }
                                    ?>
                                </table>
                            </div>
                        </td>
                    </tr>
			 </table>
			</div>
		</div>			 
       <div class="container">

            <div class="table-responsive">          
                <table class="table">
                    <tr>
                        <td colspan='2' class="text-center font-weight-bold">
                            <b>DATOS DE LA INCAPACIDAD</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-top p-0 pl-3">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr class='head'>
                                        <td colspan='5' class="text-center">
                                            
                                            <span class="font-weight-bold">Detalles de la Incapacidad</span>
                                        </td>
                                    </tr>
                                    <tr class='head'>
										<td>ID</td>
										<td>Concepto</td>
                                        <td>Fecha de Inicio</td>
                                        <td>Fecha de Final</td>
                                        <td>Dias de Incapacidad</td>
                                    </tr>
									 
                                    <?php
 								$q = "SELECT * " .
                                "FROM  form_incapacidades WHERE id=?";									
									 
									$query_doc = sqlStatement($q, array($_GET["id"]));
									 while ($doc = sqlFetchArray($query_doc)) {	
									$key= $doc['id']; 	
									$rowid= $doc['id']; 	
									$incapacidad= $doc['incapacidad']; 	
									$fecha_inicio= $doc['fecha_inicio']; 	
									$fecha_final= $doc['fecha_final'];
									$dias=validar_dias($key);									
                                        
                                        echo "    <tr class='detail' id='p_" . attr($rowid) . "'>\n";
                                        echo "     <td class='align-top'>" . attr($rowid) . "</td>\n";
                                        echo "     <td class='align-top'>" . attr($incapacidad) . "</td>\n";
                                        echo "     <td class='align-top'>" . attr($fecha_inicio) . "</td>\n";
                                        echo "     <td class='align-top'>" . attr($fecha_final) . "</td>\n";
                                         echo "     <td class='align-top'>" . attr($dias) . "</td>\n";
                                        echo "    </tr>\n";
                                        $endjs .= "pselected[" . js_escape($rowid) . "] = '';\n";
                                     }
                                    ?>
                                </table>
                            </div>
                        </td>
     
                    </tr>

					
                </table>
			</div>
		</div>
      <div class="container">

            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td colspan='2' class="text-center font-weight-bold">
                            <?php echo xlt('Issues and Encounters for'); ?> <?php echo text($patdata['fname']) . " " . text($patdata['lname']) . " (" . text($pid) . ")</b>\n"; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-top p-0 pl-3">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr class='head'>
                                        <td colspan='2' class="text-center">
                                            
                                            <span class="font-weight-bold">Sección de Díagnostico</span>
                                        </td>
                                    </tr>
                                    <tr class='head'>
                                        <td><?php echo xlt('Type'); ?></td>
                                        <td><?php echo xlt('Title'); ?></td>
                                    </tr>
                                    <?php
                                    while ($row = sqlFetchArray($diagnost)) {
                                        $rowid = $row['id'];
                                        echo "    <tr class='detail' id='p_" . attr($rowid) . "'>\n";
                                        echo "     <td class='align-top'>" . text($ISSUE_TYPES[($row['code_type'])][1]) . "</td>\n";
                                        echo "     <td class='align-top'>" . text($row['codrips']) . "</td>\n";
                                        echo "    </tr>\n";
                                        $endjs .= "pselected[" . js_escape($rowid) . "] = '';\n";
                                    }
                                    ?>
                                    <tr class='head'>
                                        <td colspan='2' class="text-center">
                                            
                                            <span class="font-weight-bold"><?php echo xlt('Issues Section'); ?></span>
                                        </td>
                                    </tr>
                                    <tr class='head'>
                                        <td><?php echo xlt('Type'); ?></td>
                                        <td><?php echo xlt('Title'); ?></td>
                                    </tr>
                                    <?php
                                    while ($row = sqlFetchArray($pres)) {
                                        $rowid = $row['id'];
                                        echo "    <tr class='detail' id='p_" . attr($rowid) . "'>\n";
                                        echo "     <td class='align-top'>" . text($ISSUE_TYPES[($row['type'])][1]) . "</td>\n";
                                        echo "     <td class='align-top'>" . text($row['title']) . "</td>\n";
                                        //echo "     <td class='align-top'>" . text($row['comments']) . "</td>\n";
                                        echo "    </tr>\n";
                                        $endjs .= "pselected[" . js_escape($rowid) . "] = '';\n";
                                    }
                                    ?>									
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
		</div>
		
       <div class="container">

            <div class="table-responsive">          
                <table class="table">
                    <tr>
                        <td colspan='2' class="text-center font-weight-bold">
                            <b>DATOS DEl ESPECIALISTA</b>
                        </td>
                    </tr>
                    <tr>
					 <?php
					 
	$Qeres = sqlStatement("SELECT * FROM form_encounter WHERE encounter=? AND pid = ? " .
"ORDER BY date DESC", array($encounter,$pid));				 
					 
					     while ($row = sqlFetchArray($Qeres)) {
							$provider_id = $row['provider_id'];
					
								$sql = "SELECT * FROM users WHERE id = ? ";
								 $query_doc = sqlStatement($sql, array($provider_id));
								 while ($rows = sqlFetchArray($query_doc)) {
											$fname = $rows['fname'].' '.$rows['mname'].' '.$rows['lname'];
											 
								  }		
							  }		
					?>	
					<td class="text-center font-weight-bold" colspan='2'>Especialista: <?php echo $fname; ?></td> 
					</tr>					
	                 <tr>
					<td class="text-center font-weight-bold" >Firma:</td><td class="text-center font-weight-bold"> </br></br></br></td>
					</tr>
				</table>
				</div>
			</div>
	  </div>
  </div>  
 

<script>
<?php
 echo $endjs;
if (!empty($_REQUEST['issue'])) {
    echo "doclick('p', " . js_escape($_REQUEST['issue']) . ");\n";
}

if ($alertmsg) {
    echo "alert(" . js_escape($alertmsg) . ");\n";
}
?>
</script>
 
    
 <div class="reqHeader" id="non-printable">
     <input type="button" onclick="printDiv('printableArea')" value="Print">
</div>

<script>
function printDiv(divname)
{
    var printContents = document.getElementById(divname).innerHTML;
    var originalContents = document.body.innerHTML;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>
</body>
 
 			 													
 																
 																
</html>
<style>
table, th, td {
     border: 1px solid black;
     border-collapse: collapse;
 }

 .req {
     margin: auto;
     width: 90%;
     padding: 10px;
 }

 .reqHeader {
     margin: auto;
     width: 90%;
     padding: 10px;
 }

 .cinfo {
     float: left;

 }

 .pdata {

     position: relative;
     right: -205px;
     z-index: -5;

 }

 #printable { display: none; }

    @media print
    {
        #non-printable { display: none; }
        #printable { display: block; }
    }

   .notes {
       padding: 5px;
       position: relative;
       float: left;
       width: 255px;
       height: 125px;
   }

  .dx {
      padding: 5px;
      position: relative;
      float: right;
      border-style: solid;
      border-width: 1px;
      width: 130px;
      height: 125px;
  }

  .plist {
      padding: 5px;
      position: relative;
      float: left;

  }

  .pFill {
      float: left;

  }
  .barcode{
      position: relative;
      right: -380px;
  }
</style>