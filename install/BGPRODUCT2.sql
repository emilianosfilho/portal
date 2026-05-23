DROP TABLE BGPRODUCT2;

-- Create table
create table BGPRODUCT2
(
  numoriginal   	VARCHAR2(50) not null PRIMARY KEY,
  productID         NUMBER(11) not null,
  name          	VARCHAR2(255) not null,
  dtcadastro    	DATE default SYSDATE,
  dtatualizacao 	DATE default SYSDATE
)