<?php


namespace MyCredHooksStorage\Admin;


class UserActivityRole
{
    function register(){
        add_action('wp_enqueue_scripts',array($this,'checkUserRole'));
    }
    function checkUserRole(){
        $id = get_current_user_id();
        $activity_exist = $this->get_row_by_user_id($id);

        if(!$activity_exist){
            $this->hide_book_activities();
        }



    }
    function get_row_by_user_id($user_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'mycred_user_activities_roles';

        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE user_id = {$user_id}"));
        return $results;
    }
    function hide_book_activities(){
        if (!is_page('books')) return;

        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <script type="application/javascript">

            jQuery(document).ready(function ($) {

                setTimeout(() => {
                    books = $('div[class*=real3dflipbook]');
                    console.log(books);
                    Array.from(books).forEach(book => {
                        //$(book).closest('.elementor-widget-container').css('display','none');
                        let bookClone = book.cloneNode(true);
                        $(bookClone).bind('click',function () {
                            alert('test')
                        });

                        book.parentNode.replaceChild(bookClone, book);



                    });
                },1000)


            });
        </script>
        <?php
    }
}