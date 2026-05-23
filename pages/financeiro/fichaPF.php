  <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
    <h1 class="display-4 fw-normal"><i class="fa-solid fa-user-edit"></i> Cadastro de Cliente VEMAP</h1>
  </div>

  <?php 
    require_once("pages/financeiro/controller.php"); 
  ?>

  <div class="card w-100">
    <div class="card-header bg-success">
      <h5 class="card-title">Cadastro Pessoal - Pessoa Física.</h5>
    </div>
    <div class="card-body">
      <form class="row" method="post" enctype="multipart/form-data" action="ficha.php">
        <input type="hidden" name="TIPOFJ" value="F">

        <h3 class="mt-3"><i class="fa-solid fa-circle-info"></i> Dados Pessoais</h3>          

        <div class="col-2 form-group">
          <label class="form-label">CPF<span class="text-danger"><b>*</b></span></label>
          <input onkeypress="mascara(this, cpf)" maxlength="14" name="CPF" class="form-control" placeholder="___.___.___-__" type="text" value="<?=$dados['CPF']?>" autocomplete="off" required>
          <small class="text-muted">formato: "999.999.999-99"</small>
        </div>   

        <div class="col-2 form-group">
          <label class="form-label">RG (Identidade)<span class="text-danger"><b>*</b></span></label>
          <input type="text" class="form-control" name="RG" value="<?=$dados['RG']?>" autocomplete="off" maxlength="20" placeholder="RG (Identidade)">
        </div> 
             
        <div class="col-4 form-group">
          <label class="form-label">Nome Completo <span class="text-danger"><b>*</b></span></label>
          <input type="text" class="form-control" name="NOME" value="<?=$dados['NOME']?>" autocomplete="off" placeholder="NOME COMPLETO">
          <small class="text-muted">Igual está no seu documento de identificação</small>
        </div>  

        <div class="col-4 form-group">
          <label class="form-label">Profissão<span class="text-danger"><b>*</b></span></label>
          <input type="text" class="form-control" name="PROFISSAO" value="<?=$dados['PROFISSAO']?>" autocomplete="off" maxlength="50" placeholder="PROFISSAO">
        </div>         

        <div class="col-6 form-group">
          <label class="form-label">Logradouro (Avenida / Rua / Beco) <span class="text-danger"><b>*</b></span></label>
          <input type="text" class="form-control" name="ENDERECO" value="<?=$dados['ENDERECO']?>" autocomplete="off" placeholder="LOGRADOURO">
        </div>

        <div class="col-2 form-group">
          <label class="form-label">Número (casa ou apto) <span class="text-danger"><b>*</b></span></label>
          <input onkeypress="mascara(this, numero)" maxlength="6" name="NUMERO" class="form-control" placeholder="999999" type="text" value="<?=$dados['NUMERO']?>" autocomplete="off">
          <small class="text-muted">formato: "99999"</small>
        </div> 
      
        <div class="col-4 form-group">
          <label class="form-label">Complemento</label>
          <input type="text" class="form-control" name="COMPLEMENTO" value="<?=$dados['COMPLEMENTO']?>" autocomplete="off" placeholder="COMPLEMENTO">
        </div>

        <div class="col-2 form-group">
          <label class="form-label">CEP <span class="text-danger"><b>*</b></span></label>
          <input onkeypress="mascara(this, cep)" maxlength="9" name="CEP" class="form-control" placeholder="_____-___" type="text" value="<?=$dados['CEP']?>" autocomplete="off">
          <small class="text-muted">formato: "99999-999"</small>
        </div>

        <div class="col-4 form-group">
          <label class="form-label mt-2 mb-0">Bairro <span class="text-danger"><b>*</b></span></label>
          <input type="text" class="form-control" name="BAIRRO" value="<?=$dados['BAIRRO']?>" autocomplete="off">
        </div>

        <div class="col-1 form-group">
          <label class="form-label px-0 mt-2 mb-0">Estado <span class="text-danger"><b>*</b></span></label>
          <select name="UF" id="UF_DADOSEMPRESARIAIS" class="form-select select2" aria-label="">
          </select>
        </div>

        <div class="col-5 form-group">
          <label class="form-label px-0 mt-2 mb-0">Município <span class="text-danger"><b>*</b></span></label>
          <select name="MUNICIPIO" id="MUNICIPIO_DADOSEMPRESARIAIS" class="form-select select2" aria-label="">
          </select>
        </div>


        <h3 class="mt-3"><i class="fa-solid fa-circle-info"></i> Contato</h3>

        <div class="col-6 form-group">
          <label class="form-label mt-2 mb-0">NOME <span class="text-danger"><b>*</b></span></label>
          <input type="text" class="form-control" name="CONTATONOME" value="<?=$dados['CONTATONOME']?>" autocomplete="off" placeholder="NOME COMPLETO">
        </div>

        <div class="col-6 form-group">
          <label class="form-label mt-2 mb-0">EMAIL <span class="text-danger"><b>*</b></span></label>
          <input type="mail" class="form-control" name="EMAIL" value="<?=$dados['EMAIL']?>" autocomplete="off" placeholder="exemplo@seudominio.com">
        </div>

        <div class="col-2 form-group">
          <label class="form-label mt-2 mb-0">TELCELULAR <span class="text-danger"><b>*</b></span></label>
          <input onkeypress="mascara(this, celular)" maxlength="15" name="TELCELULAR" class="form-control" placeholder="(__) _____-____" type="text" value="<?=$dados['TELCELULAR']?>" autocomplete="off">
          <small class="text-muted">formato: "(99) 99999-9999"</small>
        </div>          

        <div class="col-2 form-group">
          <label class="form-label mt-2 mb-0">TELFIXO</label>
          <input onkeypress="mascara(this, telefone)" maxlength="14" name="TELFIXO" class="form-control" placeholder="(__) ____-____" type="text" value="<?=$dados['TELFIXO']?>" autocomplete="off">
          <small class="text-muted">formato: "(99) 9999-9999"</small>
        </div>            

        <div class="col-2 form-group">
          <label class="form-label mt-2 mb-0">TELFINANCEIRO</label>
          <input onkeypress="mascara(this, telefone)" maxlength="14" name="TELFINANCEIRO" class="form-control" placeholder="(__) ____-____" type="text" value="<?=$dados['TELFINANCEIRO']?>" autocomplete="off">
          <small class="text-muted">formato: "(99) 9999-9999"</small>
        </div> 

        <div class="col-6 form-group">
          <label class="form-label mt-2 mb-0">OBSERVAÇÕES <span class="text-danger"><b>*</b></span></label>
          <input type="text" class="form-control px-1" name="OBSERVACOES" value="<?=$dados['TELFINANCEIRO']?>"  autocomplete="off" maxlength="50" placeholder="OBSERVACOES">
        </div>

        <h3 class="mt-3"><i class="fa-solid fa-circle-info"></i> Compradores</h3>

        <label class="form-label mt-2 mb-0"><b>COMPRADOR 1 <span class="text-danger"><b>*</b></span></b></label>
        <div class="col-8 form-group">
          <label class="form-label mt-2 mb-0">NOME <span class="text-danger"><b>*</b></span></label>
          <input type="text" class="form-control" name="COMPRADOR1NOME" value="<?=$dados['COMPRADOR1NOME']?>"  autocomplete="off">
        </div>   

        <div class="col-2 form-group">
          <label class="form-label mt-2 mb-0">CPF <span class="text-danger"><b>*</b></span></label>
          <input onkeypress="mascara(this, cpf)" maxlength="14" name="COMPRADOR1CPF" class="form-control" placeholder="___.___.___-__" type="text" value="<?=$dados['COMPRADOR1CPF']?>" autocomplete="off">
          <small class="text-muted">formato: "999.999.999-99"</small>
        </div>    

        <div class="col-2 form-group">
          <label class="form-label mt-2 mb-0">TEL CELULAR <span class="text-danger"><b>*</b></span></label>
          <input onkeypress="mascara(this, celular)" maxlength="15" name="COMPRADOR1CELULAR" class="form-control" placeholder="(__) _____-____" type="text" value="<?=$dados['COMPRADOR1CELULAR']?>" autocomplete="off">
          <small class="text-muted">formato: "(99) 99999-9999"</small>
        </div>                     

        <label class="form-label mt-2 mb-0"><b>COMPRADOR 2</b></label>
        <div class="col-8 form-group">
          <label class="form-label mt-2 mb-0">NOME</label>
          <input type="text" class="form-control" name="COMPRADOR2NOME" value="<?=$dados['COMPRADOR2NOME']?>"  autocomplete="off">
        </div>

        <div class="col-2 form-group">
          <label class="form-label mt-2 mb-0">CPF</label>
          <input onkeypress="mascara(this, cpf)" maxlength="14" name="COMPRADOR2CPF" class="form-control" placeholder="___.___.___-__" type="text" value="<?=$dados['COMPRADOR2CPF']?>" autocomplete="off">
          <small class="text-muted">formato: "999.999.999-99"</small>
        </div> 

        <div class="col-2 form-group">
          <label class="form-label mt-2 mb-0">TEL CELULAR</label>
          <input onkeypress="mascara(this, celular)" maxlength="15" name="COMPRADOR2CELULAR" class="form-control" placeholder="(__) _____-____" type="text" value="<?=$dados['COMPRADOR2CELULAR']?>" autocomplete="off">
          <small class="text-muted">formato: "(99) 99999-9999"</small>
        </div>                    

        <label class="form-label mt-2 mb-0"><b>COMPRADOR 3</b></label>
        <div class="col-8 form-group">
          <label class="form-label mt-2 mb-0">NOME</label>
          <input type="text" class="form-control" name="COMPRADOR3NOME" value="<?=$dados['COMPRADOR3NOME']?>"  autocomplete="off">
        </div>

        <div class="col-2 form-group">
          <label class="form-label mt-2 mb-0">CPF</label>
          <input onkeypress="mascara(this, cpf)" maxlength="14" name="COMPRADOR3CPF" class="form-control" placeholder="___.___.___-__" type="text" value="<?=$dados['COMPRADOR3CPF']?>" autocomplete="off">
          <small class="text-muted">formato: "999.999.999-99"</small>
        </div> 

        <div class="col-2 form-group">
          <label class="form-label mt-2 mb-0">TEL CELULAR</label>
          <input onkeypress="mascara(this, celular)" maxlength="15" name="COMPRADOR3CELULAR" class="form-control" placeholder="(__) _____-____" type="text" value="<?=$dados['COMPRADOR3CELULAR']?>" autocomplete="off">
          <small class="text-muted">formato: "(99) 99999-9999"</small>
        </div>    

        <h3 class="mt-3"><i class="fa-solid fa-circle-info"></i> Comprovantes</h3>
  
        <div class="col-12 form-group">
          <label class="form-label mt-2 mb-0">Comprovante de Documento RG ou CNH <span class="text-danger"><b>*</b></span></label>
          <input class="form-control" type="file" name="COMPDOCUMENTO1" value="<?=$dados['COMPDOCUMENTO1']?>" accept=".pdf">
          <small class="text-muted">Apenas RG ou Carteira de Habilitação - CNH</small>
        </div>    

        <div class="col-12 form-group">
          <label class="form-label mt-2 mb-0">Comprovante de Documento CPF <span class="text-danger"><b>*</b></span></label>
          <input class="form-control" type="file" name="COMPDOCUMENTO2" value="<?=$dados['COMPDOCUMENTO2']?>" accept=".pdf">
          <small class="text-muted">Apenas CPF</small>
        </div> 

        <div class="col-12 form-group">
          <label class="form-label mt-2 mb-0">Comprovante de Endereço <span class="text-danger"><b>*</b></span></label>
          <input class="form-control" type="file" name="COMPENDERECO1" value="<?=$dados['COMPENDERECO1']?>" accept=".pdf">
          <small class="text-muted">Apenas contas de Água, Energia elétrica ou de Linha telefônica</small>
        </div> 

        <div class="col-12 mt-3 border-top">
          <a href="ficha.php" class="btn btn-danger m-2 float-md-start">
            <i class="fa-solid fa-ban"></i> Cancelar
          </a>
          <button type="submit" name="acao" value="validarFichaPF" class="btn btn-success m-2 float-md-end">
            <i class="fa-solid fa-share-from-square"></i> Enviar
          </button>
        </div>

      </form>
    </div>
  </div>

<script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>    
<script type="text/javascript">
  $(document).ready(function () {

    $.ajax({ 
      type: 'GET', 
      data: { 
        acao: 'buscaTodosEstados'
      }, 
      url: 'pages/financeiro/ajax.php', 
      dataType: 'json', 
      success: function (response) { 
        console.log(response);
        if (response !== null) { 
          var selectboxMun = $('#MUNICIPIO_DADOSEMPRESARIAIS'); 
          $("#MUNICIPIO_DADOSEMPRESARIAIS").empty() 
          $('<option>').val("0").text("Selecione o Município").appendTo(selectboxMun); 

          var selectboxEst = $('#UF_DADOSEMPRESARIAIS'); 
          $("#UF_DADOSEMPRESARIAIS").empty() 
          $('<option>').val("0").text("Selecione o Estado").appendTo(selectboxEst); 
          $.each(response.data, function (i, d) { 
            $('<option>').val(d.UF).text(d.UF).appendTo(selectboxEst); 
          }) 
        }
      }
    });


    $('#UF_DADOSEMPRESARIAIS').change(function() {
      var UF = $('#UF_DADOSEMPRESARIAIS').val(); //Pegando o id do estado
      $.ajax({ 
        type: 'GET', 
        data: { 
          acao: 'buscarMunicipiosUF', 
          UF: UF 
        }, 
        url: 'pages/financeiro/ajax.php', 
        dataType: 'json', 
        success: function (response) { 
          console.log(response);
          if (response !== null) { 
            var selectbox = $('#MUNICIPIO_DADOSEMPRESARIAIS'); 
            $("#MUNICIPIO_DADOSEMPRESARIAIS").empty() 
            $('<option>').val("0").text("Selecione o Município").appendTo(selectbox); 
            $.each(response.data, function (i, d) { 
              $('<option>').val(d.MUNICIPIO).text(d.MUNICIPIO).appendTo(selectbox); 
            }) 
          }
        }
      });
    });  

    var dadosUF = "<?php echo $dados['UF']?>";
    var dadosMUNICIPIO = "<?php echo $dados['MUNICIPIO']?>";
    if (dadosUF !== "") {
      $.ajax({ 
        type: 'GET', 
        data: { 
          acao: 'buscarMunicipiosUF', 
          UF: dadosUF 
        }, 
        url: 'pages/financeiro/ajax.php', 
        dataType: 'json', 
        success: function (response) { 
          console.log(response);
          if (response !== null) { 
            var selectbox = $('#MUNICIPIO_DADOSEMPRESARIAIS'); 
            $("#MUNICIPIO_DADOSEMPRESARIAIS").empty() 
            $('<option>').val("0").text("Selecione o Município").appendTo(selectbox); 
            $.each(response.data, function (i, d) { 
              $('<option>').val(d.MUNICIPIO).text(d.MUNICIPIO).appendTo(selectbox); 
            })

            $("#UF_DADOSEMPRESARIAIS option").filter(function() {
              return this.text == dadosUF; 
            }).attr('selected', true);

            $("#MUNICIPIO_DADOSEMPRESARIAIS option").filter(function() {
              return this.text == dadosMUNICIPIO; 
            }).attr('selected', true);

          }
        }
      });
    }  

  });
</script>
    
