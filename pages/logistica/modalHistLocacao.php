<div class="modal fade" id="modalHistLocacao" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fas fa-clock"></i> Histórico de Alteração Locação da peça
        </h4>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Data</th>
              <th scope="col">Usuário</th>
              <th scope="col">Antes</th>
              <th scope="col">Depois</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($lista = buscaHistoricoLocacao($dados['CODPROD'])): ?>
              <?php foreach ($lista as $key => $value): ?>
                <tr>
                  <th scope="row"><?=($key+1)?></th>
                  <td><?=formataDataOracletoBr($value['DTALTERACAO'])?></td>
                  <td><?=$value['USUARIO']?></td>
                  <td><?=$value['LOCACAO_OLD']?></td>
                  <td><?=$value['LOCACAO_NEW']?></td>
                </tr>
              <?php endforeach ?>
            <?php else: ?>
              <tr><td colspan="5">nenhuma alteração registrada</td></tr>
            <?php endif ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>