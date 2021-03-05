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