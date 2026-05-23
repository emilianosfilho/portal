-- Create table
create table ORCVINC_COMSUB
(
  idcomponente    NUMBER(11) not null,
  idsubcomponente NUMBER(11) not null
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
alter table ORCVINC_COMSUB
  add constraint ORCVINC_COMSUB_PK primary key (IDCOMPONENTE, IDSUBCOMPONENTE)
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
