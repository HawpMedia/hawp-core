<?php
// ------------------------------------------
// Hawp Core - Lightweight GitHub Theme Updater (no third-party libs)
// ------------------------------------------

if (!class_exists('Hawp_Github_Theme_Updater')):

class Hawp_Github_Theme_Updater {

	/**
	 * Initialize hooks.
	 */
	public static function init($unused = null) {
		// Allow configuration via constants or filter.
		$config = self::get_config();
		if (empty($config['repo'])) {
			// No repo configured; do not register hooks.
			return;
		}

		add_filter('pre_set_site_transient_update_themes', [__CLASS__, 'check_for_update']);
		add_filter('themes_api', [__CLASS__, 'themes_api'], 10, 3);
		add_filter('upgrader_source_selection', [__CLASS__, 'ensure_correct_directory_name'], 10, 4);
		add_filter('http_request_args', [__CLASS__, 'add_github_headers'], 10, 2);
	}

	/**
	 * Read configuration from constants and a filter.
	 */
	protected static function get_config() {
		$defaults = [
			'repo'   => defined('HAWP_GITHUB_REPO') ? HAWP_GITHUB_REPO : 'hawpmedia/hawp-core', // e.g. owner/repo
			'branch' => defined('HAWP_GITHUB_BRANCH') ? HAWP_GITHUB_BRANCH : 'main',
			'asset'  => defined('HAWP_GITHUB_ASSET') ? HAWP_GITHUB_ASSET : '', // optional release asset zip name (e.g. hawp-core.zip)
			'token'  => defined('HAWP_GITHUB_TOKEN') ? HAWP_GITHUB_TOKEN : '',
		];
		/**
		 * Filter to override updater config programmatically.
		 *
		 * @param array $defaults
		 */
		return apply_filters('hawp_github_updater_config', $defaults);
	}

	/**
	 * Add Authorization and headers for GitHub requests.
	 */
	public static function add_github_headers($args, $url) {
		if (strpos($url, 'github.com') === false && strpos($url, 'api.github.com') === false) {
			return $args;
		}
		$config = self::get_config();
		$headers = isset($args['headers']) && is_array($args['headers']) ? $args['headers'] : [];
		$headers['User-Agent'] = isset($headers['User-Agent']) ? $headers['User-Agent'] : 'WordPress-HawpCore-Updater';
		$headers['Accept'] = 'application/vnd.github+json';
		if (!empty($config['token'])) {
			$headers['Authorization'] = 'Bearer ' . $config['token'];
		}
		$args['headers'] = $headers;
		$args['timeout'] = isset($args['timeout']) ? max(15, (int) $args['timeout']) : 20;
		return $args;
	}

	/**
	 * Inject update data into the theme updates transient.
	 */
	public static function check_for_update($transient) {
		if (!is_object($transient)) {
			$transient = new stdClass();
		}

		$theme_slug = get_template();
		$current_version = defined('HM_THEME_VERSION') ? HM_THEME_VERSION : wp_get_theme($theme_slug)->get('Version');

		$release = self::get_latest_release();
		if (!$release || empty($release['version'])) {
			return $transient;
		}

		$latest_version = $release['version'];
		if (version_compare($latest_version, $current_version, '>')) {
			$update = [
				'theme'       => $theme_slug,
				'new_version' => $latest_version,
				'url'         => $release['html_url'],
				'package'     => $release['zip_url'],
			];
			if (!isset($transient->response)) {
				$transient->response = [];
			}
			$transient->response[$theme_slug] = (object) $update;
		}

		return $transient;
	}

	/**
	 * Provide basic info to the theme installer UI (optional).
	 */
	public static function themes_api($result, $action, $args) {
		if ($action !== 'theme_information') {
			return $result;
		}
		$theme_slug = get_template();
		if (empty($args->slug) || $args->slug !== $theme_slug) {
			return $result;
		}

		$release = self::get_latest_release();
		if (!$release) {
			return $result;
		}

		$info = new stdClass();
		$info->name = wp_get_theme($theme_slug)->get('Name');
		$info->slug = $theme_slug;
		$info->version = $release['version'];
		$info->author = wp_get_theme($theme_slug)->get('Author');
		$info->homepage = $release['html_url'];
		$info->sections = [
			'description' => wp_get_theme($theme_slug)->get('Description'),
			'changelog'   => isset($release['body']) ? wp_kses_post(wpautop($release['body'])) : '',
		];
		return $info;
	}

	/**
	 * Ensure extracted directory matches the theme slug so WP replaces in-place.
	 */
	public static function ensure_correct_directory_name($source, $remote_source, $upgrader, $hook_extra) {
		if (empty($hook_extra['theme']) && empty($hook_extra['themes'])) {
			return $source;
		}
		$theme_slug = get_template();
		$basename = basename(untrailingslashit($source));
		if ($basename === $theme_slug) {
			return $source; // already correct
		}
		$corrected = trailingslashit(dirname(untrailingslashit($source))) . $theme_slug;
		// If the destination exists, remove it first to allow rename.
		if (is_dir($corrected)) {
			self::rrmdir($corrected);
		}
		@rename($source, $corrected);
		return $corrected;
	}

	protected static function rrmdir($dir) {
		if (!is_dir($dir)) return;
		$items = scandir($dir);
		foreach ($items as $item) {
			if ($item === '.' || $item === '..') continue;
			$path = $dir . DIRECTORY_SEPARATOR . $item;
			if (is_dir($path)) self::rrmdir($path); else @unlink($path);
		}
		@rmdir($dir);
	}

	/**
	 * Fetch latest release data from GitHub with caching.
	 */
	protected static function get_latest_release() {
		$config = self::get_config();
		$cache_key = 'hawp_core_github_release_' . md5(maybe_serialize($config));
		$cached = get_site_transient($cache_key);
		if (is_array($cached)) {
			return $cached;
		}

		$repo = trim($config['repo']);
		$api_url = 'https://api.github.com/repos/' . $repo . '/releases/latest';
		$response = wp_remote_get($api_url);
		if (is_wp_error($response)) {
			set_site_transient($cache_key, null, 5 * MINUTE_IN_SECONDS);
			return null;
		}
		$code = wp_remote_retrieve_response_code($response);
		$body = json_decode(wp_remote_retrieve_body($response), true);
		if ($code !== 200 || !is_array($body)) {
			set_site_transient($cache_key, null, 5 * MINUTE_IN_SECONDS);
			return null;
		}

		$tag = isset($body['tag_name']) ? $body['tag_name'] : '';
		$version = ltrim($tag, 'vV');
		$html_url = isset($body['html_url']) ? $body['html_url'] : ('https://github.com/' . $repo . '/releases');

		$zip_url = '';
		if (!empty($config['asset']) && !empty($body['assets']) && is_array($body['assets'])) {
			foreach ($body['assets'] as $asset) {
				if (!empty($asset['name']) && strtolower($asset['name']) === strtolower($config['asset'])) {
					$zip_url = isset($asset['browser_download_url']) ? $asset['browser_download_url'] : '';
					break;
				}
			}
		}
		if (empty($zip_url) && !empty($tag)) {
			// Fallback to GitHub auto-generated archive.
			$zip_url = 'https://github.com/' . $repo . '/archive/refs/tags/' . $tag . '.zip';
		}

		$result = [
			'version'  => $version,
			'html_url' => $html_url,
			'zip_url'  => $zip_url,
			'body'     => isset($body['body']) ? $body['body'] : '',
		];

		// Cache for 30 minutes to avoid rate limits.
		set_site_transient($cache_key, $result, 30 * MINUTE_IN_SECONDS);
		return $result;
	}
}

add_action('init', ['Hawp_Github_Theme_Updater', 'init']);

endif; // class_exists
