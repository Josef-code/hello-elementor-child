<?php
/**
 * Journal Archive Class
 * 
 * Handles the display of journals archive using a shortcode.
 * 
 * @package HelloElementorChild
 */

if (!class_exists('Journal_Archive')) {

    class Journal_Archive
    {

        /**
         * Constructor
         */
        public function __construct()
        {
            // Register shortcode
            add_shortcode('journals', array($this, 'render_journals_archive'));

            // Add styles for the journal grid
            add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        }

        /**
         * Enqueue necessary styles for the journal grid
         */
        public function enqueue_styles()
        {
            wp_enqueue_style(
                'journal-archive-styles',
                get_stylesheet_directory_uri() . '/assets/css/journal-archive.css',
                array(),
                '1.0.0'
            );
        }

        /**
         * Render the journals archive
         * 
         * @param array $atts Shortcode attributes
         * @return string HTML output
         */
        public function render_journals_archive($atts)
        {
            // Extract and merge attributes
            $atts = shortcode_atts(
                array(
                    'posts_per_page' => 15,
                    'paged' => max(1, get_query_var('paged')),
                ),
                $atts,
                'journals'
            );

            // Start output buffering
            ob_start();

            // Get journals
            $journals = $this->get_journals($atts);

            // Display journals
            $this->display_journals($journals, $atts);

            // Return the buffered content
            return ob_get_clean();
        }

        /**
         * Get journal posts
         * 
         * @param array $atts Query parameters
         * @return WP_Query Journal posts query
         */
        private function get_journals($atts)
        {
            // If we're on a paginated page, get the current page
            if (isset($_GET['journal_page']) && !empty($_GET['journal_page'])) {
                $current_page = intval($_GET['journal_page']);
            } else {
                $current_page = 1;
            }

            // Set up query args
            $args = array(
                'post_type' => 'journal',
                'posts_per_page' => $atts['posts_per_page'],
                'paged' => $current_page,
                'orderby' => 'date',
                'order' => 'DESC',
            );

            // Return new WP_Query instance
            return new WP_Query($args);
        }

        /**
         * Display journal posts in a grid
         * 
         * @param WP_Query $journals Journal posts query
         * @param array $atts Display parameters
         */
        private function display_journals($journals, $atts)
        {
            // Check if we have journals
            if ($journals->have_posts()) {
                // Output container
                echo '<div class="journals-archive-container">';

                // Output journal grid
                echo '<div class="journals-grid">';

                // Loop through journals
                while ($journals->have_posts()) {
                    $journals->the_post();

                    // Get the featured image
                    $featured_image = get_the_post_thumbnail(
                        get_the_ID(),
                        'medium',
                        array('class' => 'journal-featured-image')
                    );

                    // If no featured image, use a placeholder
                    if (empty($featured_image)) {
                        $featured_image = '<div class="journal-placeholder-image"></div>';
                    }

                    // Output each journal item
                    ?>
                    <div class="journal-item">
                        <a href="<?php the_permalink(); ?>" class="journal-link">
                            <div class="journal-image-wrapper">
                                <?php echo $featured_image; ?>
                            </div>
                            <h3 class="journal-title"><?php the_title(); ?></h3>
                        </a>
                    </div>
                    <?php
                }

                // Close grid container
                echo '</div>'; // .journals-grid

                // Display pagination if needed
                $this->display_pagination($journals, $atts);

                // Close main container
                echo '</div>'; // .journals-archive-container

                // Reset post data
                wp_reset_postdata();
            } else {
                // No journals found
                echo '<div class="no-journals-found">';
                echo '<p>' . __('No journals found.', 'hello-elementor-child') . '</p>';
                echo '</div>';
            }
        }

        /**
         * Display custom pagination for journals
         * 
         * @param WP_Query $journals Journal posts query
         * @param array $atts Pagination parameters
         */
        private function display_pagination($journals, $atts)
        {
            // Get total pages
            $total_pages = $journals->max_num_pages;

            // If we have more than one page, show pagination
            if ($total_pages > 1) {
                // Get current page
                $current_page = isset($_GET['journal_page']) ? intval($_GET['journal_page']) : 1;

                // Start pagination container
                echo '<div class="journal-pagination">';

                // Previous page link
                if ($current_page > 1) {
                    $prev_url = add_query_arg('journal_page', $current_page - 1);
                    echo '<a href="' . esc_url($prev_url) . '" class="journal-prev-page">&laquo; ' .
                        __('Previous', 'hello-elementor-child') . '</a>';
                }

                // Page numbers
                for ($i = 1; $i <= $total_pages; $i++) {
                    // Get page URL
                    $page_url = add_query_arg('journal_page', $i);

                    // Active class for current page
                    $active_class = ($current_page === $i) ? ' journal-page-active' : '';

                    // Output page number link
                    echo '<a href="' . esc_url($page_url) . '" class="journal-page-number' .
                        $active_class . '">' . $i . '</a>';
                }

                // Next page link
                if ($current_page < $total_pages) {
                    $next_url = add_query_arg('journal_page', $current_page + 1);
                    echo '<a href="' . esc_url($next_url) . '" class="journal-next-page">' .
                        __('Next', 'hello-elementor-child') . ' &raquo;</a>';
                }

                // Close pagination container
                echo '</div>'; // .journal-pagination
            }
        }
    }
}