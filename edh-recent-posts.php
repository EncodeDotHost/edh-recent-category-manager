<?php
/**
 * Plugin Name: EDH Recent Posts
 * Description: Automatically maintains the latest N published posts in a chosen category in real-time.
 * Version: 1.1
 * Requires at least:
 * Requires PHP:
 * Tested up to: 7.0
 * Author: EncodeDotHost
 * Author URI: https://encode.host
 * Contributor: EncodeDotHost, nbwpuk
 * License: GPL v3 or later
 * Text Domain: edh-recent-posts
 *
 * @package edh-recent-posts
 * @author EncodeDotHost
 * @contributor nbwpuk
 * @version 1.1
 * @link https://github.com/EncodeDotHost/edh-recent-posts
 * @license GPL v3 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ---------------------------------------------------------------------------
// Admin: settings page under Posts menu
// ---------------------------------------------------------------------------

add_action( 'admin_menu', 'edh_rp_add_settings_page' );
function edh_rp_add_settings_page() {
    add_posts_page(
        'EDH Recent Posts Settings',
        'Recent Posts',
        'manage_options',
        'edh-recent-posts',
        'edh_rp_render_settings_page'
    );
}

// ---------------------------------------------------------------------------
// Admin: register settings
// ---------------------------------------------------------------------------

add_action( 'admin_init', 'edh_rp_register_settings' );
function edh_rp_register_settings() {
    register_setting( 'edh_recent_posts_settings', 'edh_recent_posts_category', array(
        'sanitize_callback' => 'absint',
    ) );
    register_setting( 'edh_recent_posts_settings', 'edh_recent_posts_count', array(
        'sanitize_callback' => 'edh_rp_sanitize_count',
    ) );
}

function edh_rp_sanitize_count( $value ) {
    $value = absint( $value );
    return ( $value >= 1 && $value <= 15 ) ? $value : 6;
}

// ---------------------------------------------------------------------------
// Admin: settings page HTML
// ---------------------------------------------------------------------------

function edh_rp_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $default_cat_id  = (int) get_option( 'default_category', 1 );
    $all_categories  = get_categories( array(
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ) );
    $categories = array_filter( $all_categories, function( $cat ) use ( $default_cat_id ) {
        return (int) $cat->term_id !== $default_cat_id;
    } );

    $selected_cat   = (int) get_option( 'edh_recent_posts_category', 0 );
    $selected_count = (int) get_option( 'edh_recent_posts_count', 6 );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'edh_recent_posts_settings' ); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="edh_recent_posts_category">Managed Category</label>
                    </th>
                    <td>
                        <select name="edh_recent_posts_category" id="edh_recent_posts_category">
                            <option value="0">— Select a category —</option>
                            <?php foreach ( $categories as $cat ) : ?>
                                <option value="<?php echo esc_attr( $cat->term_id ); ?>"
                                    <?php selected( $selected_cat, $cat->term_id ); ?>>
                                    <?php echo esc_html( $cat->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">The plugin will automatically keep the latest <em>N</em> published posts in this category.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="edh_recent_posts_count">Number of posts</label>
                    </th>
                    <td>
                        <select name="edh_recent_posts_count" id="edh_recent_posts_count">
                            <?php for ( $i = 1; $i <= 15; $i++ ) : ?>
                                <option value="<?php echo esc_attr( $i ); ?>"
                                    <?php selected( $selected_count, $i ); ?>>
                                    <?php echo esc_html( $i ); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// ---------------------------------------------------------------------------
// React to option changes: clean up old category, then re-run
// ---------------------------------------------------------------------------

add_action( 'updated_option', 'edh_rp_on_option_updated', 10, 3 );
function edh_rp_on_option_updated( $option, $old_value, $new_value ) {
    if ( 'edh_recent_posts_category' === $option && $old_value && $old_value !== $new_value ) {
        $old_posts = get_posts( array(
            'numberposts' => -1,
            'category'    => (int) $old_value,
            'post_status' => 'any',
            'fields'      => 'ids',
        ) );
        foreach ( $old_posts as $post_id ) {
            wp_remove_object_terms( $post_id, (int) $old_value, 'category' );
        }
    }

    if ( in_array( $option, array( 'edh_recent_posts_category', 'edh_recent_posts_count' ), true ) ) {
        edh_rp_update_recent_articles_category();
    }
}

// Fires on first-ever save when the option doesn't yet exist in wp_options.
add_action( 'added_option', 'edh_rp_on_option_added', 10, 1 );
function edh_rp_on_option_added( $option ) {
    if ( in_array( $option, array( 'edh_recent_posts_category', 'edh_recent_posts_count' ), true ) ) {
        edh_rp_update_recent_articles_category();
    }
}

// ---------------------------------------------------------------------------
// Post lifecycle hooks
// ---------------------------------------------------------------------------

add_action( 'transition_post_status', 'edh_rp_trigger_on_status_change', 10, 3 );
function edh_rp_trigger_on_status_change( $_new_status, $_old_status, $post ) {
    if ( 'post' === $post->post_type ) {
        edh_rp_update_recent_articles_category();
    }
}

add_action( 'deleted_post', 'edh_rp_trigger_on_deletion' );
function edh_rp_trigger_on_deletion( $post_id ) {
    if ( 'post' === get_post_type( $post_id ) ) {
        edh_rp_update_recent_articles_category();
    }
}

register_activation_hook( __FILE__, 'edh_rp_activate_plugin' );
function edh_rp_activate_plugin() {
    edh_rp_update_recent_articles_category();
}

// ---------------------------------------------------------------------------
// Core function
// ---------------------------------------------------------------------------

function edh_rp_update_recent_articles_category() {
    $category_id = (int) get_option( 'edh_recent_posts_category', 0 );
    $post_count  = (int) get_option( 'edh_recent_posts_count', 6 );

    if ( ! $category_id ) {
        return;
    }

    $latest_posts = get_posts( array(
        'numberposts' => $post_count,
        'post_status' => 'publish',
        'post_type'   => 'post',
        'fields'      => 'ids',
    ) );

    $current_posts_in_cat = get_posts( array(
        'numberposts' => -1,
        'category'    => $category_id,
        'post_status' => 'any',
        'fields'      => 'ids',
    ) );

    foreach ( array_diff( $current_posts_in_cat, $latest_posts ) as $post_id ) {
        wp_remove_object_terms( $post_id, $category_id, 'category' );
    }

    foreach ( array_diff( $latest_posts, $current_posts_in_cat ) as $post_id ) {
        wp_set_post_categories( $post_id, array( $category_id ), true );
    }
}
