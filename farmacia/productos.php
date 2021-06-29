<?php

/**
 * Upload and install a designated code set to the codes table.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Rod Roark <rod@sunsetsystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2014 Rod Roark <rod@sunsetsystems.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

set_time_limit(0);

require_once("../globals.php");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

if (!empty($_POST)) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
}
 

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Medicamentos</title>
<?php
require_once("./encabezado.php");
?>
  <!-- jsGrid -->
  <link rel="stylesheet" href="./plugins/jsgrid/jsgrid.min.css">
  <link rel="stylesheet" href="./plugins/jsgrid/jsgrid-theme.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="./dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="./dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row sm-12">
          <div class="col-sm-12">
               <a class="btn btn-app" href="view.php">
                  <i class="fas fa-home"></i>Inicio
               </a><a class="btn btn-app" href="add_producto.php">
                  <i class="fas fa-save"></i>Registar Producto
               </a>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Listado de Medicamestos</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <div id="grid_table"></div>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </section>
    <!-- /.content -->
  </div>
</div>
<?php
require_once("./pie.php");
?>

</body>
</html>
<script> 
  $(function () {
	 
    $('#grid_table').jsGrid({

     width: "100%",
     height: "600px",

     filtering: true,
     inserting:false,
     editing: true,
     sorting: true,
     paging: true,
     autoload: true,
     pageSize: 10,
     pageButtonCount: 5,
     deleteConfirm: "Do you really want to delete data?",
     controller: {
      loadData: function(filter){
       return $.ajax({
        type: "GET",
        url: "crud_productos.php",
        data: filter
       });
      },
      updateItem: function(item){
       return $.ajax({
        type: "PUT",
        url: "crud_productos.php",
        data: item
       });
      },
      deleteItem: function(item){
       return $.ajax({
        type: "DELETE",
        url: "crud_productos.php",
        data: item
       });
      },
     },
     fields: [
      {
       name: "drug_id",
		type: "hidden",
		css: 'hide', 
		visible: false,
		width: 0
      },{
       name: "id_precio",
		type: "hidden",
		css: 'hide',
		 visible: false, 
		 width: 0
      },{
       name: "proverdor_id",
		type: "hidden",
		css: 'hide',
		 visible: false, 
		 width: 0
      },
      {
       name: "nom_proveedor",
	   title:"Proveedor",
		type: "text", 
		width: 50, 
		editing: false
      },
	  {
       name: "descripcion", 
	   title:"Descripcion",
		type: "text", 
		width: 150
      },
      {
       name: "precio_real",
	   title:"Precio Real",
		type: "number", 
		width: 80
      },
	  {
       name: "precio_com",
	   title:"Precio Comun",
		type: "text", 
		width: 80
      },
	  {
       name: "precio_unidad",
	   title:"Precio Unidad",
		type: "number", 
		width: 80
      },
      {
       type: "control"
      }
     ]

    }); 
	
  });
 
</script>
<!-- jsGrid -->
<script src="./plugins/jsgrid/jsgrid.min.js"></script>
<!-- AdminLTE App -->
<script src="./dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
