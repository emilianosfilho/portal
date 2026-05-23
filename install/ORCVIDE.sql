-- Create table
create table ORCVIDE
(
  idvide              NUMBER(11) not null,
  codpeca             VARCHAR2(50) not null,
  aplicmarca          VARCHAR2(50),
  vide                VARCHAR2(50),
  descricao           VARCHAR2(100),
  marca               VARCHAR2(50),
  importado           VARCHAR2(4),
  preco               VARCHAR2(10),
  data                VARCHAR2(10),
  nomeopcao           VARCHAR2(50),
  dtcadastro          DATE default SYSDATE,
  dtultalteracao      DATE,
  usuarioultalteracao VARCHAR2(100),
  usuariocadastro     VARCHAR2(100),
  dtexclusao          DATE,
  usuarioexclusao     VARCHAR2(100)
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
alter table ORCVIDE
  add constraint ORCVIDE_PK primary key (IDVIDE)
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
