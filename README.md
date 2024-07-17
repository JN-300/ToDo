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
## Testing
```console
$ ddev artisan test
```

# API Routes
## -> [All information about the different API Routes](./documentation/api/index.md)
