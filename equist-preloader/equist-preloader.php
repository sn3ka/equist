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
        'ScrollTrigger',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollTrigger.min.js',
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
}
add_action('wp_enqueue_scripts', 'ccl_enqueue_assets');

/**
 * Inject loader HTML
 */
function ccl_render_loader_html() {
    ?>
    <div id="loader_wrapper">
        <div id="overlay"></div>

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
add_action('wp_body_open', 'ccl_render_loader_html');