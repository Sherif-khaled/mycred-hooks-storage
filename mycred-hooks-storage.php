<?php
/**
 * @wordpress-plugin
 * Plugin Name:       myCred Hooks Storage
 * Plugin URI:        https://github.com/Sherif-khaled/mycred-hooks-storage
 * Description:       Add Extra Hooks to Mycred Plugin, and track all hooks activities.
 * Version:           1.0.0
 * Author:            Sherif Khaked
 * Author URI:        https://github.com/Sherif-khaled
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       MHS
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require dirname(__FILE__) . '/vendor/autoload.php';
}
$plugins_required = ['mycred'];

function MHS_plugins_required_notice($_slug)
{
    foreach ($_slug as $slug) {
        ?>
        <div class="notice notice-error">
            <p><strong>
                    <?php
                    $main_file = "{$slug}/{$slug}.php";
                    if (is_plugin_inactive($main_file)) {
                        $install_url = admin_url('plugins.php?action=activate&plugin=' . urlencode($main_file) . '&plugin_status=all&paged=1&s&_wpnonce=' . wp_create_nonce('activate-plugin_' . $main_file));
                    } else {
                        $install_url = '';
                    }
                    printf(
                        __('The myCred Hooks Storage plugin requires %1$sthe mycred Plugin to be installed and activated%2$s.', 'MHS'),
                        "<a href=\"{$install_url}\">",
                        '</a>'
                    );
                    ?>
                </strong></p>
        </div>
        <?php
    }
}

define('MHS_PLUGIN_SLUG', 'mycred-hooks-storage/mycred-hooks-storage.php');
define('MHS_PLUGIN_NAME', 'mycred-hooks-storage');
define('MHS_PATH', plugin_dir_path(__FILE__));
define('MHS_EDITION', MHS_PLUGIN_NAME);

if (is_plugin_active('mycred/mycred.php')) {
    define('MHS_ADMIN_PATH', MHS_PATH . '/Inc');
    define('MHS_HOOKS', MHS_ADMIN_PATH . '/Hooks/');
    define('MHS_BASE_PATH', MHS_PATH . '/Inc/Base');
    define('MHS_Template_PATH', MHS_PATH . '/templates');
    define('MHS_JS_PATH', plugin_dir_url(__FILE__) . 'assets/js/');
    define('MHS_CSS_PATH', plugin_dir_url(__FILE__) . 'assets/css/');

    $mhs_url_protocol = (is_ssl()) ? 'https' : 'http';
    define('MHS_URL', preg_replace('/^https?:/', "{$mhs_url_protocol}:", plugins_url('/' . MHS_PLUGIN_NAME)));

    /**
     * Returns current plugin version.
     *
     * @return string Plugin version
     */
    function MHS_plugin_info($field)
    {
        static $curr_plugins;
        if (!isset($curr_plugins)) {
            if (!function_exists('get_plugins')) {
                require_once(ABSPATH . '/wp-admin/includes/plugin.php');
            }
            $curr_plugins = get_plugins();
        }
        if (isset($curr_plugins[MBE_PLUGIN_SLUG][$field])) {
            return $curr_plugins[MBE_PLUGIN_SLUG][$field];
        }
        return '';
    }


}

use MyCredHooksStorage\Base\Activate;
use MyCredHooksStorage\Base\Deactivate;

/**
 * The code that runs during plugin activate
 */
function MHS_activate()
{
    Activate::activate();
}

/**
 * The code that runs during plugin deactivate
 */
function MHS_deactivate()
{
    Deactivate::deactivate();
}

register_activation_hook(__FILE__, 'MHS_activate');
register_deactivation_hook(__FILE__, 'MHS_deactivate');
/**
 * Initialize all the core classesof the plugin
 */
include_once(ABSPATH . 'wp-admin/includes/plugin.php');


if (is_plugin_active('mycred/mycred.php')) {
    if (class_exists('MyCredHooksStorage\\Init')) {

        MyCredHooksStorage\Init::register_service();
    }

}
