<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/compras/function.php'); 
    require_once('pages/compras/controller.php'); 
    require_once('pages/compras/sidebar.php'); 
    require_once('pages/compras/pedcompra-modalEnviarArquivo.php'); 
    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-basket-shopping"></i> Pedidos de Compra - Pesquisa</h1>
          <div class="float-end">
            <div class="btn-group">
              <button type="button" class="btn btn-secondary">Opções</button>
              <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu">
                <li>
                  <em>    
                    <button class="dropdown-item"  title="Enviar Planilha Pedido de compra" data-bs-toggle="modal" data-bs-target="#pedcompra-modalEnviarArquivo">
                      <i class="fa-solid fa-file-excel"></i> Enviar Planilha - Pedido de compra
                    </button>
                  </em>
                </li>
                <li>
                  <em>
                    <a href="index.php?op=117&aba=pedido&acao=clear" class="dropdown-item"  title="Limpar Lista"> 
                      <i class="fa-solid fa-retweet"></i> Limpar Lista 
                    </a>
                  </em>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li>
                  <em>
                    <a class="dropdown-item" href="download/MODELO_PEDCOMPRA.xlsx" target="_blanck">
                      <i class="fa-solid fa-file-arrow-down"></i> Baixar Planilha - Modelo Pedido de compra
                    </a>
                  </em>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <form class="row" action="index.php" method="POST">
            <input type="hidden" name="op" value="117">
            <input type="hidden" name="aba" value="pedido">
            
            <div class="col">
              <label class="form-label">TIPO</label>
              <select name="TIPOPESQUISA" class="form-select select2">
                <option value="PCSUGESTAOCOMPRAC" <?= ((isset($dados['TIPOPESQUISA']))?(($dados['TIPOPESQUISA']=="PCSUGESTAOCOMPRAC")?'':''):'selected')?>>SUGESTÃO DE COMPRA</option>
                <option value="PCPEDIDO" <?= ((isset($dados['TIPOPESQUISA']))?(($dados['TIPOPESQUISA']=="PCPEDIDO")?'selected':''):'')?>>PEDIDO DE COMPRA</option>
              </select>
            </div>

            <div class="col-2">
              <label class="form-label">Data Inícial</label>
              <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAINI" type="text" class="form-control" value="<?=(isset($_POST['DATAINI']))?$_POST['DATAINI']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
              </div>
            </div>

            <div class="col-2">
              <label class="form-label">Data Final</label>
              <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAFIM" type="text" class="form-control" value="<?=(isset($_POST['DATAFIM']))?$_POST['DATAFIM']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
              </div>
            </div>

            <div class="col">
              <label class="form-label">CODFORNEC</label>
              <input type="text" name="CODFORNEC" value="<?=$dados['CODFORNEC']?>" class="form-control" autofocus autocomplete="off" placeholder="COD FORNECEDOR">
            </div>

            <div class="col">
              <label class="form-label">NUMPED</label>
              <input type="text" name="NUMPED" value="<?=$dados['NUMPED']?>" class="form-control" autofocus autocomplete="off" placeholder="NUM PEDIDO DE COMPRA">
            </div>
            
            <div class="col-1">
              <button class="btn btn-secondary float-end" type="submit" name="acao" value="consultarPedidoCompras">
                <i class="fa-solid fa-search"></i> Pesquisar
              </button>
            </div>

          </form>
        </div>

        <?php if (isset($_SESSION['LISTAPEDCOMPRA']) && !empty($_SESSION['LISTAPEDCOMPRA'])): ?>
        <div class="card-body">
          <div class="row">
            <div class="col-12 text-lg">
              <table id="tb_default2" class="table table-bordered table-striped table-hover mt-4" style="width: 100%">
                <thead>
                  <tr>
                    <th>TIPO</th>
                    <th>DATA</th>
                    <th>NUMPED</th>
                    <th>FORNECEDOR</th>
                    <th>QTD ITENS</th>
                    <!-- <th width="10%">Ações</th> -->
                  </tr>
                </thead>
                <tbody>

                  <?php foreach ($_SESSION['LISTAPEDCOMPRA'] as $classe => $value): ?>
                    
                    <tr>
                      <td><?= $value["TIPOPESQUISA"] ?></td>
                      <td><?= formataDataOracleToBR($value["DATA"]) ?></td>
                      <td><?= $value["NUMPED"] ?></td>
                      <td><?= $value["CODFORNEC"].'- '.$value["FORNECEDOR"] ?></td>
                      <td><?= $value["QTD_ITENS"] ?></td>
                      <!-- <td></td> -->
                    </tr>
                  <?php endforeach ?>

                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php endif ?>

      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->