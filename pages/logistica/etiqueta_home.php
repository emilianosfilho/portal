<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
    ?>
    <main class="col ms-sm-auto px-3">

      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-tag"></i> Etiquetas
          </h1>
        </div>
      </div>

      <div class="row mt-3">

        <div class="card">
          <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link text-lg <?=(($dados['tab']=="locacao")?'active':(!isset($dados['tab'])?'active':''))?>" id="locacao-tab" data-bs-toggle="tab" data-bs-target="#locacao" type="button" role="tab" aria-controls="locacao" aria-selected="true">
                  Locação
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link text-lg <?=(($dados['tab']=="nome")?'active':'')?>" id="nome-tab" data-bs-toggle="tab" data-bs-target="#nome" type="button" role="tab" aria-controls="nome" aria-selected="true">
                  Nome
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link text-lg <?=(($dados['tab']=="computador")?'active':'')?>" id="computador-tab" data-bs-toggle="tab" data-bs-target="#computador" type="button" role="tab" aria-controls="computador" aria-selected="false">Computador</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link text-lg <?=(($dados['tab']=="entrega")?'active':'')?>" id="entrega-tab" data-bs-toggle="tab" data-bs-target="#entrega" type="button" role="tab" aria-controls="entrega" aria-selected="false">Entrega</button>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
               
              <div class="tab-pane fade <?=(($dados['tab']=="locacao")?'show active':(!isset($dados['tab'])?'show active':''))?>" id="locacao" role="tabpanel" aria-labelledby="locacao-tab">
                <div class="card">
                  <div class="card-header">
                    <form action="index.php" method="POST">
                      <input type="hidden" name="op" value="43">
                      <input type="hidden" name="nav" value="logistica">
                      <input type="hidden" name="aba" value="etiquetas">

                      <div class="row g-3">

                        <div class="col">
                          <label class="form-label">Locação</label>
                          <input type="text" name="locacao" class="form-control" autofocus autocomplete="off" placeholder="Locação a ser impressa" required>
                        </div>

                        <div class="col-1">
                          <div class="d-grid gap-2">
                          <button class="btn btn-secondary mt-4" type="submit" name="acao" value="etiqueta_locacao"><i class="fa-solid fa-forward"></i></button>
                          </div>
                        </div> 

                      </div>
                    </form>
                  </div>
                </div>
              </div>
               
              <div class="tab-pane fade <?=(($dados['tab']=="nome")?'show active':'')?>" id="nome" role="tabpanel" aria-labelledby="nome-tab">
                <div class="card">
                  <div class="card-header">
                    <form action="index.php" method="POST">
                      <input type="hidden" name="op" value="43">
                      <input type="hidden" name="nav" value="logistica">
                      <input type="hidden" name="aba" value="etiquetas">
                      <input type="hidden" name="tab" value="nome">

                      <div class="row g-3">

                        <div class="col">
                          <label class="form-label">Nome</label>
                          <input type="text" name="nome" class="form-control" autofocus autocomplete="off" placeholder="Nome a ser impresso" required>
                        </div>

                        <div class="col">
                          <label class="form-label">Tamanho do Nome</label>
                          <select name="tamanho_fonte" class="form-select select2">
                            <option>12</option>
                            <option>16</option>
                            <option>20</option>
                            <option>24</option>
                            <option>28</option>
                            <option>32</option>
                            <option>36</option>
                            <option>48</option>
                          </select>
                        </div>

                        <div class="col">
                          <label class="form-label">Rodapé <code>(opcional)</code></label>
                          <input type="text" name="rodape" class="form-control" autofocus autocomplete="off" placeholder="Rodapé a ser impresso">
                        </div>

                        <div class="col-1">
                          <div class="d-grid gap-2">
                          <button class="btn btn-secondary mt-4" type="submit" name="acao" value="etiqueta_nome"><i class="fa-solid fa-forward"></i></button>
                          </div>
                        </div> 

                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <div class="tab-pane fade <?=(($dados['tab']=="computador")?'show active':'')?>" id="computador" role="tabpanel" aria-labelledby="computador-tab">
                <div class="card">
                  <div class="card-header">
                    <form action="index.php" method="POST">
                      <input type="hidden" name="op" value="43">
                      <input type="hidden" name="nav" value="logistica">
                      <input type="hidden" name="aba" value="etiquetas">

                      <div class="row g-3">

                        <div class="col">
                          <label class="form-label">Patrimômio</label>
                          <input type="text" name="patrimonio" class="form-control" autofocus autocomplete="off" placeholder="Nr Patrimômio" required>
                        </div>

                        <div class="col">
                          <label class="form-label">Etiqueta</label>
                          <input type="text" name="etiqueta" class="form-control" autofocus autocomplete="off" placeholder="Nr etiqueta" required>
                        </div>

                        <div class="col-1">
                          <div class="d-grid gap-2">
                          <button class="btn btn-secondary mt-4" type="submit" name="acao" value="etiqueta_computador"><i class="fa-solid fa-forward"></i></button>
                          </div>
                        </div> 

                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <div class="tab-pane fade <?=(($dados['tab']=="entrega")?'show active':'')?>" id="entrega" role="tabpanel" aria-labelledby="entrega-tab">
                <div class="card">
                  <div class="card-header">
                    <form action="index.php" method="POST">
                      <input type="hidden" name="op" value="43">
                      <input type="hidden" name="nav" value="logistica">
                      <input type="hidden" name="aba" value="etiquetas">
                      <input type="hidden" name="tab" value="entrega">

                      <div class="row g-3">

                        <div class="col-2">
                          <label class="form-label">FILIAL</label>
                            <select name="CODFILIAL" class="form-select select2">
                              <option value="1" selected>1 -VEMAP COMERCIO ...</option>
                            </select>
                        </div>

                        <div class="col-5">
                          <label class="form-label">CLIENTE</label>
                          <div class="input-group mb-3">
                            <div class="col-2">
                              <input type="text" name="CODCLI" id="CODCLI" class="form-control" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
                            </div>
                            <div class="col-10">
                              <input type="text" id="CLIENTE" class="form-control" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1" disabled>
                            </div>
                          </div>
                        </div>

                        <div class="col-2">
                          <label class="form-label">NUMNOTA</label>
                          <input type="text" name="NUMNOTA" class="form-control" placeholder="">
                        </div>

                        <div class="col">
                          <div class="d-grid gap-2">
                          <button class="btn btn-secondary mt-4" type="submit" name="acao" value="pesquisaNotasEntrega"><i class="fa-solid fa-search"></i></button>
                          </div>
                        </div> 

                      </div>
                    </form>
                  </div>

                  <?php if ($_SESSION['NFENTREGA']): ?>
                  <div class="card-header">
                    <div class="table-responsive">
                      <table class="table table-hover" id="tb_default">

                        <thead>
                          <tr>
                            <th>NUMNOTA</th>
                            <th>CLIENTE</th>
                            <th>FILIAL</th>
                            <th width="10%">Ações</th>
                          </tr>
                        </thead>

                        <tbody>
                          <?php foreach ($_SESSION['NFENTREGA'] as $key => $value) : ?>

                              <tr>
                              <td><?= $value['NUMNOTA'] ?></td>
                              <td><?= $value['CLI_CODCLI'].' - '.$value['CLI_CLIENTE'] ?></td>
                              <td><?= $value['FIL_CODFILIAL'].' - '.$value['FIL_FILIAL'] ?></td>
                              <td>
                                <div class="btn-group m-0">
                                  <a target="_blanck" class="btn btn-outline" href="index.php?op=43&nav=logistica&aba=etiquetas&tab=entrega&acao=etiqueta_modalEntrega&NUMNOTA=<?= $value['NUMNOTA'] ?>" title="Gerar Etiqueta de Entrega"><i class="fa fa-file"></i> </a>
                                </div>
                              </td>
                              </tr>
                              
                          <?php endforeach; ?>
                        </tbody>

                      </table>
                    </div>
                  </div>
                  <?php endif ?>

                </div>
              </div>

            </div>
          </div>
        </div>

      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->


<script>
  
  $(document).ready(function() {
      $('#CODCLI').on('change', function() {
          var codcli = $(this).val();
          var $inputCliente = $('#CLIENTE');

          if (codcli !== "") {
              $inputCliente.val('Buscando...');

              $.ajax({
                  url: 'pages/logistica/ajax_functions.php', // Ajuste o caminho conforme sua estrutura
                  type: 'POST',
                  dataType: 'json',
                  data: { 
                      acao: 'buscar_cliente', 
                      codcli: codcli 
                  },
                  success: function(data) {
                      console.log(data);
                      console.log(data['data']['CLIENTE']);
                      if (data['status'] == "sucesso") {
                          $inputCliente.val(data['data']['CLIENTE']);
                      } else {
                          $inputCliente.val('CLIENTE NÃO ENCONTRADO');
                          console.error('Erro: ' + data.message);
                      }
                  },
                  error: function() {
                      $inputCliente.val('ERRO NA REQUISIÇÃO');
                  }
              });
          } else {
              $inputCliente.val('');
          }
      });
  });

</script>