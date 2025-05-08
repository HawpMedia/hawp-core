<?php
/**
 * Theme Core Functionality
 *
 * @package HAWP
 */

namespace HAWP\Core;

class Theme {
	/**
	 * Theme instance.
	 *
	 * @var Theme
	 */
	private static $instance = null;

	/**
	 * Get theme instance.
	 *
	 * @return Theme
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		add_action('after_setup_theme', [$this, 'setup_theme']);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
	}

	/**
	 * Theme setup.
	 */
	public function setup_theme() {
		// Add theme support
		add_theme_support('title-tag');
		add_theme_support('post-thumbnails');
		add_theme_support('html5', [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		]);
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {
		// Enqueue your styles and scripts here
		wp_enqueue_style('hawp-style', get_stylesheet_uri(), [], HM_THEME_VERSION);
	}
}

// Initialize the theme
Theme::get_instance();
