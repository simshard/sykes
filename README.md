# sykes
Sykes form demo

I got bogged down in trying to dynamically bind variables to the query  now I know I have to dynamically maintain an array of variables and types and pass the arrays by reference using call_user_func_array.
Stackoverflow   told me I should have used PDO not mysqli but I was out of time. Hence the terrible  use of non prepared sql statement and mysqli_real_escape_string. 

Pagination not finished  
code is very procedural but I would have extracted pagination code to a class if i had  finished

bookings/ date  NOT BETWEEN solution is not working and other minor bugs

UPDATE sunday added pdosearch.php - a version  using PDO to create a prepared statement - sorry no pagination yet
I know this took me more than 1 hour but now i have learned much about mysqli and  PDO  
