<main>
  <div class="container">
    <?php  
    require_once("pages/relatorios/function.php");
    require_once("pages/relatorios/controller.php");
    ?>

    <h2>
      <i class="fa-solid fa-file"></i> Relatórios</a>
      <div class="float-sm-end">
        <a href="index.php?op=93&nav=relatorios" class="btn btn-primary"><i class="fa fa-plus-square"></i> Novo Relatório SQL</a>
      </div>
    </h2>
    
    <div class="row">

      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            
            <table id="tb_order_desc" class="table table-bordered table-striped table-hover">

              <thead>
                <tr>
                  <th>Última Atualização</th>
                  <th>Descrição</th>
                  <th>Tipo</th>
                  <th>Ativo</th>
                  <th style="white-space: nowrap;">ação</th>
                </tr>
              </thead>

              <tbody>

              <?php if ( $relatorios =  listaRelatórios() ): ?>

                <?php foreach ($relatorios as $key => $value): ?>
                  <tr>
                    <td><?= formataDataOracletoBr($value['DTULTALTER']) ?></td>
                    <td><?= $value['DESCRICAO'] ?></td>
                    <td><?= $value['TIPO'] ?></td>
                    <td>
                      <?= ($value['ATIVO']=="S")
                          ?'<span class="badge bg-success">SIM</span>'
                          :'<span class="badge bg-danger">NÃO</span>' ?>
                    </td>
                    <td style="white-space: nowrap;">
                      <div class="btn-group m-0 p-0">
                        <?php if ( $_SESSION['login']['IDUSUARIO']==1 ): ?>
                          <a href="index.php?op=93&nav=relatorios&IDRELATORIO=<?= $value['IDRELATORIO'] ?>" class="btn btn-sm btn-secondary" title="Editar Script"><i class="fa-solid fa-file-code"></i> Editar</a>
                        <?php endif ?>
                        <a href="index.php?op=92&nav=relatorios&IDRELATORIO=<?= $value['IDRELATORIO'] ?>" class="btn btn-sm btn-primary" title="Emitir Relatório"><i class="fa-solid fa-file-excel"></i> Emitir</a>
                      </div>
                    </td>
                  </tr>
                  
                <?php endforeach ?>
                
              <?php endif ?>



              </tbody>

            </table>

          </div>
        </div>
      </div>
      

    </div>

  </div>
</main>