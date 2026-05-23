<?php  
include("pages/vendas/modalDuplicar.php");
// varDump2($_SESSION['ORCAMENTO']['CAB']);
$classFaturar     = '';
$activeFaturar    = '';

$classExportar    = '';
$activeExportar   = '';

$classGeral       = '';
$activeGeral      = '';


if (isset($_POST['defineCobranca']) || isset($_POST['definePlpag']) || isset($_POST['defineFrete']) || isset($_GET['clienteBalcao'])) {
  $classFaturar   = ' class="active"';
  $activeFaturar  = 'active';
}
if (isset($_POST['exportaPDF']) || isset($_POST['exportaExcel'])) {
  $classExportar   = ' class="active"';
  $activeExportar  = 'active';
}
if ($classGeral.$classExportar.$classFaturar == '') {
  $classGeral     = 'class="active"';
  $activeGeral    = 'active';
}

?>

<div class="row">
  <div class="col-md-12">
    <!-- Custom Tabs -->
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li <?=$classGeral ?>><a href="#geral" data-toggle="tab">GERAL</a></li>
        <li <?=$classExportar ?>><a href="#exportar" data-toggle="tab">EXPORTAR</a></li>
        <?php  
        if ($_SESSION['login']['PERFIL'] != 'LOGISTICA') {
          echo '<li '.$classFaturar.'><a href="#faturar" data-toggle="tab">FATURAMENTO</a></li>';
        }
        ?>
      </ul>
      <div class="tab-content">

        <div class="tab-pane <?=$activeGeral?>"  id="geral">
          <?php  
          include('pages/vendas/aba_geral.php');
          ?>
        </div>
        <!-- /.tab-pane -->

        <div class="tab-pane <?=$activeExportar?>" id="exportar">
          <?php  
          include('pages/vendas/aba_exportar.php');
          ?>
        </div>
        <!-- /.tab-pane -->

        <?php
          if ($_SESSION['login']['PERFIL'] != 'LOGISTICA') {
            require_once('pages/vendas/aba_faturar.php');
          }
        ?>
        <!-- /.tab-pane -->

      </div>
      <!-- /.tab-content -->
    </div>
    <!-- nav-tabs-custom -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->




