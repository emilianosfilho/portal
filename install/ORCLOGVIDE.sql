-- Create table
create table ORCLOGVIDE
(
  idlogvide      NUMBER(11) not null,
  data           DATE default SYSDATE,
  tipo           VARCHAR2(20),
  idusuario      NUMBER(11),
  codpeca_old    VARCHAR2(50),
  codpeca_new    VARCHAR2(50),
  aplicmarca_old VARCHAR2(50),
  aplicmarca_new VARCHAR2(50),
  vide_old       VARCHAR2(50),
  vide_new       VARCHAR2(50),
  descricao_old  VARCHAR2(100),
  descricao_new  VARCHAR2(100),
  usuario        VARCHAR2(100),
  idvide         NUMBER
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
alter table ORCLOGVIDE
  add constraint ORCLOGVIDE_PK primary key (IDLOGVIDE)
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
