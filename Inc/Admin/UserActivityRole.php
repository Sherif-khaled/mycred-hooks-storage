<?php


namespace MyCredHooksStorage\Admin;


class UserActivityRole
{
    function register(){
        add_action('wp_enqueue_scripts',array($this,'checkUserRole'));
    }

    function test()
    {
        var_dump(get_current_user_id());

    }
    function checkUserRole(){
        $id = get_current_user_id();
        $activity_exist = $this->check_user_activities_exist($id);

        if(!$activity_exist){
            $this->hide_book_activities();
        } else {
            $this->hide_book_activities_by_id(1);
        }


    }

    function get_user_books_activities_roles($user_id)
    {
        global $wpdb;
        $roles_table = $wpdb->prefix . 'mycred_user_activities_roles';
        $books_table = $wpdb->prefix . 'mycred_index_books';

        $books = $wpdb->get_results("SELECT b.activity_id,b.activity_name,a.allow 
                                            FROM {$books_table} AS b INNER JOIN {$roles_table} AS a 
                                            ON b.id = a.activity_id WHERE a.user_id = {$user_id}");
        return $books;
    }

    function get_user_games_activities_roles($user_id)
    {

    }

    function check_user_activities_exist($user_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'mycred_user_activities_roles';

        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE user_id = {$user_id}"));
        return $results;
    }

    function allow_book_activities()
    {
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

    function hide_book_activities_by_id($book_id)
    {
        if (!is_page('books')) return;

        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <script type="application/javascript">

            jQuery(document).ready(function ($) {
                let book_id = '<?php echo $book_id;?>';
                setTimeout(() => {
                    let books_array = $('div[class*=real3dflipbook]');
                    console.log(books_array);
                    book = $('div[class*=real3dflipbook-' + book_id + ']');
                    console.log()
                    // Array.from(books).forEach(book => {
                    //     let bookClone = book.cloneNode(true);
                    //     $(bookClone).bind('click',function () {
                    //         alert(book_id)
                    //     });
                    //
                    //     book.parentNode.replaceChild(bookClone, book);
                    //
                    //
                    //
                    // });
                }, 1000)


            });
        </script>
        <?php
    }
}