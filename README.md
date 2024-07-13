# Installation
Best way to install a test version of this application is to use ddev.
See https://ddev.readthedocs.io/en/stable/ for  installation instructions of ddev.

A little bit more about the advantages of development with ddev instead of a plain docker setup will hopefully come later.


## Using ddev
... more will be come later

```console
:$ cd __DEVELOPMENT_FOLDER
## either
$ git clone https://github.com/JN-300/ToDo.git
$ cd ToDo
## or
$ mkdir __REQUESTED_FOLDER__
$ cd __REQUESTED_FOLDER__
$ git clone https://github.com/JN-300/ToDo.git .
## \or

$ ddev start
$ ddev composer install
$ ddev artisan key:generate
$ ddev artisan migrate [--seed für Beispielnutzer und Testdatensätze]
```
## Testing
```console
$ ddev artisan test
```

# API Routes

## AUTH

### CREATE TOKEN (Login)

#### Request
```console
curl --request POST \
  --url ./api/token \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
	"email": __EMAIL__,
	"password": __PASSWORD__
}
```

#### Response
// success
```console
# STATUS: 200
{
    'access_token': __ACCESSS_TOKEN
}
```

// failure
```console
# STATUS: 422
{
	"message": "auth.failed",
	"errors": {
		"email": [
			"auth.failed"
		]
	}
}
```

### DELETE TOKEN (Logout)

#### Request
```console
curl --request DELETE \
  --url ./api/token \
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'accept: Application/json' \
  --header 'Content-Type: application/json' \
```

#### Response
// success
```console
# STATUS: 200
{
	"success": true,
	"message": "token deleted"
}
```

// failure
```console
# STATUS: 401 (Unauthorized)
{
	"message": "unauthenticated"
}
```

---

## TASKS 

### LIST TASKS
#### Request
```console
curl --request GET \
  --url ./api/tasks \
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json'
```

#### Response
```console
# STATUS: 200
{
    "data": [
        {
            "title": STRING
            "description": STRING
            "status": "to_do"|"in_progress"|"done"
            "created_at": DATETIME
            "updated_at": DATETIME
        },
        ...
    ]
}
```

### CREATE TASK
#### Request
```console
curl --request POST \
  --url ./api/tasks\
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
        "title": STRING
        "description": STRING
        "status": "to_do"|"in_progress"|"done"
}'
```
#### Response
```console
# STATUS: 201
{
	"data": {
		"id": __TASK_UUID__,
		"title": STRING,
		"description": STRING,
		"status": STRING "to_do"|"in_progress"|"done",
		"created_at": DATETIME,
		"updated_at": DATETIME
	},
	"success": true,
	"message": "Task successfully generated"
}
```

### SHOW SINGLE TASK
#### Request
```console
curl --request GET \
  --url ./api/tasks/__TASK_UUID__ \
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json'
```
#### Response
// success
```console
# STATUS: 200
{
    "data": 
        {
            "id": UUID __TASK_UUID__,
            "title": STRING
            "description": STRING
            "status": "to_do"|"in_progress"|"done"
            "created_at": DATETIME
            "updated_at": DATETIME
        }
}
```

// failure
```console
# STATUS: 422
{
	"message": STRING __ERROR_MESSAGE__,
	"errors": {...}
}
```

### UPDATE TASK

#### Request
```console
curl --request PATCH \
  --url ./api/tasks/__TASK_UUID__ \
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
        "title": STRING
        "description": STRING
        "status": "to_do"|"in_progress"|"done"
    }'
```

#### Response

// success
```console
# STATUS: 200
{
	"data": {
		"id": __TASK_UUID__,
		"title": STRING,
		"description": STRING,
		"status": STRING "to_do"|"in_progress"|"done",
		"created_at": DATETIME,
		"updated_at": DATETIME
	},
	"success": true,
	"message": "Task successfully updated"
}
```

// failure
```console
# STATUS: 422
{
	"message": STRING __ERROR_MESSAGE__,
	"errors": {...}
}
```

### DELETE TASK
#### Request
```console
curl --request DELETE \
  --url ./api/tasks/__TASK_UUID__ \
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' 
```

#### Response

// success
```console
# STATUS: 200
{
	"success": true,
	"message": "Task successfully deleted"
}
```

// failure
```console
# STATUS: 422
{
	"message": STRING __ERROR_MESSAGE__,
	"errors": {...}
}

```
