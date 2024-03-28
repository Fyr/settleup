# SettleUp
## Description
SaaS application allows for compensation and deduction files to be uploaded and recurring items to be scheduled for each period. Our system calculates the net settlement to disburse to contractors and the deduction and reserve amounts to remit to vendors. Reduce your accounting team's workload with our comprehensive reports that show all settlement activity.

## Components overview 
The application encompasses six key components:
1. The primary web server, driven by Nginx, handles all incoming HTTPS traffic.
1. The main PHP server, built on Zend Framework 1 and using PHP 8.1, processes requests from the primary web server and houses all business logic.
1. An auth web server, also backed by Nginx, handles requests directed from the main PHP server.
1. The auth PHP server, developed with Lumen and running on PHP 8.2, processes requests originating from the authentication web server. This server focuses on authentication and data decryption processes.
1. The main MySQL 5.7 database is responsible for data storage.
1. The auth MySQL 5.7 database is responsible for storing credentials and encrypted data.

## How to deploy project locally
Setting up the project on your local environment can be challenging. Therefore, ensure you adhere to the steps provided below:
1. Install necessary software on your local machine.
1. Download repositories.
1. Create local copies of configuration files.
1. Deploy containers using Docker.
1. Install PHP packages.
1. Apply database migrations.
1. Install frontend packages.
1. Build frontend assets.

Please see details below.
### Prerequisites

Before start please make sure to install and configure the following:

* Install Docker. You can refer to this [installation guide](https://docs.docker.com/get-docker/)
* Install Docker Compose V2 plugin. You can refer to this [installation guide](https://docs.docker.com/compose/migrate/)

### Download repositories
The first step is to download two repositories:
* [Main repository](https://ctforward@dev.azure.com/ctforward/SettleUP/_git/SettleUP)
* [Auth repository](https://ctforward@dev.azure.com/ctforward/SettleUP/_git/settlement_auth)

Please make sure the both repositories are in the same parent folder.
### Create configuration files:
#### Main repository
Create new configurations files by copying samples and adjusting as needed:
1. Navigate to the root of the Main repository. 
1. Create a copy of the main configuration file:
    ```bash
     cp application/configs/application.ini.sample application/configs/application.ini
    ```
1. Create a copy of the database migration configuration file:
    ```bash
    cp scripts/db/database.ini.dist scripts/db/database.ini
    ``` 
NOTE: If you make any changes in the database configuration, please make sure to reflect those changes in the `docker-compose.yml` file:
* `MYSQL_DATABASE` - This variable is allows you to specify the name of a database to be created on image startup. If a user/password was supplied (see below) then that user will be granted superuser access (corresponding to GRANT ALL) to this database.
* `MYSQL_USER`, `MYSQL_PASSWORD` - These variables used in conjunction to create a new user and to set that user's password. This user will be granted superuser permissions (see above) for the database specified by the MYSQL_DATABASE variable. Both variables are required for a user to be created.
#### Auth repository
Create new configurations file by copying a sample and adjusting as needed:
1. Navigate to the root of the Auth repository. 
1. Create a copy of the .env file:
    ```bash
     cp .env.example .env
    ```
### Deploy containers 
1. Navigate to the root of the Main repository.
1. Deploy containers: 
```bash
docker compose up -d
```
If everything goes smooth you should see all six containers up and running:
```
$ docker compose ps
NAME                  COMMAND                  SERVICE             STATUS              PORTS
settleup-auth-db      "docker-entrypoint.s…"   database_auth       running             0.0.0.0:33060->3306/tcp, :::33060->3306/tcp
settleup-auth-nginx   "/docker-entrypoint.…"   web_auth            running             0.0.0.0:8080->80/tcp, :::8080->80/tcp
settleup-auth-php     "docker-php-entrypoi…"   app_auth            running             9000/tcp
settleup-main-db      "docker-entrypoint.s…"   database            running             0.0.0.0:3306->3306/tcp, :::3306->3306/tcp
settleup-main-nginx   "/docker-entrypoint.…"   web                 running             0.0.0.0:80->80/tcp, :::80->80/tcp
settleup-main-php     "docker-php-entrypoi…"   app                 running             9000/tcp

```    
### Install PHP packages:
#### Main repository
Run the following command to install PHP packages using Composer:
```bash
docker exec -it settleup-main-php composer install
``` 
You should get the following result:
```
Installing dependencies from lock file (including require-dev)
Verifying lock file contents can be installed on current platform.
Package operations: 45 installs, 0 updates, 0 removals
  - Downloading composer/pcre (3.1.0)
  - Downloading psr/container (2.0.2)
    [...skipped...] 
  - Installing rector/rector (0.18.5): Extracting archive
  - Installing shardj/zf1-future (1.23.5): Extracting archive
Generating autoload files
30 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
```
#### Auth repository
Run the following command to install PHP packages using Composer:
```bash
docker exec -it settleup-auth-php composer install
``` 
You should get the following result:
```
Installing dependencies from lock file (including require-dev)
Verifying lock file contents can be installed on current platform.
Package operations: 124 installs, 0 updates, 0 removals
  - Downloading phpstan/phpdoc-parser (1.24.2)
  - Downloading doctrine/deprecations (1.1.2)
    [...skipped...]
  - Installing myclabs/deep-copy (1.11.1): Extracting archive
  - Installing phpunit/phpunit (10.4.1): Extracting archive
Generating optimized autoload files
75 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
```
### Apply migrations:
#### Main repository
The main database image comes with a SQL dump that brings most of the data. So, only a few migrations should be applied after that. Run the following command to apply the latest SQL migrations: 
```bash
docker compose exec database "./migrate.sh"
```
You should something like this as a response:
```
update20160609_150000.sql - skipped
update20161123_140000.sql - skipped
update20161124_180000.sql - skipped
update20170413_110000.sql - Ok!
update20170418_160000.sql - Ok!
update20170419_110000.sql - Ok!
update20170420_110000.sql - Ok!
update20170427_110000.sql - Ok!
```
#### Auth repository
The auth database comes empty. So, you need to do two things:
1. Create schema:
    ```bash
    docker exec -it settleup-auth-php php artisan migrate
    ```
    You should get the following result:
    ```
       INFO  Preparing database.  
    
      Creating migration table ............................................................................................................ 31ms DONE
    
       INFO  Running migrations.  

      2015_03_12_000000_create_users_table ................................................................................................ 28ms DONE
      2015_03_12_070132_create_token_table ................................................................................................ 26ms DONE
      2015_03_12_122223_create_carrier_keys_table ......................................................................................... 80ms DONE
      2015_07_15_083744_user_entity_table ................................................................................................. 31ms DONE
    ```
1. Populate test data:
    ```bash
    docker exec -it settleup-auth-php php artisan db:seed 
    ```    
    You should get the following result:
    ```
       INFO  Seeding database.  
    
      Database\Seeders\UserTableSeeder ...................................................................................................... RUNNING  
      Database\Seeders\UserTableSeeder ................................................................................................ 51.13 ms DONE  
    ```
### Install Frontend packages:
```bash
docker exec -it settleup-main-php npm i
```
### Rebuild Frontend assets:
```bash
docker exec -it settleup-main-php npm run build
```
## Login into the system
If you successfully completed all the previous steps then you should be able login to the system:
 1. Open a web browser 
 1. Enter `localhost`
 1. Use the following credentials: `qa@nikait.co` / `pass`
## Troubleshooting
### How to totally clean up database:
1. Stop the database container:
    ```
    docker stop settleup-main-db
    ```
1. Remove the database container:
    ```bash
    docker rm settleup-main-db
    ```
1. Remove the database volume:
    ```
    docker volume rm settleup_mysql_data
    ```
1. Rebuild image if needed (only if you made changes to the database image):
    ```
    docker compose build --no-cache database
    
    ```
1. Set up container again:
    ```
    docker compose up -d database
    ```
You should see the following in the logs:
```
settleup_db  | >>> Import initial data into the pfleet database.
settleup_db  | >>> Initial data import has been finished successfully.
```

## Development process
Before you start work on a task, please ensure the following:
1. Pull latest changes from the remote repo.
1. Create a new branch for your task using the JIRA ticket number.
1. Install PHP packages.
1. Apply database migrations.
1. Install frontend packages.
1. Build frontend assets.

You can find more details by the [link](https://drive.google.com/file/d/1JSvjufKF_o4tp8_CsQWaIaug6mgzJdKq/view).

## Unit testing
To run unit-tests:
```
docker exec -it settleup-main-php composer run tests
```
To run test coverage:
```
docker exec -it settleup-main-php composer run tests-coverage
```
Coverage report will be available here: [http://localhost/tests-coverage.html]

## Using coding standards tools

When creating a pull request, the pipeline will be processed with automatic checking for styles and static code
analysis.

If some checks fail, you won't be able to merge your code into the main branch.

The following tools are used:

1. [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) `Reports and fixes issues by coding standards` To run
   the checker and automatically fix issues, use the command below in the console:
    ```bash
   docker exec -it settleup-main-php composer run phpcs-fix
1. [Phpstan](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) `Reports issues in logic` To run the checker, run the command
   below in the console. There is no automatic fix issues, only reports.
   ```bash
   docker exec -it settleup-main-php composer run phpstan
1. [Rector](https://github.com/phpstan/phpstan) `Reports and fixes issues in logic` To run the checker and automatically fix issues, run the command
   below in the console:
   ```bash
   docker exec -it settleup-main-php composer run rector-fix
