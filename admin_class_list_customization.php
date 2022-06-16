<?php

// Register Last Updated and Region column to the table
function register_custom_columns( $columns ) {
        unset($columns['date']);
        $columns['Last Updated'] = 'Last Updated';
        $columns['Region'] = 'Region';
        $columns["Happening Now?"] = "Happening Now?";
        $columns["Organization"] = "Organization";
        $columns["Updater Name"] = "Updater Name";
         return $columns;
}
add_filter( 'manage_class_posts_columns', 'register_custom_columns' );

// Adding data to each row for the newly registered columns 
function add_custom_columns_data_to_display( $column_name, $post_id ) {
        switch ( $column_name ) {
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
        case 'Happening Now?':
                  echo '<p class="happening_now">';
                  echo get_post_meta($post_id, 'happening_now', true);
                  echo '</p>';
                  break;
        case 'Updater Name':
                echo '<p class="updater_name">';
                echo get_post_meta($post_id, 'updater_name', true);
                echo '</p>';
                break;
        }
}
add_action( 'manage_class_posts_custom_column', 'add_custom_columns_data_to_display', 10, 2 );

// Allow ASC/DESC sortable property
function register_custom_columns_sortable( $columns ) {
        $columns['Last Updated'] = 'last_updated';
        $columns['Region'] = 'region';
        $columns['Happening Now?'] = 'happening_now';
        $columns['Updater Name'] = 'updater_name';
        $columns['Organization'] = 'organization';
        return $columns;
}
add_filter( 'manage_edit-class_sortable_columns', 'register_custom_columns_sortable' );


// Custom field data like region require implemation unlike modified
// region column sortable ASC/DESC by region
function custom_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'region' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'region',
            'orderby' => 'meta_value'
        ) );
    }
    
    if ( isset( $vars['orderby'] ) && 'happening_now' == $vars['orderby'] ) {
      $vars = array_merge( $vars, array(
          'meta_key' => 'happening_now',
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
    if ( $post_type == 'class' ) {


        $regions = get_data_list("region");
        $happening_now = get_data_list("happening_now");
        $organizations = get_data_list("organization");
        $updaters = get_data_list("updater_name");
        $dates = get_dates_list();
        
        populate_dropdown_date($dates, "All dates", "m", "date");
        populate_dropdown($regions, "All regions", "r", "region");
        populate_dropdown($happening_now, "Happening Now?", "hn", "happening_now");
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
// Date Dropdown -- Change this something else like a date picker
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
  if( is_admin() AND $query->query['post_type'] == 'class' ) {
    $qv = &$query->query_vars;
    $qv['meta_query'] = array();
    $qv['date_query'] = array();

    //region is select ACF field
    if( !empty( $_GET['r'] ) ) {
      $qv['meta_query'][] = array(
        'field'     => 'region',
        'value'     => $_GET['r'],
        'compare'   => '=',
        'type'      => 'CHAR'
      );
    }
    
    //hn is select ACF field
    if( !empty( $_GET['hn'] ) ) {
      $qv['meta_query'][] = array(
        'field'     => 'happening_now',
        'value'     => $_GET['hn'],
        'compare'   => '=',
        'type'      => 'CHAR'
      );
    }
    
    //org is select ACF field
    if( !empty( $_GET['org'] ) ) {
      $qv['meta_query'][] = array(
        'field'     => 'organization',
        'value'     => $_GET['org'],
        'compare'   => '=',
        'type'      => 'CHAR'
      );
    }
    
    //updn is text ACF field
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
add_filter('months_dropdown_results', '__return_empty_array');
?>