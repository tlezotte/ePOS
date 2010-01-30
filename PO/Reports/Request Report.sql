select p.po, concat(e.lst, ', ', e.fst) as requester, p.reqDate, l.name as Plant, v.BTNAME as vendor, p.department, p.purpose, p.total, p.cer 
from PO p
  inner join Standards.Employees e on e.eid=p.req
  inner join Standards.Vendor v on v.BTVEND=p.sup
  inner join Standards.Plants l on l.id=p.ship
where p.reqDate >= '2006-11-01'
  and p.status IN ('A', 'O', 'R')
order by p.reqDate