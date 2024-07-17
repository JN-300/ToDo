# API


## STATUS
With the exception of the ``POST: ./api/token` route, all requests must be authenticated with a BearerToken. If the token is missing or incorrect, requests are answered with a status **401**.

All normal Api requests respond with a status **200**, except for all POST requests, which respond with a **201** if the write process is successful.
If validations fail, the system returns a status **422**.

If access rights are missing, status **403** is returned.

The following areas with the respective routes are available for managing the tasks:

## ROUTES
The API always expects **JSON** for requests and always returns JSON. Therefore, all requests must be sent with the following headers:
```console
Accept: application/json
Content-Type: application/json
```
More information will be found inside the swagger api documentation tool, which can be start with 
```console
ddev launch swagger
```


=====================================================================================================

### Auth
```console
POST: ./api/token
```
Expects email and password and returns an access token if successful
```console
DELETE: ./api/token
```
Must be called as an authenticated user and deletes the current access token


=====================================================================================================

### Projects
Projects can be created, read, edited and deleted via the `./api/projects`` routes.

#### Finds all projects and returns them
```console
GET: ./api/projects
```
> Additional query parameter<br>
> This query params can be freely combined:

> You can use the following query parameters to enrich the output
- attach the tasks to the project response
```console
?with[]=tasks
```
- also you can limit the output
```console
?limit=INTEGER&page=INTEGER
```
------------------------------------------------------------------------------------------

#### Creates a project and returns it
```console
POST: ./api/projects
{
    "title": STRING
}
```

------------------------------------------------------------------------------------------

#### Finds a project based on the uuid and returns it
```console
GET: ./api/projects/{project.id}
```

> You can use the following query parameters to enrich the output
- attach the tasks to the project response
```console
?with[]=tasks
```

------------------------------------------------------------------------------------------

#### Update a project based on the uuid and returns it
```console
PATCH: ./api/projects/{project.id}
{
    "title": STRING
}
```

------------------------------------------------------------------------------------------

#### Deletes a project based on the Uuid
```console
DELETE: ./api/projects/{project.id}
```

=====================================================================================================

### Tasks
Tasks can be created, read, edited and deleted via the ``./api/tasks`` routes.

For each task, the current user is entered as the owner and only they (as well as users with the Admin status) can edit tasks.
Furthermore, it is not possible for a normal user to create a task or to edit a task for which the deadline is in the past.

#### Finds all tasks by current user and returns them
```console
GET: ./api/tasks
```

> Additional query parameter<br>
> This query params can be freely combined:

> You can use the following query parameters to enrich the output
- attach the project to the task response
```console
?with[]=project
```

- show only overdue tasks (all task which deadline is lower than now and status is not done)
```console
?filter[overdue]=true
```

- At last but not least you can limit the output
```console
?limit=INTEGER&page=INTEGER
```

------------------------------------------------------------------------------------------

#### Creates a task for current user and returns it
```console
POST: ./api/tasks
{
    "title": STRING,
    "description": STRING,
    "status": STRING [to_do|in_progress|done],
    "deadline": STRING DATETIME,
    "project_id": STRING UUID
}
```

------------------------------------------------------------------------------------------

#### Finds a task  from current user based on the uuid and returns it
```console
GET: ./api/tasks/{task.id}
```

> You can use the following query parameters to enrich the output
- attach the project to the task response
```console
?with[]=project
```

------------------------------------------------------------------------------------------

#### Update a task from current user based on the uuid and returns it
```console
PATCH: ./api/tasks/{task.id}
{
    "title": STRING,
    "description": STRING,
    "status": STRING [to_do|in_progress|done],
    "deadline": STRING DATETIME,
    "project_id": STRING UUID
}
```

------------------------------------------------------------------------------------------

#### Deletes a task from current user  based on the Uuid
```console
DELETE: ./api/tasks/{task.id}
```

=====================================================================================================

### ADMIN AREA
All routes below ./api/admin are protected with middleware that only grants access to users with Admin authorization.
The following routes are currently available:

#### Finds all task from all users 
```console
GET: ./api/admmin/tasks
```
> Additional query parameter<br>
> This query params can be freely combined:

> You can use the following query parameters to enrich the output
- attach the project to the task response
```console
?with[]=project
```
- attach the owner to the task response
```console
?with[]=owner
```

> There are also the following query parameters available to filter the output
- show only tasks from specified users
```console
?filter[users][]={user.id}
```
- show only tasks from specified projects
```console
?filter[projects][]={project.id}
```
- show only overdue tasks (all task which deadline is lower than now and status is not done)
```console
?filter[overdue]=true
```

- At last but not least you can limit the output
```console
?limit=INTEGER&page=INTEGER
```

------------------------------------------------------------------------------------------

#### Find a task
```console
GET: ./api/admin/tasks/{task.id}
```

------------------------------------------------------------------------------------------

#### Edit a (also overdue) task
```console
PATCH: ./api/admin/tasks/{task.id}
{
    "title": STRING,
    "description": STRING,
    "status": STRING [to_do|in_progress|done],
    "deadline": STRING DATETIME,
    "project_id": STRING UUID
}
```

------------------------------------------------------------------------------------------

#### Delete a task
```console
DELETE: ./api/admin/tasks/{task.id}
```
