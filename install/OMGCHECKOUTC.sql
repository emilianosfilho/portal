-- Create table
create table OMGCHECKOUTC
(
  idcheckout       NUMBER(10) not null,
  numped           NUMBER(10) not null,
  numpedcli        NUMBER(10),
  dtinicio         DATE default SYSDATE,
  dtfim            DATE,
  dtpedido         DATE,
  codusur          NUMBER(18),
  codcli           NUMBER(18),
  idusurconferente NUMBER(18),
  dtconferencia    DATE,
  numnota          NUMBER(10),
  emtransito       VARCHAR2(1) default 'N',
  dtemtransito     DATE,
  dtprevisao       DATE,
  previsao         NUMBER(10)
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
