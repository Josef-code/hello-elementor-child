<?php
/**
 * Journal Single Page Class
 * 
 * Handles the display of single journal pages with custom fields and related articles.
 * 
 * @package HelloElementorChild
 */

if (!class_exists('Journal_Single')) {

    class Journal_Single {
        
        /**
         * Constructor
         */
        public function __construct() {
            // Filter the single template for journals
            add_filter('single_template', array($this, 'get_journal_template'));
            
            // Add custom content to journal pages
            add_filter('the_content', array($this, 'display_journal_content'), 20);
            
            // Enqueue styles for single journal page
            add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        }
        
        /**
         * Enqueue necessary styles for the single journal page
         */
        public function enqueue_styles() {
            // Only enqueue on single journal pages
            if (is_singular('journal')) {
                wp_enqueue_style(
                    'journal-single-styles',
                    get_stylesheet_directory_uri() . '/assets/css/journal-single.css',
                    array(),
                    '1.0.0'
                );
            }
        }
        
        /**
         * Get custom template for single journal
         *
         * @param string $single_template The path to the single template
         * @return string Modified single template path
         */
        public function get_journal_template($single_template) {
            global $post;
            
            // Check if we're on a single journal post
            if ($post->post_type === 'journal') {
                // Create the template path
                $template_path = get_stylesheet_directory() . '/templates/single-journal.php';
                
                // Check if custom template exists
                if (file_exists($template_path)) {
                    return $template_path;
                }
            }
            
            return $single_template;
        }
        
        /**
         * Display custom journal content
         * 
         * @param string $content The original post content
         * @return string Modified post content with journal details and related articles
         */
        public function display_journal_content($content) {
            // Only run on single journal pages in the main query
            if (!is_singular('journal') || !in_the_loop() || !is_main_query()) {
                return $content;
            }
            
            // Start output buffering
            ob_start();
            
            // Get journal meta data
            $volume_number = $this->get_journal_field('volume_number');
            $issue_number = $this->get_journal_field('issue_number');
            $publication_date = $this->get_journal_field('publication_date');
            
            // Format the publication date if it exists
            $formatted_date = '';
            if (!empty($publication_date)) {
                $formatted_date = date_i18n(get_option('date_format'), strtotime($publication_date));
            }
            
            // Display journal metadata
            echo '<div class="journal-meta">';
            
            if (!empty($volume_number)) {
                echo '<div class="journal-volume"><strong>' . __('Volume', 'hello-elementor-child') . ':</strong> ' . esc_html($volume_number) . '</div>';
            }
            
            if (!empty($issue_number)) {
                echo '<div class="journal-issue"><strong>' . __('Issue', 'hello-elementor-child') . ':</strong> ' . esc_html($issue_number) . '</div>';
            }
            
            if (!empty($formatted_date)) {
                echo '<div class="journal-date"><strong>' . __('Published', 'hello-elementor-child') . ':</strong> ' . esc_html($formatted_date) . '</div>';
            }
            
            echo '</div>'; // .journal-meta
            
            // Display the original content
            echo '<div class="journal-content">';
            echo $content;
            echo '</div>'; // .journal-content
            
            // Display related articles
            $this->display_related_articles();
            
            // Return the buffered content
            return ob_get_clean();
        }
        
        /**
         * Get ACF field value for a journal
         * 
         * @param string $field_name The name of the field to retrieve
         * @return mixed The field value or empty string if not found
         */
        private function get_journal_field($field_name) {
            // Check if Advanced Custom Fields is active
            if (function_exists('get_field')) {
                return get_field($field_name) ?: '';
            }
            
            // Fallback to post meta if ACF is not available
            return get_post_meta(get_the_ID(), $field_name, true) ?: '';
        }
        
        /**
         * Display related articles for the current journal
         */
        private function display_related_articles() {
            // Get the current journal ID
            $journal_id = get_the_ID();
            
            // Query for related articles
            $args = array(
                'post_type' => 'article',
                'posts_per_page' => -1, // Get all related articles
                'meta_query' => array(
                    array(
                        'key' => 'journal', // The relationship field name
                        'value' => '"' . $journal_id . '"', // ACF stores relationship as serialized data, so we need to search for the ID in quotes
                        'compare' => 'LIKE'
                    )
                ),
                'orderby' => 'title',
                'order' => 'ASC'
            );
            
            $related_articles = new WP_Query($args);
            
            // Check if we have related articles
            if ($related_articles->have_posts()) {
                echo '<div class="journal-related-articles">';
                echo '<h3 class="related-articles-title">' . __('Articles in this Journal', 'hello-elementor-child') . '</h3>';
                
                echo '<ul class="articles-list">';
                while ($related_articles->have_posts()) {
                    $related_articles->the_post();
                    
                    echo '<li class="article-item">';
                    echo '<a href="' . get_permalink() . '" class="article-link">' . get_the_title() . '</a>';
                    
                    // You can add more article meta info here if needed
                    // e.g., author, excerpt, etc.
                    
                    echo '</li>';
                }
                echo '</ul>';
                
                echo '</div>'; // .journal-related-articles
                
                // Reset post data
                wp_reset_postdata();
            }
        }
    }
}