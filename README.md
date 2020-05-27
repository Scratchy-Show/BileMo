# BileMo

Creating of an API Rest for BileMo, a fictitious phone sales company.

------------------------------------------------------------------------------------------------------------------------------------------

## Codacy Badge
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/02618234586b4420baaa0a602c86a9ac)](https://www.codacy.com/manual/Scratchy-Show/BileMo?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Scratchy-Show/BileMo&amp;utm_campaign=Badge_Grade)

------------------------------------------------------------------------------------------------------------------------------------------
## Environment used for development

* Symfony 4.14.3

* Composer 1.9.1

* Wampserver 3.2.0
  * PHP 7.4.1
  * Apache 2.4.41
  * MySQL 8.0.18
    
------------------------------------------------------------------------------------------------------------------------------------------

## Install the project

1. Download and install WampServer (or equivalent: MampServer, XampServer, LampServer).
 2. Download the project clone in the www folder of WampServer :
```
git clone https://github.com/Scratchy-Show/BileMo.git
```

 3. Configure the `DATABASE_URL` environment variable to connect to your database in `.env` file.
 4. **Install the dependencies** - In the root directory of the project, open the CLI (Command-Line Interface) and execute the command :
```
composer install
```

 5. **Create the database** - Execute the command :
```
php bin/console doctrine:database:create
```

 6. **Update database** - Execute the command :
```
php bin/console doctrine:schema:update --force
```

 7. **Load fixtures** - Execute the command :
```
php bin/console doctrine:fixtures:load
```

 8. **Run the Symfony server** - Execute the command :
```
symfony server:start
```

 9. **Access the site** - Enter the address indicated by the web server in your browser :
```
Example: <http://127.0.0.1:8000>
```
