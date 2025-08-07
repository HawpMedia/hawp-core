<?php
// ------------------------------------------
// Theme setup functions.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!class_exists('Hawp_Theme_Setup')):

class Hawp_Theme_Setup {

	/**
	 * Constructor.
	 */
	public function setup() {
		add_action('after_setup_theme', [$this, 'theme_setup']);
		add_action('init', [$this, 'register_scripts']);
		add_filter('widget_text', 'do_shortcode');
		add_action('widgets_init', [$this, 'widgets_init']);
		add_filter('wp_nav_menu_items', 'do_shortcode');
		add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts'], 99999);
		add_action('wp_head', [$this, 'add_head_code']);
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		add_action('wp_body_open', [$this, 'add_body_code']);
		add_action('wp_footer', [$this, 'add_footer_code']);
		add_filter('body_class', [$this, 'add_featured_image_body_class']);
		add_action('wp_print_styles', [$this, 'enqueue_gutenberg_style'], 100);
		remove_action('wp_print_styles', 'print_emoji_styles');
		add_filter('tiny_mce_before_init', [$this, 'override_mce_options'], 99);
		add_action('wp_default_scripts', [$this, 'enqueue_jquery_migrate']);
		add_post_type_support('page', 'excerpt');
		add_filter('excerpt_more', [$this, 'excerpt_more']);
		add_filter('get_the_excerpt', 'do_shortcode');
		add_filter('get_the_archive_title', [$this, 'remove_archive_title']);
		add_action('pre_get_posts', [$this, 'num_posts_per_page']);
		add_filter('excerpt_length', [$this, 'adjust_excerpt_length']);
		add_filter('gform_tabindex', '__return_false');
		remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
		remove_action('wp_body_open', 'gutenberg_global_styles_render_svg_filters');
	}

	/**
	 * Set up theme.
	 */
	public function theme_setup() {
		load_theme_textdomain('hawp');
		add_theme_support('title-tag');
		add_theme_support('automatic-feed-links');
		add_theme_support('post-thumbnails');
		add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
		add_theme_support('editor-styles');
		add_theme_support('wp-block-styles');
		add_post_type_support('page', 'excerpt');
		register_nav_menus(apply_filters('hawp_menus', [
			'primary' => 'Primary Menu',
			'top' => 'Top Menu',
			'secondary' => 'Footer Menu',
			'social' => 'Social Menu',
			'copyright' => 'Copyright Menu',
		]));
	}

	/**
	 * Register scripts and styles.
	 */
	public function register_scripts() {
		// Lity - modal popups
		wp_register_script('hm-lity-script', HM_URL.'/assets/lib/lity/2.4.0/lity.min.js', ['jquery'], null, true);
		wp_register_style('hm-lity-style', HM_URL.'/assets/lib/lity/2.4.0/lity.min.css');

		if (get_theme_option('enqueue_lity_styles_scripts') == true) {
			wp_enqueue_script('hm-lity-script');
			wp_enqueue_style('hm-lity-style');
		}

		// Swiper - carousel slider
		wp_register_script('hm-swiper-script', HM_URL.'/assets/lib/swiper/8.1.4/swiper-bundle.min.js', [], null, true);
		wp_register_style('hm-swiper-style', HM_URL.'/assets/lib/swiper/8.1.4/swiper-bundle.min.css');

		if (get_theme_option('enqueue_swiper_styles_scripts') == true) {
			wp_enqueue_script('hm-swiper-script');
			wp_enqueue_style('hm-swiper-style');
		}

		// Owl - carousel slider
		wp_register_script('hm-owl-script', HM_URL.'/assets/lib/owl/2.3.4/owl.carousel.min.js', ['jquery'], null, true);
		wp_register_style('hm-owl-style', HM_URL.'/assets/lib/owl/2.3.4/owl.carousel.min.css');
		wp_register_style('hm-owl-theme-style', HM_URL.'/assets/lib/owl/2.3.4/owl.theme.default.min.css');
		if (get_theme_option('enqueue_owl_styles_scripts') == true) {
			wp_enqueue_script('hm-owl-script');
			wp_enqueue_style('hm-owl-style');
			wp_enqueue_style('hm-owl-theme-style');
		}

		// Select2 - a better select field
		wp_register_script('hm-select2-script', HM_URL.'/assets/lib/select2/4.0.13/select2.min.js', ['jquery'], null, true);
		wp_register_style('hm-select2-style', HM_URL.'/assets/lib/select2/4.0.13/select2.min.css');
		if (get_theme_option('enqueue_select2_styles_scripts') == true) {
			wp_enqueue_script('hm-select2-script');
			wp_enqueue_style('hm-select2-style');
		}

		// Litepicker - date range picker
		wp_register_script('hm-litepicker-script', HM_URL.'/assets/lib/litepicker/2.0.12/litepicker.min.js', [], null, true);
		wp_register_style('hm-litepicker-style', HM_URL.'/assets/lib/litepicker/2.0.12/litepicker.min.css');
		if (get_theme_option('enqueue_litepicker_styles_scripts') == true) {
			wp_enqueue_script('hm-litepicker-script');
			wp_enqueue_style('hm-litepicker-style');
		}

		// Mixitup - ajax tabs
		wp_register_script('hm-mixitup-script', HM_URL.'/assets/lib/mixitup/3.3.1/mixitup.min.js', [], null, true);
		wp_register_script('hm-mixitup-pagination-script', HM_URL.'/assets/lib/mixitup/3.3.1/mixitup-pagination.min.js', [], null, true);
		if (get_theme_option('enqueue_mixitup_styles_scripts')) {
			wp_enqueue_script('hm-mixitup-script');
			wp_enqueue_script('hm-mixitup-pagination-script');
		}

		// Font Awesome - versions 5-6
		wp_register_style('hm-fontawesome-5-style', HM_URL.'/assets/lib/fontawesome/5.15.4/css/all.min.css');
		wp_register_style('hm-fontawesome-6-style', HM_URL.'/assets/lib/fontawesome/6.5.1/css/all.min.css');
		if (get_theme_option('enqueue_fontawesome_5_style') == true) {
			wp_enqueue_style('hm-fontawesome-5-style');
		}
		if (get_theme_option('enqueue_fontawesome_6_style') == true) {
			wp_enqueue_style('hm-fontawesome-6-style');
		}
	}

	/**
	 * Set up theme widgets.
	 */
	public function widgets_init() {
		register_sidebar([
			'name' => esc_html__('Footer', 'hawp'),
			'id' => 'footer',
			'description' => esc_html__('Add widgets here to appear in your footer.', 'hawp'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		]);
		register_sidebar([
			'name' => esc_html__('Site Sidebar', 'hawp'),
			'id' => 'site',
			'description' => esc_html__('Add widgets here to appear in your site sidebar.', 'hawp'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		]);
		register_sidebar([
			'name' => esc_html__('Blog Sidebar', 'hawp'),
			'id' => 'blog',
			'description' => esc_html__('Add widgets here to appear in your blog sidebar.', 'hawp'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		]);
	}

	/**
	 * Scripts and styles.
	 */
	public function wp_enqueue_scripts() {
		if (is_singular() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}

		$js_deps = ['jquery'];

		// Child theme script
		if (file_exists(HMC_PATH.'/assets/js/script.js')) {
			$version = filemtime(HMC_PATH.'/assets/js/script.js');
			wp_register_script('hm-child-script', HMC_URL.'/assets/js/script.js', $js_deps, $version);
			wp_enqueue_script('hm-child-script');
			$js_deps[] = 'hm-child-script';
		}

		// Deprecated: for child theme with older script path
		if (file_exists(HMC_PATH.'/js/script.js')) {
			$version = filemtime(HMC_PATH.'/js/script.js');
			wp_register_script('hm-child-script-old', HMC_URL.'/js/script.js', $js_deps, $version);
			wp_enqueue_script('hm-child-script-old');
			$js_deps[] = 'hm-child-script-old';
		}

		// Localize theme options to hm-child-script
		$theme_data = array();

		// Array of keys to exclude from being passed to JavaScript
		$exclude_keys = array('head_code', 'body_code', 'footer_code', 'svgs', 'acf_migration_completed');

		// Get all theme options dynamically from the database
		$prefix = hawp_theme()::$theme['option_prefix'];
		global $wpdb;
		$options = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s",
				$prefix . '%'
			)
		);

		// Process the options and exclude unwanted ones
		if (!empty($options)) {
			foreach ($options as $option) {
				// Remove the prefix from the key for a cleaner JS object
				$key = str_replace($prefix, '', $option->option_name);
				$value = $option->option_value;

				// Skip any keys that are in the exclude list
				if (in_array($key, $exclude_keys)) {
					continue;
				}

				// Check if the value is numeric and corresponds to a valid post
				if (is_numeric($value)) {
					// Check if it's an image attachment
					if (wp_attachment_is_image($value)) {
						$theme_data[$key] = wp_get_attachment_url($value);
					} 
					// Check if it's a valid post
					elseif (get_post($value)) {
						$theme_data[$key] = get_permalink($value);
					} 
					// Otherwise, just use the value as-is
					else {
						$theme_data[$key] = $value;
					}
				} else {
					$theme_data[$key] = $value;
				}
			}
		}

		// Pass the filtered theme options to the child script.
		wp_localize_script('hm-child-script', 'theme_options', $theme_data);

		// Child theme google fonts
		$css_deps = [];
		$google_fonts = get_theme_option('google_fonts');
		if ($google_fonts) {
			wp_enqueue_style('hm-google-fonts-style', $google_fonts, $css_deps);
			$css_deps[] = 'hm-google-fonts-style';
		}

		// Child theme compiled scss stylesheet
		if (file_exists(HMC_PATH.'/assets/css/compiled.css')) {
			$version = filemtime(HMC_PATH.'/assets/css/compiled.css');
			wp_register_style('hm-child-style-compiled', HMC_URL.'/assets/css/compiled.css', $css_deps, $version);
			wp_enqueue_style('hm-child-style-compiled');
			$css_deps[] = 'hm-child-style-compiled';
		}

		// Deprecated: for child theme with older compiled scss paths
		if (file_exists(HMC_PATH.'/css/compiled.css')) {
			$version = filemtime(HMC_PATH.'/css/compiled.css');
			wp_register_style('hm-child-style-compiled-old', HMC_URL.'/css/compiled.css', $css_deps, $version);
			wp_enqueue_style('hm-child-style-compiled-old');
			$css_deps[] = 'hm-child-style-compiled-old';
		}

		// Child theme stylesheet - this is required so no need to check if it exists
		$version = filemtime(HMC_PATH.'/style.css');
		wp_register_style('hm-child-style', HMC_URL.'/style.css', $css_deps, $version);
		wp_enqueue_style('hm-child-style');
		$css_deps[] = 'hm-child-style';
	}

	public function add_head_code() {
		echo get_theme_option('head_code');
	}

	public function add_body_code() {
		echo get_theme_option('body_code');
	}

	public function add_footer_code() {
		echo get_theme_option('footer_code');
	}

	public function add_featured_image_body_class($classes) {
		// 1) Handle singular posts/pages
		if ( is_singular() ) {
			global $post;
			if ( isset( $post->ID ) && has_post_thumbnail( $post->ID ) ) {
				$classes[] = 'has-featured-image';
			}
		}

		// 2) Handle the designated blog page (the "Posts Page")
		elseif ( is_home() ) {
			$page_for_posts = get_option( 'page_for_posts' );
			if ( $page_for_posts && has_post_thumbnail( $page_for_posts ) ) {
				$classes[] = 'has-featured-image';
			}
		}

		return $classes;
	}

	/**
	 * Gutenberg style option.
	 */
	public function enqueue_gutenberg_style() {
		if (get_theme_option('dequeue_gutenberg_style') == false) {
			wp_dequeue_style('wp-block-library');
			remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
		}
	}

	/**
	 * jQuery Migrate script option.
	 */
	public function enqueue_jquery_migrate($scripts){
		if (get_theme_option('enqueue_jquery_migrate') == false) {
			if (!is_admin() && isset($scripts->registered['jquery'])) {
				$script = $scripts->registered['jquery'];
				if ($script->deps) { // Check whether the script has any dependencies
					$script->deps = array_diff($script->deps, ['jquery-migrate']);
				}
			}
		}
	}

	/**
	 * Adjust excerpt length.
	 */
	public function adjust_excerpt_length($length) {
		if (get_theme_option('excerpt_length')) {
			return esc_attr(get_theme_option('excerpt_length'));
		}
	}

	/**
	 * The excerpt more string at the end of an excerpt.
	 */
	public function excerpt_more($more) {
		return '...';
	}

	/**
	 * Remove archive labels.
	 */
	public function remove_archive_title($title) {
		if (is_category()) {
			$title = single_cat_title('', false);
		} elseif (is_tag()) {
			$title = single_tag_title('', false);
		} elseif (is_author()) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif (is_post_type_archive()) {
			$title = post_type_archive_title('', false);
		} elseif (is_tax()) {
			$title = single_term_title('', false);
		} elseif (is_home()) {
			$title = single_post_title('', false);
		}

		return $title;
	}

	/**
	 * Number of posts per page.
	 */
	public function num_posts_per_page($query = false) {
		if (is_admin()) {
			return;
		}

		if (!is_a($query, 'WP_Query') || !$query->is_main_query()) {
			return;
		}

		if ($query->is_category) {
			$query->set('posts_per_page', (int) get_theme_option('catnum_posts'));
		} elseif ($query->is_tag) {
			$query->set('posts_per_page', (int) get_theme_option('tagnum_posts'));
		} elseif ($query->is_search) {
			$query->set('posts_per_page', (int) get_theme_option('searchnum_posts'));
		} elseif ($query->is_archive) {
			if (function_exists('is_woocommerce') && is_woocommerce()) {
				// Plugin Compatibility :: Skip query->set if "loop_shop_per_page" filter is being used by 3rd party plugins
				if (!has_filter('loop_shop_per_page')) {
					$query->set('posts_per_page', get_theme_option('woocommerce_archive_num_posts'));
				}
			} else {
				$query->set('posts_per_page', get_theme_option('archivenum_posts'));
			}
		}
	}

	/**
	 * Restore image title.
	 */
	public function restore_image_title($html, $id) {
		$attachment = get_post($id);
		if (strpos($html, 'title=')) {
			return $html;
		} else {
			$mytitle = esc_attr($attachment->post_title);
			return str_replace('<img', '<img title="'. $mytitle .'"', $html);
		}
	}

	/**
	 * Restore image title to gallery images.
	 */
	public function restore_title_to_gallery($content, $id) {
		$thumb_title = get_the_title($id);
		return str_replace('<a', '<a title="'. esc_attr($thumb_title) .'" ', $content);
	}

	/**
	 * Stop TinyMCE from modifying WYSIWYG code.
	 */
	public function override_mce_options($initArray) {
		$opts = '*[*]';
		$initArray['valid_elements'] = $opts;
		$initArray['extended_valid_elements'] = $opts;
		return $initArray;
	}

}

/**
 * Initialize Hawp_Theme_Setup class with a function.
 */
function hawp_theme_setup() {
	global $hawp_theme_setup;

	// Instantiate only once.
	if (!isset($hawp_theme_setup)) {
		$hawp_theme_setup = new Hawp_Theme_Setup();
		$hawp_theme_setup->setup();
	}
	return $hawp_theme_setup;
}
hawp_theme_setup();

endif; // class_exists check
