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


class Functions
{

    private $hook = null;

    static function convert_slug_to_title($slug)
    {
        $title = str_replace('_', ' ', $slug);
        $title = ucwords(strtolower($title));
        return $title;
    }

    function register_pages_submenu($pages_title)
    {

        foreach ($pages_title as $page) {

            $this->hook = $page['page_slug'];
            add_submenu_page(
                'mycred',
                __($page['page_name'], 'MHS'),
                __($page['page_name'], 'MHS'),
                'manage_options',
                'mycred-' . $page['page_slug'],
                array($this, 'mhs_' . $page['page_slug'] . '_page_callback'));
        }
    }

    /**
     *
     */
    function mhs_books_page_callback()
    {
        // include MHS_Template_PATH . '/add_new_activity.php';
        //Prepare Table of elements
        $wp_list_table = new GenerateHooksTableList('books');
        $wp_list_table->prepare_items();
        //Table of elements
        $wp_list_table->display();
    }

    /**
     *
     */
    function mhs_games_page_callback()
    {
        //include MHS_Template_PATH . '/add_new_activity.php';
        //Prepare Table of elements
        $wp_list_table = new GenerateHooksTableList('games');
        $wp_list_table->prepare_items();
        //Table of elements
        $wp_list_table->display();
    }

    /**
     *
     */
    function mhs_user_activities_page_callback()
    {
        //include MHS_Template_PATH . '/add_new_activity.php';
        //Prepare Table of elements
        $wp_list_table = new GenerateUserActivitiesTableList('user_activities');
        $wp_list_table->prepare_items();
        //Table of elements
        $wp_list_table->display();
    }

}