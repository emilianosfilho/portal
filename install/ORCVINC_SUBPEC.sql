-- Create table
create table ORCVINC_SUBPEC
(
  idsubcomponente NUMBER(11) not null,
  idpeca          NUMBER(11) not null
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
alter table ORCVINC_SUBPEC
  add constraint ORCVINC_SUBPEC_PK primary key (IDSUBCOMPONENTE, IDPECA)
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
