<div class="modal fade" id="modalClasse" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <?php
      if (isset($dados['CODLINHA']) && $dados['CODLINHA'] !== "") {
        // $classe = buscaCODLINHA($dados['CODLINHA']);
      }
      switch ($dados['acao']) {
          case 'montadoraAtualizar':
            $tipo = "Update";
            $background = "primary";
            $title = '<i class="fa-regular fa-edit"></i> Editar Montadora';
            break;
          case 'montadoraExcluir':
            $tipo = "Delete";
            $background = "danger";
            $title = '<i class="fa-regular fa-trash"></i> Excluir Montadora';
            break;
          default:
            $tipo = "Insert";
            $background = "info";
            $title = '<i class="fa-regular fa-plus"></i> Adicionar Classe';
            break;
        }
        // varDump2($dados);  
        // varDump2($montadora);  
      ?>
      <div class="modal-header bg-<?=$background?>">
        <h5 class="modal-title" id="exampleModalLabel">
          <?=$title?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="op" value="126">
          <input type="hidden" name="aba" value="classes">
          
          <?php if ($tipo == "Insert"): ?>
            <div class="input-group mb-3">
              <input type="text" name="LINHA" class="form-control" placeholder="Novo registro" aria-label="Novo registro" aria-describedby="button-addon2" required autocomplete="off">
              <button type="submit" name="acao" value="classe<?=$tipo?>" class="btn btn-info">Salvar Novo</button>
            </div>
          <?php else: ?>
            <?php if ($tipo == "Update"): ?>
              <input type="hidden" name="CODLINHA" value="<?=$classe['CODLINHA']?>">
              <div class="input-group mb-3">
                <input type="text" name="LINHA" value="<?=$classe['LINHA']?>" class="form-control" placeholder="Novo registro" aria-label="Novo registro" aria-describedby="button-addon2" required autocomplete="off">
                <button type="submit" name="acao" value="classe<?=$tipo?>" class="btn btn-primary">Salvar Alterações</button>
              </div>
            <?php else: ?>
              <input type="hidden" name="CODLINHA" value="<?=$classe['CODLINHA']?>">
              <div class="input-group mb-3">
                <input type="text" name="LINHA" value="<?=$classe['LINHA']?>" class="form-control" aria-describedby="button-addon2" disabled readonly>
                <button type="submit" name="acao" value="classe<?=$tipo?>" class="btn btn-danger">Confirmar Exclusão</button>
              </div>
            <?php endif ?>
          <?php endif ?>

        </form>
      </div>
    </div>
  </div>
</div>