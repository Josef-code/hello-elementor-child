<?php
/**
 * Hello Elementor Child Theme functions and definitions
 */

// Enqueue parent theme styles
function hello_elementor_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_enqueue_styles');

// Include our journal archive functionality
require_once get_stylesheet_directory() . '/includes/class-journal-archive.php';

// Initialize the Journal Archive class
function hello_elementor_child_init() {
    new Journal_Archive();
}
add_action('init', 'hello_elementor_child_init', 20);


/**
 * Include and initialize Journal Single class
 */
function hello_elementor_child_include_journal_single() {
    // Define the path to the Journal Single class file
    $journal_single_file = get_stylesheet_directory() . '/includes/class-journal-single.php';
    
    // Check if the file exists, and include it
    if (file_exists($journal_single_file)) {
        require_once $journal_single_file;
        
        // Initialize the Journal Single class
        new Journal_Single();
    } else {
        // Add an admin notice if the file doesn't exist
        add_action('admin_notices', function() {
            ?>
            <div class="notice notice-error">
                <p><?php _e('Journal Single class file not found. Please make sure it exists at: ' . get_stylesheet_directory() . '/includes/class-journal-single.php', 'hello-elementor-child'); ?></p>
            </div>
            <?php
        });
    }
}
add_action('init', 'hello_elementor_child_include_journal_single');

