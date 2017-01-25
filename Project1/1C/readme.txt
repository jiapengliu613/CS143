##UID 704590086 (Use one grace day for this part of project)

###homepage.html can guide the visitors to scan this website

### AddPeople.php is used for adding actor and director to the database.
- for the identity check, if there exists one in the Actor database having the same first name, last name ,gender and dob as the person you want to add, then it's considered as that person is already in the database so that you cannot add him/her again. If there exists one in the Director database having the same first name, last name  and dob as the person you want to add, then it's considered as that person is already in the database so that you cannot add him/her again.

### AddMovie.php is used for adding movie into the database.
- for the identity check, if there exists one in the Movie database having the same title, year and company as the movie you want to add, then it's considered as that movie is already in the database so that you cannot add it again.

### AddMovieActor.php for adding actor into a movie
- Use the actor id and movie id as the identity check

### AddMovieDirector.php for adding director into a movie
- Use the director id and movie id as the identity check

