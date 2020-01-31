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


class Database
{
    function create_user_activity_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'mycred_user_activities ';

        if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
            $sql = "CREATE TABLE " . $table_name .
                " (`id` INT(6) NOT NULL AUTO_INCREMENT,
			       `user_id` INT(6) NOT NULL,
			       `hook_name` VARCHAR(30) NOT NULL,
			       `activity_id` INT(6) NOT NULL,
			       `score` INT(6) DEFAULT 0,
			       `created_at` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				   UNIQUE KEY id (id)
			     );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

    }

    function create_table($tables)
    {
        global $wpdb;
        foreach ($tables as $table) {
            $table_name = $wpdb->prefix . 'mycred_index_' . $table;

            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
                $sql = "CREATE TABLE " . $table_name .
                    " (`id` INT(6) NOT NULL AUTO_INCREMENT,
			       `activity_id` INT(6) NOT NULL,
			       `activity_name` VARCHAR(30) NOT NULL,
			       `created_at` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				   UNIQUE KEY id (id)
			     );";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }

    }

    function _get_rows($table_name)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'mycred_' . $table_name;

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} ")
        );
        return $results;
    }

    function get_rows($table_name)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'mycred_index_' . $table_name;

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} ")
        );
        return $results;
    }

    function get_row_by_id($table_name, $id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'mycred_index_' . $table_name;

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = {$id}")
        );
        return $results;
    }

    function get_row_by_activity_id($table_name, $activity_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'mycred_index_' . $table_name;

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE activity_id = {$activity_id}")
        );
        return $results;
    }

    function insert_into($table_name, $data)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'mycred_index_' . $table_name;

        $_data = array(
            'activity_id' => $data['id'],
            'activity_name' => $data['name'],
            'created_at' => date("Y-m-d H:i:s"));

        $format = array('%d', '%s', '%s');

        $wpdb->insert($table, $_data, $format);


        $inserted_id = $wpdb->insert_id;

        return $inserted_id;
    }

    function update_table($table_name, $data, $where)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'mycred_index_' . $table_name;

        $updated = $wpdb->update($table, $data, $where);

        if (false === $updated) {
            return 'There was an error';
        }

        return true;
    }

    function delete_rows_by($table_name, $column)
    {
        if (!empty($column)) {

            global $wpdb;
            $table = $wpdb->prefix . 'mycred_index_' . $table_name;

            $wpdb->delete($table, $column);

        }
    }

    function delete_row_by_id($table_name, $id)
    {
        if (!empty($id)) {

            global $wpdb;
            $table = $wpdb->prefix . 'mycred_index_' . $table_name;

            $wpdb->delete($table, array('id' => $id));

        }
    }
}