<?php


namespace MyCredHooksStorage\Admin;

use MyCredHooksStorage\Base\Functions;

class RegisterHooks
{

    function register()
    {
        add_action('admin_menu', array($this, 'register_hooks_menus'));
        add_filter('mycred_setup_hooks', array($this, 'register_mycred_hooks'));
        add_action('mycred_load_hooks', array($this, 'mhs_activate_hooks'));

    }

    /**
     * @param $installed
     * @return mixed
     */
    function register_mycred_hooks($installed)
    {
        $installed['hook_reading_book'] = array(
            'title' => __('Points for reading Books', 'mycred'),
            'description' => __('Awards %_plural% for reading book.', 'mycred'),
            'documentation' => '',
            'callback' => array('myCRED_Reading_Books')
        );

        $installed['hook_playing_games'] = array(
            'title' => __('Points for playing games', 'mycred'),
            'description' => __('Awards %_plural% for playing games.', 'mycred'),
            'documentation' => '',
            'callback' => array('myCRED_Playing_Games')
        );

        return $installed;
    }

    /**
     * Register Hooks Submenu
     */
    function register_hooks_menus()
    {
        $hook = new Functions;
        $hooks_menu = apply_filters('hooks_menu', array(
            array(
                'page_name' => 'Games Hook',
                'page_slug' => 'games'
            ),
            array(
                'page_name' => 'Books Hook',
                'page_slug' => 'books'
            ),
            array(
                'page_name' => 'User Activities',
                'page_slug' => 'user_activities'
            ),
        ));
        $hook->register_pages_submenu($hooks_menu);
    }

    /**
     * Activate Hooks
     */
    function mhs_activate_hooks()
    {
        require_once MHS_HOOKS . 'hooks-storage.php';

        points_for_reading_books();
        points_for_playing_games_books();
    }
}