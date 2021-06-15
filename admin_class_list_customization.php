<?php

// Register Last Updated and Region column to the table
function register_custom_columns( $columns ) {
        unset($columns['date']);
        // Add custom columns
        $columns['Last Updated'] = 'Last Updated';
        $columns['Region'] = 'Region';
        $columns["Organization"] = "Organization";
        $columns["Updater Name"] = "Updater Name";

        return $columns;
}
// change this to manage_<post_type>_posts_columns
add_filter( 'manage_class_posts_columns', 'register_custom_columns' );

// Adding data to each row for the newly registered columns 
function add_custom_columns_data_to_display( $column_name, $post_id ) {
        switch ( $column_name ) {
            // Change to desired columns
        case 'Last Updated':
                echo '<p class="mod-date">';
                echo get_the_modified_date().' at '.get_the_modified_time();
                echo '</p>';
                break;
        case 'Region':
                echo '<p class="region">';
                echo get_post_meta( $post_id, 'region', true);
                echo '</p>';
                break;
        case 'Organization':
                echo '<p class="organization">';
                echo get_post_meta($post_id, 'organization', true);
                echo '</p>';
                break;
        case 'Updater Name':
                echo '<p class="updater_name">';
                echo get_post_meta($post_id, 'updater_name', true);
                echo '</p>';
                break;
        }
}
// change to 'manage_<post_type>_posts...'
add_action( 'manage_class_posts_custom_column', 'add_custom_columns_data_to_display', 10, 2 );

// Allow ASC/DESC sortable property
function register_custom_columns_sortable( $columns ) {
        // Use field names
        $columns['Last Updated'] = 'last_updated';
        $columns['Region'] = 'region';
        $columns['Updater Name'] = 'updater_name';
        $columns['Organization'] = 'organization';
        return $columns;
}
// change to 'manage_edit-<post_type>_sortable...'
add_filter( 'manage_edit-class_sortable_columns', 'register_custom_columns_sortable' );


// Custom-fields like region require below function
// region column sortable ASC/DESC by region
function custom_column_orderby( $vars ) {
    // change to desired fields
    if ( isset( $vars['orderby'] ) && 'region' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'region',
            'orderby' => 'meta_value'
        ) );
    }
   
    if ( isset( $vars['orderby'] ) && 'organization' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'organization',
            'orderby' => 'meta_value'
        ) );
    }

    if ( isset( $vars['orderby'] ) && 'updater_name' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'updater_name',
            'orderby' => 'meta_value'
        ) );
    }

    return $vars;
}

add_filter( 'request', 'custom_column_orderby' );

// Adding filter dropdown for custom field data
function custom_filter_dropdown() {
    global $post_type;
    // Change to deisred post type
    if ( $post_type == 'class' ) {
        //change to desired fields
        $regions = get_data_list("region");
        $organizations = get_data_list("organization");
        $updaters = get_data_list("updater_name");
        $dates = get_dates_list();

        // Data, initial dropdown choice, URL param (choose a short-hand name), field  
        populate_dropdown_date($dates, "All dates", "m", "date");
        populate_dropdown($regions, "All regions", "r", "region");
        populate_dropdown($organizations, "All organizations", "org", "organization");
        populate_dropdown($updaters, "All updaters", "updn", "updater");

    }
}
// fetches the list of regions in the database
function get_data_list($meta_key) {
    global $wpdb;
    $data = $wpdb->get_results("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = '" . $meta_key . "' LIMIT 20");
    return $data;
}

function populate_dropdown( $list_of_data, $select_none_option_name, $param_string_name, $filter_name){
    echo '<label for="filter-by-' . $filter_name . '" class="screen-reader-text">Filter by ' . $filter_name . '</label>';
    echo '<select name="' . $param_string_name . '" id="filter-by-' . $filter_name . '">';
        echo '<option selected value="0"> ' . $select_none_option_name . ' </option>';
        foreach( $list_of_data as $data ) {
            $selected = ( !empty( $_GET[$param_string_name] ) AND $_GET[$param_string_name] == $data->meta_value ) ? 'selected' : '';
            echo '<option '. $selected . ' value="' . $data->meta_value .'">'. $data->meta_value . '</option>';
        }
    echo '</select>';
}
// Date Dropdown -- TODO: change later this something else like a date picker
function get_dates_list(){
    global $wpdb;
    $data = $wpdb->get_results("SELECT DISTINCT YEAR( post_modified ) AS year, MONTH( post_modified ) AS month FROM $wpdb->posts WHERE post_type = 'class' LIMIT 24");
    return $data;
}
function populate_dropdown_date( $list_of_data, $select_none_option_name, $param_string_name, $filter_name){
    echo '<label for="filter-by-' . $filter_name . '" class="screen-reader-text">Filter by ' . $filter_name . '</label>';
    echo '<select name="' . $param_string_name . '" id="filter-by-' . $filter_name . '">';
        echo '<option selected value="0"> ' . $select_none_option_name . ' </option>';
        foreach( $list_of_data as $data ) {
            $date = DateTime::createFromFormat('!m', $data->month)->format('F') . " " . $data->year;

            $selected = ( !empty( $_GET[$param_string_name] ) AND $_GET[$param_string_name] == $date ) ? 'selected' : '';
            echo '<option '. $selected . ' value="' . $date .'">'. $date . '</option>';
        }
    echo '</select>';
}

add_action( 'restrict_manage_posts', 'custom_filter_dropdown' );

// Add filter logic to region dropdown values
function custom_filter_dropdown_logic( $query ) {
    // change to desired post_type
  if( is_admin() AND $query->query['post_type'] == 'class' ) {
    // Change field and url value
    $qv = &$query->query_vars;
    $qv['meta_query'] = array();
    $qv['date_query'] = array();

    if( !empty( $_GET['r'] ) ) {
      $qv['meta_query'][] = array(
        'field'     => 'region',
        'value'     => $_GET['r'],
        'compare'   => '=',
        'type'      => 'CHAR'
      );
    }
    if( !empty( $_GET['org'] ) ) {
      $qv['meta_query'][] = array(
        'field'     => 'organization',
        'value'     => $_GET['org'],
        'compare'   => '=',
        'type'      => 'CHAR'
      );
    }
    if( !empty( $_GET['updn'] ) ) {
      $qv['meta_query'][] = array(
        'field'     => 'updater_name',
        'value'     => $_GET['updn'],
        'compare'   => '=',
        'type'      => 'CHAR'
      );
    }
    if( !empty( $_GET['m'] ) ) {
      $qv['date_query'][] = array(
        'column'     => 'post_modified_gmt',
        'month'      => date('m', strtotime($_GET['m'])),
        'year'       => date('Y', strtotime($_GET['m']))
      );
    }
  }
}

add_filter( 'parse_query','custom_filter_dropdown_logic' );
// Removes Default Date filter
add_filter('months_dropdown_results', '__return_empty_array');
?>