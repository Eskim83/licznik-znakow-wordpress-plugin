<?php

/**
 * Plugin Name:       Licznik znaków
 * Plugin URI:        https://eskim.pl/wlasna-wtyczka-w-wordpress-na-przykladzie-licznika-znakow/
 * Description:       Liczy znaki w tekście.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            Maciej Włodarczak
 * Author URI:        https://eskim.pl
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       licznik-znakow
 * Domain Path:       /languages
 */

if ( !function_exists( 'add_action' ) ) {

	echo 'Zapraszam do artykułu <a href="https://eskim.pl/wlasna-wtyczka-w-wordpress-na-przykladzie-licznika-znakow//">Własna wtyczka na przykładzie licznika znaków</a>';
	exit;
}


$eskim_pl_post_length = 0;
function eskim_pl_count_post_chars($content) {

    global $eskim_pl_post_length;
    $eskim_pl_post_length = strlen(wp_strip_all_tags($content));
    $add_text = "<hr>".__('Chars count','licznik-znakow').": $eskim_pl_post_length<hr>";
    return $content.$add_text;
}

add_action('plugins_loaded', function () {
	load_plugin_textdomain( 'licznik-znakow', false, basename( dirname( __FILE__ ) ) . '/languages');
});

add_action('init', function () {
   	if (current_user_can('edit_others_posts')) {
		
		add_filter('the_content', 'eskim_pl_count_post_chars');
	} 
	add_shortcode(
	    'eskim_pl_ile_znakow',
       function () {
			    global $eskim_pl_post_length;
			    return $eskim_pl_post_length;
		   }
    );
});

add_action( 'admin_init', function() {

  add_option('eskim_pl_chars_counter_monetize_option', 100);
  add_option('eskim_pl_chars_counter_article_option', false);

	register_setting (
	   'eskim_pl_chars_counter_settings_menu',
	   'eskim_pl_chars_counter_settings'
	);

	add_settings_section (
	   'eskim_pl_chars_counter_settings_section',
	   '<h2>'.__('Section settings').'</h2>',
	   'eskim_pl_chars_counter_settings_section_render',
	   'eskim_pl_chars_counter_settings_menu'
	);

	add_settings_field (
		'eskim_pl_chars_counter_monetize_field',
		__('Value for 1000 chars'),
		'eskim_pl_chars_counter_monetize_fill',
		'eskim_pl_chars_counter_settings_menu ',
		'eskim_pl_chars_counter_settings_section'
	);

	add_settings_field (
		'eskim_pl_chars_counter_article_field',
		__('Value for 1000 chars'),
		'eskim_pl_chars_counter_article_fill',
		'eskim_pl_chars_counter_settings_menu ',
		'eskim_pl_chars_counter_settings_section'
	);
});

add_action('admin_menu', function () {

    add_menu_page(
        __('Chars counter','licznik-znakow'), // tytuł strony
        __('Chars counter','licznik-znakow'), // tytuł w menu
        'manage_options',  // widoczne tylko osobom, które mogą zmieniać ustawienia
        'menu-licznik-znakow', // identyfikator
        'eskim_pl_all_posts_chars_counter_info', // funkcja, którą zostanie wywołana po kliknięciu
        '',               // link do ikony
        20                // pozycja w menu
    );

    add_submenu_page(
        'menu-licznik-znakow',          // menu do którego się podpinasz
        __('Chars counter','licznik-znakow').' - '.__('statistics','licznik-znakow'), // tytuł strony
        __('Counter table','licznik-znakow'),  // tytuł w menu
        'manage_options',               // widoczne tylko osobom, które mogą zmieniać ustawienia
        'submenu-licznik-znakow-statystyki', // identyfikator
        'eskim_pl_count_all_posts_chars_render', // funkcja, którą zostanie wywołana po kliknięciu
		);

    add_options_page(
    		__('Settings','licznik-znakow'),
    		__('Chars counter','licznik-znakow'),
    		'manage_options',
    		'eskim_pl_chars_counter_settings_menu',
        'eskim_pl_chars_counter_settings_page_render'
    );	
});

function eskim_pl_chars_counter_settings_page_render() {
   do_settings_sections('eskim_pl_chars_counter_settings_menu');
}

function eskim_pl_chars_counter_settings_section_render() {
   _e('<h2>Różne ustawienia</h2>');
}

function eskim_pl_chars_counter_monetize_fill() {
   ?>
   <input type="number" value="
   <?php echo get_option('eskim_pl_chars_counter_monetize_option'); ?>
   />
   <?php
}

function eskim_pl_chars_counter_article_fill() {
   ?>
   <input type="checkbox" value="
   <?php echo get_option('eskim_pl_chars_counter_article_option'); ?>
   />
   <?php
}

function eskim_pl_all_posts_chars_counter_info() {
    ?>
    <div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
    <hr class="wp-header-end">
    <p>Wtyczka liczy znaki w artykułach</p>
    </div>
    <?php
    }

function eskim_pl_count_all_posts_chars_render() {
		
    if (!current_user_can( 'manage_options' )) return;
    ?>
    <div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
    <hr class="wp-header-end">
    <?php
    $params = array(
        'numberposts' => -1
    );
    $posts = get_posts($params);

    $all_post_count = 0;
    $all_excerp_count = 0;
    $all_articles_count = 0;
    echo '<h2>Ilości znaków</h2>';
    $posts_list_table = '
        <table class="wp-list-table widefat fixed striped">
        <thead><tr>
            <th>Tytuł</th>
            <th>Artykuł</th>
            <th>Zajawka</th>
        </tr></thead><tbody>';
    foreach ($posts as $post) {
					
        $content_post_count = strlen(wp_strip_all_tags($post->post_content));
        if ($content_post_count < 1) continue;
        $all_post_count += $content_post_count;
        $excerp_count = strlen(wp_strip_all_tags($post->post_excerpt));
        $all_excerp_count += $excerp_count;
        $posts_list_table .= "
            <tr>
                <td>$post->post_title</td>
                <td>$content_post_count</td>
                <td>$excerp_count</td>
            </tr>
        ";
        $all_articles_count++;
    }
    $posts_list_table .= "
        </tbody><tfoot><tr>
            <td>$all_articles_count</td>
            <td>$all_post_count</td>
            <td>$all_excerp_count</td>
        </tr></tbody></table>
    ";
    echo $posts_list_table;
    ?>
     </div>
<?php
}

?>