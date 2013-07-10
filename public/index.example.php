<?php
 
/**
 * Index page
 * @copyright (C) 2013 by OKFN Belgium
 * @license AGPLv3
 * @author Leen De Baets
 * @author Jeppe Knockaert
 * @author Nicolas Dierck
 */

require_once '../vendor/autoload.php';

//Construct the Silex application
$app = new Silex\Application();

// Set the environment for error reporting
define('ENVIRONMENT', 'development');


/**
 * Alright, here we go!
 *
 * -----------------
 * DANGER ZONE BELOW
 * -----------------
 */

if (defined('ENVIRONMENT'))
{
    switch (ENVIRONMENT)
    {
        case 'development':
            error_reporting(E_ALL);
            ini_set('display_errors', True);
            $app['debug'] = true;
        break;

        case 'testing':
        case 'production':
            error_reporting(0);
        break;

        default:
            exit('The application environment is not set correctly.');
    }
}


// Website document root
define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);

// Application directory
define('APPPATH', realpath(__DIR__.'/../app/').DIRECTORY_SEPARATOR);

// Vendor directory
define('VENDORPATH', realpath(__DIR__.'/../vendor/').DIRECTORY_SEPARATOR);

// TODO: remove the lines below and use configuration instead

// Hostname of The DataTank installation (With trailing slash!)
define('HOSTNAME', "...");

// Path to the local tdt-start folder (With trailing slash!)
define("STARTPATH", "...");

//Register the Twig Service Provider
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => DOCROOT.'views'
));

// Register the Form Service Provider
$app->register(new Silex\Provider\FormServiceProvider());

// Register the Validator and Translation Service Providers
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.messages' => array(),
));

// Register the session service provider object
$app->register(new Silex\Provider\SessionServiceProvider());

// Register the security service provider object
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(),
));

// register ...
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// must be included first
require_once 'authentication.php';

use Symfony\Component\Security\Http\Firewall;

// Authorization
$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/login$',
    ),
    'secured' => array(
        'pattern' => '^.*$',
        'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
    ),
);

// If root is asked, redirect to the resource management
$app->get('/', function () use ($app) {
    return $app->redirect('/package');
});

// The parameters that cannot be edited
$app['session']->set('notedible',array('generic_type' => 'generic_type', 
                                        'resource_type' => 'resource_type',
                                        'columns' => 'columns',
                                        'column_aliases' => 'column_aliases'));

//start with resources management
require_once 'packagesAndResources.php';
require_once 'usermanagement.php';
require_once 'routemanagement.php';
require_once 'choosefile.php';
require_once 'generictypes.php';
require_once 'puttingfile.php';
require_once 'editPackagesAndResources.php';
require_once 'editResource.php';


$app->run();
