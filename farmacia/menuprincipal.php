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
 
							$queryp = "SELECT COUNT(*) AS TotalReg " .
                                "FROM  farmacia_medicamentos" .
                                " ORDER BY drug_id";
                                $bres = sqlStatement($queryp);
                               while ($prow = sqlFetchArray($bres)) {						
									$TotalMedica=$prow['TotalReg'];	
								}
							$queryp = "SELECT COUNT(*) AS TotalReg " .
                                "FROM  proveedores_medicamentos" .
                                " ORDER BY id";
                                $bres = sqlStatement($queryp);
                               while ($prow = sqlFetchArray($bres)) {						
									$TotalProveedores=$prow['TotalReg'];	
								}
							$queryp = "SELECT COUNT(*) AS TotalReg " .
                                "FROM   farmacia_medicamentos_precios" .
                                " ORDER BY id";
                                $bres = sqlStatement($queryp);
                               while ($prow = sqlFetchArray($bres)) {						
									$TotalCompras=$prow['TotalReg'];	
								}	

 						
?>
 
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>

<body>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?php echo utf8_encode('Módulo de Farmacia') ?>
			</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item active"><?php echo utf8_encode('Unidad Médica D&D') ?></li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo $TotalMedica; ?></h3>
                <p><?php echo utf8_encode('Medicamestos') ?></p> 
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="productos.php" class="small-box-footer">Ver<i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo $TotalProveedores; ?><sup style="font-size: 20px"></sup></h3>

                <p>Provedores</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="proveedores.php" class="small-box-footer">Ver<i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php echo $TotalCompras; ?></h3>

                <p>Compras</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="compras.php" class="small-box-footer">Ver<i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>65</h3>

                <p>Reporte</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="reporte.php" class="small-box-footer">Ver <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
  </div>
  </section>
  
</div>
</body>
<html>