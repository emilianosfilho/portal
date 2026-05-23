<div class="modal fade" id="modalAddClassificacao" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">
          <i class="fa-regular fa-plus"></i> Adicionar Classificação
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="op" value="<?=$dados['op']?>">
          <input type="hidden" name="aba" value="<?=$dados['aba']?>">
          
          <div class="input-group mb-3">
            <select name="CLASSIFICACAO" class="form-select select2">
              <option value="CATEGORIA">CATEGORIA</option>
              <option value="SUBCATEGORIA">SUBCATEGORIA</option>
              <option value="GRUPO">GRUPO</option>
              <option value="SUBGRUPO">SUBGRUPO</option>
            </select>
          </div>

          <div class="input-group mb-3">
            <input type="text" name="NOME" class="form-control" placeholder="Novo registro" aria-label="Novo registro" aria-describedby="button-addon2" required autocomplete="off">
            <button type="submit" name="acao" value="inserirClassificacao" class="btn btn-primary">Salvar</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>