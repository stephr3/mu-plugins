<?php
/**
Plugin Name: Show modified Date in admin lists
Plugin URI: https://apasionados.es
Description: Shows a new, sortable, column with the modified date in the lists of pages and posts in the WordPress admin panel. It also shows the username that did the last update.
Version: 1.3
Author: Apasionados.es
Author URI: https://apasionados.es
License: GPL2
Text Domain: show-modified-date-in-admin-lists
*/
 /*  Copyright 2016  Apasionados.es  (email: info@apasionados.es)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// $plugin_header_translate = array( __('Show modified Date in admin lists', 'show-modified-date-in-admin-lists'), __('Shows a new, sortable, column with the modified date in the lists of pages and posts in the WordPress admin panel. It also shows the username that did the last update.', 'show-modified-date-in-admin-lists') );

// add_action( 'admin_init', 'show_modified_date_in_admin_lists_language' );
// function show_modified_date_in_admin_lists_language() {
//         load_plugin_textdomain( 'show-modified-date-in-admin-lists', false,  dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
// }

// Register Modified Date Column for both posts & pages
function modified_column_register( $columns ) {
        $columns['Modified'] = __( 'Last Updated', 'show-modified-date-in-admin-lists' );
        return $columns;
}
add_filter( 'manage_posts_columns', 'modified_column_register' );
add_filter( 'manage_pages_columns', 'modified_column_register' );

function modified_column_display( $column_name, $post_id ) {
        switch ( $column_name ) {
        case 'Modified':
                global $post; 
                echo '<p class="mod-date">';
                // echo 'Updated:';
                // echo '<br>';
                echo get_the_modified_date().' at '.get_the_modified_time();
                echo '</p>';
                break; // end all case breaks
        }
}
add_action( 'manage_posts_custom_column', 'modified_column_display', 10, 2 );
add_action( 'manage_pages_custom_column', 'modified_column_display', 10, 2 );

function modified_column_register_sortable( $columns ) {
        $columns['Modified'] = 'modified';
        return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'modified_column_register_sortable' );
add_filter( 'manage_edit-page_sortable_columns', 'modified_column_register_sortable' );

?>