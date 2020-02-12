<?php


/**
 * @package    mycred
 * @subpackage myCred Hooks Storage
 * @author     Sherif Khaled <sherif.khaleed@gmail.com>
 * @copyright  2019-2020
 * @since      1.0
 * @license    GPL
 * Text Domain: MHS
 * Domain Path:/languages
 */

namespace MyCredHooksStorage;

if (!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}


final class Init
{
    /**
     * Loop through the classes, initialize them,
     * it exist and call the register() method if
     * @return
     */
    public static function register_service()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Store all the classes inside an array.
     * @return array full list of classes
     */

    public static function get_services()
    {
        return [
            Base\Activate::class,
            Base\Deactivate::class,
            Base\Database::class,
            Base\Enqueue::class,
            Admin\HooksTableListActions::class,
            Admin\RegisterHooks::class,
            Admin\UserActivityRole::class,

        ];
    }

    /**
     * initialize the class.
     * @param class $class class from the services array
     * @return class instance  the new instance of the class.
     */
    private static function instantiate($class)
    {
        $service = new $class();
        return $service;
    }
}