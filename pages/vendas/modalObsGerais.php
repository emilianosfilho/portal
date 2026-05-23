<div class="modal fade" id="modalObsGerais" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-pen"></i> Define Observaçõe Gerais
        </h4>
      </div>

      <form name="form" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="<?=$dados['op']?>">
        <input type="hidden" name="IDORCAMENTO" value="<?=$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']?>">

        <div class="modal-body">

          <div class="mb-1 row">
            <div class="col">
              <label class="form-label">Observações Gerais</label>
              <input type="text" name="OBSGERAIS" id="OBSGERAIS" class="form-control" autocomplete="off" placeholder="Informe Observações Gerais" maxlength="75" value="<?=$_SESSION['ORCAMENTO']['CAB']['OBSGERAIS']?>">
              <small class="mt-1">
                <ul class="list-unstyled ">
                  <li>Preencher o campo Observações Gerais quando:
                    <ul>
                      <li>O orçamento se tratar de uma pesquisa aleatória de código certo. Exemplo: o Cliente falou que quer um reparo e você não sabe qual cilindro exatamente. Pode preencher como PESQUISA TESTE ou TESTE ou ESTUDO.</li>
                    </ul>
                  </li>
                  <li>Atenção: Este campo não será enviado para o Winthor.</li>
                </ul>
              </small>
            </div>
          </div>        

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success" name="acao" value="defineObsGerais">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>



      </form>

    </div>
  </div>
</div>