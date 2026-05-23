<main>
  <div class="container">
    <?php  
    require_once("pages/compras/function.php");
    require_once("pages/compras/controller.php");
    
    if(isset($_SESSION['LISTAPRODUTOSSALVOS'])) 
      unset($_SESSION['LISTAPRODUTOSSALVOS']);
    if(isset($_SESSION['VALIDACADASTROS'])) 
      unset($_SESSION['VALIDACADASTROS']);
    if(isset($_SESSION['INEXISTENTES'])) 
      unset($_SESSION['INEXISTENTES']);

    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    
    ?>
    <div class="card">
      <div class="card-header">
        <h2><i class="fa-solid fa-file-circle-plus"></i> Cadastro de Produtos</h2>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            <form action="index.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="op" value="110">
              <input type="hidden" name="NOMEARQUIVO" value="PRODUTO_CADASTRO">
              <label>Envio do arquivo</label>
              <div class="input-group mb-3">
                <input type="file" name="arquivo" class="form-control" id="inputGroupFile02" required>
                <button type="submit" name="acao" value="validaArquivoCadastro" class="btn btn-secondary" for="inputGroupFile02">Upload</button>
              </div>
            </form>
          </div>

          <div class="col-md-2">
            <!-- <a href="" class="btn btn-danger mt-3"><i class="fa-solid fa-gear"></i> Parâmetros</a> -->
          </div>

          <div class="col-md-2">
            <a target="_blanck" href="<?=@DIR_DOWNLOAD.'MODELO_CADASTRO_PRODUTO.xlsx'?>" class="btn btn-primary mt-3"><i class="fa-solid fa-file-arrow-down"></i> Baixar modelo</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</main>