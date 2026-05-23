-- Create table
create table ORCCOMPRAS
(
  idcompras  NUMBER(10) not null,
  data       DATE,
  codcli     VARCHAR2(20),
  produto    VARCHAR2(20),
  cliente    VARCHAR2(50),
  preco      VARCHAR2(20),
  quantidade VARCHAR2(20),
  marcaalfa  VARCHAR2(20)
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
alter table ORCCOMPRAS
  add constraint ORCCOMPRAS_PK primary key (IDCOMPRAS)
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
