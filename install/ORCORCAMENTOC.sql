-- Create table
create table ORCORCAMENTOC
(
  idorcamento         NUMBER(11) not null,
  idusuario           NUMBER(11) not null,
  codcli              NUMBER(11) not null,
  data                DATE default SYSDATE,
  status              VARCHAR2(30) default 'ORCAMENTO',
  origem              VARCHAR2(100) default 'DIGITACAO COLABORADOR',
  numpedrca           NUMBER(11),
  numped              NUMBER(11),
  codcob              VARCHAR2(5) default 'DH',
  codplpag            VARCHAR2(5) default 1,
  datafaturamento     DATE,
  atendimento         VARCHAR2(20),
  observacao          VARCHAR2(100),
  contato             VARCHAR2(100),
  telcelular          VARCHAR2(20),
  telfixo             VARCHAR2(20),
  fretedespacho       VARCHAR2(1) default 'G',
  valorfrete          NUMBER(11,4) default 0.0000,
  maquina             VARCHAR2(50),
  codusur             VARCHAR2(10),
  observacao2         VARCHAR2(50),
  clientebalcao       VARCHAR2(1) default 'N',
  idorcamentooriginal NUMBER(11),
  percdesc            NUMBER(10,4),
  numregiao           NUMBER(4),
  codfilialnf         NUMBER(4) default 1,
  logidseparador      NUMBER,
  logdatainiseparacao DATE,
  logdatafimseparacao DATE,
  logidconferente     NUMBER,
  logdataconferencia  DATE,
  mesavendedor        VARCHAR2(20),
  numpedcomp          NUMBER(10),
  obsentrega1         VARCHAR2(75) default 'NAO',
  obsentrega2         VARCHAR2(75) default 'NAO',
  obsentrega3         VARCHAR2(75),
  separacao           VARCHAR2(1) default 'S',
  codcontato          NUMBER(10),
  observacao3         VARCHAR2(50),
  obsentrega4         VARCHAR2(75)
)
tablespace TS_DADOS
  pctfree 10
  initrans 1
  maxtrans 255
  storage
  (
    initial 64K
    next 1M
    minextents 1
    maxextents unlimited
  );
-- Add comments to the columns 
comment on column ORCORCAMENTOC.observacao
  is 'GUARDA INFO DE ORDEM DE COMPRA';
comment on column ORCORCAMENTOC.fretedespacho
  is 'C- Contratac?o do Frete por conta do Remetente (CIF);
F- Contratac?o do Frete por conta do Destinatario (FOB);
T- Contratac?o do Frete por conta de Terceiros;
R- Transporte Proprio por conta do Remetente;
D- Transporte Proprio por conta do Destinatario;
G- Sem Ocorrencia de Transporte';
comment on column ORCORCAMENTOC.percdesc
  is 'FAST DO CLIENTE TABELA PCDESCONTO';
comment on column ORCORCAMENTOC.numregiao
  is 'REGIAO DE PRE?O DO CLIENTE';
comment on column ORCORCAMENTOC.numpedcomp
  is 'NUMERO DE PEDIDO DO COMPRADOR';
comment on column ORCORCAMENTOC.obsentrega1
  is 'CLIENTE BALCAO';
comment on column ORCORCAMENTOC.obsentrega2
  is 'SEPARAR PEDIDO';
comment on column ORCORCAMENTOC.separacao
  is 'INFORMAR SE O PEDIDO DEVE SER SEPARADO PELA LOGISTICA';
comment on column ORCORCAMENTOC.observacao3
  is 'GUARDA INFO SOBRE INFORMACOES GERAIS';
-- Create/Recreate primary, unique and foreign key constraints 
alter table ORCORCAMENTOC
  add constraint ORCORCAMENTOC_PK primary key (IDORCAMENTO)
  using index 
  tablespace TS_DADOS
  pctfree 10
  initrans 2
  maxtrans 255
  storage
  (
    initial 64K
    next 1M
    minextents 1
    maxextents unlimited
  );
alter table ORCORCAMENTOC
  add constraint ORCORCAMENTOC_FK1 foreign key (IDUSUARIO)
  references ORCUSUARIO (IDUSUARIO);
alter table ORCORCAMENTOC
  add constraint ORCORCAMENTOC_FK2 foreign key (CODCLI)
  references PCCLIENT (CODCLI);
