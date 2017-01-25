
-- tag Primary Key Violation

insert into Movie values (20, 'a', 2000, 0, 'a');
-- tag dupicate entry for primary key is not allowed
-- tag ERROR 1062 (23000): Duplicate entry '20' for key 'PRIMARY'

insert into Actor values(10, 'a', 'a', 'Male', 02-07-1949, null);
-- tag dupicate entry for primary key is not allowed
-- tag ERROR 1062 (23000): Duplicate entry '10' for key 'PRIMARY'

insert into Director values(104, 'a', 'a', 02-07-1949, null);
-- tag dupicate entry for primary key is not allowed
-- tag ERROR 1062 (23000): Duplicate entry '104' for key 'PRIMARY'



-- tag Check Violation

-- tag insert into Movie values (10,'', 2000, 0, 'a');
-- tag length of the movie title should be  larger than 0

-- tag insert into Actor values (0, '', '', 'Male', 02-07-1949, null);
-- tag length of last and first name should be  larger than 0


-- tag insert into Review values ('a', 12345678, 20, 15, 'a');
-- tag rating should between 0 and 5




-- tag Foreign Key Violation 

insert into MovieGenre values (0, 'a');
-- tag Error : foriegn key fails , there is no id = 0 in Movie table
-- tag ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`TEST`.`MovieGenre`, CONSTRAINT 
-- tag `MovieGenre_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

insert into MovieDirector values (0, 10);
-- tag Error : foriegn key fails , there is no id = 0 in Movie table 
-- tag ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails  (`TEST`.`MovieDirector`, CONSTRAINT 
-- tag `MovieDirector_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

insert into MovieDirector values (4500, 0);
-- tag Error : foriegn key fails , there is no id = 0 in Director table 
-- tag ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails 
-- tag (`TEST`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_2` FOREIGN KEY (`did`) REFERENCES `Director` (`id`))

insert into MovieActor values (0, 10, 'a');
-- tag Error : foriegn key fails , there is no id = 0 in Movie table
-- tag ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails
-- tag (`TEST`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

insert into MovieActor values (4500, 0, 'a');
-- tag Error : foriegn key fails , there is no id = 0 in Actor table
-- tag ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails 
-- tag (`TEST`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Actor` (`id`))


insert into Review values('a', 123454656, 0, 3, 'a');
-- tag Error : foriegn key fails , there is no id = 0 in Movie table
-- tag ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails 
-- tag (`TEST`.`Review`, CONSTRAINT `Review_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))