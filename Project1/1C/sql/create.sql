create table IF NOT EXISTS Movie (
	id int not null, 
	title varchar(100), 
	year int, 
	rating varchar(10), 
	company varchar(50),
	primary key(id),
	check (title is not null and LENGTH(title) > 0)
	/*the length of the movie title should be larger than 0*/
)ENGINE = INNODB;

create table IF NOT EXISTS Actor (
	id int not null, 
	last varchar(20),
	first varchar(20),
	sex varchar(6) not null,
	dob date not null,
	dod date,
	primary key(id)
)ENGINE = INNODB;

create table IF NOT EXISTS Director (
	id int not null,
	last varchar(20),
	first varchar(20), 
	dob date not null, 
	dod date,
	primary key(id),
	check (dob is not null)
	/* dob cannot be null */
)ENGINE = INNODB;

create table IF NOT EXISTS MovieGenre (
	mid int not null , 
	genre varchar(20),
	
	foreign key(mid) references Movie(id)
)ENGINE = INNODB;

create table IF NOT EXISTS MovieDirector (
	mid int not null, 
	did int not null,
	primary key(mid, did),
	foreign key(mid) references Movie(id),
	foreign key(did) references Director(id)
)ENGINE = INNODB;

create table IF NOT EXISTS MovieActor (
	mid int not null,
	aid int not null,
	role varchar(50) not null,
	primary key(mid, aid),
	foreign key(mid) references Movie(id),
	foreign key(aid) references Actor(id)
)ENGINE = INNODB; 

create table IF NOT EXISTS Review (
	name varchar(20),
	time timestamp,
	mid int not null,
	rating int,
	comment varchar(500),
	
	foreign key(mid) references Movie(id),
	check(rating >= 0 and rating <= 5)
	/*rating is between 0 and 5*/
)ENGINE = INNODB;

create table IF NOT EXISTS MaxPersonID (
	id int not null
)ENGINE = INNODB;

create table IF NOT EXISTS MaxMovieID (
	id int not null
)ENGINE = INNODB;





