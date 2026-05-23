-- Create table
create table ORCORCAMENTOI
(
  idorcamentoi    NUMBER(11) not null,
  idorcamento     NUMBER(11) not null,
  validacao       VARCHAR2(100) not null,
  codpeca         VARCHAR2(30) not null,
  codvide         VARCHAR2(30),
  codprod         NUMBER(11),
  descricao       VARCHAR2(100),
  marca           VARCHAR2(100),
  qtpedida        NUMBER(11),
  qtdisponivel    NUMBER(11),
  disponibilidade VARCHAR2(50) not null,
  icms            NUMBER(11,4) not null,
  ptabela         NUMBER(11,4),
  pvenda          NUMBER(11,4),
  procedencia     VARCHAR2(4) default 'N',
  observacao      VARCHAR2(150),
  status          VARCHAR2(1),
  locacao         VARCHAR2(50),
  dv              NUMBER(11),
  cst             VARCHAR2(5),
  ncm             VARCHAR2(10),
  data            DATE default SYSDATE,
  qtdisppeca      NUMBER(11) default 0,
  numoriginal     VARCHAR2(30),
  tribut          VARCHAR2(1) default 'S',
  pvendamin       NUMBER(11,4)
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
-- Create/Recreate primary, unique and foreign key constraints 
alter table ORCORCAMENTOI
  add constraint ORCORCAMENTOI_PK primary key (IDORCAMENTOI)
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
alter table ORCORCAMENTOI
  add constraint ORCORCAMENTOI_FK1 foreign key (IDORCAMENTO)
  references ORCORCAMENTOC (IDORCAMENTO);
