<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/compras/function.php'); 
    require_once('pages/compras/controller.php'); 
    require_once('pages/compras/sidebar.php'); 
    require_once('pages/compras/produto-modalEnviarArquivo.php'); 
    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    ?>
    <main class="col-11 ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-boxes"></i> Produtos</h1>
          <div class="float-end">
            <div class="btn-group">
              <button 
                class="btn btn-outline-secondary px-3"  
                data-bs-toggle="modal" 
                data-bs-target="#produto-modalEnviarArquivo"
                title="Enviar Planilha - Produtos" 
                >
                <i class="fa-solid fa-upload"></i> 
              </button>
              <a 
                href="index.php?op=110&nav=compras&aba=produto&acao=clear" 
                class="btn btn-outline-secondary"  
                title="Limpar Lista"
                > 
                <i class="fa-solid fa-broom"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <form class="row" action="index.php" method="POST">
            <input type="hidden" name="op" value="110">
            <input type="hidden" name="aba" value="produtos">
            
            <div class="col-2">
              <label class="form-label">NUMORIGINAL</label>
              <input type="text" name="NUMORIGINAL" value="<?=$dados['NUMORIGINAL']?>" class="form-control" autofocus autocomplete="off" placeholder="NUMORIGINAL">
            </div>
            
            <div class="col-2">
              <label class="form-label">CODPROD</label>
              <input type="text" name="CODPROD" value="<?=$dados['CODPROD']?>" class="form-control" autofocus autocomplete="off" placeholder="CODPROD">
            </div>
            
            <div class="col-2">
              <label class="form-label">CODFAB</label>
              <input type="text" name="CODFAB" value="<?=$dados['CODFAB']?>" class="form-control" autofocus autocomplete="off" placeholder="CODFAB">
            </div>
            
            <div class="col-1">
              <button class="btn btn-secondary float-end" type="submit" name="acao" value="pesquisarprodutos">
                <i class="fa-solid fa-search"></i> Pesquisar
              </button>
            </div>

          </form>
        </div>

        <?php if (isset($_SESSION['PRODUTOS']) && !empty($_SESSION['PRODUTOS'])): ?>
        <div class="card-body">
          <div class="row">
            <div class="col-12 text-lg">
              <table id="tb_default2" class="table table-bordered table-striped table-hover mt-4" style="width: 100%">
                <thead>
                  <tr>
                    <th>FILIAL</th>
                    <th>CLASSE</th>
                    <th>NUMORIGINAL</th>
                    <th width="10%">Ações</th>
                  </tr>
                </thead>
                <tbody>

                  <?php foreach ($_SESSION['PRODUTOS'] as $classe => $value): ?>
                    
                    <tr>
                      <td><?= $value["CODFILIAL"].'- '.$value["FILIAL"] ?></td>
                      <td><?= $classe ?></td>
                      <td><?= $value["NUMORIGINAL"] ?></td>
                      <td><?= $value["CODPROD"] ?></td>
                      <td><?= $value["PRODUTO"] ?></td>
                      <td></td>
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