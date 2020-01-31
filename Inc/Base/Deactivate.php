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

namespace MyCredHooksStorage\Base;
if (!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}


class Deactivate
{
    public static function deactivate()
    {
        flush_rewrite_rules();
    }

}