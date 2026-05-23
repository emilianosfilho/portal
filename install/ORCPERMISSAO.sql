-- Create table
create table ORCPERMISSAO
(
  idpermissao NUMBER(11) not null,
  permissao   VARCHAR2(30) not null
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
