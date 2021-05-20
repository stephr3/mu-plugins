<?php

function post_types() {
  register_post_type('class', array(
    'rewrite' => array('slug' => 'classes'),
    'has_archive' => true,    
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

  //Student Learning Resources Post Type
  register_post_type('studentresource', array(
    'rewrite' => array('slug' => 'studentresources'),
    'has_archive' => true,    
    'public' => true,
    'labels' => array(
      'name' => 'Student Learning Resources',
      'add_new_item' => 'Add New Resource',
      'edit_item' => 'Edit Resource',
      'all_items' => 'All Resources',
      'singular_name' => 'Student Learning Resource'
    ),
    'menu_icon' => 'dashicons-portfolio'
  ));

  //Teaching Resources Post Type
  register_post_type('teachingresource', array(
    'rewrite' => array('slug' => 'teachingresources'),
    'has_archive' => true,    
    'public' => true,
    'labels' => array(
      'name' => 'Teacher Resources',
      'add_new_item' => 'Add New Resource',
      'edit_item' => 'Edit Resource',
      'all_items' => 'All Resources',
      'singular_name' => 'Teaching Resource'
    ),
    'menu_icon' => 'dashicons-edit-large'
  ));

  //Real World Learning Resources Post Type
  register_post_type('realworldlearning', array(
    'rewrite' => array('slug' => 'realworldlearning'),
    'has_archive' => true,    
    'public' => true,
    'labels' => array(
      'name' => 'Real World Learning Resources',
      'add_new_item' => 'Add New Resource',
      'edit_item' => 'Edit Resource',
      'all_items' => 'All Resources',
      'singular_name' => 'Real World Learning Resource'
    ),
    'menu_icon' => 'dashicons-location-alt'
  ));
}

add_action('init', 'post_types');

// remove default editor and title from Classes in admin - only custom fields will show 
add_action('init', function() {
remove_post_type_support( 'class', 'editor' );
}, 99);

