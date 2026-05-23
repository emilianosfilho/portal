-- Create table
create table ORCDESCRICAO
(
  iddescricao NUMBER(11) not null,
  codpeca     VARCHAR2(50) not null,
  descricao   VARCHAR2(500) not null
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
alter table ORCDESCRICAO
  add primary key (IDDESCRICAO)
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
