<?php

/**
 * Plugin Name:       Prosty licznik znaków od eskim.pl
 * Plugin URI:        https://eskim.pl/kurs-tworzenia-wtyczek-w-wordpress/
 * Description:       Przykład tworzenia wtyczek w WordPress na podstawie kursu https://eskim.pl/kurs-tworzenia-wtyczek-w-wordpress/
 * Version:           1.01
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            Maciej Włodarczak
 * Author URI:        https://eskim.pl
 * License:           GPL v2 or later
 * Text Domain:       prosty-licznik-znakow-eskim-pl
 * Domain Path:       /languages
 */
 
define('ESKIM_PL_IMAGES_URL', plugin_dir_url(__FILE__).'images/');

if (!function_exists('eskim_pl_admin_menu_render')) :
function eskim_pl_admin_menu_render() {
	
	?>
		<div class="notice notice-warning">
		<h1 class="wp-heading-inline">Witaj!</h1>
		<p>To jest demonstracyjna wersja wtyczki zbudowana na podstawie <a href="https://eskim.pl/kurs-tworzenia-wtyczek-w-wordpress/">Kursu tworzenia wtyczek w WordPress</a></p>
		<p>Jeżeli przydał Ci się skrypt lub masz jakiekolwiek uwagi wejdź na stronę i zostaw komentarz (nie trzeba się rejestrować). Będzie mi bardzo miło.</p>
		<p>Będzie mi jeszcze milej, jeżeli zostawisz link do powyższej strony lub artykułu.</p>
		</div>
	<?php 
}
endif;

if (!function_exists('eskim_pl_add_menu_eskim')) :
function eskim_pl_add_menu_eskim() {

	if (function_exists('eskim_pl_admin_menu_render'))
	add_action('admin_menu', function () {
		
		add_menu_page(
			'Eskim',
			'Eskim',
			'manage_options',
			'eskim',
			'eskim_pl_admin_menu_render',
			ESKIM_PL_IMAGES_URL.'icon16.png',
			1
		);
	});
}
eskim_pl_add_menu_eskim();
endif;


// statsy
if (!function_exists('eskim_pl_admin_statystyki_render')) :
function eskim_pl_admin_statystyki_render() {
	
	if (!current_user_can( 'manage_options' )) return;
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
		<hr class="wp-header-end">
		<?php
		
			$post_params = array(
				'numberposts' => -1,
				'order' => 'ASC',
				'orderby' => 'title',
				'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit') 
			);

			$all_post_chars_count = 0;
			$all_excerp_chars_count = 0;
			$all_articles_count = 0;
			$all_text_value = 0;
			$all_text_chars_count = 0;
			$category_count = 0;
			echo '<h2>Ilości znaków</h2>';
			$posts_list_table = '
				<table class="searchable sortable wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th>Kategoria</th>
							<th>Tytuł</th>
							<th>Status</th>
							<th>Artykuł</th>
							<th>Zajawka</th>
							<th>Suma</th>
						</tr>
					</thead>
				<tbody>';
			
			$terms = get_terms(array(
				'order' => 'ASC',
				'orderby' => 'name'
			));
			
			foreach ($terms as $term) {
				
				$category_name = $term->name;
				$category_count++;
				$post_params['category'] = $term->term_id;
				$posts = get_posts($post_params);
				foreach ($posts as $post) {
				
					$content_post_chars_count = strlen(wp_strip_all_tags($post->post_content));
					if ($content_post_chars_count < 1) continue;
					$all_post_chars_count += $content_post_chars_count;
					$excerp_chars_count = strlen(wp_strip_all_tags($post->post_excerpt));
					$text_chars_count = $content_post_chars_count+$excerp_chars_count;
					$all_text_value += $value;
					$all_text_chars_count += $text_chars_count;
					$all_excerp_chars_count += $excerp_chars_count;
				
					$posts_list_table .= "<tr><td>$category_name</td><td>$post->post_title</td><td>$post->post_status</td><td>$content_post_chars_count</td><td>$excerp_chars_count</td><td>$text_chars_count</td></tr>";
					$all_articles_count++;
				}
			}
			
			$all_text_value_formated = number_format($all_text_value,2);
			$posts_list_table .= "</tbody><tfoot><tr><td>$category_count</td><td>$all_articles_count</td><td>-</td><td>$all_post_chars_count</td><td>$all_excerp_chars_count</td><td>$all_text_chars_count</td></tr></tbody></table>";
			echo $posts_list_table;
		?>
	</div>
	<?php
}
endif;

if (!function_exists('eskim_pl_add_submenu_statystyki')) :
function eskim_pl_add_submenu_statystyki() {
		
	if (function_exists('eskim_pl_admin_statystyki_render')) {
		add_action('admin_menu', function () {
			
			add_submenu_page(
		
				'eskim',
				'Statystki',
				'Statystki',
				'manage_options',
				'eskim-statystyki',
				'eskim_pl_admin_statystyki_render'
			);
		});
		
	}
}
eskim_pl_add_submenu_statystyki();
endif;


 ?>