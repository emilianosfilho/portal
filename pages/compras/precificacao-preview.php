<div class="container-fluid mt-5">
  <div class="row">
    <?php 
    require_once('pages/compras/function.php'); 
    require_once('pages/compras/controller.php'); 
    require_once('pages/compras/sidebar.php'); 
    require_once('pages/compras/precificacao-modalEnviarArquivo.php'); 
    require_once('pages/compras/precificacao-validaArquivo.php'); 
    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-dollar"></i> Precificação - Preview</h1>
          <div class="float-end">
            <div class="btn-group">
              <button class="btn btn-outline-secondary px-3"  title="Enviar Planilha - Produtos" data-bs-toggle="modal" data-bs-target="#precificacao-modalEnviarArquivo">
                <i class="fa-solid fa-upload"></i> 
              </button>
              <a href="index.php?op=114&nav=compras&aba=precificacao&acao=clear" class="btn btn-outline-secondary"  title="Limpar Lista"> 
                <i class="fa-solid fa-broom"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="row text-md">
        <div class="table-responsive">
          <table id="tb_default" class="table table-bordered table-striped table-hover" style="width: 100%">
            <thead>
              <tr>
                <th>Cód Winthor</th>
                <th>Descrição</th>
                <th>Numoriginal</th>
                <th>Marca</th>
                <th>Preço Atual</th>
                <th>Preço Novo</th>
              </tr>
            </thead>

            <tbody>
              <?php
              if ($_SESSION['PRECIFICACAO']) {
                $CONTA_ERROS = 0;
                foreach ($_SESSION['PRECIFICACAO'] as $key => $value) {
                  // varDump2($value);  
                  echo PHP_EOL;
                  echo '<tr>';

                    echo '<td><div class="d-flex justify-content-between">';
                    if (isset($value['V_CODPROD']) || isset($value['V_DV'])) {
                      $CONTA_ERROS++;
                      echo '<i class="fa-solid fa-square-xmark text-xl text-danger" title="'.$value['V_CODPROD'].' '.$value['V_DV'].'"></i>';
                    } else {
                      echo '<i class="fa-solid fa-square-check text-xl text-success" title="OK"></i>';
                    }
                    echo $value['CODPROD'].'-'.$value['DV'].'</div></td>';

                    echo '<td>'.$value['DESCRICAO'].'</td>';
                    echo '<td>'.$value['NUMORIGINAL'].'</td>';
                    echo '<td>'.$value['MARCA'].'</td>';
                    echo '<td><div class="float-end">R$ '.moeda($value['PVENDA_OLD'], 4).'</div></td>';

                    echo '<td><div class="d-flex justify-content-between">';
                    if (isset($value['V_PVENDA'])) {
                      $CONTA_ERROS++;
                      echo '<i class="fa-solid fa-ban text-primary"></i>';
                    } else {
                      if (moedaPHP($value['PVENDA_OLD']) == moedaPHP($value['PVENDA'])) {
                        echo '<i class="fa-solid fa-square-minus text-xl text-primary" title="IGUAL"></i>';
                      } else {
                        if (moedaPHP($value['PVENDA_OLD']) < moedaPHP($value['PVENDA'])) {
                          echo '<i class="fa-solid fa-square-caret-up text-xl text-success" title="ACRÉSCIMO"></i>';
                        } else {
                          echo '<i class="fa-solid fa-square-caret-down text-xl text-danger" title="REDUÇÃO"></i>';
                        }
                      }
                    }
                    echo 'R$ '.moeda($value['PVENDA'], 4).'</div></td>';

                  echo '</tr>';
                }
              }
              ?>
            </tbody>

            <tfooter>
              <tr>
                <th>Cód Winthor</th>
                <th>Descrição</th>
                <th>Numoriginal</th>
                <th>Marca</th>
                <th>Preço Atual</th>
                <th>Preço Novo</th>
              </tr>
            </tfooter>
          </table>
        </div>
      </div>

      <div class="row float-start">
        <?php  
        if ($_SESSION['PRECIFICACAO'] && $CONTA_ERROS == 0) {
          echo '<a href="index.php?op=116&nav=compras&aba=precificacao" class="btn btn-lg btn-success"><i class="fa-solid fa-forward"></i> PROSSEGUIR </a>';
        } else {
          echo '<div class="alert alert-danger" role="alert">Você deve corrigir os problemas para prosseguir</div>';
        }
        ?>
      </div>



    </main>
  </div>
</div>