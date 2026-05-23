<?php
require_once('pages/conf/function.php');
require_once('pages/conf/controller.php');
foreach ($_SESSION as $key => $value) {
  if ($key <> "login") {
    unset($_SESSION[$key]);
  }
}

/**
 * Dashboard de Performance Individual - Sales Excellence
 * Desenvolvido para PHP 8 Procedural & Bootstrap 5.1.3
 * Focado em integração com ERP Winthor (Oracle)
 */

// Simulação de dados (Aqui você integraria com selectOracle)
$faturamentoRealizado = 85400.00;
$metaMensal = 120000.00;
$diasUteisTotais = 22;
$diasTranscorridos = 15;

// Cálculos de Negócio (Lógica Procedural PHP 8)
$gapMeta = $metaMensal - $faturamentoRealizado;
$percentualAtingimento = ($faturamentoRealizado / $metaMensal) * 100;
$projecaoFechamento = ($faturamentoRealizado / $diasTranscorridos) * $diasUteisTotais;
$statusCor = $projecaoFechamento >= $metaMensal ? 'success' : ($projecaoFechamento >= $metaMensal * 0.9 ? 'warning' : 'danger');

// Dados de Eficiência
$paAtual = 1.2;
$paMeta = 1.8;
$taxaConversao = 8.0;
$mediaEquipeConversao = 12.0;
?>
<main class="flex-shrink-0">
  <div class="container">
    <h2 class="mt-2 mb-3">
      <i class="fa-solid fa-home"></i> Seja bem vindo, <?=obterPrimeiroEUltimoNome($_SESSION['login']['NOME'])?>
    </h2>
    <hr>
    <?php  
    $resumo = buscaFaturamentoRca($_SESSION['login']['CODUSUR']);
    // varDump2($resumo);
    $ranking = rankingVendedores();
    // varDump2($ranking);
    if ($ranking['posicao'] <= 3){
      $ranking['bg'] = "success";
    } else if ($ranking['posicao'] <= 6){
      $ranking['bg'] = "primary";
    } else if ($ranking['posicao'] <= 9){
      $ranking['bg'] = "warning";
    } else {
      $ranking['bg'] = "danger";
    }
    ?>
    <h3 class="mt-2 mb-3">
      <i class="fa-regular fa-chart-mixed-up-circle-dollar"></i> Dashboard de desempenho
      <small class="text-muted">Período de apuração: <?= $resumo['DATAINI'] ?> a <?= $resumo['DATAFIM'] ?></small>
    </h3>
    <!--begin::Row-->
    <div class="row">

      <!--begin::Col-->
      <div class="col-lg-3 col-6">
        <div class="card shadow-sm">
          <div class="card-body bg-secondary text-white position-relative overflow-hidden">
            <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= $resumo['MIX'] ?></h5>
            <h6 class="card-subtitle mb-2">Mix</h6>
            <i class="fa-solid fa-boxes-stacked card-bg-icon"></i>
          </div>
        </div>
        <div class="card-footer py-0 text-start text-muted text-small">
          Contagem de produtos distintos vendidos
        </div>
      </div>
      <!--begin::Col-->
      <div class="col-lg-3 col-6">
        <div class="card shadow-sm">
          <div class="card-body bg-info text-white position-relative overflow-hidden">
            <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= $resumo['POSITIVACAO'] ?></h5>
            <h6 class="card-subtitle mb-2">Positivação</h6>
            <i class="fa-solid fa-user card-bg-icon"></i>
          </div>
        </div>
        <div class="card-footer py-0 text-start text-muted text-small">
          Contagem de clientes distintos atendidos
        </div>
      </div>
      <!--begin::Col-->
      <div class="col-lg-3 col-6">
        <div class="card shadow-sm">
          <div class="card-body bg-primary text-white position-relative overflow-hidden">
            <h5 class="card-title" style="font-size: 32px; font-weight: bold;">R$ <?= moeda($resumo['VLVENDA_LIQUIDA']) ?></h5>
            <h6 class="card-subtitle mb-2">Faturamento Líquido</h6>
            <i class="fa-solid fa-brazilian-real-sign card-bg-icon"></i>
          </div>
        </div>
        <div class="card-footer py-0 text-start text-muted text-small">
          Soma do faturamento abatendo as devoluções
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="card shadow-sm">
          <div class="card-body bg-<?= $ranking['bg'] ?> text-dark position-relative overflow-hidden">
            <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= $ranking['posicao'] ?>º de <?= $ranking['total'] ?> RCAs</h5>
            <h6 class="card-subtitle mb-2">Ranking</h6>
            <i class="fa-solid fa-award card-bg-icon"></i>
          </div>
        </div>
        <div class="card-footer py-0 text-start text-muted text-small">
          Sua colocação em volume de faturamento líquido
        </div>
      </div>
      <!--begin::Col-->
    </div>
    <!--end::Row-->

  </div>
 

</main>