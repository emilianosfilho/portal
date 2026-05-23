-- Create table
create table OMGINVENTARIOI
(
  idinventario     NUMBER(8) not null,
  codprod          NUMBER(8) not null,
  numoriginal      VARCHAR2(40),
  descricao        VARCHAR2(40),
  marca            VARCHAR2(40),
  qtestoque        NUMBER(8) default 0,
  qtpedido         NUMBER(8) default 0,
  qtavaria         NUMBER(8) default 0,
  qtconferida      NUMBER(8),
  dtultconferencia DATE,
  idconferente     NUMBER(8)
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
