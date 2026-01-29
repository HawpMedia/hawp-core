<?php
// ------------------------------------------
// Theme options class.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!class_exists('Hawp_Theme_Options')):

class Hawp_Theme_Options {
    private $option_prefix;
    private $option_group = 'hawp_theme_options';
    private $page_slug = 'hawptheme';
    private $tab_pages = [
        'general' => 'General',
        'integration' => 'Integration',
        'scripts_styles' => 'Scripts & Styles',
        'utilities' => 'Utilities',
        'admin_ui' => 'Admin UI',
        'svg_library' => 'SVG Library',
        'custom_settings' => 'Custom'
    ];
    private $current_tab;
    private $field_registry = [];
    private $suppress_admin_header = false;

    /**
     * Sanitization rules for different field types
     */
    private $sanitization_rules = [
        'image' => 'absint',
        'url' => 'esc_url_raw',
        'code' => 'wp_kses_post',
        'text' => 'sanitize_text_field',
        'number' => 'absint',
        'checkbox' => 'absint',
        'svg_array' => 'sanitize_svg_array'
    ];

    /**
     * Sanitize tab name for class name
     */
    private function sanitize_tab_class($tab_name) {
        // Convert to lowercase
        $class = strtolower($tab_name);
        // Replace spaces and special characters with hyphens
        $class = preg_replace('/[^a-z0-9]+/', '-', $class);
        // Remove leading/trailing hyphens
        $class = trim($class, '-');
        return 'hm-nav-tab-' . $class;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->option_prefix = hawp_theme()::$theme['option_prefix'];
        add_action('admin_menu', [$this, 'add_options_pages']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_filter('admin_body_class', [$this, 'add_admin_body_class']);
        //add_action('admin_notices', [$this, 'disable_admin_notices'], 1);
    }

    /**
     * Disable admin notices on theme options pages
     */
    public function disable_admin_notices() {
        $screen = get_current_screen();
        // Allow notices on the Hawp Core options page (needed for ACF save messages and WP notices).
        if (strpos($screen->id, $this->page_slug) !== false) {
            return;
        }
    }

    /**
     * Add theme options pages.
     */
    public function add_options_pages() {
        // Add as top-level menu (keeps original icon placement) while still using hash-based tabs.
        add_menu_page(
            __('Theme Options'),
            hawp_theme()::$theme['name'],
            'manage_options',
            $this->page_slug,
            [$this, 'render_options_page'],
            'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgNTAgNDMuNSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+PGNsaXBQYXRoIGlkPSJhIj48cGF0aCBkPSJtMCAwaDUwdjQzLjVoLTUweiI+PC9wYXRoPjwvY2xpcFBhdGg+PGcgY2xpcC1wYXRoPSJ1cmwoI2EpIiBmaWxsPSJjdXJyZW50Q29sb3IiPjxwYXRoIGQ9Im0zNS44IDIwLjA5YzYuNDUuNzQgMy42MSA0Ljg5LTEuNzggNi42NiAzLjU3LTUuMy43Ny0zLjkzLTcuMDUtNi4yMSAxMy4yNS00LjQgMTMuMi05LjkyIDEyLjUtMTQuMTItMS4yNC0yLjQ0LTMuMi00LjQzLTEuNzktNi40My45Ny41IDIuNjYgMi4xNyAzLjEyIDMuMDIgMi44My40NSA0LjY5IDMuMTEgOS4xNyA1LjQxLjA3LjI0LS4xIDEuNC0xLjI1IDEuODYtMS40OC41OS0zLjIxLS41MS00LjgzIDEuMTItMS4wNSAxLjA2LTEuMjQgMy4wOC0yLjE5IDQuMzYtMS41NCAyLjEzLTMuOTcgMy40NS01LjkxIDQuMzJ6Ij48L3BhdGg+PHBhdGggZD0ibTM2Ljg0IDkuMzhjMCAuNTctLjkxIDUuMDEtNy4xMyA3LTIuNjUuNzItNS40MS42OS04LjYzLS42IDguMTIgOC42OC0uMTMgMTEuNTEtMi44MSAxNy41LS42NiAxLjc5Ljg2IDUuMDQuODggNi43NC0uMDYgMS44MS0xLjQ0IDMuMy0zLjI0IDMuNDkuMDctLjU0LjUyLTMuMjYuMzItNC4zMy0uMjMtMS4yMS0xLjM4LTIuOTktMS42MS00LjYyLS40LTIuOSAyLjc5LTYuMDEgMy4yNC04LjE3LjE3LS43OS0uMzYtMS44LTEuMTEtMy4wMi0uNzktMS4yNy0xLjkyLTIuMzItMi4wNC0yLjI4LS41OS4zMS0xLjUgMi4zMS0xLjgxIDMuMTctMS4wMiAyLjc5LTIuNjggOC42OC00LjU3IDEwLjk3LTEuMzggMS42Ny00Ljg3IDMuMzMtOC4zMSAyLjM0IDMuMDkgMCA2LjA1LTIuMzMgNi44NC00LjE3IDEuOTktNC42OS45NC0xMi42IDYuNTgtMTkuMzEgNi41LTYuOTkgMTMuMDUtMy44NiAxOC42LTMuNjcgMi44Mi4wOSA0Ljc3LTEgNC44Mi0xLjAzeiI+PC9wYXRoPjwvZz48L3N2Zz4=',
            98
        );
    }

    /**
     * Render section.
     */
    public function render_section($args) {
        return '';
    }

    /**
     * Get sanitization callback for a field
     */
    private function get_sanitization_callback($field_id, $args = []) {
        // Check if a specific callback is provided
        if (isset($args['sanitize_callback'])) {
            return $args['sanitize_callback'];
        }

        // Determine field type based on field ID or args
        $field_type = $this->determine_field_type($field_id, $args);

        // Return appropriate sanitization callback
        if (isset($this->sanitization_rules[$field_type])) {
            if ($field_type === 'svg_array') {
                return [$this, 'sanitize_svg_array'];
            }
            if ($field_type === 'code') {
                return [$this, 'sanitize_code_field'];
            }
            return $this->sanitization_rules[$field_type];
        }

        // Default to text sanitization
        return 'sanitize_text_field';
    }

    /**
     * Determine field type based on field ID or args
     */
    private function determine_field_type($field_id, $args) {
        // Check if type is explicitly set
        if (isset($args['type'])) {
            return $args['type'];
        }

        // Determine type based on field ID
        if (strpos($field_id, 'image') !== false) {
            return 'image';
        }
        if (strpos($field_id, 'url') !== false || strpos($field_id, 'fonts') !== false) {
            return 'url';
        }
        if (strpos($field_id, 'code') !== false) {
            return 'code';
        }
        if (strpos($field_id, 'svgs') !== false) {
            return 'svg_array';
        }

        return 'text';
    }

    /**
     * Register a field
     */
    private function register_field($tab, $id, $title, $callback, $args = []) {
        $field_id = $this->option_prefix . $id;
        
        // Get sanitization callback
        $sanitize_callback = $this->get_sanitization_callback($id, $args);
        
        // Register the setting with WordPress
        register_setting(
            $this->option_group . '_' . $tab,
            $field_id,
            $sanitize_callback
        );

        // Store field information in registry
        $this->field_registry[$tab][$id] = [
            'id' => $field_id,
            'title' => $title,
            'callback' => $callback,
            'args' => array_merge([
                'name' => $field_id,
                'description' => '',
            ], $args)
        ];
        return $field_id;
    }

    /**
     * Get all fields for a tab
     */
    private function get_tab_fields($tab) {
        return isset($this->field_registry[$tab]) ? $this->field_registry[$tab] : [];
    }

    /**
     * Register settings.
     */
    public function register_settings() {
        // Register option groups for each tab
        foreach ($this->tab_pages as $tab_id => $tab_name) {
            register_setting($this->option_group . '_' . $tab_id, $this->option_group . '_' . $tab_id);
        }

        // Register individual fields
        $this->register_general_settings();
        $this->register_integration_settings();
        $this->register_scripts_styles_settings();
        $this->register_utilities_settings();
        $this->register_admin_ui_settings();
        $this->register_svg_library_settings();

        // Migrate ACF options to new format
        $this->migrate_acf_options();

        // Register sections and fields for each tab
        foreach ($this->tab_pages as $tab_id => $tab_name) {
            // Register section
            add_settings_section(
                $tab_id, 
                '', // Empty title to hide the section title
                [$this, 'render_section'], 
                $this->page_slug
            );

            // Register fields for this tab
            $fields = $this->get_tab_fields($tab_id);
            foreach ($fields as $field) {
                add_settings_field(
                    $field['id'],
                    $field['title'],
                    $field['callback'],
                    $this->page_slug,
                    $tab_id,
                    $field['args']
                );
            }
        }
    }

    /**
     * Register general settings
     */
    private function register_general_settings() {
        $this->register_field('general', 'logo', __('Logo'), [$this, 'render_image_field'], [
            'description' => 'Used with <code>[logo]</code> shortcode.'
        ]);
        $this->register_field('general', 'header_bg_image', __('Default header background image'), [$this, 'render_image_field']);
        $this->register_field('general', 'content_cta', __('Content CTA'), [$this, 'render_textarea_field'], [
            'description' => 'This code will be used to generate the content that is placed in the <code>[content_cta]</code> shortcode.',
            'rows' => 4
        ]);
        $this->register_field('general', 'catnum_posts', __('Number of Posts displayed on Category pages'), [$this, 'render_number_field'], [
            'default' => 5
        ]);
        $this->register_field('general', 'archivenum_posts', __('Number of Posts displayed on Archive pages'), [$this, 'render_number_field'], [
            'default' => 5
        ]);
        $this->register_field('general', 'searchnum_posts', __('Number of Posts displayed on Search pages'), [$this, 'render_number_field'], [
            'default' => 5
        ]);
        $this->register_field('general', 'tagnum_posts', __('Number of Posts displayed on Tag pages'), [$this, 'render_number_field'], [
            'default' => 5
        ]);
        $this->register_field('general', 'woocommerce_archive_num_posts', __('Number of Products displayed on WooCommerce archive pages'), [$this, 'render_number_field'], [
            'default' => 9
        ]);
        $this->register_field('general', 'excerpt_length', __('Change the default excerpt length'), [$this, 'render_number_field'], [
            'placeholder' => 'defaults to 55'
        ]);
    }

    /**
     * Register integration settings
     */
    private function register_integration_settings() {
        $this->register_field('integration', 'google_fonts', __('Google Fonts embed URL'), [$this, 'render_url_field']);
        $this->register_field('integration', 'head_code', __('Add code to the &lt;head&gt;'), [$this, 'render_textarea_field'], [
            'rows' => 16
        ]);
        $this->register_field('integration', 'body_code', __('Add code to the &lt;body&gt;'), [$this, 'render_textarea_field'], [
            'description' => 'Good for tracking codes such as google analytics.',
            'rows' => 14
        ]);
        $this->register_field('integration', 'footer_code', __('Add code above the &lt;/body&gt;'), [$this, 'render_textarea_field'], [
            'description' => 'Adds code to bottom of the site, before the closing body tag.',
            'rows' => 14
        ]);
        $this->register_field('integration', 'disable_rankmath_modules', __('Disable RankMath Modules'), [$this, 'render_checkbox_field'], [
            'description' => 'Disables unnecessary RankMath modules to improve performance.',
            'default' => 1,
            'type' => 'checkbox'
        ]);
    }

    /**
     * Register scripts & styles settings
     */
    private function register_scripts_styles_settings() {
        $this->register_field('scripts_styles', 'dequeue_gutenberg_style', __('Gutenberg Stylesheet'), [$this, 'render_checkbox_field'], [
            'description' => 'The default block builder stylesheet.',
            'default' => 1,
            'type' => 'checkbox'
        ]);
        $this->register_field('scripts_styles', 'enqueue_jquery_migrate', __('jQuery Migrate'), [$this, 'render_checkbox_field'], [
            'description' => 'Preserves compatibility of jQuery for versions of jQuery older than 1.9.',
            'default' => 0,
            'type' => 'checkbox'
        ]);
        $this->register_field('scripts_styles', 'enqueue_lity_styles_scripts', __('Lity Lightbox'), [$this, 'render_checkbox_field'], [
            'description' => 'A jQuery library for developers to create lightweight, accessible and responsive lightboxes.',
            'default' => 0,
            'type' => 'checkbox'
        ]);
        $this->register_field('scripts_styles', 'enqueue_swiper_styles_scripts', __('Swiper'), [$this, 'render_checkbox_field'], [
            'default' => 0,
            'type' => 'checkbox'
        ]);
        $this->register_field('scripts_styles', 'enqueue_owl_styles_scripts', __('Owl Carousel'), [$this, 'render_checkbox_field'], [
            'description' => 'A jQuery library for developers to create responsive and touch enabled carousel sliders.',
            'default' => 0,
            'type' => 'checkbox'
        ]);
        $this->register_field('scripts_styles', 'enqueue_litepicker_styles_scripts', __('Litepicker'), [$this, 'render_checkbox_field'], [
            'description' => 'A JavaScript library with no dependencies for developers to create lightweight date range pickers.',
            'default' => 0,
            'type' => 'checkbox'
        ]);
        $this->register_field('scripts_styles', 'enqueue_mixitup_styles_scripts', __('Mixitup'), [$this, 'render_checkbox_field'], [
            'description' => 'A JavaScript library with no dependencies for developers to create animated filtering and sorting elements.',
            'default' => 0,
            'type' => 'checkbox'
        ]);
        $this->register_field('scripts_styles', 'enqueue_select2_styles_scripts', __('Select2'), [$this, 'render_checkbox_field'], [
            'description' => 'The jQuery replacement for select boxes.',
            'default' => 1,
            'type' => 'checkbox'
        ]);
        $this->register_field('scripts_styles', 'enqueue_fontawesome_5_style', __('FontAwesome 5'), [$this, 'render_checkbox_field'], [
            'default' => 0,
            'type' => 'checkbox'
        ]);
        $this->register_field('scripts_styles', 'enqueue_fontawesome_6_style', __('FontAwesome 6'), [$this, 'render_checkbox_field'], [
            'default' => 1,
            'type' => 'checkbox'
        ]);
    }

    /**
     * Register utilities settings
     */
    private function register_utilities_settings() {
        $this->register_field('utilities', 'force_ssl', __('Force SSL URL\'s'), [$this, 'render_checkbox_field'], [
            'description' => 'Force SSL redirect on internal URL\'s if the Site Address URL includes HTTPS.',
            'default' => 0,
            'type' => 'checkbox'
        ]);
        $this->register_field('utilities', 'prefix_post_urls', __('Prefix Post URL\'s'), [$this, 'render_checkbox_field'], [
            'description' => 'Rewrite post urls to include /blog/ before the post slug.',
            'default' => 1,
            'type' => 'checkbox'
        ]);
        $this->register_field('utilities', 'allow_svg_upload', __('Allow SVG Uploads'), [$this, 'render_checkbox_field'], [
            'description' => 'Allow SVG files to be uploaded in the media library. Make sure you add the code below to the top of your svg file: <pre><code>&#x3C;?xml version="1.0" encoding="utf-8"?&#x3E;</code></pre>',
            'default' => 1,
            'type' => 'checkbox'
        ]);
    }

    /**
     * Register admin UI settings
     */
    private function register_admin_ui_settings() {
        $this->register_field('admin_ui', 'login_logo', __('Login Logo'), [$this, 'render_image_field'], [
            'description' => 'Appears on the login page. Should be a square to display properly. (160x160 recommended)'
        ]);
        $this->register_field('admin_ui', 'comments_admin_menu_item', __('Comments Menu Item'), [$this, 'render_checkbox_field'], [
            'description' => 'Show or hide the Comments menu item.',
            'default' => 0,
            'type' => 'checkbox'
        ]);
        $this->register_field('admin_ui', 'wordpress_admin_item', __('WordPress Admin Items'), [$this, 'render_checkbox_field'], [
            'description' => 'Show or hide the wordpress.org related widgets & menus.',
            'default' => 0,
            'type' => 'checkbox'
        ]);
    }

    /**
     * Register SVG library settings
     */
    private function register_svg_library_settings() {
        $this->register_field('svg_library', 'svgs', __('SVG Library'), [$this, 'render_svg_library_field'], [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitize_svg_array']
        ]);
    }

    /**
     * Migrate ACF options to new format
     */
    private function migrate_acf_options() {
        // Check if migration has already been completed
        $migration_version = '1.0';
        $migration_completed = get_option($this->option_prefix . 'acf_migration_completed');
        
        if ($migration_completed === $migration_version) {
            return; // Migration already completed
        }

        // Field definitions for migration
        $general_fields = [
            'logo',
            'header_bg_image',
            'catnum_posts',
            'archivenum_posts',
            'searchnum_posts',
            'tagnum_posts',
            'woocommerce_archive_num_posts',
            'excerpt_length',
            'content_cta'
        ];

        $integration_fields = [
            'google_fonts',
            'head_code',
            'body_code',
            'footer_code'
        ];

        $scripts_styles_fields = [
            'dequeue_gutenberg_style',
            'enqueue_jquery_migrate',
            'enqueue_lity_styles_scripts',
            'enqueue_swiper_styles_scripts',
            'enqueue_owl_styles_scripts',
            'enqueue_litepicker_styles_scripts',
            'enqueue_mixitup_styles_scripts',
            'enqueue_select2_styles_scripts',
            'enqueue_fontawesome_5_style',
            'enqueue_fontawesome_6_style'
        ];

        $utilities_fields = [
            'force_ssl',
            'prefix_post_urls',
            'allow_svg_upload'
        ];

        $admin_ui_fields = [
            'login_logo',
            'comments_admin_menu_item',
            'wordpress_admin_item'
        ];

        // Get all registered fields
        $fields = array_merge(
            $general_fields,
            $integration_fields,
            $scripts_styles_fields,
            $utilities_fields,
            $admin_ui_fields
        );

        $migrated_count = 0;
        foreach ($fields as $field) {
            $old_option = 'options_' . $this->option_prefix . $field;
            $new_option = $this->option_prefix . $field;
            
            // Check if old option exists
            $old_value = get_option($old_option);
            if ($old_value !== false) {
                // Get current new option value
                $new_value = get_option($new_option);
                
                // Only migrate if new option doesn't exist or is empty
                if ($new_value === false || empty($new_value)) {
                    if (update_option($new_option, $old_value)) {
                        // Only delete the old option if migration was successful
                        delete_option($old_option);
                        $migrated_count++;
                    }
                }
            }
        }

        // Mark migration as completed
        update_option($this->option_prefix . 'acf_migration_completed', $migration_version);
        
        // Log migration completion (optional)
        if ($migrated_count > 0) {
            error_log("Hawp Theme: Migrated {$migrated_count} ACF options to new format");
        }
    }

    /**
     * Sanitize settings before saving
     */
    public function sanitize_settings($input) {
        // Ensure we're working with an array
        if (!is_array($input)) {
            return $input;
        }

        $sanitized = [];
        foreach ($input as $key => $value) {
            $field_id = str_replace($this->option_prefix, '', $key);
            $sanitize_callback = $this->get_sanitization_callback($field_id);
            
            if (is_callable($sanitize_callback)) {
                $sanitized[$key] = call_user_func($sanitize_callback, $value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    /**
     * Sanitize SVG array
     */
    public function sanitize_svg_array($input) {
        if (!is_array($input)) {
            return [];
        }

        $sanitized = [];
        foreach ($input as $svg) {
            if (!isset($svg['label']) || !isset($svg['code'])) {
                continue;
            }

            $sanitized[] = [
                'label' => sanitize_text_field($svg['label']),
                'code' => $this->remove_script_tags($svg['code'])
            ];
        }
        return $sanitized;
    }

    /**
     * Remove script tags, event handlers, and HTML comments from SVG code
     */
    private function remove_script_tags($code) {
        // Remove script tags
        $code = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $code);
        
        // Remove on* attributes
        $code = preg_replace('/\s+on\w+="[^"]*"/i', '', $code);
        $code = preg_replace('/\s+on\w+=\'[^\']*\'/i', '', $code);
        
        // Remove HTML comments
        $code = preg_replace('/<!--(.*?)-->/s', '', $code);
        
        return $code;
    }

    /**
     * Sanitize code fields, allowing scripts for trusted admins
     */
    public function sanitize_code_field($value) {
        if (current_user_can('unfiltered_html')) {
            return $value;
        }
        return wp_kses_post($value);
    }

    /**
     * Render image field.
     */
    public function render_image_field($args) {
        $value = get_option($args['name']);
        ?>

        <input type="hidden" name="<?php echo esc_attr($args['name']); ?>" id="<?php echo esc_attr($args['name']); ?>" value="<?php echo esc_attr($value); ?>" />
        <div class="hawp-image-controls">
            <input type="button" class="button button-secondary" value="<?php _e('Select Image'); ?>" onclick="hawpSelectImage('<?php echo esc_attr($args['name']); ?>')" />
            <?php if ($value) : ?>
                <input type="button" class="button button-link-delete" value="<?php _e('Remove Image'); ?>" onclick="hawpRemoveImage('<?php echo esc_attr($args['name']); ?>')" />
            <?php endif; ?>
        </div>
        <div id="<?php echo esc_attr($args['name']); ?>_preview" class="hawp-image-preview">
            <?php if ($value) : ?>
                <?php echo wp_get_attachment_image($value, 'thumbnail'); ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($args['description'])) : ?>
            <p class="description"><?php echo $args['description']; ?></p>
        <?php endif; ?>

        <?php
    }

    /**
     * Render number field.
     */
    public function render_number_field($args) {
        $value = get_option($args['name'], $args['default'] ?? '');
        ?>

        <input type="number" name="<?php echo esc_attr($args['name']); ?>" id="<?php echo esc_attr($args['name']); ?>" value="<?php echo esc_attr($value); ?>" <?php echo isset($args['placeholder']) ? 'placeholder="' . esc_attr($args['placeholder']) . '"' : ''; ?> />
        <?php if (!empty($args['description'])) : ?>
            <p class="description"><?php echo $args['description']; ?></p>
        <?php endif; ?>

        <?php
    }

    /**
     * Render textarea field.
     */
    public function render_textarea_field($args) {
        $value = get_option($args['name']);
        ?>
        <textarea name="<?php echo esc_attr($args['name']); ?>" id="<?php echo esc_attr($args['name']); ?>" rows="<?php echo esc_attr($args['rows'] ?? 5); ?>" class="large-text code"><?php echo esc_textarea($value); ?></textarea>
        <?php if (!empty($args['description'])) : ?>
            <p class="description"><?php echo $args['description']; ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Render URL field.
     */
    public function render_url_field($args) {
        $value = get_option($args['name']);
        ?>

        <input type="url" name="<?php echo esc_attr($args['name']); ?>" id="<?php echo esc_attr($args['name']); ?>" value="<?php echo esc_url($value); ?>" class="regular-text" />
        <?php if (!empty($args['description'])) : ?>
            <p class="description"><?php echo $args['description']; ?></p>
        <?php endif; ?>

        <?php
    }

    /**
     * Render checkbox field.
     */
    public function render_checkbox_field($args) {
        $value = get_option($args['name'], $args['default'] ?? 0);
        ?>

        <label>
            <input type="checkbox" name="<?php echo esc_attr($args['name']); ?>" id="<?php echo esc_attr($args['name']); ?>" value="1" <?php checked($value, 1); ?> />
            <?php _e('Enable'); ?>
        </label>
        <?php if (!empty($args['description'])) : ?>
            <p class="description"><?php echo $args['description']; ?></p>
        <?php endif; ?>

        <?php
    }

    /**
     * Render admin header.
     */
    public function render_admin_header() {
        if ($this->suppress_admin_header) {
            return;
        }
        if (!isset($_GET['page']) || sanitize_text_field($_GET['page']) !== $this->page_slug) {
            return;
        }

        $requested_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
        $active_tab = isset($this->tab_pages[$requested_tab]) ? $requested_tab : 'general';

        // Simple check if the custom settings field group exists
        $has_custom_fields = false;
        if (function_exists('acf_get_field_group')) {
            $field_group = acf_get_field_group('group_custom_settings');
            $has_custom_fields = ($field_group !== false);
        }

        ?>
        <div id="hm-admin-toolbar" class="hm-admin-toolbar">
            <div class="hm-admin-toolbar-inner">
                <div class="hm-nav-wrap">
                    <!-- <a href="<?php echo admin_url('themes.php?page=' . $this->page_slug); ?>" class="hm-logo">
                        <img src="http://hawpv6.local/wp-content/plugins/advanced-custom-fields-pro/assets/images/acf-pro-logo.svg" alt="Advanced Custom Fields logo">
                    </a> -->

                    <?php 
                    // Only show General and Integration in main nav
                    $main_tabs = ['general', 'integration'];
                    foreach ($this->tab_pages as $tab_id => $tab_name) {
                        // Skip if not in main tabs
                        if (!in_array($tab_id, $main_tabs)) {
                            continue;
                        }
                        // Skip custom_settings tab if no fields are registered
                        if ($tab_id === 'custom_settings' && !$has_custom_fields) {
                            continue;
                        }
                        $active_class = ($active_tab === $tab_id) ? 'active' : '';
                        $tab_class = $this->sanitize_tab_class($tab_name);
                            $page_url = admin_url('admin.php?page=' . $this->page_slug) . '#' . $tab_id;
                            echo '<a data-tab="' . esc_attr($tab_id) . '" href="' . esc_url($page_url) . '" class="hm-nav-tab ' . $active_class . ' ' . $tab_class . '">' . esc_html($tab_name) . '</a>';
                    } ?>

                <div class="hm-more hm-header-tab-hm-more" tabindex="0">
					<span class="hm-nav-tab hm-more-tab">More </span>
					<ul>
						<?php foreach ($this->tab_pages as $tab_id => $tab_name) {
                            // Skip custom_settings tab if no fields are registered
                            if ($tab_id === 'custom_settings' && !$has_custom_fields) {
                                continue;
                            }
                            // Skip tabs that are already in main nav
                            if (in_array($tab_id, $main_tabs)) {
                                continue;
                            }
                            $active_class = ($active_tab === $tab_id) ? 'active' : '';
                            $tab_class = $this->sanitize_tab_class($tab_name);
                            $page_url = admin_url('admin.php?page=' . $this->page_slug) . '#' . $tab_id;
                            echo '<li><a data-tab="' . esc_attr($tab_id) . '" href="' . esc_url($page_url) . '" class="hm-nav-tab ' . $active_class . ' ' . $tab_class . '">' . esc_html($tab_name) . '</a></li>';
                        } ?>
                        <!--<li class="hm-hawp-media"><a class="hm-nav-tab acf-header-tab-spanimg-classacf-wp-engine-pro-srchttp--hawpv6local-wp-content-plugins-advanced-custom-fields-pro-assets-images-wp-engine-horizontal-blacksvg-altwp-engine---spanspan-classacf-wp-engine-upsell-pill4-months-free-span" href="https://wpengine.com/plans/?coupon=freedomtocreate&amp;utm_source=acf_plugin&amp;utm_medium=referral&amp;utm_campaign=bx_prod_referral&amp;utm_content=acf_pro_plugin_topbar_dropdown_cta" target="_blank"><i class="acf-icon"></i><span><img class="acf-wp-engine-pro" src="http://hawpv6.local/wp-content/plugins/advanced-custom-fields-pro/assets/images/wp-engine-horizontal-black.svg" alt="WP Engine"></span><span class="acf-wp-engine-upsell-pill">4 Months Free</span></a></li>-->
                    </ul>
				</div>

                </div>
                <div class="hm-nav-upgrade-wrap">
                    <a href="https://hawpmedia.com/?utm_source=hm_theme&amp;utm_medium=referral&amp;utm_content=hm_theme_topbar_logo" target="_blank" class="hm-nav-hawp-logo">
                        <?php echo hawp_theme()::$theme['name']; ?>
                    </a>
                </div>
            </div>
        </div>
        <div id="hm-admin-headerbar" class="hm-admin-headerbar">
            <h1 class="hm-page-title"><?php echo esc_html(isset($this->tab_pages[$active_tab]) ? $this->tab_pages[$active_tab] : ''); ?></h1>
        </div>
        <?php
    }

    /**
     * Render a custom settings box
     */
    private function render_settings_box($title, $field_ids, $description = '') {
        ?>
        <div class="hm-settings-box">
            <div class="hm-settings-box-header">
                <h3><?php echo esc_html($title); ?></h3>
                <?php if ($description): ?>
                    <p class="description"><?php echo $description; ?></p>
                <?php endif; ?>
            </div>
            <div class="hm-settings-box-content">
                <?php
                foreach ($field_ids as $field_id) {
                    if (isset($this->field_registry[$this->current_tab][$field_id])) {
                        $field = $this->field_registry[$this->current_tab][$field_id];
                        ?>
                        <div class="hm-settings-field">
                            <div class="hm-settings-label">
                                <label for="<?php echo esc_attr($field['args']['name']); ?>">
                                    <?php echo esc_html($field['title']); ?>
                                </label>
                            </div>
                            <div class="hm-settings-input">
                                <?php call_user_func($field['callback'], $field['args']); ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Get settings structure configuration
     */
    private function get_settings_structure() {
        return [
            'general' => [
                [
                    'title' => 'Site Identity',
                    'description' => 'Configure your site\'s main identity elements',
                    'fields' => ['logo', 'header_bg_image', 'content_cta']
                ],
                [
                    'title' => 'Post Display Settings',
                    'description' => 'Control how many posts are displayed on different archive pages',
                    'fields' => [
                        'catnum_posts',
                        'archivenum_posts',
                        'searchnum_posts',
                        'tagnum_posts',
                        'woocommerce_archive_num_posts',
                        'excerpt_length'
                    ]
                ]
            ],
            'integration' => [
                [
                    'title' => 'Google Fonts',
                    'description' => 'Add Google Fonts to your site',
                    'fields' => ['google_fonts']
                ],
                [
                    'title' => 'Custom Code',
                    'description' => 'Add custom code to different parts of your site',
                    'fields' => [
                        'head_code',
                        'body_code',
                        'footer_code'
                    ]
                ],
                [
                    'title' => 'Plugin Integrations',
                    'description' => 'Configure third-party plugin settings',
                    'fields' => ['disable_rankmath_modules']
                ]
            ],
            'scripts_styles' => [
                [
                    'title' => 'Core Scripts & Styles',
                    'description' => 'Configure core WordPress scripts and styles',
                    'fields' => [
                        'dequeue_gutenberg_style',
                        'enqueue_jquery_migrate'
                    ]
                ],
                [
                    'title' => 'UI Components',
                    'description' => 'Enable or disable various UI components and libraries',
                    'fields' => [
                        'enqueue_lity_styles_scripts',
                        'enqueue_swiper_styles_scripts',
                        'enqueue_owl_styles_scripts',
                        'enqueue_litepicker_styles_scripts',
                        'enqueue_mixitup_styles_scripts',
                        'enqueue_select2_styles_scripts'
                    ]
                ],
                [
                    'title' => 'Icon Libraries',
                    'description' => 'Choose which icon library to use on your site',
                    'fields' => [
                        'enqueue_fontawesome_5_style',
                        'enqueue_fontawesome_6_style'
                    ]
                ]
            ],
            'utilities' => [
                [
                    'title' => 'URL & Security Settings',
                    'description' => 'Configure URL structure and security settings',
                    'fields' => [
                        'force_ssl',
                        'prefix_post_urls'
                    ]
                ],
                [
                    'title' => 'Media Settings',
                    'description' => 'Configure media upload settings',
                    'fields' => ['allow_svg_upload']
                ]
            ],
            'admin_ui' => [
                [
                    'title' => 'Admin Customization',
                    'description' => 'Customize the WordPress admin interface',
                    'fields' => [
                        'login_logo',
                        'comments_admin_menu_item',
                        'wordpress_admin_item'
                    ]
                ]
            ],
            'svg_library' => [
                [
                    'title' => 'SVG Library',
                    'description' => 'Manage your SVG icon library',
                    'fields' => ['svgs']
                ]
            ]
        ];
    }

    /**
     * Render options page.
     */
    public function render_options_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $requested_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
        $default_tab = isset($this->tab_pages[$requested_tab]) ? $requested_tab : 'general';

        // Prevent header action from rendering separately; we render inline below.
        $this->suppress_admin_header = true;

        // Simple check if the custom settings field group exists
                $has_custom_fields = false;
                if (function_exists('acf_get_field_group')) {
                    $field_group = acf_get_field_group('group_custom_settings');
                    $has_custom_fields = ($field_group !== false);
                }

        // Keep all tabs; custom_settings will render an info message if no fields are present.
        $tabs_to_render = $this->tab_pages;

        if (function_exists('acf_form_head')) {
            acf_form_head();
        }

        // Basic descriptions for nav items
        $tab_descriptions = [
            'general' => 'Site identity & counts',
            'integration' => 'Code & embeds',
            'scripts_styles' => 'Scripts & styles',
            'utilities' => 'URLs & utilities',
            'admin_ui' => 'Admin UI',
            'svg_library' => 'SVG library',
            'custom_settings' => 'Custom'
        ];

        ?>
        <div class="hm-wrap wrap hm-theme-options">
            <?php settings_errors(); ?>
            <div class="hm-body">
                <header class="hm-Header">
                    <div class="hm-Header-logo">
                        <div class="hm-Header-logo-text"><?php echo esc_html(hawp_theme()::$theme['name']); ?></div>
                    </div>
                    <div class="hm-Header-nav">
                        <?php foreach ($tabs_to_render as $tab_id => $tab_name) :
                            $is_active = ($tab_id === $default_tab);
                            $desc = $tab_descriptions[$tab_id] ?? '';
                            $tab_class = $this->sanitize_tab_class($tab_name);
                            $page_url = admin_url('admin.php?page=' . $this->page_slug) . '#' . $tab_id;
                            ?>
                            <a data-tab="<?php echo esc_attr($tab_id); ?>" href="<?php echo esc_url($page_url); ?>" class="hm-nav-tab hm-menuItem <?php echo $is_active ? 'isActive' : ''; ?> <?php echo esc_attr($tab_class); ?>">
                                <div class="hm-menuItem-title"><?php echo esc_html($tab_name); ?></div>
                                <?php if ($desc): ?>
                                    <div class="hm-menuItem-description"><?php echo esc_html($desc); ?></div>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="hm-Header-footer">
                        version <?php echo esc_html(hawp_theme()::$theme['version']); ?>
                    </div>
                </header>

                <section class="hm-Content isNotFull">
                    <div class="hm-tab-panels">
                        <?php foreach ($tabs_to_render as $tab_id => $tab_name) :
                            $this->current_tab = $tab_id;
                            $is_active = ($tab_id === $default_tab);
                            // Get the structure for the current tab
                            $structure = $this->get_settings_structure();
                            $structure = apply_filters('hawp_theme_settings_structure', $structure, $this->current_tab);
                            ?>
                            <div id="<?php echo esc_attr($tab_id); ?>" class="hm-tab-panel hm-Page <?php echo $is_active ? 'is-active' : ''; ?>" data-tab="<?php echo esc_attr($tab_id); ?>">
                                <?php if ($tab_id === 'custom_settings' && function_exists('acf_form')) : ?>
                                    <div class="hm-settings-box">
                                        <div class="hm-settings-box-header">
                                            <h3><?php echo esc_html($tab_name); ?></h3>
                                        </div>
                                        <div class="hm-settings-box-content">
                                            <?php
                                            if (!$has_custom_fields) {
                                                echo '<div class="notice notice-info">';
                                                echo '<p>Not sure how you ended up here! If you\'re not a developer, there\'s nothing to see here. If you are, then continue reading. </p>';
                                                echo '<p>To add custom settings, navigate to your child theme\'s functions.php file and add them to the group <code>group_custom_settings</code> using the <code>acf_add_local_field_group</code> function.</p>';
                                                echo '<p>Here\'s an example of what this could look like:</p>';

echo '<pre><code>function add_acf_custom_settings() {
    acf_add_local_field_group([
        \'key\' => \'group_custom_settings\',
        \'title\' => \'Custom Settings\',
        \'fields\' => [
            [
                \'key\' => \'field_custom_text\',
                \'label\' => \'Custom Text Field\',
                \'name\' => \'custom_text\',
                \'type\' => \'text\',
                \'instructions\' => \'Enter some custom text\',
                \'required\' => 0,
            ],
            [
                \'key\' => \'field_custom_image\',
                \'label\' => \'Custom Image\',
                \'name\' => \'custom_image\',
                \'type\' => \'image\',
                \'return_format\' => \'id\',
                \'preview_size\' => \'thumbnail\',
                \'library\' => \'all\',
            ],
        ],
        \'location\' => [[[
            \'param\' => \'options_page\',
            \'operator\' => \'==\',
            \'value\' => \'hawptheme\',
        ]]],
        \'menu_order\' => 0,
        \'position\' => \'normal\',
        \'style\' => \'default\',
        \'label_placement\' => \'top\',
        \'instruction_placement\' => \'label\',
        \'hide_on_screen\' => \'\',
        \'active\' => true,
    ]);
}
add_action(\'acf/init\', \'add_acf_custom_settings\', 5);</code></pre>';
echo '</div>';

                                            } else {
                                                $custom_return = admin_url('admin.php?page=' . $this->page_slug . '&tab=custom_settings#custom_settings');
                                                acf_form([
                                                    'post_id' => 'options',
                                                    'field_groups' => ['group_custom_settings'],
                                                    'form' => true,
                                                    'return' => $custom_return,
                                                    'submit_value' => 'Save Custom Options',
                                                ]);
                                            } ?>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <form action="options.php" method="post">
                                        <?php
                                            // Use the tab's option group
                                            settings_fields($this->option_group . '_' . $tab_id);
                                        ?>
                                        <div class="tab-content">
                                            <?php if (isset($structure[$tab_id])) {
                                                foreach ($structure[$tab_id] as $box) {
                                                    $this->render_settings_box(
                                                        $box['title'],
                                                        $box['fields'],
                                                        $box['description']
                                                    );
                                                }
                                            } ?>
                                        </div>
                                        <?php submit_button(); ?>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function admin_enqueue_scripts($hook) {
        if (!str_contains($hook, 'page_' . $this->page_slug)) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('hawp-admin-options', HM_URL . '/assets/css/admin-options.css');
        wp_enqueue_script('hawp-admin-options', HM_URL . '/assets/js/admin-options.js', ['jquery'], null, true);
    }

    /**
     * Render SVG library field
     */
    public function render_svg_library_field($args) {
        $svgs = get_option($args['name'], []);
        ?>
        <div class="hawp-svg-library">
            <div class="hawp-svg-list">
                <?php foreach ($svgs as $index => $svg): ?>
                    <div class="hawp-svg-item">
                        <div class="hawp-svg-item-header">
                            <div class="hawp-svg-preview">
                                <?php echo $svg['code']; ?>
                            </div>
                            <div class="hawp-svg-controls">
                                <input type="text" 
                                       name="<?php echo esc_attr($args['name']); ?>[<?php echo $index; ?>][label]" 
                                       value="<?php echo esc_attr($svg['label']); ?>" 
                                       placeholder="Label" />
                                <button type="button" class="button button-secondary hawp-remove-svg">Remove</button>
                            </div>
                        </div>
                        <textarea name="<?php echo esc_attr($args['name']); ?>[<?php echo $index; ?>][code]" 
                                  rows="4" 
                                  class="large-text code"><?php echo esc_textarea($svg['code']); ?></textarea>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button button-secondary hawp-add-svg">Add SVG</button>
            <?php if (!empty($args['description'])): ?>
                <p class="description"><?php echo $args['description']; ?></p>
            <?php endif; ?>
        </div>
        <script>
            jQuery(document).ready(function($) {
                $('.hawp-add-svg').on('click', function() {
                    var index = $('.hawp-svg-item').length;
                    var template = `
                        <div class="hawp-svg-item">
                            <div class="hawp-svg-item-header">
                                <div class="hawp-svg-preview"></div>
                                <div class="hawp-svg-controls">
                                    <input type="text" 
                                           name="<?php echo esc_attr($args['name']); ?>[${index}][label]" 
                                           value="" 
                                           placeholder="Label" />
                                    <button type="button" class="button button-secondary hawp-remove-svg">Remove</button>
                                </div>
                            </div>
                            <textarea name="<?php echo esc_attr($args['name']); ?>[${index}][code]" 
                                      rows="4" 
                                      class="large-text code"></textarea>
                        </div>
                    `;
                    $('.hawp-svg-list').append(template);
                });

                $(document).on('click', '.hawp-remove-svg', function() {
                    if (!confirm('Are you sure you want to remove this SVG? This action cannot be undone.')) {
                        return;
                    }
                    $(this).closest('.hawp-svg-item').remove();
                });

                // Update preview when code changes
                $(document).on('input', '.hawp-svg-item textarea', function() {
                    var preview = $(this).closest('.hawp-svg-item').find('.hawp-svg-preview');
                    preview.html($(this).val());
                });
            });
        </script>
        <?php
    }

    /**
     * Add custom body class to theme options pages
     */
    public function add_admin_body_class($classes) {
        $screen = get_current_screen();
        if (strpos($screen->id, $this->page_slug) !== false) {
            $classes .= ' hm-admin-page';
        }
        return $classes;
    }
}

/**
 * Initialize Hawp_Theme_Options class with a function.
 */
function hawp_theme_options() {
    global $hawp_theme_options;

    // Instantiate only once.
    if (!isset($hawp_theme_options)) {
        $hawp_theme_options = new Hawp_Theme_Options();
    }
    return $hawp_theme_options;
}
hawp_theme_options();

endif; // class_exists check 