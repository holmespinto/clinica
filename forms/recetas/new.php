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
require_once($GLOBALS['srcdir'] . '/csv_like_join.php');
require_once($GLOBALS['fileroot'] . '/custom/code_types.inc.php');

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
$fileroots=$GLOBALS['srcdir']. '/templates/prescription/';
$returnurl = 'encounter_top.php';
$encounter = $_SESSION['encounter'];
//$check_res = $encounter ? formFetch("prescriptions", $encounter) : array();

  $sqd = "SELECT COUNT(*) AS num
     FROM prescriptions
     WHERE encounter= ?";
    $qnum = sqlStatement($sqd, array($encounter));
        while ($numrow = sqlFetchArray($qnum)) {
		$num = $numrow['num'];
		} 
?>

<html>
    <head>
        <title><?php echo xlt("Resetas"); ?></title>
  
<?php Header::setupHeader('datetime-picker'); ?>
 
<html>
<head>
 
 <body id="prescription_list">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
	                    <div class="col-12">
                                
									
                             	<a href="#" onclick="guardarres();" style="color:#676666">
									<button value='btn' class='editscript btn btn-primary btn-sm btn-edit'>Registrar Receta</button></a>
									<script> 
									function guardarres() {
										var ruta= document.location.href="<?php echo $GLOBALS['webroot']; ?>/controller.php?prescription&edit&id=0&pid=<?php echo $_SESSION['pid']; ?>&encounter=<?php echo $_SESSION['encounter']; ?>";
											dlgopen(ruta, '_blank', 700, 400, '', '', {onClosed: 'refreshme'});
											return true;
										}
									
									</script>                      
                    </div>		
<?php
			
		if($num>0){	

	?>	
                <div id="prescription_list">
                    <form name="presc">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                    <!-- TajEmo Changes 2012/06/14 02:01:43 PM by CB added Heading for checkbox column -->
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th><?php echo xlt("Drug"); ?></th>
                                        <th><?php echo xlt("RxNorm"); ?></th>
                                        <th><?php echo xlt("Created"); ?><br /><?php echo xlt("Changed"); ?></th>
                                         <th><?php echo xlt("Dosage"); ?></th>
                                         <th><?php echo xlt("Qty"); ?></th>
                                         <th><?php echo xlt("Unit"); ?></th>
                                         <th><?php echo xlt("Provider"); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
			 
<?php  

    $sql = "SELECT *
     FROM prescriptions AS po
     WHERE po.encounter= ?";

    $qres = sqlStatement($sql, array($_SESSION['encounter']));
 			
        while ($qrow = sqlFetchArray($qres)) {
          
            $id = $qrow['id'];
            $active = $qrow['active'];
            $erx_source =$qrow['erx_source'];
            $note =$qrow['note'];			
            $drug =$qrow['drug'];		
            $rxnorm_drugcode =$qrow['rxnorm_drugcode'];		
            $date_added =$qrow['date_added'];		
            $date_modified =$qrow['date_modified'];		
            $get_dosage_display =$qrow['get_dosage_display'];		
            $quantity =$qrow['quantity'];		
            $refills =$qrow['refills'];		
            $dosage =$qrow['dosage'];		
			
			
?>								
                                
                            <!-- TajEmo Changes 2012/06/14 02:03:17 PM by CB added cursor:pointer for easier user understanding -->
                            <tr id="<?php echo $id; ?>" class="showborder <?php if($active>=0){echo "inactive";} ?>">
                                <td class="text-center">
                                <input class="check_list" id="check_list" type="checkbox" value="">
                                </td>
								<?php if(empty($erx_source)|| ($erx_source==0)){ ?>
                                 
                                <td class="editscript"  id="<?php echo $id; ?>">
                                    <a class='editscript btn btn-primary btn-sm btn-edit' id='<?php echo $id; ?>' href="<?php echo $GLOBALS['webroot']; ?>/controller.php?prescription&edit&id=<?php echo $id; ?>">Edit</a>
                                 <script>
									var ShowScript = function(eObj) {
									top.restoreSession();
									 
									document.location.href="<?php echo $GLOBALS['webroot']; ?>/controller.php?prescription&edit&id=<?php echo $id; ?>";
									return false;
								};								 
								 $("#<?php echo $id; ?>").on("click", function() { ShowScript(<?php echo $id; ?>); });
									
									
									</script>
									
                                </td>
								<?php } ?>
                                <td class="editscript"  id="<?php echo $id; ?>">
								 
                                <b><?php echo $rxnorm_drugcode; ?>-<?php echo $drug; ?>
                                </td>
                                  
                                <td id="<?php echo $id; ?>">
								<?php echo $note; ?>
								</td>
                                <td id="<?php echo $id; ?>">
								<?php echo $date_added; ?><br />
								<?php echo $date_modified; ?>
                                </td>
                                <td id="<?php echo $id; ?>">
								<?php echo $dosage; ?>
                                 
                                </td>
                                 
                                <td class="editscript" id="<?php echo $id; ?>">
                                <?php echo $quantity; ?>&nbsp;
                                </td>
                                <td id="<?php echo $id; ?>">
                                <?php echo $quantity; ?>&nbsp;
                                </td>
                                <td id="<?php echo $id; ?>">
								<?php echo $quantity; ?>&nbsp;
                             
                                </td>
                                <td id="<?php echo $id; ?>">
								<?php echo $get_name_display(); ?>
                                 &nbsp;
                                </td>
                                <td><a href="#" id="deleteDrug" class="btn btn-danger btn-sm btn-delete" onclick="deleteDrug({$prescription->id|attr})">{xlt t='Delete'}</a></td>
                            </tr>
							<?php }?>
                                 
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
           <?php }else{?>
                <div id="drug-drug">
                    <hr>
                    <h3> </h3>
                    <p title=""><a href="#">NO EXISTEN REGISTROS DE RECETAS</a></p>
                    <div id="return_info">
                                
									
                             	<a href="#" onclick="guardar();" style="color:#676666">
									<button value='btn<?php echo attr($key); ?>' class='editscript btn btn-primary btn-sm btn-edit'></button></a>
									<script> 
									function guardar() {
										var ruta= document.location.href="<?php echo $GLOBALS['webroot']; ?>/controller.php?prescription&edit&id=0&pid=<?php echo $_SESSION['pid']; ?>&encounter=<?php echo $_SESSION['encounter']; ?>";
											dlgopen(ruta, '_blank', 700, 400, '', '', {onClosed: 'refreshme'});
											return true;
										}
									
									</script>                      
                    </div>
                    <hr>
                </div>
              <?php }?>

 
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
