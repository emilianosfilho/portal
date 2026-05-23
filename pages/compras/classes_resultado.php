<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/compras/function.php'); 
    require_once('pages/compras/controller.php'); 
    require_once('pages/compras/sidebar.php'); 
    require_once('pages/compras/classes_validarArquivo.php'); 

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

      <div class="row">

        <div class="col-9">
          <div class="card">
            <div class="card-header">
              <h5>Preview do arquivo</h5>
            </div>
            <div class="card-body">
              <?php if (isset($_SESSION['ARQUIVO']) && !empty($_SESSION['ARQUIVO'])): ?>
              <table id="tb_default2" class="table table-bordered table-striped table-hover mt-4" style="width: 100%">
                <thead>
                  <tr>
                    <th>VALIDAÇÃO</th>
                    <th>CLASSE</th>
                    <th>NUMORIGINAL</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                foreach ($_SESSION['ARQUIVO'] as $item) {

                    $classe = $item['CLASSE'];
                    $temCadastro = isset($classesComCadastro[$classe]);

                    $status = match (true) {
                        strlen($classe) !== 3 => ['0', 'fa-circle-xmark', 'text-danger', 'CLASSE INVÁLIDA'],
                        $temCadastro          => ['2', 'fa-circle-check', 'text-success', 'CLASSE VÁLIDA'],
                        default               => ['1', 'fa-triangle-exclamation', 'text-warning', 'CLASSE SEM CADASTRO'],
                    };

                    echo sprintf(
                        '<tr>
                            <td>
                                <p style="display:none;">%s</p>
                                <i class="fa-solid %s %s"></i> %s
                            </td>
                            <td>%s</td>
                            <td>%s</td>
                        </tr>',
                        $status[0],
                        $status[1],
                        $status[2],
                        $status[3],
                        htmlspecialchars($classe),
                        htmlspecialchars((string) $item['NUMORIGINAL'])
                    );

                    if ($status[0] == 1) {
                      $classesSemCadastro[$classe] = true;
                    }
                }
                ?>


                </tbody>
              </table>
              <?php endif ?>
              
            </div>
          </div>
        </div>

        <div class="col-3">
          <div class="card">
            <div class="card-header">
              <h5>Validação do Arquivo</h5>
            </div>
            <div class="card-body">
              <table class="table">
                <tbody>
                  <tr>
                    <td>Total de Registros no arquivo</th>
                    <td><i class="fa-solid fa-circle-info text-info"></i></th>
                    <th scope="row"><?= moeda(count($_SESSION['ARQUIVO']), 0) ?></td>
                  </tr>
                  <tr>
                    <td>Total de Classes distintas no arquivo</th>
                    <td><i class="fa-solid fa-circle-info text-info"></i></th>
                    <th scope="row"><?= moeda(count($classesArquivo),0) ?></td>
                  </tr>
                    <tr>
                      <td>Classes Inválidas</th>
                      <td><i class="fa-solid fa-circle-xmark text-danger"></i></th>
                      <th scope="row"><?= moeda(count($classesInvalidas),0) ?></td>
                    </tr>
                    <tr>
                      <td>Classes Sem cadastro</th>
                      <td><i class="fa-solid fa-triangle-exclamation text-warning"></i></th>
                      <th scope="row"><?= moeda(count($classesSemCadastro),0) ?></td>
                    </tr>
                  <tr>
                    <td>Classes Válidas</th>
                    <td><i class="fa-solid fa-circle-check text-success"></i></th>
                    <th scope="row"><?= moeda(count($classesComCadastro),0) ?></td>
                  </tr>
                </tbody>
              </table>
              <div class="d-grid gap-2">
                <?php if (count($classesInvalidas) > 0): ?>
                  <a href="index.php?op=127&aba=classes&acao=classes_aplicarArquivo" title="Prosseguir" class="btn btn-success"><i class="fa-solid fa-forward"></i> Prosseguir</a>
                <?php else: ?>
                  <button title="Ajuste os erros para prosseguir" class="btn btn-danger"><i class="fa-solid fa-circle-xmark"></i> Resolver Pendências</button>
                <?php endif ?>
              </div>

              <div class="d-grid gap-2 mt-3">
                <h5>Regras de validação</h5>
                <ul>
                  <li><i class="fa-solid fa-circle-xmark text-danger"></i> Classe inválida: Acontece quando a classe informada no arquivo possui uma quantidade de caracteres diferente de 3. Estes registros <b>Não serão processados</b>.</li>
                  <li><i class="fa-solid fa-triangle-exclamation text-warning"></i> Classe sem cadastro: Acontece quando a classe informada no arquivo possui uma quantidade de caracteres igual a 3 mas ainda não possui cadsatro na tabela de linhas no Winthor. Estes registros <b>serão processados</b> e as novas classes serão cadastradas.</li>
                  <li><i class="fa-solid fa-circle-check text-success"></i> Classe válida: Acontece quando a classe informada no arquivo possui a quantidade de caracteres igual a 3 e já possui cadastro na tabela de Linhas no Winthor. Estes registros <b>serão processados</b>.</li>
                </ul>
              </div>

            </div>
          </div>
        </div>

      </div>


    </main>
  </div><!-- row -->
</div><!-- container-fluid -->