<?php
require_once('pages/conf/function.php');
require_once('pages/conf/controller.php');
require_once('pages/admin/function.php');
require_once('pages/admin/controller.php');
foreach ($_SESSION as $key => $value) {
  if ($key <> "login") {
    unset($_SESSION[$key]);
  }
}


if ($_SESSION['login']['PERFIL'] == 'CLIENTE'){
  $hrefNovoOrcamento = "index.php?op=150&acao=novo";
  $lista = buscaOrcamentosRecentesCliente($_SESSION['login']['CODCLI']);
} else {
  $hrefNovoOrcamento = "index.php?op=61";
  $lista = buscaOrcamentosRecentes($_SESSION['login']['CODUSUR']);
}


?>
<main class="flex-shrink-0">
  <div class="container">
    <h2>
      <i class="fa-solid fa-home"></i> Seja bem vindo, <?=obterPrimeiroEUltimoNome($_SESSION['login']['NOME'])?>
      <div class="float-sm-end">
        <a href="<?=$hrefNovoOrcamento?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus"></i> Novo Orçamento</a>
      </div>
    </h2>
    <div class="row"> 

      <?php 
        // varDump2($_SESSION['login']);
        if ($_SESSION['login']['DTULTALTERACAO'] == "") {
          include_once("pages/admin/modalNovaSenha.php");
        }
      ?>

      <div class="col-md-3">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title"><i class="fas fa-memory"></i> Memória (RAM)</h5>
            </div>
            <div class="card-body">
              Memória RAM em uso  <?=$ram_perc_uso?>%
              <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="<?=$ram_perc_uso?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$ram_perc_uso?>%"><?=$ram_perc_uso?>%</div>
              </div>
            </div>
            <div class="card-footer">
              <div class="accordion accordion-flush py-0" id="accordionFlushExample">
                <div class="accordion-item">
                  <h2 class="accordion-header" id="flush-headingOne">
                    <button class="accordion-button collapsed py-1 text-sm" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">Ver mais</button>
                  </h2>
                  <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body py-1">
                      <dl class="row">
                        <dt class="col-8">Memória em uso</dt>
                        <dd class="col-4 d-flex justify-content-end"><?= moeda(round((($USADA)/1024/1024),2)) . ' GB' ?></dd>

                        <dt class="col-8">Memória livre</dt>
                        <dd class="col-4 d-flex justify-content-end"><?= moeda(round((($TOTAL-$USADA)/1024/1024),2)) . ' GB' ?></dd>

                        <dt class="col-8">Memória total</dt>
                        <dd class="col-4 d-flex justify-content-end"><?= moeda(round((($TOTAL)/1024/1024),2)) . ' GB'?></dd>
                      </dl>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title"><i class="fas fa-hdd"></i> Disco Rígido (HDD)</h5>
            </div>
            <div class="card-body">
              Disco em uso  <?= round(((sprintf('%1.2f' , $hdd_uso / pow($base,$class_uso)) / sprintf('%1.2f' , $hdd_total / pow($base,$class_total)))*100),0)."%"?>
              <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated <?=$bg_hdd_perc_uso?>" role="progressbar" aria-valuenow="<?= round(((sprintf('%1.2f' , $hdd_uso / pow($base,$class_uso)) / sprintf('%1.2f' , $hdd_total / pow($base,$class_total)))*100),0) ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= round(((sprintf('%1.2f' , $hdd_uso / pow($base,$class_uso)) / sprintf('%1.2f' , $hdd_total / pow($base,$class_total)))*100),0) ?>%">
                  <?= round(((sprintf('%1.2f' , $hdd_uso / pow($base,$class_uso)) / sprintf('%1.2f' , $hdd_total / pow($base,$class_total)))*100),0)."%"?>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <div class="accordion accordion-flush py-0" id="accordionFlushTwo">
                <div class="accordion-item">
                  <h2 class="accordion-header" id="flush-headingTwo">
                    <button class="accordion-button collapsed py-1 text-sm" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">Ver mais</button>
                  </h2>
                  <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-collapseTwo" data-bs-parent="#accordionFlushTwo">
                    <div class="accordion-body py-1">
                      <dl class="row">
                        <dt class="col-8">HDD em uso</dt>
                        <dd class="col-4 d-flex justify-content-end"><?= sprintf('%1.2f' , $hdd_uso / pow($base,$class_uso)) . ' ' . $si_prefix[$class_uso] ?></dd>

                        <dt class="col-8">HDD livre</dt>
                        <dd class="col-4 d-flex justify-content-end"><?= sprintf('%1.2f' , $hdd_livre / pow($base,$class_livre)) . ' ' . $si_prefix[$class_livre] ?></dd>

                        <dt class="col-8">HDD total</dt>
                        <dd class="col-4 d-flex justify-content-end"><?= sprintf('%1.2f' , $hdd_total / pow($base,$class_total)) . ' ' . $si_prefix[$class_total] ?></dd>
                      </dl>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="col-md-9">

        <div class="card">
          <div class="card-header">
            <h5 class="card-title"><i class="fa-solid fa-list"></i> Faturamento Mensal</h5>
          </div>
          <div class="card-body">
            <canvas class="w-100 border" id="myChart" width="900" height="200"></canvas>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h5 class="card-title"><i class="fa-solid fa-list"></i> Meus Orçamentos (últimos 15 dias)</h5>
          </div>
          <div class="card-body">
            <table class="table" id="tb_order_desc">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Data</th>
                  <th scope="col">Cliente</th>
                  <th scope="col">Máquina</th>
                  <th scope="col">Valor</th>
                  <th scope="col">Ação</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($lista): ?>
                  <?php foreach ($lista as $key => $value): ?>
                    <tr>
                      <th scope="row"><?=$value['IDORCAMENTO']?></th>
                      <td><?=formataDataOracletoBr($value['DATA'])?></td>
                      <td><?=$value['CLIENTE']?></td>
                      <td><?=$value['MAQUINA']?></td>
                      <td><?=moeda($value['VALORTOTAL'], 2)?></td>
                      <td><a href="index.php?op=<?=($_SESSION['login']['PERFIL'] == 'CLIENTE')?'151':'62'?>&acao=consultaOrcamento&IDORCAMENTO=<?=$value['IDORCAMENTO']?>" target="_blank" class=" p-0 btn btn-xs btn-outline-success "><i class=" fa-solid fa-forward"></i> Abrir</a></td>
                    </tr>
                  <?php endforeach ?>
                <?php endif ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>

    </div>

  </div>
</main>

<script type="text/javascript" src="dist/js/feather.min.js"></script>
<script type="text/javascript" src="dist/js/Chart.min.js"></script>
<script type="text/javascript">
/* globals Chart:false, feather:false */

(function () {
  'use strict'

  feather.replace({ 'aria-hidden': 'true' })

  // Graphs
  var ctx = document.getElementById('myChart')
  // eslint-disable-next-line no-unused-vars

  var histFaturamento = <?php echo json_encode(buscaHistFaturamento($_SESSION['login']['CODUSUR'])); ?>;
  // console.log(histFaturamento);

  var labelHis = [];
  var dataHis = [];
  histFaturamento.forEach(async (historico) => {
    labelHis.push(historico['MES'])
    dataHis.push(historico['VLVENDA'])
  })
  // console.log(labelHis);
  // console.log(dataHis);


  var chartData = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labelHis,
      datasets: [{
        label: 'Faturamento',
        data: dataHis,
        lineTension: 0,
        borderColor: '#007bff',
        borderWidth: 1,
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  })


})()  

</script>