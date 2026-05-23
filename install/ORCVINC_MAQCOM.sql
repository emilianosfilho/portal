-- Create table
create table ORCVINC_MAQCOM
(
  idmaquina    NUMBER(11) not null,
  idcomponente NUMBER(11) not null
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
alter table ORCVINC_MAQCOM
  add constraint ORCVINC_MAQCOM_PK primary key (IDMAQUINA, IDCOMPONENTE)
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
