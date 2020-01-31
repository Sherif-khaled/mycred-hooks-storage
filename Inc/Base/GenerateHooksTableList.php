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


use WP_List_Table;

class GenerateHooksTableList extends WP_List_Table
{

    private $hook = null;

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     * @param $hook
     */
    function __construct($hook)
    {
        parent::__construct(array(
            'singular' => 'wp_list_hook_activity', //Singular label
            'plural' => 'wp_list_hook_activity', //plural label, also this well be one of the table css class
            'ajax' => false //We won't support Ajax for this table
        ));
        $this->hook = $hook;
    }

    /**
     * Add extra markup in the toolbars before or after the list
     * @param string $which , helps you decide if you add the markup after (bottom) or before (top) the list
     */
    function extra_tablenav($which)
    {
        if ($which == "top") {
            //The code that goes before the table is here
            echo "<h1>" . ucfirst(strtolower($this->hook)) . " Activities List</h1>";
        }

    }

    /**
     * Decide which columns to activate the sorting functionality on
     * @return array $sortable, the array of columns that can be sorted by the user
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'activity_id' => array('activity_id', false),
            'activity_name' => array('activity_name', true),
            'created_at' => array('created_at', false)

        );
        return $sortable_columns;
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $per_page = 5;
        $current_page = $this->get_pagenum();
        $db = new Database();
        $rows = $db->get_rows($this->hook);
        $rows = json_decode(json_encode($rows), true);
        $total_items = count($rows);

        // only ncessary because we have sample data
        $this->items = array_slice($rows, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page' => $per_page                     //WE have to determine how many items to show on a page
        ));

        // $this->items = $this->example_data;

    }

    /**
     * Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     */
    function get_columns()
    {
        return $columns = array(
            //  'cb'            => '<input type="checkbox" />',
            'activity_id' => __('Activity ID'),
            'activity_name' => __('Activity Name'),
            'created_at' => __('Created At.'),

        );
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'activity_id':
            case 'activity_name':
            case 'created_at':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }
    /**
     * Display the rows of records in the table
     * @param $item
     * @return string, echo the markup of the rows
     */
//    function display_rows() {
//
//        //Get the records registered in the prepare_items method
//        $records = $this->items;
//
//        //Get the columns registered in the get_columns and get_sortable_columns methods
//        list( $columns, $hidden ) = $this->get_column_info();
//
//        //Loop for each record
//        if(!empty($records)){foreach($records as $rec){
//
//            //Open the line
//            echo '< tr id="record_'.$rec->id.'">';
//            foreach ( $columns as $column_name => $column_display_name ) {
//
//                //Style attributes for each col
//                $class = "class='$column_name column-$column_name'";
//                $style = "";
//                if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
//                $attributes = $class . $style;
//
//                //edit link
//                $editlink  = '/wp-admin/link.php?action=edit&link_id='.(int)$rec->id;
//
//                //Display the cell
//                switch ( $column_name ) {
//                    case "col_id":  echo '< td '.$attributes.'>'.stripslashes($rec->id).'< /td>';   break;
//                    case "col_activity_id": echo '< td '.$attributes.'>'.stripslashes($rec->activity_id).'< /td>'; break;
//                    case "col_activity_name": echo '< td '.$attributes.'>'.stripslashes($rec->activity_name).'< /td>'; break;
//                    case "col_created_at": echo '< td '.$attributes.'>'.$rec->created_at.'< /td>'; break;
//                }
//            }
//
//            //Close the line
//            echo'< /tr>';
//        }}
//    }
//    function column_activity_id( $item ) {
//
//        // create a nonce
//        $delete_nonce = wp_create_nonce( 'sp_delete_activity' );
//
//        $title = '<strong>' . $item['activity_id'] . '</strong>';
//
//        $actions = [
//            'delete' => sprintf( '<a href="?page=%s&action=%s&id=%s&hook=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ),$this->hook, $delete_nonce )
//        ];
//
//        return $title . $this->row_actions( $actions );
//    }
    /**
     * @param object $item
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="book[]" value="%s" />', $item['id']
        );
    }

//    function get_bulk_actions()
//    {
//        $actions = array(
//            'bulk-delete' => 'Delete'
//        );
//        // return $actions;
//    }
}