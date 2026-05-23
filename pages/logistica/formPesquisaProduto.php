
<main>
  <div class="container">
    <?php  
    require_once("pages/logistica/function.php");
    require_once("pages/logistica/controller.php");
    // varDump2($dados);
    ?>
    <h2>
      <i class="fa-solid fa-search"></i> Pesquisa Produto
    </h2>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          
          <form class="row g-3" action="index.php" method="POST">
            <input type="hidden" name="op" value="132">

            <div class="col-md-6">
              <label for="inputState" class="form-label">Campo pesquisa</label>
              <br>

              <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check" name="CAMPO" value="CODPROD" id="btnradio1" autocomplete="off" <?=((!isset($dados['CAMPO']))?'checked':(($dados['CAMPO']=='CODPROD')?'checked':'')) ?>>
                <label class="btn btn-sm btn-outline-primary" for="btnradio1">CODPROD</label>

                <input type="radio" class="btn-check" name="CAMPO" value="NUMORIGINAL" id="btnradio2" autocomplete="off" <?=(($dados['CAMPO']=='NUMORIGINAL')?'checked':'')?>>
                <label class="btn btn-sm btn-outline-primary" for="btnradio2">NUMORIGINAL</label>

                <input type="radio" class="btn-check" name="CAMPO" value="LOCACAO" id="btnradio3" autocomplete="off" <?=(($dados['CAMPO']=='LOCACAO')?'checked':'')?>>
                <label class="btn btn-sm btn-outline-primary" for="btnradio3">LOCACAO</label>

                <input type="radio" class="btn-check" name="CAMPO" value="DESCRICAO" id="btnradio4" autocomplete="off" <?=(($dados['CAMPO']=='DESCRICAO')?'checked':'')?>>
                <label class="btn btn-sm btn-outline-primary" for="btnradio4">DESCRICAO</label>

                <input type="radio" class="btn-check" name="CAMPO" value="NUMNOTA" id="btnradio5" autocomplete="off" <?=(($dados['CAMPO']=='NUMNOTA')?'checked':'')?>>
                <label class="btn btn-sm btn-outline-primary" for="btnradio5">NUMNOTA</label>

                <input type="radio" class="btn-check" name="CAMPO" value="VIDE" id="btnradio6" autocomplete="off" <?=(($dados['CAMPO']=='VIDE')?'checked':'')?>>
                <label class="btn btn-sm btn-outline-primary" for="btnradio6">VIDE</label>
              </div>

            </div>

            <div class="col-md-6">
              <label for="inputState" class="form-label">Pesquisa</label>
              <br>

              <div class="input-group mb-3">
                <input type="text" name="VALOR" class="form-control" required autocomplete="off" value="<?=($_POST['VALOR']<>"")?$_POST['VALOR']:""?>">
                <button type="submit" name="acao" value="pesquisaProduto" class="btn btn-sm btn-outline-primary">Pesquisar</button>
                <?php if ($dados['acao']=="pesquisaProduto"): ?>
                  <?php if ($_SESSION['produtos'] = buscaProdutosLogistica($dados)): ?>
                    <button type="submit" name="acao" value="gerarEtiquetasProduto" class="btn btn-sm btn-outline-secondary"><i class="fa fa-solid fa-print"></i></button>
                  <?php endif ?>
                <?php endif ?>
              </div>
            </div>

          </form>
        </div>
        <div class="card-body">
          <?php 
          if (isset($dados['acao'])) {
            if ($dados['acao'] == "pesquisaProduto") {
              require_once('pages/logistica/listaProdutos.php');
            }
          }
          ?>
        </div>
      </div>
    </div>
    


  </div>
</main>


