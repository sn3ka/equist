<?php
/**
 * Plugin Name: Equist Canvas Preloader
 * Description: Global animated canvas loader (Adobe Animate + GSAP)
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue assets
 */
function ccl_enqueue_assets() {

    $mode = get_option('ccl_loader_mode', 'hybrid');

    /* === CSS === */
    wp_enqueue_style(
        'ccl-loader-style',
        plugin_dir_url(__FILE__) . 'assets/css/loader.css',
        [],
        '1.0.0'
    );

    /* === JS dependencies === */
    wp_enqueue_script(
        'gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js',
        [],
        '3.13.0',
        true
    );

    wp_enqueue_script(
        'createjs',
        'https://code.createjs.com/1.0.0/createjs.min.js',
        [],
        '1.0.0',
        true
    );

    /* === Loader JS === */
    wp_enqueue_script(
        'ccl-loader-script',
        plugin_dir_url(__FILE__) . 'assets/js/loader.js',
        ['gsap', 'createjs'],
        '1.0.0',
        true
    );

    /* === Pass settings to JS === */
    wp_localize_script(
        'ccl-loader-script',
        'CCL_LOADER',
        [
            'mode'   => $mode,
            'isHome' => is_front_page() || is_home(),
        ]
    );
}
add_action('wp_enqueue_scripts', 'ccl_enqueue_assets');

/**
 * Inject loader HTML (only if not OFF)
 */
function ccl_render_loader_html() {
    $mode = get_option('ccl_loader_mode', 'hybrid');

    if ($mode === 'off') {
        return;
    }

    $isHome = is_front_page() || is_home();

    $should_output = false;

    if ($mode === 'home') {
        $should_output = $isHome;           // every homepage visit
    } elseif ($mode === 'home-hybrid') {
        $should_output = $isHome;           // JS will limit to once/day
    } elseif ($mode === 'hybrid') {
        $should_output = true;              // JS decides everywhere
    }

    if (!$should_output) {
        return;
    }

    // Just the HTML – no <script> block
    ?>
    <div id="loader_wrapper" class="is-loading">
        <div id="loader-overlay"></div>
        <div class="frame top"></div>
        <div class="frame bottom"></div>
        <div class="frame left"></div>
        <div class="frame right"></div>
        <div id="animation_container">
            <canvas id="canvas" width="600" height="600"></canvas>
        </div>
    </div>
    <?php
}
add_action('wp_body_open', 'ccl_render_loader_html', 1);

/**
 * Admin setting (Settings → Reading)
 */
add_action('admin_init', function () {
    register_setting('reading', 'ccl_loader_mode');

    add_settings_field(
        'ccl_loader_mode',
        'Canvas Preloader',
        function () {
            $value = get_option('ccl_loader_mode', 'hybrid');
            ?>
            <select name="ccl_loader_mode">
    <option value="off" <?php selected($value, 'off'); ?>>Off</option>
    <option value="home" <?php selected($value, 'home'); ?>>Homepage only – every visit</option>
    <option value="home-hybrid" <?php selected($value, 'home-hybrid'); ?>>Homepage only – once per day</option>
    <option value="hybrid" <?php selected($value, 'hybrid'); ?>>Hybrid (any page once per day)</option>
</select>
<p class="description">Homepage only – every visit: shows animation on every homepage load.<br>Homepage only – once per day: shows only the first homepage visit each day.</p>
            <?php
        },
        'reading'
    );
});

