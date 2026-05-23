-- Create table
create table ORCMAQUINA
(
  idmaquina  NUMBER(11) not null,
  modelo     VARCHAR2(100) not null,
  fabricante VARCHAR2(100)
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
alter table ORCMAQUINA
  add constraint ORCMAQUINA_PK primary key (IDMAQUINA)
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
