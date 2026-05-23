-- Create table
create table ORCLOGALTPRECO
(
  idlog         NUMBER(11) not null,
  data          DATE default SYSDATE,
  idusuario     NUMBER(11) not null,
  codcli        NUMBER(11) not null,
  idorcamento   NUMBER(11) not null,
  codpeca_old   VARCHAR2(30),
  codprod       NUMBER(11),
  descricao_old VARCHAR2(100),
  qtpedida_new  NUMBER(11) not null,
  pvenda_old    NUMBER(11,4) not null,
  pvenda_new    NUMBER(11,4) not null,
  qtpedida_old  NUMBER,
  marca_old     VARCHAR2(30),
  marca_new     VARCHAR2(30),
  descricao_new VARCHAR2(100),
  codpeca_new   VARCHAR2(30)
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
alter table ORCLOGALTPRECO
  add primary key (IDLOG)
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
