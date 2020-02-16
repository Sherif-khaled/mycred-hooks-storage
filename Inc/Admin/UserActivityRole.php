<?php


namespace MyCredHooksStorage\Admin;


class UserActivityRole
{
    function register(){
        add_action('wp_enqueue_scripts',array($this,'checkUserRole'));
    }

    function test()
    {
        $books = $this->get_user_books_activities_roles(9);

        $games = $this->get_user_games_activities_roles(9);

        var_dump($games);

    }
    function checkUserRole(){
        $id = get_current_user_id();

        if(is_page('books')){
            $books = $this->get_user_books_activities_roles($id);
            if(!$books || !is_user_logged_in()){
                $this->hide_book_activities();
            }
            else{
                $this->hide_book_activities_by_id($books);
            }
        }
        elseif(is_page('games')){
            $games = $this->get_user_games_activities_roles($id);
            if(!$games || !is_user_logged_in()){
                $this->hide_game_activities();
            }
            else{
                $this->hide_game_activities_by_id($games);
            }
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
        return json_decode(json_encode($books), true);
    }
    function get_user_games_activities_roles($user_id)
    {
        global $wpdb;
        $roles_table = $wpdb->prefix . 'mycred_user_activities_roles';
        $games_table = $wpdb->prefix . 'mycred_index_games';

        $books = $wpdb->get_results("SELECT b.activity_id,b.activity_name,a.allow 
                                            FROM {$games_table} AS b INNER JOIN {$roles_table} AS a 
                                            ON b.id = a.activity_id WHERE a.user_id = {$user_id}");
        return json_decode(json_encode($books), true);
    }

    function check_user_activities_exist($user_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'mycred_user_activities_roles';

        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE user_id = {$user_id}"));
        return $results;
    }

    function hide_book_activities()
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
                        $(book).closest('.elementor-top-column').remove();
                        // let bookClone = book.cloneNode(true);
                        // $(bookClone).bind('click',function () {
                        //     alert('test')
                        // });
                        //
                        // book.parentNode.replaceChild(bookClone, book);


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
                let book_id = '<?php echo json_encode($book_id);?>';
                book_id = JSON.parse(book_id);
                setTimeout(() => {

                    function extractBookId(str) {
                        if(str === null){
                            return;
                        }
                        return str.substring(
                            str.lastIndexOf("-") + 1,
                            str.lastIndexOf("_")
                        );
                    }

                    book_id = book_id.map(function(a) {
                        return parseInt(a.activity_id);
                    });

                    let books_array = Array.from($('div[class*=real3dflipbook]'));

                    let filter = jQuery.grep(books_array, function (value) {
                        let _book_id = extractBookId($(value).attr('class'));

                        if(book_id.indexOf(parseInt(_book_id)) === -1){
                            return value;
                        }
                    });

                    Array.from(filter).forEach(book => {
                        $(book).closest('.elementor-top-column').remove();

                        // let bookClone = book.cloneNode(true);
                        // $(bookClone).bind('click',function () {
                        //
                        // });
                        //
                        // book.parentNode.replaceChild(bookClone, book);

                    });
                }, 1000)


            });
        </script>
        <?php
    }
    function hide_game_activities()
    {
        if (!is_page('games')) return;

        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <script type="application/javascript">

            jQuery(document).ready(function ($) {

                setTimeout(() => {
                    games = $('div[class*=zadgame]');
                    console.log(games);
                    Array.from(games).forEach(game => {
                        $(game).closest('.elementor-top-column').remove();
                    });
                },1000)


            });
        </script>
        <?php
    }
    function hide_game_activities_by_id($game_id)
    {
        if (!is_page('games')) return;

        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <script type="application/javascript">

            jQuery(document).ready(function ($) {
                let game_id = '<?php echo json_encode($game_id);?>';
                game_id = JSON.parse(game_id);
                setTimeout(() => {
                    function extractGameId(elem) {

                        let check = "zadgame-";
                        let classes = $(elem).attr("class").split(/\s+/);
                        let id = 0;
                            $.each(classes, function (index, item) {
                                if (item.indexOf(check) === 0)
                                    id = parseInt(item.substr(check.length), 10);
                            });

                            return id;

                    }

                    game_id = game_id.map(function(a) {
                        return parseInt(a.activity_id);
                    });

                    let games_array = Array.from($('div[class*=zadgame]'));

                    let filter = jQuery.grep(games_array, function (value) {
                        let _game_id = extractGameId(value);

                        if(game_id.indexOf(parseInt(_game_id)) === -1){
                            return value;
                        }
                    });

                    Array.from(filter).forEach(game => {
                        $(game).closest('.elementor-top-column').remove();
                    });
                }, 1000)


            });
        </script>
        <?php
    }

}