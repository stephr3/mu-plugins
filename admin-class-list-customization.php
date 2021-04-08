<?php

// Register Last Updated and Region column to the table
function register_custom_columns( $columns ) {
        unset($columns['date']);
        $columns['Modified'] = 'Last Updated';
        $columns['Region'] = 'Region';
        return $columns;
}
add_filter( 'manage_class_posts_columns', 'register_custom_columns' );

// Adding data to each row for the newly registed columns 
function add_custom_columns_data_to_display( $column_name, $post_id ) {
        switch ( $column_name ) {
        case 'Modified':
                echo '<p class="mod-date">';
                echo get_the_modified_date().' at '.get_the_modified_time();
                echo '</p>';
                break;
        case 'Region':
                echo '<p class="region">';
                echo get_post_meta( $post_id, 'region', true );
                echo '</p>';
                break;
        }
}
add_action( 'manage_class_posts_custom_column', 'add_custom_columns_data_to_display', 10, 2 );

// Allow ASC/DESC sortable property
function register_custom_columns_sortable( $columns ) {
        $columns['Modified'] = 'modified';
        $columns['Region'] = 'region';
        return $columns;
}
add_filter( 'manage_edit-class_sortable_columns', 'register_custom_columns_sortable' );

// Custom field data like region require implemation unlike modified
// region column sortable ASC/DESC by region
function region_custom_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'region' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'region',
            'orderby' => 'meta_value'
        ) );
    }

    return $vars;
}
add_filter( 'request', 'region_custom_column_orderby' );

// Adding filter dropdown for custom field data
function region_custom_filter_dropdown() {
    global $post_type;
    if ( $post_type == 'class' ) {

        $regions = get_regions_list();

        echo '<label for="filter-by-region" class="screen-reader-text">Filter by region</label>';
        echo '<select name="r" id="filter-by-region">';
            echo '<option selected value="0"> All region </option>';
            foreach( $regions as $region ) {
                $selected = ( !empty( $_GET['r'] ) AND $_GET['r'] == $region->meta_value ) ? 'selected="selected"' : '';
                echo '<option'. $selected . ' value="' . $region->meta_value .'">'. $region->meta_value . '</option>';
            }
        echo '</select>';

    }
}
// fetches the list of regions in the database
function get_regions_list() {
    global $wpdb;
    $regions = $wpdb->get_results("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = 'region' LIMIT 20");
    return $regions;
}

add_action( 'restrict_manage_posts', 'region_custom_filter_dropdown' );

// Add filter logic to region dropdown values
function region_custom_filter_dropdown_logic( $query ) {
  if( is_admin() AND $query->query['post_type'] == 'class' ) {
    $qv = &$query->query_vars;
    $qv['meta_query'] = array();

    if( !empty( $_GET['r'] ) ) {
      $qv['meta_query'][] = array(
        'field' => 'region',
        'value' => $_GET['r'],
        'compare' => '=',
        'type' => 'CHAR'
      );
    }
  }
}

add_filter( 'parse_query','region_custom_filter_dropdown_logic' );
?>