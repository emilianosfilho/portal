
<main>
  <div class="container">
    <?php  
    require_once("pages/vendas/function.php");
    require_once("pages/vendas/controller.php");
    // varDump2($_SESSION['login']);
    ?>
    <h2>
      <i class="fa-solid fa-search"></i> Consulta Orçamento
      <div class="float-sm-end">
        <div class="btn-group">
          <?php if (array_search('RADAR', $_SESSION['login']['PERMISSOES']) !== false): ?>
            <a href="radar.php" class="btn btn-sm bg-dark text-white"><i class="fa-solid fa-tv"></i> Radar</a>
          <?php endif ?>
          <a href="index.php?op=61" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus"></i> Novo Orçamento</a>
        </div>
      </div>
    </h2>

    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          
          <form class="row g-3" action="index.php" method="POST">
            <input type="hidden" name="op" value="68">

            <div class="col-md-2">
              <label class="form-label">Data Início</label>
              <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="dataini" type="text" class="form-control" value="<?=(isset($_POST['dataini']))?$_POST['dataini']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
              </div>
            </div>
            <div class="col-md-2">
              <label class="form-label">Data Fim</label>
              <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="datafim" type="text" class="form-control" value="<?=(isset($_POST['datafim']))?$_POST['datafim']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
              </div>
            </div>
            <div class="col-md-2">
              <label for="inputState" class="form-label">Vendedor</label>
              <select name="CODUSUR" class="form-select select2">
                <option value="all" selected>Todos</option>
                <?php
                if ($vendedores = buscaVendedores()) {
                    foreach ($vendedores as $key => $value) {
                      echo '<option value="'.$value['CODUSUR'].'"';
                      if ($_SESSION['login']['CODUSUR'] == $value['CODUSUR']) {
                        echo ' selected';
                      }
                      echo '>'.$value['NOME'].' ['.$value['CODUSUR'].']</option>';
                    }
                  }  
                ?>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Cód. Cliente</label>
              <input type="text" name="CODCLI" class="form-control" autocomplete="off" value="<?=($_POST['CODCLI']<>"")?$_POST['CODCLI']:""?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Nr Orçamento</label>
              <input type="text" name="IDORCAMENTO" class="form-control" autocomplete="off" autofocus value="<?=($_POST['IDORCAMENTO']<>"")?$_POST['IDORCAMENTO']:""?>" autocomplete="off">
            </div>
            <div class="col">
              <button type="submit" name="acao" value="pesquisaOrcamento" class="btn btn-secondary btn-block mt-4 w-100"><i class="fa fa-solid fa-search"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    

    <?php 
    require_once('pages/vendas/listaOrcamentos.php');
    ?>

  </div>
</main>


