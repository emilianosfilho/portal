-- Create table
create table ORCCOMPONENTE
(
  idcomponente NUMBER(11) not null,
  componente   VARCHAR2(100) not null
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
alter table ORCCOMPONENTE
  add constraint ORCCOMPONENTE_PK primary key (IDCOMPONENTE)
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
