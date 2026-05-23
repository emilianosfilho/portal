<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/compras/function.php'); 
    require_once('pages/compras/controller.php'); 
    require_once('pages/compras/sidebar.php'); 
    require_once('pages/compras/produto-aplicarArquivo.php'); 
    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    // varDump2($_SESSION['RESULTADO']);
    
    ?>
    <main class="col-11 ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-boxes"></i> Produtos - Resultado</h1>
          <div class="float-end">
            <div class="btn-group">
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

      <div class="row">

        <div class="col-3">
          <?php if ($_SESSION['RESULTADO']): ?>
          <ol class="list-group list-group-numbered">
            <li class="list-group-item d-flex justify-content-between align-items-start">
              <div class="ms-2 me-auto">
                <div class="fw-bold">ITENS PROCESSADOS</div>
              </div>
              <span class="badge bg-primary rounded-pill text-xl"><?= count($_SESSION['RESULTADO']) ?></span>
            </li>
          </ol>
          <?php endif ?>
        </div>

        <div class="col-3">
          <a class="btn btn-primary" href="index.php?op=110&nav=compras&aba=produto&acao=produto-exportaResultado"> 
            <i class="fa-solid fa-file-excel"></i> Exportar resultados para excel
          </a>
        </div>

      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->