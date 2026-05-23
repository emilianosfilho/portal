<div class="modal fade" id="modalFinalizar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-check"></i> Finalizar Chamado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-contato" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="36">
        <input type="hidden" name="nav" value="chamados">
        <input type="hidden" name="ID_CHAMADO" value="<?=$dados['ID_CHAMADO']?>">

        <div class="modal-body">
          <div class="col">
            <label class="form-label">Solução aplicada:</label>

            <div class="list-group">

              <?php
              // 1. Caminho do arquivo JSON
              $arquivo = 'pages/chamados/solucao.json';

              if (file_exists($arquivo)) {
                // 2. Ler o conteúdo do arquivo
                $jsonString = file_get_contents($arquivo);
                // varDump2($jsonString);

                // 3. Decodificar a string JSON em array associativo
                $dadosJson = json_decode($jsonString, true);

                // 4. Verificar se a decodificação funcionou
                if ($dadosJson === null) {
                    echo "Erro ao decodificar JSON";
                } else {
                    // 5. Manipular os dados
                    // varDump2($dadosJson);
                    foreach ($dadosJson as $value) {
                      echo '<label class="list-group-item"><input class="form-check-input me-1" type="radio" name="ID_SOLUCAO" value="'.$value['id'].'">'.$value['solucao'].'</label>';
                    }
                }
              } else {
                echo "Arquivo não existe! ".$arquivo;
              }
                
              ?>

            </div>


          </div>
          <br>
          <div class="mb-3 float-md-end">
            <button type="submit" name="acao" value="chamado_finalizar" class="btn btn-primary">Finalizar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready ( function() {
    $('#modalFinalizar').modal('show');
  });
</script>