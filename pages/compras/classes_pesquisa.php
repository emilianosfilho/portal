<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/compras/function.php'); 
    require_once('pages/compras/controller.php'); 
    require_once('pages/compras/sidebar.php'); 
    require_once('pages/compras/classes_modalEnviarArquivo.php'); 
    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-regular fa-file-signature"></i> Classes</h1>
          <div class="float-end">
            <div class="btn-group">
              <button type="button" class="btn btn-secondary">Opções</button>
              <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu">
                <li>
                  <em>
                    <a class="dropdown-item" href="index.php?op=126&aba=classes&acao=linhaInsert">
                      <i class="fa-solid fa-circle-plus"></i> Cadastrar Nova Linha
                    </a>
                  </em>
                </li>
                <li>
                  <em>
                    <a class="dropdown-item" href="index.php?op=126&aba=classes&acao=vincluloClasseInsert">
                      <i class="fa-solid fa-circle-plus"></i> Cadastrar Vinculo Classe -> NUMORIGINAL
                    </a>
                  </em>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>

                <li>
                  <em>    
                    <button class="dropdown-item"  title="Enviar Planilha Classes" data-bs-toggle="modal" data-bs-target="#classes_modalEnviarArquivo">
                      <i class="fa-solid fa-file-excel"></i> Enviar Planilha Classes
                    </button>
                  </em>
                </li>
                <li>
                  <em>
                    <a class="dropdown-item" href="download/MODELO_CLASSES.xlsx" target="_blanck">
                      <i class="fa-solid fa-file-arrow-down"></i> Baixar Planilha Modelo - Classes
                    </a>
                  </em>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li>
                  <em>
                    <a href="index.php?op=126&aba=classes&acao=clear" class="dropdown-item"  title="Limpar Lista"> 
                      <i class="fa-solid fa-retweet"></i> Limpar Lista 
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
            <input type="hidden" name="op" value="126">
            <input type="hidden" name="aba" value="classes">
            
            <div class="col-2">
              <label class="form-label">FILIAL</label>
              <select name="CODFILIAL" class="form-select select2">
                <?php
                if ($filiais = buscaFiliais()){
                  foreach ($filiais as $key => $value) {
                    echo '<option value="'.$value['CODFILIAL'].'"';
                    if (isset($dados["CODFILIAL"]) && $dados["CODFILIAL"] == $value['CODFILIAL']) {
                      echo 'selected';
                    }
                    echo '>'.$value['CODFILIAL'].'- '.$value['FANTASIA'].'</option>';
                  }
                }
                ?>
              </select>
            </div>
            
            <div class="col-2">
              <label class="form-label">CLASSE (Linha)</label>
              <select name="CODLINHA" class="form-select select2">
                <option value="ALL">Todas</option>
                <?php
                if ($classes = buscaClasses()){
                  foreach ($classes as $key => $value) {
                    echo '<option value="'.$value['CODLINHA'].'"';
                    if (isset($dados["CODLINHA"]) && $dados["CODLINHA"] == $value['CODLINHA']) {
                      echo 'selected';
                    }
                    echo '>'.$value['CLASSE'].' ['.$value['QT_PRODUTOS'].']</option>';
                  }
                }
                ?>
              </select>
            </div>

            <div class="col-2">
              <label class="form-label">NUMORIGINAL</label>
              <input type="text" name="NUMORIGINAL" value="<?=$dados['NUMORIGINAL']?>" class="form-control" autocomplete="off" placeholder="NUMORIGINAL">
            </div>
            
            <div class="col-1">
              <button class="btn btn-secondary float-end" type="submit" name="acao" value="pesquisarClasses">
                <i class="fa-solid fa-search"></i> Pesquisar
              </button>
            </div>

          </form>
        </div>
        <div class="card-body">

          <?php if (isset($_SESSION['LISTACLASSES']) && !empty($_SESSION['LISTACLASSES'])): ?>
          <div class="row">
            <div class="col-12 text-lg">
              <table id="tb_default2" class="table table-bordered table-striped table-hover mt-4" style="width: 100%">
                <thead>
                  <tr>
                    <th>FILIAL</th>
                    <th>CLASSE</th>
                    <th>NUMORIGINAL</th>
                    <th>Qt Prod.</th>
                    <!-- <th width="10%">Ações</th> -->
                  </tr>
                </thead>
                <tbody>

                  <?php foreach ($_SESSION['LISTACLASSES'] as $classe => $value): ?>
                    
                    <tr>
                      <td><?= $value["CODFILIAL"].'- '.$value["FILIAL"] ?></td>
                      <td><?= $value["CLASSE"] ?></td>
                      <td><?= $value["NUMORIGINAL"] ?></td>
                      <td><?= $value["QT_PROD"] ?></td>
                    </tr>
                  <?php endforeach ?>

                </tbody>
              </table>
            </div>
          </div>
          <?php endif ?>
          
        </div>
      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->