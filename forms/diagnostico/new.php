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

$returnurl = 'encounter_top.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);
$check_res = $formid ? formFetch("form_clinical_instructions", $formid) : array();

 
							/*	
 							if (empty($_GET['search_form'])) {
							$CantidadMostrar=5;
							}else{
								$CantidadMostrar=$_GET['form_CantidadMostrar'];
							}
								
							$queryp = "SELECT COUNT(*) AS TotalReg " .
                                "FROM  codes_rips" .
                                " ORDER BY id";
                                $bres = sqlStatement($queryp);
                                while ($prow = sqlFetchArray($bres)) {						
									$TotalReg=$prow['TotalReg'];	
								}

								$compag=(int)(!isset($_GET['pag'])) ? 1 : $_GET['pag']; 
								$TotalRegistro  =ceil($TotalReg/$CantidadMostrar); 
								*/
?>
<html>
    <head>
        <title><?php echo xlt("Diagnostico"); ?></title>
  
<?php Header::setupHeader('datetime-picker'); ?>
    <script>
 
    
 



												$(function(){

												  $('.validanumericos').keypress(function(e) {
													if(isNaN(this.value + String.fromCharCode(e.charCode))) 
													 return false;
												  }).on("cut copy paste",function(e){
													e.preventDefault();
												  });

												});	
            $(function () {
                // special case to deal with static and dynamic datepicker items
                $(document).on('mouseover','.datepicker', function(){
                    $(this).datetimepicker({
                        <?php $datetimepicker_timepicker = false; ?>
                        <?php $datetimepicker_showseconds = false; ?>
                        <?php $datetimepicker_formatInput = false; ?>
                        <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
                        <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
                    });
                });
            });											
    </script>
			 <script>
			$(function () {
			// show the 'searching...' status and submit the form
			var SubmitForm = function(eObj) {
				$("#search_button").attr("disabled", "true");
				$("#searchspinner").css("visibility", "visible");
				return top.restoreSession();
			}								       
		
			$("#theform").on("submit", function() { SubmitForm(this); });
			});
			 </script>	
   </head>
 <div class="row">
  <div class='text col-sm-12'>
   <a class="btn btn-primary col-sm-12" data-toggle="collapse" href="#report_parameters" role="button" aria-expanded="true" aria-controls="report_parameters">
    Buscador de Diagnostico Clínico 
  </a>
 
<div class="collapse.show" id="report_parameters" > 
<fieldset>
<legend>Buscador</legend> 

<form name="theform" id="theform" method="get" action="<?php echo $GLOBALS['webroot'] ?>/interface/forms/diagnostico/new.php">
<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
<input type="hidden" name="mode" value="search" />
<input type='hidden' name='form_CantidadMostrar' id='form_CantidadMostrar' size='20' value='<?php echo attr($CantidadMostrar); ?>'/>
<table>

 <tr>
  <td width='470px'>
    <div class="float-left">

    <table class='text'>
                   <tr>
                      <td class='col-form-label'>
                            <?php echo xlt('Busqueda por palabra clave del díagnostico'); ?>:
                      </td>
                      <td>
                         <input type='text' name='search_form' id='search_form' size='120' value=''
                            class='form-control' required />
                      </td>
 
            <td>
                <div class="text-center">
          <div class="btn-group" role="group">
            <a href='#' id='search_button' class='btn btn-secondary btn-search' onclick='top.restoreSession(); $("#theform").submit()'>
                <?php echo xlt('Buscar'); ?>
            </a>
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

</form> <!-- end of search parameters -->
  </div>
<div class="col-sm-12"> 
             <?php
            if (!empty($_REQUEST['mode'])) {
                 
                ?>
						<div class="row">
							<div class="col-12">
								<div class="table-responsive">
                            <table class="table table-bordered" id="table_display">
							   <thead>
									<tr scope="row" style="border: 1px solid rgba(238, 87, 85, 0.3);">
									  <th class="font-weight-bold text-center" colspan="9" style='border:white 1px solid;background:rgba(238, 87, 85, 0.3);'>RESULTADOS DE LA BUSQUEDA</th>
									</tr>  							   
									<tr scope="row" style="border: 1px solid rgba(100, 200, 0, 0.3);">
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: gray;'>NID</th>
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: gray;'><?php echo xlt('Código'); ?></th>
									  <th class="font-weight-bold text-center" style='border:white 1px solid;background: gray;'><?php echo xlt('Descripción'); ?></th>
									   <td class="font-weight-bold text-center" style='width:10px;' id="td_head_accion"></td>										
									</tr>
<?php
									$search_term = $_REQUEST['search_form'];
									 $query .= "SELECT * FROM codes_rips WHERE " .
											 "descripcion LIKE ? " .
											 "ORDER BY id";
									$query_doc = sqlStatement($query, array("%" . $search_term. "%"));							
									 $k=1;
									 while ($doc = sqlFetchArray($query_doc)) {	
									$key= $doc['id'];	
									$descripcion= $doc['descripcion'];	
									$cod_principal= $doc['cod_principal'];		
									 	
									 
								?>	 
									<tr class="table">								
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $k; ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $cod_principal; ?></td>
									<td class="font-weight-bold" style="background:#EAEAEA;"><?php echo $descripcion; ?></td>
									
									<td class="font-weight-bold"><a class="oe-pull-away oe-help-redirect"  href="#" onclick="sel_guardar<?php echo attr($key); ?>(this.parentElement.parentElement.parentElement.id);" id="help-href" name="help-href" style="color:#676666">
									<button value='btn<?php echo attr($key); ?>' class='btn btn-primary btn-sm editenc btn-save'></button></a>
									</td>
		
									<script>
									function sel_guardar<?php echo attr($key); ?>(id) {
											dlgopen('<?php echo $GLOBALS['webroot'] . "/interface/forms/diagnostico/" ?>diagnostico_guardar.php?key=<?php echo attr($key); ?>&pid=<?php echo attr($pid); ?>&pid=<?php echo attr($pid); ?>&form_create=true&csrf_token_form=<?php echo attr(CsrfUtils::collectCsrfToken()); ?>', '_blank', 700, 400, '', '', {onClosed: 'refreshme'});
										}
									
									</script>
									</tr>
									 <?php 
									 $k++;
									 } ?>
									<tfoot>
										<tr>
										 <td class="font-weight-bold text-center" colspan="26" style="background: #E1E4F0;">
												 
										 </td>
										 </tr>
									</tfoot>										
								</table>
								</div>
								</div>
								</div>
<?php } ?>								
								
	 </div>
	 </fieldset>
 </div>  																
 </div>  																
 </div>  																
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
