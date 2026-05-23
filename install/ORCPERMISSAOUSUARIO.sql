-- Create table
create table ORCPERMISSAOUSUARIO
(
  idpermissao NUMBER(11) not null,
  idusuario   NUMBER(11) not null,
  dtcadastro  DATE default SYSDATE
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
