select p.*, c.name as categoria_nome
  from bgproduct2 p, bgcategoria c
 where p.categoria_id = c.id(+)
   and p.dtatualizacao like '2024-08-06%'
