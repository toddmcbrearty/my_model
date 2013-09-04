my_model
========

Codeigniter model that helps you start up faster. I actively build it for my own use. 
So there are tons of features it can have but I have not needed yet. Any ideas let me know.


Getting started
---------------

Simply drop MY_Model.php into your application/core folder. 
If you changed the prefix for your core models change MY_Model.php accordingly.

Now extend your models with MY_Model

```
class User_model extends MY_Model { }
```

Next there are a few variables required and optional
```
class User_model extends MY_Model
{
	//table that this model users [required]
	public $table = 'users';

	//never show these fields on select unless overriden
	public $exceptions = array( 'password', 'activate_code' );

	//only will override exceptions
	public $only = array();	
	
}
```

$table is required. This is the table that is used for this class.
$exceptions (optional). If you add field names in here they will never return in a select unless overriden
$only (optional). Only will override exceptions. You can pass just the fields you want returned. 

both exceptions and only can be set prior to a call and not nessecarly as a class variable.

Making a call
-------------

Syntax for making a select:
(these are currently available. more to come)
```
$this->model_name->find_by__id( 1 ); //finds an object by id
$this->model_name->find_in__status( '1,2,3' ); //finds an object in the status
$this->model_name->find_gt__status( 2 ); //returns all objects with a status greater than 2
$this->model_name->find_lt__status( 2 ); //returns all object with a status less than 2
```

Benefits of insert and update is it will check to see if the table has a created_at or updated_at field.
if it does it will take care of populating them. No need for you to waste time doing it.
Syntax for making an insert:
```
$data = array(
	'name' => 'todd',
	'email' => 'todd@example.com',
);

return $this->model_name->insert( $data );
```
returns the newly created id

Syntax for making an update:
```
$id = 1;

$data = array(
	'name' => 'krista',
	'email' => 'krista@example.com'
);

return $this->model_name->update( $id, $data, TRUE );
```

if the 3rd parameter is not supplied it will return the entire object that was just modified.
If the 3rd parameter is set to TRUE it will return only the newly modified objects.

Syntax for deleting:
```
$this->model_name->delete( 1 ); //deletes row id 1
$this->model_name->delete( 'all' ); //truncates the table
```








