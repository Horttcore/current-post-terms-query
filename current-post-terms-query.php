<?php
/**
 * Plugin Name: Current Post Terms Query
 * Description: Adds an option to show only the terms assigned to the current post in Terms Query blocks.
 * Author: Ralf Hortt
 * Author URI: mailto:mail@ralfhortt.dev
 * Version: 1.0.0
 * Requires at least: 6.1
 * Requires PHP: 8.1
 * License: GPL-2.0-or-later
 * Text Domain: current-post-terms-query
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/src/CurrentPostTermsQueryFilter.php';

\CurrentPostTermsQuery\CurrentPostTermsQueryFilter::init();

add_action('enqueue_block_editor_assets', static function (): void {
    $asset_file = __DIR__ . '/build/index.tsx.asset.php';
    $script_file = __DIR__ . '/build/index.tsx.js';

    if (! is_readable($asset_file) || ! is_readable($script_file)) {
        return;
    }

    $asset = require $asset_file;

    wp_enqueue_script(
        'current-post-terms-query-editor',
        plugins_url('build/index.tsx.js', __FILE__),
        $asset['dependencies'] ?? [],
        $asset['version'] ?? filemtime($script_file),
        true,
    );
});
