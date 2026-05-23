<div class="container-lg">
  <div class="row mt-4">
    <main class="col-12 m-4">

      <?php  
        require_once("pages/logistica/function.php");
        require_once("pages/logistica/controller.php");
      ?>

      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fa-solid fa-file-lines"></i> Relatórios Logística</h1>
        <!-- <a href="index.php?op=160" class="btn btn-outline-secondary"><i class="fa-solid fa-file-arrow-down"></i> Check-out (Saída)</a> -->
      </div>

      <div class="row">
        <div class="col-12">
         

          <form class="row g-3" action="index.php" method="POST">
            <input type="hidden" name="op" value="160">

            <div class="col-md-2">
              <label for="inputState" class="form-label">Tipo</label>
              <select name="TIPO" class="form-select select2">
                <option value="CHECKOUT">Resumo Checkout</option>
              </select>
            </div>
            <div class="col-md-2">
              <label for="inputState" class="form-label">Posição</label>
              <select name="POSICAO" class="form-select select2">
                <option value="ALL" selected>Todos</option>
                <?php if ($dados['POSICAO'] == 'FINALIZADOS'): ?>
                  <option value="FINALIZADOS" selected>
                <?php else: ?>
                  <option value="FINALIZADOS">
                <?php endif ?>
                Apenas Finalizados</option>
                <?php if ($dados['POSICAO'] == 'PENDENTES'): ?>
                  <option value="PENDENTES" selected>
                <?php else: ?>
                  <option value="PENDENTES">
                <?php endif ?>
                Apenas Pendentes</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="inputState" class="form-label">Conferente</label>
              <select name="CODUSUR" class="form-select select2">
                <option value="ALL" selected>Todos</option>
                <?php
                if ($variable = buscaUsuariosLogistica()) {
                    foreach ($variable as $key => $value) {
                      echo '<option value="'.$value['IDUSUARIO'].'"';
                      if ($dados['IDUSUARIO'] == $value['IDUSUARIO']) {
                        echo ' selected';
                      }
                      echo '>'.$value['NOME'].'</option>';
                    }
                  }  
                ?>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Data Início</label>
              <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAINI" type="text" class="form-control" value="<?=(isset($_POST['DATAINI']))?$_POST['DATAINI']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
              </div>
            </div>
            <div class="col-md-2">
              <label class="form-label">Data Fim</label>
              <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAFIM" type="text" class="form-control" value="<?=(isset($_POST['DATAFIM']))?$_POST['DATAFIM']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
              </div>
            </div>
            <div class="col-md-1">
              <button type="submit" name="acao" value="pesquisaRelatorio" class="btn btn-secondary btn-block mt-4 w-100"><i class="fa fa-solid fa-search"></i></button>
            </div>
          </form>

        </div>
      </div>

      <div class="row mt-3">
        <div class="col-12">

          <table class="table" id="tb_order_desc">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Data</th>
                <th scope="col">Posição</th>
                <th scope="col">Conferente</th>
                <th scope="col">Vendedor</th>
                <th scope="col">Cliente</th>
                <th scope="col">Numped</th>
                <th scope="col">Orçamento</th>
                <!-- <th scope="col">Ação</th> -->
              </tr>
            </thead>
            <tbody>
              <?php if ($REL_CHECKOUT): ?>
                <?php foreach ($REL_CHECKOUT as $key => $value): ?>
                  <tr>
                    <th scope="row"><?=$value['IDCHECKOUT']?></th>
                    <td><?=formataDataOracletoBr($value['DTINICIO'])?></td>
                    <td><?=$value['POSICAO']?></td>
                    <td><?=$value['CONFERENTE']?></td>
                    <td><?=$value['VENDEDOR']?></td>
                    <td><?=$value['CODCLI'].'- '.$value['CLIENTE']?></td>
                    <td><?=$value['NUMPED']?></td>
                    <td><?=$value['NUMPEDCLI']?></td>
                  </tr>
                <?php endforeach ?>
              <?php endif ?>
            </tbody>
          </table>

        </div>
      </div>

    </main>
  </div>
</div>