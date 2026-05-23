<main>
  <div class="container">
    <?php  
    require_once("pages/compras/function.php");
    require_once("pages/compras/controller.php");
    
    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    
    ?>
    <div class="card">
      <div class="card-header">
        <h2><i class="fa-solid fa-file-import"></i> Alteração em massa</h2>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <form action="index.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="NOMEARQUIVO" value="PRODUTO_ALTERACAO">
              <label>Envio do arquivo</label>
              <input type="hidden" name="op" value="116">
              <div class="input-group mb-3">
                <input type="file" name="arquivo" class="form-control" id="inputGroupFile02" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                <button type="submit" name="acao" value="enviarArquivoAlteracaoMassa" class="btn btn-secondary" for="inputGroupFile02">Upload</button>
              </div>
            </form>
          </div>

          <div class="col-md-4">
            <!-- <a href="" class="btn btn-danger mt-3"><i class="fa-solid fa-gear"></i> Parâmetros</a> -->
          </div>

          <div class="col-md-2">
            <a target="_blanck" href="<?=@DIR_DOWNLOAD.'MODELO_ALTERACAO_MASSA.xlsx'?>" class="btn btn-primary mt-3"><i class="fa-solid fa-file-arrow-down"></i> Baixar modelo</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</main>