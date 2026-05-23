<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-left-to-bracket"></i> Consulta Entradas
          </h1>
          <div class="btn-group float-end">
            <a class="btn btn-outline-secondary" href="index.php?op=<?=$dados['op']?>&acao=limparLista&aba=<?=$dados['aba']?>" title="Limpar lista"><i class="fa-solid fa-broom"></i></a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <form action="index.php" method="POST">
            <input type="hidden" name="op" value="133">
            <input type="hidden" name="aba" value="entrada">

            <div class="row g-3">
              <div class="col-2">
                <label class="form-label">DATA INÍCIO</label>
                <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAINI" type="text" class="form-control" value="<?=(isset($dados['DATAINI']))?$dados['DATAINI']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
                </div> 
              </div>

              <div class="col-2">
                <label class="form-label">DATA FIM</label>
                <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAFIM" type="text" class="form-control" value="<?=(isset($dados['DATAFIM']))?$dados['DATAFIM']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
                </div> 
              </div>

              <div class="col">
                <label class="form-label">CHECK-IN</label>
                <select name="STATUSCHECKIN" class="form-select select2">
                  <option value="ALL" <?=(($dados['STATUSCHECKIN']=='ALL')?'selected':'')?> >Todos</option>
                  <option value="NAOFINALIZADO" <?=(($dados['STATUSCHECKIN']=='NAOFINALIZADO')?'selected':((!isset($dados['STATUSCHECKIN']))?'selected':''))?> >Não Finalizado</option>
                  <option value="FINALIZADO" <?=(($dados['STATUSCHECKIN']=='FINALIZADO')?'selected':'')?> >Finalizado</option>
                  <option value="EM TRANSITO" <?=(($dados['STATUSCHECKIN']=='EM TRANSITO')?'selected':'')?> >Em Trânsito</option>
                  <option value="DEVOL" <?=(($dados['STATUSCHECKIN']=='DEVOL')?'selected':'')?> >Devol. Cliente</option>
                </select>
              </div>

              <div class="col">
                <label class="form-label">Cód. Fornecedor</label>
                <input type="text" name="CODFORNEC" class="form-control" autocomplete="off" placeholder="CODFORNEC">
              </div>

              <div class="col">
                <label class="form-label">Núm. Nota</label>
                <input type="text" name="NUMNOTA" class="form-control" autofocus autocomplete="off" placeholder="NUMNOTA">
              </div>

              <div class="col-1">
                <div class="d-grid gap-2">
                <button class="btn btn-secondary mt-4" type="submit" name="acao" value="checkin_pesquisar"><i class="fa-solid fa-search"></i></button>
                </div>
              </div>

            </div>
          </form>
        </div>

        <?php if (isset($checkin_lista) && !empty($checkin_lista)): ?>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover" id="tb_default">

				<thead>
					<tr>
						<th>#</th>
						<th>Núm Nota</th>
						<th>Núm Trans.</th>
						<th>Emissão</th>
            <?php if ($dados['STATUSCHECKIN']=='DEVOL'): ?>
              <th>Cliente</th>
            <?php else: ?>
						  <th>Fornecedor</th>
            <?php endif ?>
						<th>Operação</th>
						<th>Comprador</th>
						<th>Check-in</th>
						<th>Conferente</th>
						<th width="10%">Ações</th>
					</tr>
				</thead>

                <tbody>
				<?php foreach ($checkin_lista as $key => $value) : ?>
					<tr>
					<td><?= ($key+1) ?></td>
					<td><?= $value['NUMNOTA'] ?></td>
					<td><?= $value['NUMTRANSENT'] ?></td>
					<td><?= formataDataOracletoBr($value['DTEMISSAO']) ?></td>
          <?php if (isset($value['CLIENTE'])): ?>
             <td><?= $value['CODCLI'].'- '.$value['CLIENTE'] ?></td>
          <?php else: ?>
					   <td><?= $value['CODFORNEC'].'- '.$value['FORNECEDOR'] ?></td>
          <?php endif ?>
					<td><?= $value['OPERACAO'] ?></td>
					<td><?= substr($value['CODFUNCLANC'].'- '.$value['NOMEFUNCIONARIO'],0, 18) ?></td>
					<td>
						<?php 
							if ($value['STATUS_CHECKIN'] == 'PENDENTE') {
								echo '<span class="badge bg-secondary">PENDENTE</span>';
							} else {
								if ($value['STATUS_CHECKIN'] == 'SEPARANDO') {
									echo '<span class="badge bg-primary">SEPARANDO</span>';
								} else {
									if ($value['STATUS_CHECKIN'] == 'FINALIZADO') {
										echo '<span class="badge bg-success">FINALIZADO</span>';
									} else {
										echo '<span class="badge bg-warning text-black">EM TRANSITO</span>';
									}
								}
							}
						?>
					</td>
					<td><?= (($value['IDCHECKIN']<>"")?reset(explode(' ', $value['CONFERENTE'])):'') ?></td>
					<td>
						<form action="index.php" method="POST">
              <input type="hidden" name="op" value="133">
              <input type="hidden" name="nav" value="logistica">
							<input type="hidden" name="aba" value="entrada">
              <input type="hidden" name="IDCHECKIN" value="<?=$value['IDCHECKIN']?>">
							<input type="hidden" name="NUMNOTA" value="<?=$value['NUMNOTA']?>">
							<input type="hidden" name="NUMTRANSENT" value="<?=$value['NUMTRANSENT']?>">
							<div class="btn-group">
                <?php if (isset($value['IDCHECKIN']) && !empty($value['IDCHECKIN'])): ?>
                  <button type="submit" name="acao" value="checkin_abrir" class="btn btn-sm btn-outline-primary" title="Abrir Check-in">
                    <i class="fa-solid fa-list-check"></i>
                  </button>
                  <?php if ($_SESSION['login']['PERFIL'] == "ADMINISTRADOR"): ?>
                    <button type="submit" name="acao" value="checkin_modalConfirmaExcluir" class="btn btn-sm btn-outline-danger" title="Excluir Check-in">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  <?php endif ?>
                <?php else: ?>
                  <button type="submit" name="acao" value="checkin_modalConfirmaAbrir" class="btn btn-sm btn-outline-secondary" title="Iniciar Check-in">
                    <i class="fa-solid fa-list-check"></i>
                  </button>
                <?php endif ?>
							</div>
						</form>
					</td>
					</tr>
				<?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php endif ?>
      </div>
    </main>
  </div><!-- row -->
</div><!-- container-fluid -->