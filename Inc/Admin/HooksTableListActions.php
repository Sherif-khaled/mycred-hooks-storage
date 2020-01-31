<?php


namespace MyCredHooksStorage\Admin;


use MyCredHooksStorage\Base\Database;

class HooksTableListActions extends Database
{
    function register()
    {

        add_action('wp_ajax_r3d_save', array($this, 'insert_book_activity'));
        add_action('wp_ajax_nopriv_r3d_save', array($this, 'insert_book_activity'));

        add_action('wp_ajax_r3d_delete', array($this, 'delete_book_activity'));
        add_action('wp_ajax_nopriv_r3d_delete', array($this, 'delete_book_activity'));

        add_action('save_post_elementor_library', array($this, 'insert_game_activity'), 10, 2);
        add_action('trashed_post', array($this, 'delete_games_activity'));
        add_action('after_delete_post', array($this, 'delete_games_activity'));


        add_action('wp_ajax_add_new_activity', array($this, 'add_new_activity'));
        add_action('wp_ajax_nopriv_add_new_activity', array($this, 'add_new_activity'));

        add_action('wp_ajax_delete_activity', array($this, 'delete_activity'));
        add_action('wp_ajax_nopriv_delete_activity', array($this, 'delete_activity'));
    }

    function insert_game_activity($post_id, $post)
    {

        if (wp_is_post_revision($post_id))
            return;

        if (!get_post_type($post_id) == 'elementor_library') {
            return;
        }

        if (!get_post_status($post_id) == 'publish') {
            return;
        }

        if (strstr($post->post_content, '[embedx') == false) {
            return;
        }

        $db = new Database();

        $game = $db->get_row_by_activity_id('games', intval($post_id));

        if (count($game) !== 0) {

            $data = ['activity_name' => $post->post_title];

            $where = ['activity_id' => $post_id];

            $db->update_table('games', $data, $where);
        } else if (count($game) == 0) {

            $data = array('id' => $post_id, 'name' => $post->post_title);

            $db->insert_into('games', $data);
        }
    }

    function delete_games_activity($post_id)
    {

        $post_type = get_post_type($post_id);

        if ($post_type == 'elementor_library') {

            $db = new Database();

            $db->delete_rows_by('games', array('activity_id' => intval($post_id)));

        }

    }

    function insert_book_activity()
    {

        $db = new Database();

        $book = $db->get_row_by_activity_id('books', intval($_POST['id']));

        if (count($book) !== 0) {

            $data = ['activity_name' => $_POST['name']];

            $where = ['activity_id' => $_POST['id']];

            $db->update_table('books', $data, $where);

        } else if (count($book) == 0) {

            $data = array('id' => intval($_POST['id']), 'name' => $_POST['name']);

            $db->insert_into('books', $data);
        }

    }

    function delete_book_activity()
    {

        $current_id = sanitize_text_field($_POST['currentId']);

        $ids = explode(',', $current_id);

        $db = new Database();

        foreach ($ids as $id) {

            $db->delete_rows_by('books', array('activity_id' => intval($id)));
        }
    }

    function add_new_activity()
    {
        if (!$_REQUEST['hook_data']['hook_name']) {
            wp_send_json('hook name not defined');

        }
        $insertedID = self::insert_into($_REQUEST['hook_data']['hook_name'], $_REQUEST['hook_data']);

        if (!$insertedID) {
            wp_send_json('Filed Inserting');

        }
        $last_row = self::get_row_by_id($_REQUEST['hook_data']['hook_name'], $insertedID);

        wp_send_json($last_row{0});

    }

    function delete_activity()
    {

        if (!$_REQUEST['hook_data']['hook_name']) {
            wp_send_json('hook name not defined');

        }


        $nonce = esc_attr($_REQUEST['_wpnonce']);

        if (!wp_verify_nonce($nonce, 'sp_delete_activity')) {
            die('Go get a life script kiddies');
        } else {
            self::delete_row_by_id($_REQUEST['hook_data']['hook_name'], absint($_REQUEST['hook_data']['id']));

            wp_send_json('success');

        }


    }
}