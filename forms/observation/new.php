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

if ($formid) {
    $sql = "SELECT * FROM `form_observation` WHERE id=? AND pid = ? AND encounter = ?";
    $res = sqlStatement($sql, array($formid,$_SESSION["pid"], $_SESSION["encounter"]));

    $all = [];
    for ($iter = 0; $row = sqlFetchArray($res); $iter++) {
        $all[$iter] = $row;
    }
    $check_res = $all;
}

$check_res = $formid ? $check_res : array();

?>
<html>
    <head>
        <title><?php echo xlt("Observation"); ?></title>

        <?php Header::setupHeader(['datetime-picker']);?>

        <script>

            function duplicateRow(e) {
                var newRow = e.cloneNode(true);
                e.parentNode.insertBefore(newRow, e.nextSibling);
                changeIds('tb_row');
                changeIds('comments');
                changeIds('code');
                changeIds('description');
                changeIds('code_date');
                changeIds('displaytext');
                changeIds('code_type');
                changeIds('table_code');
                changeIds('ob_value');
                changeIds('ob_unit');
                changeIds('ob_value_phin');
                changeIds('ob_value_head');
                changeIds('ob_unit_head');
                removeVal(newRow.id);
            }

            function removeVal(rowid) {
                rowid1 = rowid.split('tb_row_');
                document.getElementById("comments_" + rowid1[1]).value = '';
                document.getElementById("code_" + rowid1[1]).value = '';
                document.getElementById("description_" + rowid1[1]).value = '';
                document.getElementById("code_date_" + rowid1[1]).value = '';
                document.getElementById("displaytext_" + rowid1[1]).innerHTML = '';
                document.getElementById("code_type_" + rowid1[1]).value = '';
                document.getElementById("table_code_" + rowid1[1]).value = '';
                document.getElementById("ob_value_" + rowid1[1]).value = '';
                document.getElementById("ob_unit_" + rowid1[1]).value = '';
                document.getElementById("ob_value_phin_" + rowid1[1]).value = '';
                document.getElementById("ob_value_head_" + rowid1[1]).innerHTML = '';
                document.getElementById("ob_unit_head_" + rowid1[1]).innerHTML = '';
            }

            function changeIds(class_val) {
                var elem = document.getElementsByClassName(class_val);
                for (let i = 0; i < elem.length; i++) {
                    if (elem[i].id) {
                        index = i + 1;
                        elem[i].id = class_val + "_" + index;
                    }
                }
            }

            function deleteRow(rowId) {
                if (rowId !== 'tb_row_1') {
                    var elem = document.getElementById(rowId);
                    elem.parentNode.removeChild(elem);
                }
            }

            function sel_code(id) {
                id = id.split('tb_row_');
                var checkId = '_' + id[1];
                document.getElementById('clickId').value = checkId;
                dlgopen('<?php echo $GLOBALS['webroot'] . "/interface/patient_file/encounter/" ?>find_code_popup.php?codetype=' + encodeURIComponent('LOINC,PHIN Questions'), '_blank', 700, 400);
            }

            function set_related(codetype, code, selector, codedesc) {
                var checkId = document.getElementById('clickId').value;
                document.getElementById("code" + checkId).value = code;
                document.getElementById("description" + checkId).value = codedesc;
                document.getElementById("displaytext" + checkId).innerHTML  = codedesc;
                document.getElementById("code_type" + checkId).value = codetype;
                if(codetype === 'LOINC') {
                  document.getElementById("table_code" + checkId).value = 'LN';
                  if(code === '21612-7') {
                    document.getElementById('ob_value_head' + checkId).style.display = '';
                    document.getElementById('ob_unit_head' + checkId).style.display = '';
                    document.getElementById('ob_value' + checkId).style.display = '';
                    var sel_unit_age = document.getElementById('ob_unit' + checkId);
                      if(document.getElementById('ob_unit' + checkId).value == '') {
                        var opt = document.createElement("option");
                        opt.value='d';
                        opt.text='Day';
                        sel_unit_age.appendChild(opt);
                        var opt1 = document.createElement("option");
                        opt1.value='mo';
                        opt1.text='Month';
                        sel_unit_age.appendChild(opt1);
                        var opt2 = document.createElement("option");
                        opt2.value='UNK';
                        opt2.text='Unknown';
                        sel_unit_age.appendChild(opt2);
                        var opt3 = document.createElement("option");
                        opt3.value='wk';
                        opt3.text='Week';
                        sel_unit_age.appendChild(opt3);
                        var opt4 = document.createElement("option");
                        opt4.value='a';
                        opt4.text='Year';
                        sel_unit_age.appendChild(opt4);
                    }
                    document.getElementById('ob_unit' + checkId).style.display = 'block';
                    document.getElementById('ob_value_phin' + checkId).style.display = 'none';
                  }
                  else if (code === '8661-1'){
                    document.getElementById('ob_unit_head' + checkId).style.display = 'none';
                    var select = document.getElementById('ob_unit' + checkId);
                    select.innerHTML= "";
                    document.getElementById('ob_unit' + checkId).style.display = 'none';
                    document.getElementById('ob_value_phin' + checkId).style.display = 'none';
                    document.getElementById('ob_value_head' + checkId).style.display = '';
                    document.getElementById('ob_value' + checkId).style.display = '';
                  }
                }
                else {
                  document.getElementById("table_code" + checkId).value = 'PHINQUESTION';
                  document.getElementById('ob_value_head' + checkId).style.display = '';
                  document.getElementById('ob_unit_head' + checkId).style.display = 'none';
                  var select_unit = document.getElementById('ob_unit' + checkId);
                  select_unit.innerHTML= "";
                  document.getElementById('ob_value' + checkId).value = '';
                  document.getElementById('ob_value' + checkId).style.display = 'none';
                  document.getElementById('ob_unit' + checkId).style.display = 'none';
                  document.getElementById('ob_value_phin' + checkId).style.display = '';
                }
            }

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
    </head>
    <body>
        <div class="container mt-3">
            <div class="row">
                <div class="col-12">
                    <h2><?php echo xlt('Observation'); ?></h2>
                    <form method='post' name='my_form' action='<?php echo $rootdir; ?>/forms/observation/save.php?id=<?php echo attr_url($formid); ?>'>
                        <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                        <fieldset>
                            <legend><?php echo xlt('Enter Details'); ?></legend>
                            <div class="container">

                                    <div class="tb_row" id="tb_row_1">
                                        <div class="form-row">
                                            <div class="forms col-md-2">
                                                 <span id="displaytext_1" class="displaytext help-block"></span>
                                                <input type="hidden" id="description_1" name="description[]" class="description" value="<?php echo attr($obj["description"] ?? ''); ?>" />
                                                <input type="hidden" id="code_type_1" name="code_type[]" class="code_type" value="<?php echo attr($obj["code_type"] ?? ''); ?>" />
                                                <input type="hidden" id="table_code_1" name="table_code[]" class="table_code" value="<?php echo attr($obj["table_code"] ?? ''); ?>" />
                                            </div>

                                            <div class="forms col-md-2">
                                                <label for="code_date_1" class="h5"><?php echo xlt('Date'); ?>:</label>
                                                <input type='text' id="code_date_1" name='code_date[]' class="form-control code_date datepicker" value='<?php echo attr($obj["date"] ?? ''); ?>' title='<?php echo xla('yyyy-mm-dd Date of service'); ?>' />
                                            </div>
                                            <div class="forms col-md-2">
                                                <label for="comments_1" class="h5"><?php echo xlt('Comments'); ?>:</label>
                                                <textarea name="comments[]" id="comments_1" class="form-control comments"  rows="3" ><?php echo text($obj["observation"] ?? ''); ?></textarea>
                                            </div>
                                            <div class="forms col-md-2">
                                                <button type="button" class="btn btn-primary btn-sm btn-add" onclick="duplicateRow(this.parentElement.parentElement.parentElement);" title='<?php echo xla('Click here to duplicate the row'); ?>'>
                                                    <?php echo xlt('Add'); ?>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete" onclick="deleteRow(this.parentElement.parentElement.parentElement.id);" title='<?php echo xla('Click here to delete the row'); ?>'>
                                                    <?php echo xlt('Delete'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                           
                            </div>
                        </fieldset>
                        <div class="form-group clearfix">
                            <div class="col-sm-12 position-override">
                                <div class="btn-group" role="group">
                                    <button type="submit" onclick='top.restoreSession()' class="btn btn-primary btn-save"><?php echo xlt('Save'); ?></button>
                                    <button type="button" class="btn btn-secondary btn-cancel" onclick="top.restoreSession(); parent.closeTab(window.name, false);"><?php echo xlt('Cancel');?></button>
                                </div>
                                <input type="hidden" id="clickId" value="" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
