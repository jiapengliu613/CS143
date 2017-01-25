-- tag all the actors in the movie 'Die Another Day'
select concat(first, ' ', last) as Names
from Movie M, Actor A, MovieActor MA
where M.title = 'Die Another Day' and M.id = MA.mid and A.id = MA.aid;

-- tag the count of all the actors who acted in multiple movies
select count(*) as TotalNumber
from (select aid
	 from MovieActor 
	 group by aid having count(*) > 1) ActorList;

-- tag all the females who are both director and actor
select concat(A.first, ' ', A.last) as Name
from Actor A, Director D
where A.sex = 'Female' and A.id = D.id