<?php
// ------------------------------------------
// RankMath integration.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!class_exists('Hawp_RankMath')):

class Hawp_RankMath {

    public function __construct() {

        // Only add the filter if RankMath is active
        if ($this->is_rankmath_active()) {
            add_filter('rank_math/modules', [$this, 'disable_modules']);
        }
    }

    /**
     * Check if RankMath is active
     */
    private function is_rankmath_active() {
        return class_exists('RankMath');
    }

    /**
     * Disable RankMath modules based on theme option
     */
    public function disable_modules($modules) {
        // Only proceed if the option is enabled
        if (!get_theme_option('disable_rankmath_modules')) {
            return $modules;
        }

        // List of modules to disable
        $disabled_modules = [
            '404-monitor',
            'role-manager',
            'amp',
            'bbpress',
            'buddypress',
            'redirections',
            'web-stories'
        ];

        // Remove each disabled module
        foreach ($disabled_modules as $module) {
            unset($modules[$module]);
        }

        return $modules;
    }
}

/**
 * Initialize Hawp_RankMath class with a function.
 */
function hawp_rankmath() {
    global $hawp_rankmath;

    // Instantiate only once.
    if (!isset($hawp_rankmath)) {
        $hawp_rankmath = new Hawp_RankMath();
    }
    return $hawp_rankmath;
}
hawp_rankmath();

endif; // class_exists check 
