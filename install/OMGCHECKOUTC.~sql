-- Create table
create table OMGINVENTARIOC
(
  idinventario        NUMBER(8) not null,
  dtinicio            DATE default SYSDATE,
  dtfim               DATE,
  idusuarioconferente VARCHAR2(40),
  locacao             VARCHAR2(40),
  dtexclusao          DATE,
  usuarioexclusao     NUMBER,
  horainicio          VARCHAR2(10) default '00:00:00'
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
alter table OMGINVENTARIOC
  add primary key (IDINVENTARIO)
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
