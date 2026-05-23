-- Create table
create table ORCPARAMETROS
(
  idparametro NUMBER not null,
  parametro   VARCHAR2(100) not null,
  valor       VARCHAR2(100),
  valorpadrao VARCHAR2(100),
  tipo        VARCHAR2(100) default 'CAD_PRODUTOS',
  comentario  VARCHAR2(100)
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
alter table ORCPARAMETROS
  add primary key (IDPARAMETRO)
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
