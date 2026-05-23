<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/compras/function.php'); 
    require_once('pages/compras/controller.php'); 
    require_once('pages/compras/sidebar.php'); 
    require_once('pages/compras/precificacao-modalEnviarArquivo.php'); 
    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-dollar"></i> Precificação</h1>
          <div class="float-end">
            <div class="btn-group">
              <button class="btn btn-outline-secondary px-3"  title="Enviar Planilha - Produtos" data-bs-toggle="modal" data-bs-target="#precificacao-modalEnviarArquivo">
                <i class="fa-solid fa-upload"></i> 
              </button>
              <?php if (isset($_SESSION['PRECIFICACAO']) && !empty($_SESSION['PRECIFICACAO'])): ?>
                <a href="index.php?op=114&nav=compras&aba=precificacao&acao=exportaExcel&nome=produtosSemPreco" class="btn btn-outline-secondary"  title="Exportar Produtos sem preço"> 
                  <i class="fa-solid fa-file-excel"></i>
                </a>
                <a href="index.php?op=114&nav=compras&aba=precificacao&acao=clear" class="btn btn-outline-secondary"  title="Limpar Lista"> 
                  <i class="fa-solid fa-broom"></i>
                </a>
              <?php endif ?>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <form class="row" action="index.php" method="POST">
            <input type="hidden" name="op" value="114">
            <input type="hidden" name="nav" value="compras">
            <input type="hidden" name="aba" value="precificacao">
            
            <div class="col">
              <label class="form-label">NUMORIGINAL</label>
              <input type="text" name="NUMORIGINAL" value="<?=$dados['NUMORIGINAL']?>" class="form-control" autofocus autocomplete="off" placeholder="NUMORIGINAL">
            </div>
            
            <div class="col">
              <label class="form-label">CODPROD</label>
              <input type="text" name="CODPROD" value="<?=$dados['CODPROD']?>" class="form-control" autofocus autocomplete="off" placeholder="CODPROD">
            </div>
            
            <div class="col">
              <label class="form-label">CODMARCA</label>
              <input type="text" name="CODMARCA" value="<?=$dados['CODMARCA']?>" class="form-control" autofocus autocomplete="off" placeholder="CODMARCA">
            </div>
            
            <div class="col">
              <label class="form-label">CODFORNEC</label>
              <input type="text" name="CODFORNEC" value="<?=$dados['CODFORNEC']?>" class="form-control" autofocus autocomplete="off" placeholder="CODFORNEC">
            </div>
            
            <div class="col-1">
              <button class="btn btn-secondary float-end" type="submit" name="acao" value="precificacao_pesquisar">
                <i class="fa-solid fa-search"></i> Pesquisar
              </button>
            </div>

          </form>
        </div>

        <?php if (isset($_SESSION['PRECIFICACAO']) && !empty($_SESSION['PRECIFICACAO'])): ?>
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <table id="tb_default2" class="table table-bordered table-striped table-hover mt-4" style="width: 100%">
                <thead>
                  <tr>
                    <th>CODPROD</th>
                    <th>NUMORIGINAL</th>
                    <th>DESCRIÇÃO</th>
                    <th>MARCA</th>
                    <th>FORNECEDOR</th>
                    <th>ULT ENTRADA</th>
                    <th>ULT SAIDA</th>
                    <th>STATUS</th>
                    <th>P. VENDA</th>
                    <th width="10%">Ações</th>
                  </tr>
                </thead>
                
                <tbody>
                  <?php foreach ($_SESSION['PRECIFICACAO'] as $classe => $value): ?>
                    <tr>
                      <td><?= $value["CODPROD"] . "-" . $value["DV"] ?></td>
                      <td><?= $value["NUMORIGINAL"] ?></td>
                      <td><?= $value["DESCRICAO"] ?></td>
                      <td><?= $value["MARCA"] ?></td>
                      <td><?= $value["CODFORNEC"].'-'.$value["FORNECEDOR"] ?></td>
                      <td><?= formataDataOracleToBr($value["DTULTENTRADA"]) ?></td>
                      <td><?= formataDataOracleToBr($value["DTULTSAIDA"]) ?></td>
                      <td><?= (($value["PRODUTO_EXCLUIDO"]=="S")?'<span class="badge bg-danger">Excluído</span>':'<span class="badge bg-success">Ativo</span>') ?></td>
                      <td style="text-align: right;"><?= moeda($value["PVENDA"],4) ?></td>
                      <td>
                        <div class="btn-group">
                          <a class="btn btn-xs p-1" href="index.php?op=114&nav=compras&aba=precificacao&acao=precificacao-modalEditarPreco&CODPROD=<?= $value["CODPROD"] ?>&PVENDAOLD=<?= $value["PVENDA"] ?>" title="Atualizar Preço de venda">
                            <i class="fa-solid fa-arrows-rotate"></i>
                          </a>
                          <a class="btn btn-xs p-1" href="index.php?op=114&nav=compras&aba=precificacao&acao=precificacao-modalZerarPreco&CODPROD=<?= $value["CODPROD"] ?>&PVENDAOLD=<?= $value["PVENDA"] ?>" title="Zerar Preço de venda">
                            <i class="fa-solid fa-trash text-danger"></i>
                          </a>
                        </div>
                      </td>
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
  </div>
</div>