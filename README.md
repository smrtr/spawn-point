spawn-point
===========

A quick and dirty dispatch process using Haltorouter.

This featherweight stack uses [Symfony's HttpFoundation][1] Request & Response objects with [Smrtr's Haltorouter][2]
object to enable you to start developing extremely quickly.

## Installation
From your project root:

 1. Require spawn-point with composer. Add `"smrtr/spawn-point": "~1.0"` to the `require` section of your `composer.json`.
 2. Run `composer update` to download the spawn-point library.
 3. Run `vendor/bin/spawn spawn` to create the required project files.

## Configuration

### vHost
Apache2 vhost configuration must declare the rewrite engine to be on and the document root to be inside a directory
called `public` inside your project root. See the following vhost for a project called Buzz located in `/var/www/Buzz`:

    # Buzz
    <VirtualHost *:80>
        ServerName buzz.local
        ServerAlias www.buzz.local
        ServerAlias private.buzz.local
        DocumentRoot "/var/www/Buzz/public"
        RewriteEngine on
        SetEnv APP_ENV "development"
        <Directory "/var/www/Buzz/public">
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Satisfy Any
            Allow from all
            Require all granted
        </Directory>
    </VirtualHost>

### Hostgroups
Use `app/config/hostgroups.ini` to organize hostnames into groups. These hostgroups are used to match requests.
See the following hostgroup configuration for example which creates two groups, 'Public' and 'Private':

    [development]
    Public[] = "buzz.local"
    Public[] = "www.buzz.local"
    Private[] = "private.buzz.local"

### Routes
Use `app/config/routes.ini` to define your routes. These routes will be passed into a `Smrtr\Haltorouter` instance.
Routes can be grouped by environment if you wish.
See the following example which defines a route to the homepage across the development & production environments:

    [bootstrap]
    homepage.route = "/"
    homepage.method = "GET"
    homepage.hostgroup = "Public"
    homepage.target = "\Buzz\HomepageController@homepage"

    [development : bootstrap]

    [production : bootstrap]

The 'target' of the route tells the application which class to load and which method to call.

### PHP settings
You may specify php settings, much like you would in php.ini, in `app/config/phpSettings.ini`. This is a convenient way
to define php settings at runtime without depending on the php settings of the environment.

## Usage
To complete our example we need to implement the homepage controller which we defined in our routes config.

In `src\Buzz\HomepageController.php`:

    <?php
    namespace Buzz;
    use Smrtr\SpawnPoint\AbstractController;

    class HomepageController extends AbstractController
    {
        public function homepage()
        {
            echo "hello world!";
        }
    }

### Request parameters

##### 1. Add a parametrised route to your `app/config/routes.ini`:

    user.route = "/user/[i:id]"
    user.method = "GET"
    user.hostgroup = "Public"
    user.target = "\Buzz\UserController@user"

##### 2. Retrieve the parameter from the request object in `src/Buzz/UserController.php`:

    <?php
    namespace Buzz;
    use Smrtr\SpawnPoint\AbstractController;

    class UserController extends AbstractController
    {
        public function user()
        {
            $id = (int) $this->getRoutedParam('id');
        }
    }

  [1]: http://symfony.com/doc/current/components/http_foundation/introduction.html
  [2]: http://resources.smrtr.co.uk/haltorouter
