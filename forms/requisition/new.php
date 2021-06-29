<?php

/**
 *  Lab Requisition Form
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2016-2017 Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright Copyright (c) 2019 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once(__DIR__ . "/../../globals.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/lab.inc");

use OpenEMR\Core\Header;

formHeader("Form:Lab Requisition");

$returnurl = 'encounter_top.php';

$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : '');
$obj = $formid ? formFetch("form_requisition", $formid) : array();

global $pid ;

$encounter = $_SESSION['encounter'];

$oid = fetchProcedureId($pid, $encounter);

if (empty($oid)) {
    print "<center>" . xlt('No Order found, please enter procedure order first') . "</center>";
    exit;
}

    $patient_id = $pid;
    $pdata = getPatientData($pid);
    $facility = getFacility();
    $ins = getAllinsurances($pid);

if (empty($ins)) {
    $responsibleParty = getSelfPay($pid);
}
    
	$order = getProceduresInfo($oid, $encounter);


if (empty($order)) {
    echo xlt('procedure order not found in database contact tech support');
    exit;
}

    $prov_id   = $order[5];
    $lab       = $order[7];
    $provider  = getLabProviders($prov_id);
    $npi       = getNPI($prov_id);
    $pp        = getProcedureProvider($lab);
    $provLabId = getLabconfig();



?>

<!DOCTYPE html>
<html>
<head>
    <?php Header::setupHeader(); ?>

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
</head>

<body>
<div class="container">
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
        <div class="reqHeader" id="printableArea">
        <p><font size="4"><b><?php print xlt('Requisition Number') ?>:</b> <?php echo text($bar); ?>  &#160;&#160;&#160;&#160;&#160;&#160;<b><?php print xlt('Client Number') ?>:</b> <?php echo text($provLabId['recv_fac_id']); ?></font></p>
           <div class="cinfo">
           <font size="4">
                <?php echo text($facility['name']) . "<br />" . text($facility['street']) . "<br />" .
                          text($facility['city']) . "," . text($facility['state']) . "," . text($facility['postal_code']) . "<br />" .
                          text($facility['phone']); ?>
                          </font>
           </div>
           <div class="pdata">
                 <p><font size="4">
            <?php echo text($pp['organization']) . "<br />" .
            text($pp['street']) . " | " . text($pp['city']) . ", " . text($pp['state']) . " " . text($pp['zip']) . "<br />" .
            "O:" . text($pp['phone']) . " | F:" . text($pp['fax']) . "<br />";
            ?></font></p>

           </div>
        </div>
        <div class="req" id="printableArea">
            <table class="table" style="width:800px" border="1">
               <tr style="height:125px;">
                   <td style="vertical-align:top; width:400px;" >
                   <div class="plist">
                        <?php echo xlt('Collection Date/Time')?>:<br />
                        <?php echo xlt('Lab Reference ID') ?>:<br />
                        <?php echo xlt('Fasting')?>:<br />
                        <?php echo xlt('Hours')?>:<br />
                     </div>
                    <div class="pFill">
                        <?php echo text($order[6]);?> <br />
                        <?php echo text($order[0]);?>
                    </div>
                   </td>
                   <td style="vertical-align:top width: 800px">
                    <div class="plist">
                       <b><?php echo xlt('Patient ID') ?>: </b>  <br />
                       <b><?php echo xlt('DOB') ?>: </b> <br />
                       <b><?php echo xlt('Sex') ?>: </b>    <br />
                       <b><?php echo xlt('Patient Name') ?>: </b>  <br />
                    </div>
                    <div class="pFill">
                        <?php echo text($pid); ?><br />
                        <?php echo text($pdata['DOB']); ?><br />
                        <?php echo text(getListItemTitle('sex', $pdata['sex'])); ?><br />
                        <?php echo text($pdata['fname']) . " " . text($pdata['lname']); ?><br />
                    </div>
                   </td>
               </tr>
			   
	 <?php 		   
    $sql = "SELECT pc.procedure_order_id, pc.procedure_order_seq, pc.procedure_code, pc.procedure_name,
	 pc.diagnoses, po.provider_id, po.date_collected,po.lab_id, po.clinical_hx, po.date_ordered, po.patient_instructions, po.specimen_type, 
	 po.specimen_location, po.specimen_volume
     FROM procedure_order_code AS pc,  
     procedure_order AS po 
     WHERE po.encounter_id = ?
	 AND  pc.procedure_order_id=po.procedure_order_id
	 ";

    $qres = sqlStatement($sql, array($encounter));
 			
        while ($qrow = sqlFetchArray($qres)) {
            $procedure_order_seq = trim($qrow['procedure_order_seq']);
            $procedure_order_id = trim($qrow['procedure_order_id']);
            $procedure_code = trim($qrow['procedure_code']);
            $procedure_name = trim($qrow['procedure_name']);			
            $patient_instructions = trim($qrow['patient_instructions']);			
 
			
  	   ?>
               <tr style="height:125px">
                   <td style="vertical-align:top; width:400px;">
                       <div class="notes">
                         <font size="4"><strong><?php echo xlt('Prueba Ordenada') ?>:</strong></font><br />
                            <?php echo text($procedure_order_seq) . " " . text($procedure_code); ?><br />
                            <?php echo text($order[17]) . " " . text($procedure_name); ?><br />
                       </div>
                   </td>
                   <td style="vertical-align:top">
                    <div class="notes">
                     <font size="4"><strong><?php echo xlt('Notas') ?>:</strong></font><br />
                        <?php echo text($patient_instructions); ?>
                     </div>
                   <?php 
				   /*
				   <div class="dx">
                     <font size="4"><strong><?php echo xlt('Dx Codes') ?>:</strong></font><br />
                        <?php echo text($order[4]); ?><br />
                        <?php echo text($order[18]); ?><br />
                        <?php echo text($order[30]); ?><br />
                   </div>
				   */
				   ?>
                   </td>
               </tr>
			<?php }?>
			
            </table>
            <?php if (!empty($order['question_text'])) { // display this table only if there are questions ?>
            <table style="width:800px" border="1">
               <tr style="height:125px">
                  <td style="vertical-align:top">
                       <font size="4"><strong><?php echo xlt('AOE Q&A') ?>: </strong></font><br />
                       <b>Question:</b> <?php print text($order['question_text']); ?><br />
                       <b>Answer:</b> <?php print text($order['answer']); ?>
                  </td>
               </tr>
            </table>
            <?php } ?>
            <br />
            <br />
            &#160;&#160;&#160;&#160;&#160; <?php echo xlt('End of Requisition') ?> #:  <?php echo text($bar); ?>
        </div>
</div>
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

