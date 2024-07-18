# Installation
Best way to install a test version of this application is to use ddev.
See https://ddev.readthedocs.io/en/stable/ for  installation instructions of ddev.

A little bit more about the advantages of development with ddev instead of a plain docker setup will hopefully come later.

## Using ddev

```console
$ cd __DEVELOPMENT_FOLDER__

## either

$ git clone https://github.com/JN-300/ToDo.git
$ cd ToDo

## or

$ mkdir __REQUESTED_FOLDER__
$ cd __REQUESTED_FOLDER__
$ git clone https://github.com/JN-300/ToDo.git .

## \either

$ ddev start
$ ddev composer install
$ ddev artisan key:generate
$ ddev artisan migrate [--seed für Beispielnutzer und Testdatensätze]
```
The seeders will generate:
- 1 admin user with 
  - email: admin@example.de 
  - password: password

- 1 fixed user with
    - email: user@example.de
    - password: password
- 20 random user
- 10 random projects
- fixed set of 5 overdue tasks and 20 active tasks for fixed user
- a random set of 20 overdue task and 100 active tasks for other users

## Testing
```console
$ ddev artisan test
```

For mailing tests there is also a mailpit available which catches all mails
You can start i with
```console
$ ddev launch -m
```
Mails will only send in a running environment (not in unit tests)!<br>
To test the notification, update a task with an overdue deadline and a status != **done** as admin.


# API Routes
## -> [All information about the different API Routes](./documentation/api/index.md)
