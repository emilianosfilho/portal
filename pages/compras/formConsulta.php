<main>
  <div class="container">
    <?php  
    require_once("pages/compras/function.php");
    require_once("pages/compras/controller.php");
    
    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    
    ?>
    <div class="card">
      <div class="card-header">
        <h2><i class="fa-solid fa-magnifying-glass"></i> Consulta de Produtos</h2>
      </div>
      <div class="card-body">
        <form class="row g-3" action="index.php" method="POST">
          <input type="hidden" name="op" value="114">
          <div class="col-md-3">
            <label for="inputPassword4" class="form-label">Data Início</label>
            <input type="text" name="dataini" class="form-control" autocomplete="off" value="<?=(isset($dados['dataini'])) ? $dados['dataini'] : @date('d/m/Y');  ?>">
          </div>
          <div class="col-md-3">
            <label for="inputPassword4" class="form-label">Data Fim</label>
            <input type="text" name="datafim" class="form-control" autocomplete="off" value="<?=(isset($dados['datafim'])) ? $dados['datafim'] : @date('d/m/Y');  ?>">
          </div>
          <div class="col-2">
            <button type="submit" name="acao" value="consultarProdutos" class="mt-3 btn btn-primary">Consultar</button>
          </div>
          <div class="col-4">
            <?php if ($produtos): ?>
              <button type="submit" name="acao" value="exportarExcel" class="mt-3 btn btn-success">Exportar Excel</button>
            <?php endif ?>
          </div>
        </form>

      </div>

      <div class="card-footer">
      <?php if ($produtos): ?>

          <table id="tb_full" class="table table-bordered table-striped table-hover" style="width: 100%">

            <thead>
              <tr>
                <th>CODPROD</th>
                <th>NUMORIGINAL</th>
                <th>MARCA</th>
                <th>DESCRICAO</th>
                <th>CODFAB</th>
                <th>CODFORNEC</th>
                <th>DTCADASTRO</th>
              </tr>
            </thead>

            <tbody>
              <?php
              // varDump2($_SESSION['ARQUIVO']);
              foreach ($produtos as $key => $value) {
                // varDump2($value);  die();

                echo PHP_EOL;
                echo '<tr>';
                echo '<td>'.$value['CODPROD'].'-'.$value['DV'].'</td>';
                echo '<td>'.$value['NUMORIGINAL'].'</td>';
                echo '<td>'.$value['MARCA'].'</td>';
                echo '<td>'.$value['DESCRICAO'].'</td>';
                echo '<td>'.$value['CODFAB'].'</td>';
                echo '<td>'.$value['CODFORNEC'].'</td>';
                echo '<td>'.$value['DTCADASTRO'].'</td>';
                echo '</tr>';
              }
              ?>
            </tbody>

          </table>

      <?php endif ?>
      </div>
    </div>
  </div>
</main>