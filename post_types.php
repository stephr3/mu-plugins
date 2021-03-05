<?php

function post_types() {
  register_post_type('class', array(
    'public' => true,
    'labels' => array(
      'name' => 'Classes',
      'add_new_item' => 'Add New Class',
      'edit_item' => 'Edit Class',
      'all_items' => 'All Classes',
      'singular_name' => 'Class'
    ),
    'menu_icon' => 'dashicons-book'
  ));
}

add_action('init', 'post_types');

// remove default editor and title from Classes in admin - only custom fields will show 
add_action('init', function() {
remove_post_type_support( 'class', 'editor' );
remove_post_type_support( 'class', 'title' );
}, 99);