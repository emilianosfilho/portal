<main>
  <div class="container">
    <?php  
    require_once("pages/compras/function.php");
    require_once("pages/compras/controller.php");
    
    if ($_SESSION['login']['MATRICULA'] == "") {
      insereModal('danger', '<h3>O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</a>');
    }

    ?>
    <div class="card">
      <div class="card-header">
        <h2><i class="fa-solid fa-arrow-rotate-right"></i> Alteração de Produtos</h2>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            <form action="index.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="NOMEARQUIVO" value="PRODUTO_ALTERACAO">
              <label>Envio do arquivo</label>
              <input type="hidden" name="op" value="110">
              <div class="input-group mb-3">
                <input type="file" name="arquivo" class="form-control" id="inputGroupFile02" required>
                <button type="submit" name="acao" value="arquivoAlteracaoProduto" class="btn btn-secondary" for="inputGroupFile02">Upload</button>
              </div>
            </form>
          </div>

          <div class="col-md-2">
          </div>

          <div class="col-md-2">
            <a target="_blanck" href="<?=@DIR_DOWNLOAD.'MODELO_ALTERACAO_PRODUTO.xlsx'?>" class="btn btn-primary mt-3"><i class="fa-solid fa-file-arrow-down"></i> Baixar modelo</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</main>