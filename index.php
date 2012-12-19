<?php
Dispatcher::main();
class Dispatcher
{
    private static $controller;

    public static function main()
    {
        $time_start = microtime(true);
        require_once('lib/functions.php');
        session_start();
        header('Content-Type: text/html; charset=UTF-8');

        if (!function_exists('apache_request_headers')) {
            exit('Apache server is required. (PHP function "apache_request_headers" not found)');
        }
        $request_headers = apache_request_headers(); //get headers for AJAX detection


        define('BASEDIR', dirname(__FILE__));
        define('IS_AJAX',
            (empty($request_headers['X-Requested-With']) OR
            $request_headers['X-Requested-With'] != 'XMLHttpRequest') ?
            false : true );

        self::$controller = new Controller();
        self::$controller->View = new View(self::$controller);

        $action = 'index';
        if (!empty($_GET['url']))
        {
            $params = explode("/", $_GET['url']);
            $action = preg_replace('/[^a-zA-Z0-9]/', '', $params[0]);

            if (substr($action, 0, 1) == '_') {
                exit("Failure to load pseudo-protected controller action $action");
            }

            if (!method_exists('Controller', $action)) {
                exit("Failure to load Controller action <b>$action</b>");
            }
        }

        self::$controller->$action(); //runs a method off of the controller named after action param
        self::$controller->View->render(); //renders the 'view'

        if (IS_AJAX === FALSE) //print out total time to run script if not an ajax call
        {
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            echo "<!--".$time."-->";
        }
    }
}