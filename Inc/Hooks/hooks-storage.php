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
if (!defined('ABSPATH')) exit;
if (!defined('myCRED_VERSION')) exit;

function points_for_reading_books()
{
    class myCRED_Reading_Books extends myCRED_Hook
    {

        /**
         * Construct
         * @param $hook_prefs
         * @param $type
         */
        function __construct($hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY)
        {

            parent::__construct(array(
                'id' => 'hook_reading_book',
                'defaults' => array(
                    'limit_by' => 'none',
                    'creds' => 1,
                    'log' => '%plural% for reading book to: %url%',
                    'logic' => 'quarter',
                    'duration' => 8,

                )
            ), $hook_prefs, $type);

        }

        function run()
        {

            if (!is_user_logged_in()) return;

            add_action('wp_enqueue_scripts', array($this, 'register_script'));
            add_action('wp_ajax_reading_book', array($this, 'reading_book'));
            add_action('wp_ajax_nopriv_reading_book', array($this, 'reading_book'));
            add_filter('hooks_menu', array($this, 'test'));
        }

        function test($hooks_menu)
        {
            $hooks_menu[] = array(array(
                'page_name' => 'Games Hook',
                'page_slug' => 'games'
            ),);
            return $hooks_menu;
        }

        function register_script()
        {
            if (!is_page('books')) return;

            wp_enqueue_script('mycred-book-points', MHS_JS_PATH . 'mycred-reading-book-hook.js', array('jquery'), false, true);

            $localize = array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'token' => wp_create_nonce("reading_book"),

            );
            wp_localize_script('mycred-book-points', 'myCREDbook', $localize);
        }

        /**
         * Parse Custom Tags in Log
         * @param $content
         * @param $log_entry
         * @return mixed $content
         */
        public function parse_custom_tags($content, $log_entry)
        {

            $data = maybe_unserialize($log_entry->data);
            if (isset($data['book_title']))
                $content = str_replace('%title%', $data['book_title'], $content);

            return $content;
        }

        /**
         * Parse Duration to minutes
         * @param $read_pages
         * @param $page_duration
         * @param $received_duration
         * @return float|int $interval if book interval less than 60 seconds return 0
         */
        public function calculate_read_pages($read_pages, $page_duration, $received_duration)
        {

            $expected_time = intval($page_duration) * intval($read_pages);

            if ($received_duration >= $expected_time) {
                return $read_pages;
            }

            $unread_pages_duration = $expected_time - $received_duration;

            $unread_pages = round($unread_pages_duration / $page_duration);

            $read_pages = $read_pages - $unread_pages;

            return round($read_pages);
        }

        public function reading_book()
        {

            // We must be logged in
            if (!is_user_logged_in()) return;

            // Make sure we only handle our own point type
            //if (!isset($_POST['ctype']) || $_POST['ctype'] != $this->mycred_type || !isset($_POST['book_data'])) return;

            // Security
            check_ajax_referer('reading_book', 'token');

            // Current User
            $user_id = get_current_user_id();

            //prevent multiple simultaneous AJAX calls from any one user
            if (mycred_force_singular_session($user_id, 'mycred-last-book-played'))
                wp_send_json(101);

            // Check if user should be excluded
            if ($this->core->exclude_user($user_id)) wp_send_json(200);

            $data = array(
                'ref_type' => 'book',
                'book_title' => $_POST['book_data']['book_title'],
                'book_id' => $_POST['book_data']['book_id'],
                'total_pages' => $_POST['book_data']['total_pages'],
                'read_pages' => $_POST['book_data']['read_pages'],
                'page_interval' => $_POST['book_data']['page_interval'],
            );

            $points = $this->calculate_points($data);

            $this->core->add_creds(
                'reading_book',
                $user_id,
                $points,
                $this->prefs['log'],
                0,
                $data,
                $this->mycred_type
            );

            $data['eared_points'] = $points; //this will deleted after final test
            $data['book_interval'] = $_POST['book_data']['page_interval'];

            //***************************************************************
            global $wpdb;
            $table_name = $wpdb->prefix . 'mycred_index_books';

            $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$table_name} WHERE activity_id = %s", $data['book_id'])
            );

            $user_activity_table = $wpdb->prefix . 'mycred_user_activities';

            $_data = array(
                'user_id' => $user_id,
                'hook_name' => 'books',
                'activity_id' => intval($data['book_id']),
                'score' => $data['eared_points'],
                'created_at' => date("Y-m-d H:i:s"));

            $format = array('%d', '%s', '%d', '%d', '%s');

            $wpdb->insert($user_activity_table, $_data, $format);

            //**************************************************************

            wp_send_json($data);

        }

        function calculate_quarters($unit)
        {
            $quarter = 0;
            if ($unit < 25) {
                $quarter = 0;
            } elseif ($unit >= 25 && $unit < 50) {
                $quarter = 1;
            } elseif ($unit >= 50 && $unit < 75) {
                $quarter = 2;
            } elseif ($unit >= 75 && $unit < 100) {
                $quarter = 3;
            } elseif ($unit == 100) {
                $quarter = 4;
            }
            return $quarter;
        }

        function calculate_points($data)
        {

            if ((intval($data['read_pages']) == 0) && (intval($data['total_pages']) == 0)) return 0;

            $read_pages = $this->calculate_read_pages(intval($data['read_pages']),
                intval($this->prefs['duration']),
                intval($data['page_interval']));
            //$read_pages = $data['read_pages'];

            $quarter_count = ($read_pages / $data['total_pages']) * 100;

            $quarter_count = $this->calculate_quarters($quarter_count);

            if ($quarter_count == 0) return 0;

            if ($this->prefs['logic'] == 'quarter') {

                $points = $quarter_count * $this->prefs['creds'];

            } elseif ($this->prefs['logic'] == 'half') {

                $points = floor($quarter_count / 2) * $this->prefs['creds'];

            } elseif ($this->prefs['logic'] == 'full') {

                $points = floor($quarter_count / 4) * $this->prefs['creds'];

            } else {
                return 0;
            }

            return $points;
        }

        /**
         * Preference for reading book
         */
        public function preferences()
        {

            $prefs = $this->prefs;

            ?>

            <div class="hook-instance">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label for="<?php echo $this->field_id('creds'); ?>"><?php echo $this->core->plural(); ?></label>
                            <input type="text" name="<?php echo $this->field_name('creds'); ?>"
                                   id="<?php echo $this->field_id('creds'); ?>"
                                   value="<?php echo $this->core->number($prefs['creds']); ?>" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label for="<?php echo $this->field_id('log'); ?>"><?php _e('Log Template', 'mycred'); ?></label>
                            <input type="text" name="<?php echo $this->field_name('log'); ?>"
                                   id="<?php echo $this->field_id('log'); ?>"
                                   placeholder="<?php _e('required', 'mycred'); ?>"
                                   value="<?php echo esc_attr($prefs['log']); ?>" class="form-control"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-9 col-md-6 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label for="<?php echo $this->field_id(array('logic' => 'quarter')); ?>"><?php _e('Award Logic', 'mycred'); ?></label>
                            <div class="checkbox">
                                <label for="<?php echo $this->field_id(array('logic' => 'full')); ?>"><input
                                            type="radio" name="<?php echo $this->field_name('logic'); ?>"
                                            id="<?php echo $this->field_id(array('logic' => 'full')); ?>"<?php checked($prefs['logic'], 'full'); ?>
                                            value="full"
                                            class="toggle-hook-option"/> <?php _e('Full - For each book that has been fully read.', 'mycred'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label for="<?php echo $this->field_id(array('logic' => 'half')); ?>"><input
                                            type="radio" name="<?php echo $this->field_name('logic'); ?>"
                                            id="<?php echo $this->field_id(array('logic' => 'half')); ?>"<?php checked($prefs['logic'], 'half'); ?>
                                            value="half"
                                            class="toggle-hook-option"/> <?php _e('Half - For each half part of the book has been read.', 'mycred'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label for="<?php echo $this->field_id(array('logic' => 'quarter')); ?>"><input
                                            type="radio" name="<?php echo $this->field_name('logic'); ?>"
                                            id="<?php echo $this->field_id(array('logic' => 'score')); ?>"<?php checked($prefs['logic'], 'quarter'); ?>
                                            value="quarter"
                                            class="toggle-hook-option"/> <?php _e('Quarter - For each quarter part of the book has been read.', 'mycred'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label for="<?php echo $this->field_id('duration'); ?>"><?php _e('Duration', 'mycred'); ?></label>
                            <input type="text" name="<?php echo $this->field_name('duration'); ?>"
                                   id="<?php echo $this->field_id('duration'); ?>"
                                   placeholder="<?php _e('required', 'mycred'); ?>"
                                   value="<?php echo $this->core->number($prefs['duration']); ?>" class="form-control"/>
                            <span class="description"><?php printf(__('Duration of the expected completion of the reading of the book page - [Seconds].', 'mycred'), $this->core->plural()); ?></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label><?php _e('Available Shortcode', 'mycred'); ?></label>
                            <p class="form-control-static"><a href="http://codex.mycred.me/shortcodes/mycred_link/"
                                                              target="_blank">[mycred_link]</a></p>
                        </div>
                    </div>
                </div>

            </div>
            <?php

        }
    }

}

function points_for_playing_games_books()
{
    class myCRED_Playing_Games extends myCRED_Hook
    {

        /**
         * Construct
         * @param $hook_prefs
         * @param $type
         */
        function __construct($hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY)
        {

            parent::__construct(array(
                'id' => 'hook_playing_games',
                'defaults' => array(
                    'limit_by' => 'none',
                    'creds' => 1,
                    'log' => '%plural% for playing game to: %url%',
                    'logic' => 'score',
                    'score' => 100,
                    'max-score' => 500,
                    'interval' => '',
                )
            ), $hook_prefs, $type);

        }

        function run()
        {

            if (!is_user_logged_in()) return;

            add_action('wp_enqueue_scripts', array($this, 'register_script'));
            add_action('wp_ajax_playing_game', array($this, 'playing_game'));
            add_action('wp_ajax_nopriv_playing_game', array($this, 'playing_game'));

        }

        function register_script()
        {
            if (!is_page('games')) return;

            wp_enqueue_script('mycred-game-points', MHS_JS_PATH . 'mycred-playing-games-hook.js', array('jquery'), false, true);

            $localize = array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'token' => wp_create_nonce("playing_game"),

            );
            wp_localize_script('mycred-game-points', 'myCREDgame', $localize);
        }

        /**
         * Parse Custom Tags in Log
         * @param $content
         * @param $log_entry
         * @return mixed $content
         */
        public function parse_custom_tags($content, $log_entry)
        {

            $data = maybe_unserialize($log_entry->data);
            if (isset($data['game_title']))
                $content = str_replace('%title%', $data['game_title'], $content);

            return $content;
        }

        /**
         * Parse Duration to minutes
         * @param $interval $interval seconds
         * @return float|int $interval if game interval less than 60 seconds return 0
         */
        public function parse_interval($interval)
        {
            if ($interval < 60) {
                $interval = 0;
            } else {
                $interval = round(($interval / 60));

            }
            return $interval;
        }

        public function playing_game()
        {

            // We must be logged in
            if (!is_user_logged_in()) return;

            // Make sure we only handle our own point type
            if (!isset($_POST['ctype']) || $_POST['ctype'] != $this->mycred_type || !isset($_POST['game_data'])) return;

            // Security
            check_ajax_referer('playing_game', 'token');

            // Current User
            $user_id = get_current_user_id();

            //prevent multiple simultaneous AJAX calls from any one user
            if (mycred_force_singular_session($user_id, 'mycred-last-game-played'))
                wp_send_json(101);

            // Check if user should be excluded
            if ($this->core->exclude_user($user_id)) wp_send_json(200);

            $data = array(
                'ref_type' => 'game',
                'game_title' => $_POST['game_data']['title'],
                'game_id' => $_POST['game_data']['id'],
                'current_score' => $_POST['game_data']['current_score'],
                'game_best_score' => $_POST['game_data']['best_score'],
                'game_interval' => $_POST['game_data']['interval'],
            );

            $points = $this->calculate_points($data);

            $this->core->add_creds(
                'playing_game',
                $user_id,
                $points,
                $this->prefs['log'],
                0,
                $data,
                $this->mycred_type
            );

            $data['eared_points'] = $points; //this will deleted after final test
            $data['game_interval'] = $_POST['game_data']['interval'];

            //***************************************************************
            global $wpdb;
            $table_name = $wpdb->prefix . 'mycred_index_games';

            $results = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$table_name} WHERE activity_id = %s", $data['game_id'])
            );

            $user_activity_table = $wpdb->prefix . 'mycred_user_activities';

            $_data = array(
                'user_id' => $user_id,
                'hook_name' => 'games',
                'activity_id' => $results[0]->id,
                'score' => $data['eared_points'],
                'created_at' => date("Y-m-d H:i:s"));

            $format = array('%d', '%s', '%d', '%d', '%s');

            $wpdb->insert($user_activity_table, $_data, $format);
            //**************************************************************
            wp_send_json($data);

        }

        function calculate_points($data)
        {

            if ($this->prefs['logic'] == 'score') {

                if ($data['current_score'] < $this->prefs['score']) return 0;

                if ($data['current_score'] > $this->prefs['max-score']) $data['current_score'] = $data['current_score'] - $this->prefs['max-score'];

                $points = floor(($data['current_score'] / $this->prefs['score'])) * $this->prefs['creds'];

            } elseif ($this->prefs['logic'] == 'interval') {

                $data['game_interval'] = $this->parse_interval($data['game_interval']);

                if ($data['game_interval'] < $this->prefs['interval']) return 0;

                $points = floor(($data['game_interval'] / $this->prefs['interval'])) * $this->prefs['creds'];

            } elseif ($this->prefs['logic'] == 'game') {

                $points = $this->prefs['creds'];

            } else {
                return 0;
            }

            return $points;
        }

        /**
         * Preference for Playing Games
         */
        public function preferences()
        {

            $prefs = $this->prefs;

            ?>

            <div class="hook-instance">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label for="<?php echo $this->field_id('creds'); ?>"><?php echo $this->core->plural(); ?></label>
                            <input type="text" name="<?php echo $this->field_name('creds'); ?>"
                                   id="<?php echo $this->field_id('creds'); ?>"
                                   value="<?php echo $this->core->number($prefs['creds']); ?>" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label for="<?php echo $this->field_id('log'); ?>"><?php _e('Log Template', 'mycred'); ?></label>
                            <input type="text" name="<?php echo $this->field_name('log'); ?>"
                                   id="<?php echo $this->field_id('log'); ?>"
                                   placeholder="<?php _e('required', 'mycred'); ?>"
                                   value="<?php echo esc_attr($prefs['log']); ?>" class="form-control"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!--	                **********-->
                    <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label for="<?php echo $this->field_id(array('logic' => 'score')); ?>"><?php _e('Award Logic', 'mycred'); ?></label>
                            <div class="checkbox">
                                <label for="<?php echo $this->field_id(array('logic' => 'score')); ?>"><input
                                            type="radio" name="<?php echo $this->field_name('logic'); ?>"
                                            id="<?php echo $this->field_id(array('logic' => 'score')); ?>"<?php checked($prefs['logic'], 'score'); ?>
                                            value="score"
                                            class="toggle-hook-option"/> <?php _e('Score - For each x number of game scores given.', 'mycred'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label for="<?php echo $this->field_id(array('logic' => 'interval')); ?>"><input
                                            type="radio" name="<?php echo $this->field_name('logic'); ?>"
                                            id="<?php echo $this->field_id(array('logic' => 'interval')); ?>"<?php checked($prefs['logic'], 'interval'); ?>
                                            value="interval"
                                            class="toggle-hook-option"/> <?php _e('Interval - For each x number of minutes played.', 'mycred'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label for="<?php echo $this->field_id(array('logic' => 'game')); ?>"><input
                                            type="radio" name="<?php echo $this->field_name('logic'); ?>"
                                            id="<?php echo $this->field_id(array('logic' => 'game')); ?>"<?php checked($prefs['logic'], 'game'); ?>
                                            value="game"
                                            class="toggle-hook-option"/> <?php echo $this->core->template_tags_general(__('Game - For each unique game has been played.', 'mycred')); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12">
                        <div id="<?php echo $this->field_id(array('logic-option-score')); ?>"<?php if ($prefs['logic'] != 'score') echo ' style="display: none;"'; ?>>
                            <div class="form-group">
                                <label for="<?php echo $this->field_id('score'); ?>"><?php _e('Scores', 'mycred'); ?></label>
                                <input type="text" name="<?php echo $this->field_name('score'); ?>"
                                       id="<?php echo $this->field_id('score'); ?>"
                                       placeholder="<?php _e('required', 'mycred'); ?>"
                                       value="<?php echo esc_attr($prefs['score']); ?>" class="form-control"/>
                                <span class="description"><?php printf(__('The number of game scores a user must collected in order to get %s.', 'mycred'), $this->core->plural()); ?></span>
                            </div>
                        </div>
                        <div id="<?php echo $this->field_id(array('logic-option-max-score')); ?>"<?php if ($prefs['logic'] != 'score') echo ' style="display: none;"'; ?>>
                            <div class="form-group">
                                <label for="<?php echo $this->field_id('max-score'); ?>"><?php _e('Max score', 'mycred'); ?></label>
                                <input type="text" name="<?php echo $this->field_name('max-score'); ?>"
                                       id="<?php echo $this->field_id('max-score'); ?>"
                                       placeholder="<?php _e('required', 'mycred'); ?>"
                                       value="<?php echo esc_attr($prefs['max-score']); ?>" class="form-control"/>
                                <span class="description"><?php printf(__('The number of score per game that grants points to the player. Use zero for unlimited. in order to get %s.', 'mycred'), $this->core->plural()); ?></span>
                            </div>
                        </div>
                        <div id="<?php echo $this->field_id(array('logic-option-interval')); ?>"<?php if ($prefs['logic'] != 'interval') echo ' style="display: none;"'; ?>>
                            <div class="form-group">
                                <label for="<?php echo $this->field_id('interval'); ?>"><?php _e('Intervals', 'mycred'); ?></label>
                                <input type="text" name="<?php echo $this->field_name('interval'); ?>"
                                       id="<?php echo $this->field_id('interval'); ?>"
                                       placeholder="<?php _e('required', 'mycred'); ?>"
                                       value="<?php echo esc_attr($prefs['interval']); ?>" class="form-control"/>
                                <span class="description"><?php printf(__('The number of minutes a user must be played in order to get %s.', 'mycred'), $this->core->plural()); ?></span>
                            </div>
                        </div>
                    </div>
                    <!--	                **********-->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label><?php _e('Available Shortcode', 'mycred'); ?></label>
                            <p class="form-control-static"><a href="http://codex.mycred.me/shortcodes/mycred_link/"
                                                              target="_blank">[mycred_link]</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(function ($) {

                    $('#sidebar-active .toggle-hook-option').change(function () {

                        if ($(this).val() === 'score') {
                            $('#<?php echo $this->field_id(array('logic-option-score')); ?>').show();
                            $('#<?php echo $this->field_id(array('logic-option-max-score')); ?>').show();
                            $('#<?php echo $this->field_id(array('logic-option-interval')); ?>').hide();
                        } else if ($(this).val() === 'interval') {
                            $('#<?php echo $this->field_id(array('logic-option-interval')); ?>').show();
                            $('#<?php echo $this->field_id(array('logic-option-score')); ?>').hide();
                            $('#<?php echo $this->field_id(array('logic-option-max-score')); ?>').hide();
                        } else {
                            $('#<?php echo $this->field_id(array('logic-option-interval')); ?>').hide();
                            $('#<?php echo $this->field_id(array('logic-option-score')); ?>').hide();
                            $('#<?php echo $this->field_id(array('logic-option-max-score')); ?>').hide();
                        }

                    })

                })
            </script>
            <?php

        }
    }

}