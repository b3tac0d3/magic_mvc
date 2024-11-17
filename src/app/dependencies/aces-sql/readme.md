
## Before you begin
Make a reference to the main query class. The other classes are loaded automatically when this class is called. All functionality for basic usage will be through the 'query' class.

#### Special Notes:
- All functions can be chained or used separately. 
- Each new query requires a new object to be created.
- Most single input functions have an alternate mult-input counterpart.
- Functions can be called in any order EXCEPT for the final query function which must be called last.

## Config File
- Config file automatically loaded from ~/src/app/plugins/aces/config.php
- Uses constants to define database structure and log files
    - Default log files are located under aces/logs/
    - Custom log files can be used by change log file path constants
    - If log file doesn't exist but is called, it will be created

- Default select is all columns (*)
- Default return is all rows fetchAll(PDO::FETCH_ASSOC)

## Include class
```php
$query = new aces\query();
```

# Query Types
---

## Select
Select has two arguments. With no select_column entered, default value is to select all.
- $table_name [required]
- $alias [optional] **Recommended when using joins**
Here's a basic select query that would get all users from the users table.
```php
// Select all records from users table
$query -> select("users")
```
The query return is an array containing results. Depending on the fetch method used, it could be one result or multiple. Either way, the returned result is always an array.

## Update
Only one argument is set in the update function. Update values and where values are set in their own functions which will be discussed later.
- $table_name
```php
// Update column with id = 1 to value jimmy
$query 
    -> set_update_column("username", "jimmy") 
    -> set_where("id", 1) 
    -> update("users");
```
The return value is always an array. On error, the error handler will be thrown. On successful query, the return will be status => 1.

## Insert
Only one argument is set in the insert function. Insert values are set in their own functions which will be discussed later.
- $table_name
```php
// Insert record in to users with a username column value of jimmy
$query 
    -> set_insert_column("username", "jimmy") 
    -> insert("users");
```
The return array will have 2 keys. status => 1, last_insert_id.

## Delete
Only one argument is set in the delete function. Delete values and where values are set in their own functions which will be discussed later.
- $table_name
```php
// Delete record from users where id col = 1
$query 
    -> set_where("id", 1) 
    -> delete("users");
```
The return array will be status => 1 on success.

## Deactivate
Aces uses table record logs that are stored in a log table. Some applications may want to be able to "soft" delete rows by deactivating the row instead of deleting the record. Deactivate queries just give a shortcut to update the active status and update the log table with the proper record tracking. This is in lieu of having to run an update query for both the record and the log table.

## Where Statments
Where statements work for all applicable query types. They have two required arguments and two optional arguments. 

This is how the where function looks in the query class. The arguments are as follows.
- $column [required]
- $value [required]
- $operator [optional] {default "="} **Accepts all possible SQL values**
- $logical_operator [optional] {default "AND"} **Accepts all possible SQL values**

### Set Single Where Statement
The use of the where function is as follows:
```php
// Set where statement with id = 1 then run select query
$query 
    -> set_where("username", "b3tac0d3") 
    -> select("users");
```
**or**
```php
// Same as above but without chained functions
$query -> set_where("id", 1);
$query -> select("users");
```
Multiple where statements can be chained together **OR** you can use ```set_where_array```

### Set Multiple Where Statements
set_where_array functions like set_where. It only allows for 2 arguments:
- $columns {simple array}
- $values {simple array} **Corresponding directly with columns list**
This function does not allow for operators or logical_operators to be set but provides a way to run multiple simple where conditions in a single function. Each function is subsquently run through the set_where function to be handled.
```php
// Set 3 where statements: id = 1, username = b3tac0d3, active = 1
$query -> set_where_array(["id", "username", "active"], [1, "b3tac0d3", 1]);
```
This function can also be chained and/or combined with additional set_where statements.


## Join Statements
The join statement has 4 arguments. The join statement accepts arrays so you can set multiple joins in one method call.
- $table_name [required]
- $alias [highly recommended] **Can also be used in where statements**
- $ons_array [required] **Must be array, even if only single statement**
- $type [required] {default = "INNER"}
```php
// Get users with corresponding contact information
$query 
    -> set_join("contacts", "c", ["c.id" => "u.id", "u.active" => 1]) 
    -> set_where("u.id", 1) 
    -> select("users", "u");
```

## Query Limit
Query limit is the count and limit of rows returned. This can also be chained.
There are two arguments.
- $start [required] | First record to show in results
- $limit [required] | Number of records to return
```php
// Start at record 0 and return only 25 records (Rows 1-25)
$query -> set_limit(0, 25)
```

## Query order
Set the order of results based on column. There are two arguments.
- $column [required]
- $order [required] {default: "ASC" (ascending)}
```php
// Order by ID's in reverse order
$query -> set_order("id", "DESC");
```

## Record Grouping
Group records by columns.
- $column [required]
```php
// Group results by active status
$query -> set_group("active");
```

## Fetch statements (PDO::FETCH)
Set the number of records to fetch and the type of PDO::FETCH you want. All possible PDO fetch values are able to be used here. There are two arguments.
- $fetch_solo [required] {default: 0/false} **Fetch a single record or multiple. Default is multiple.**
- $fetch [required] {default: PDO::FETCH_ASSOC}
```php
// Maintain default fetch status while only requesting one record to be returned.
$query -> set_fetch(1);
```

## Set Select Column
Set a single column to be selected when running the query. Multiple methods can be chained.
- $column [required] {default: */ALL}
```php
// Select the username column from the table
$query -> set_select_column("username");
```

## Set Multiple Select Columns
Set multiple select statements with one method. Runs a set_select_column for each instance of the array.
- $columns [required] **simple array**
```php
// Select id, username and active status
$query -> set_select_array(["id", "username", "active"]);
```

## Set Update Column 
Set column and value for update query. Is chainable.
- $column [required]
- $value [required]
```php
// Update username in user table
$query 
    -> set_update_column("username", "jimmy") 
    -> set_where("id", 1) 
    -> update("users");
```

## Set Multiple Update Columns
Set multiple update columns in one method call.
- $columns [required] **Simple array**
- $values [required] **Simple array corresponding to columns array**
```php
// Update contacts table first name and last name
$query 
    -> set_update_array(["firstname", "lastname"], ["Jimmy", "McGovern"]) 
    -> set_where("id", 1) 
    -> update("contacts");
```

## Set Insert Column
Set insert value for insert query. Is chainable.
- $column [required]
- $value [required]
```php
// Insert user to users table
$query 
    -> set_insert_column("username", "admin") 
    -> set_insert_column("password", "hash")
    -> set_insert_column("salt", 1234)
    -> insert("users");
```

## Set Multiple Insert Columns
Set multiple insert statements in one method call.
- $columns [required]
- $values [required]
```php
// Add the same values to the insert table
$query 
    -> set_insert_array(["username", "password", "salt"], ["admin", "hash", 1234]);
```

## Set Table Alias
Alias is assigned to the main table being called in the query. Sub-table aliases can be assigned when calling joins.
- $alias [required] **Table alias**
```php
// Users table would have alias of "u" for for rest of object use.
$query 
    -> set_alias("u") 
    -> select("users");
```

## Get Last Record Insert ID
Returns the record ID of the last inserted row. No arguments.
```php
$id = $query -> get_lastInsertId();
```

## Get Results
Returns an array with the results from the last query. This is also automatically returned when running the query. Queries other than select return confirmation values as well. See query types for return values.
```php
$results = $query -> get_results();
```

## Get Record Row Count
Returns the row count from the previous query.
```php
$count = $query -> get_rowCount();
```