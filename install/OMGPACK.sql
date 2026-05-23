-- Create table
create table OMGPACK
(
  idpack              NUMBER,
  dtcadastro          DATE default SYSDATE,
  pack                VARCHAR2(50),
  codprod             NUMBER,
  qtd                 NUMBER,
  dtfim               DATE,
  qtconferida         NUMBER,
  dtultalteracao      DATE,
  idusuarioconferente NUMBER
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
