<?php

/**
 *
 *
 *
 * @package    mycred
 * @subpackage myCred Hooks Storage
 * @author     Sherif Khaled <sherif.khaleed@gmail.com>
 * @copyright  2019-2020
 * @since      1.0
 * @license    GPL
 * Text Domain: MHS
 * Domain Path:/languages
 */

namespace MyCredHooksStorage\Base;
if (!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}


class Activate
{
    public static function activate()
    {

        $tables = array(
            'books',
            'games'
        );

        (new Database)->create_table($tables);
        (new Database)->create_user_activity_table();
        (new Database)->create_user_activity_role_table();

    }


}