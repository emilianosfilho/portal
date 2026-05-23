<div class="container-fluid mt-5">
  <div class="row">
    <?php 
      require_once('pages/compras/sidebar.php'); 
      
      echo '<main class="col-11 ms-sm-auto px-3 pe-5">';

      require_once('pages/compras/function.php'); 
      require_once('pages/compras/controller.php'); 
      
      if ($_SESSION['login']['MATRICULA'] == "") {
        echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
      }
    ?>

    <div class="row">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fa-solid fa-brazilian-real-sign"></i> Cadastro de Precificação</h1>
        <a target="_blanck" href="<?=@DIR_DOWNLOAD.'MODELO_PRECIFICACAO.xlsx'?>" class="btn btn-outline-primary mt-3"><i class="fa-solid fa-file-arrow-down"></i> Baixar modelo</a>
      </div>
    </div>

    <div class="row">
      <div class="col-5">
        <form action="index.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="op" value="<?=$dados['op']?>">
          <input type="hidden" name="acao" value="validaArquivoPrecificacao">
          <input type="hidden" name="NOMEARQUIVO" value="PRECIFICACAO">
          <label>Envio do arquivo</label>
          <div class="input-group mb-3">
            <input type="file" name="arquivo" class="form-control" id="inputGroupFile02" required>
            <button type="submit" class="btn btn-secondary" for="inputGroupFile02">Enviar arquivo</button>
          </div>
        </form>
      </div>

      <div class="col-1"></div>

      <div class="col-6 bg-info">
        <p class="lead">
          <strong><i class="fa-solid fa-circle-info"></i> ATENÇÃO</strong>
        </p>
        <p class="lead">
          <strong>
            Nesta Seção você poderá realizar o cadastro automatizado de precificação de produtos no winthor, mas atenção:
            <ul>
              <li>O campo CODPROD é de preeenchimento obrigatório, para identificar o produto</li>
              <li>O campo DV é de preeenchimento obrigatório, para validar se o produto está correto</li>
              <li>O campo PVENDA é de preeenchimento obrigatório e deverá ser maior que 0,0001</li>
              <li>O campo PVENDA informado na planilha será aplicado igualmente a <b>TODAS AS REGIÕES ATIVAS</b> no momento que a planilha for importada</li>
            </ul>
          </strong>
        </p>
      </div>
    </div>



    </main>
  </div><!-- row -->
</div><!-- container-fluid -->
