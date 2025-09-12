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
		add_filter('http_request_args', [__CLASS__, 'add_github_headers'], 10, 2);
		add_filter('upgrader_pre_download', [__CLASS__, 'pre_download'], 10, 4);
	}

	/**
	 * Read configuration from constants and a filter.
	 */
	protected static function get_config() {
		$defaults = [
			'repo'   => defined('HAWP_GITHUB_REPO') ? HAWP_GITHUB_REPO : 'hawpmedia/hawp-core', // e.g. owner/repo
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
		if (strpos($url, 'github.com') === false && strpos($url, 'api.github.com') === false && strpos($url, 'codeload.github.com') === false) {
			return $args;
		}
		$config = self::get_config();
		$headers = isset($args['headers']) && is_array($args['headers']) ? $args['headers'] : [];
		$headers['User-Agent'] = isset($headers['User-Agent']) ? $headers['User-Agent'] : 'WordPress-HawpCore-Updater';
		// Accept headers: JSON for API, binary for archives/assets.
		if (strpos($url, 'api.github.com') !== false) {
			$headers['Accept'] = 'application/vnd.github+json';
		} else {
			$headers['Accept'] = 'application/octet-stream';
		}
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
				'new_version' => $latest_version,
				'url'         => $release['html_url'],
				'package'     => $release['zip_url'],
			];
			if (!isset($transient->response) || !is_array($transient->response)) {
				$transient->response = [];
			}
			$transient->response[$theme_slug] = $update;
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
	 * Intercept package download. Fetch GitHub asset/tag zip, normalize it so the
	 * root folder is the theme slug and contains style.css, then return the local
	 * zip path for WordPress to install.
	 */
	public static function pre_download($reply, $package, $upgrader, $hook_extra) {
		// Only for theme updates; allow if hook_extra indicates theme, or package matches our repo.
		if (!empty($hook_extra['type']) && $hook_extra['type'] !== 'theme') {
			return $reply;
		}
		$theme_slug = get_template();
		if (!empty($hook_extra['theme']) && $hook_extra['theme'] !== $theme_slug) {
			return $reply;
		}

		$config = self::get_config();
		if (empty($config['repo'])) {
			return $reply;
		}

		$release = self::get_latest_release();
		if (!$release) {
			return $reply;
		}

		// Prefer asset if configured and available; else use tag zip.
		$download_url = $release['zip_url'];

		// Download to a temp file.
		// Restrict to GitHub hosts (safety):
		$host = wp_parse_url($download_url, PHP_URL_HOST);
		if (!in_array($host, ['github.com', 'codeload.github.com'], true)) {
			return $reply;
		}
		$package_file = download_url($download_url);
		if (is_wp_error($package_file)) {
			return $reply; // Fallback to default flow
		}

		$normalized = self::normalize_theme_zip($package_file, $theme_slug);
		if ($normalized) {
			@unlink($package_file);
			return $normalized;
		}
		return $package_file;
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
	 * Normalize a theme zip so it has a single root folder named after the theme slug
	 * and contains style.css directly inside it. Returns path to a new zip or null on failure.
	 */
	protected static function normalize_theme_zip($zip_path, $theme_slug) {
		// Use ZipArchive for reliability without requiring FS creds.
		if (!class_exists('ZipArchive')) {
			return null;
		}
		$zip = new ZipArchive();
		if ($zip->open($zip_path) !== true) {
			return null;
		}
		// Quick check: if zip already has theme_slug/style.css, just return original.
		$has_direct = false;
		for ($i = 0; $i < $zip->numFiles; $i++) {
			$stat = $zip->statIndex($i);
			$name = isset($stat['name']) ? $stat['name'] : '';
			if (preg_match('#^' . preg_quote($theme_slug, '#') . '/style\\.css$#i', $name)) {
				$has_direct = true;
				break;
			}
		}
		$zip->close();
		if ($has_direct) {
			return $zip_path;
		}

		// Extract to temp dir.
		$base = trailingslashit(get_temp_dir()) . 'hawp_updater_' . wp_generate_password(8, false, false);
		$extract_dir = $base . '_extract';
		$normalized_zip = $base . '_normalized.zip';
		@mkdir($extract_dir, 0755, true);

		$zip = new ZipArchive();
		if ($zip->open($zip_path) !== true) {
			return null;
		}
		$zip->extractTo($extract_dir);
		$zip->close();

		// Locate theme root directory.
		$theme_root = self::find_theme_slug_dir($extract_dir, $theme_slug, 8);
		if (!$theme_root) {
			$theme_root = self::find_theme_root_dir($extract_dir, 8);
		}
		if (!$theme_root || !file_exists(trailingslashit($theme_root) . 'style.css')) {
			self::rrmdir($extract_dir);
			return null;
		}

		// Create normalized zip with entries under theme_slug/ prefix.
		$zip = new ZipArchive();
		if ($zip->open($normalized_zip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
			self::rrmdir($extract_dir);
			return null;
		}
		self::zip_directory($zip, $theme_root, $theme_slug);
		$zip->close();

		self::rrmdir($extract_dir);
		return $normalized_zip;
	}

	protected static function zip_directory(ZipArchive $zip, $directory, $prefixInZip) {
		$directory = untrailingslashit($directory);
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::SELF_FIRST
		);
		foreach ($files as $file) {
			$localPath = $prefixInZip . '/' . ltrim(str_replace($directory, '', $file->getPathname()), '/\\');
			$localPath = str_replace('\\', '/', $localPath);
			if (is_dir($file)) {
				$zip->addEmptyDir(rtrim($localPath, '/') . '/');
			} else {
				$zip->addFile($file->getPathname(), $localPath);
			}
		}
	}

	/**
	 * Recursively locate a directory containing style.css within a limited depth.
	 */
	protected static function find_theme_root_dir($start, $maxDepth = 8, $depth = 0) {
		$start = untrailingslashit($start);
		if ($depth > $maxDepth) {
			return null;
		}
		if (file_exists($start . '/style.css')) {
			return $start;
		}
		$items = @scandir($start);
		if (!is_array($items)) {
			return null;
		}
		foreach ($items as $item) {
			if ($item === '.' || $item === '..') continue;
			$path = $start . '/' . $item;
			if (is_dir($path)) {
				$found = self::find_theme_root_dir($path, $maxDepth, $depth + 1);
				if ($found) {
					return $found;
				}
			}
		}
		return null;
	}

	/**
	 * Find a directory named like the theme slug containing style.css (limited depth).
	 */
	protected static function find_theme_slug_dir($start, $theme_slug, $maxDepth = 8, $depth = 0) {
		$start = untrailingslashit($start);
		if ($depth > $maxDepth) {
			return null;
		}
		$items = @scandir($start);
		if (!is_array($items)) {
			return null;
		}
		foreach ($items as $item) {
			if ($item === '.' || $item === '..') continue;
			$path = $start . '/' . $item;
			if (is_dir($path)) {
				if ($item === $theme_slug && file_exists($path . '/style.css')) {
					return $path;
				}
				$found = self::find_theme_slug_dir($path, $theme_slug, $maxDepth, $depth + 1);
				if ($found) {
					return $found;
				}
			}
		}
		return null;
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

		// Cache for 30 minutes to avoid rate limits (filterable TTL).
		$ttl = apply_filters('hawp_github_updater_cache_ttl', 30 * MINUTE_IN_SECONDS, $result);
		set_site_transient($cache_key, $result, $ttl);
		return $result;
	}
}

add_action('init', ['Hawp_Github_Theme_Updater', 'init']);

endif; // class_exists
