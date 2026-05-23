-- Create table
create table OMGLOGALTERALOCACAO
(
  dtalteracao DATE not null,
  idusuario   NUMBER not null,
  codprod     NUMBER not null,
  locacao_old VARCHAR2(500),
  locacao_new VARCHAR2(500) not null
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
