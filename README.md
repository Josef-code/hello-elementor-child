# Hello Elementor Child Theme - Journal Feature

This repository contains a custom child theme for the [Hello Elementor](https://wordpress.org/themes/hello-elementor/) WordPress theme. It extends the base theme by adding a journal publishing feature, including custom styles, templates, and functionality.

## Features

- **Journal Archive**: Displays a grid of journal entries with pagination.
- **Single Journal Page**: Custom layout for individual journal entries with metadata and related articles.
- **Responsive Design**: Fully responsive layouts for journal archives and single pages.
- **Custom Shortcode**: `[journals]` shortcode to display the journal archive.

## Folder Structure

```
.
├── assets/
│   └── css/
│       ├── journal-archive.css   # Styles for the journal archive
│       ├── journal-single.css    # Styles for single journal pages
├── includes/
│   ├── class-journal-archive.php # Handles journal archive functionality
│   ├── class-journal-single.php  # Handles single journal page functionality
├── templates/
│   └── single-journal.php        # Template for single journal entries
├── functions.php                 # Theme setup and customizations
├── style.css                     # Child theme metadata and custom styles
```

## Installation

1. Download or clone this repository into your WordPress `wp-content/themes` directory.
2. Ensure the parent theme, [Hello Elementor](https://wordpress.org/themes/hello-elementor/), is installed and activated.
3. Activate the child theme from the WordPress admin dashboard.

## Usage

### Display Journal Archive
Use the `[journals]` shortcode in any post, page, or widget to display the journal archive.

```php
[journals posts_per_page="10"]
```

### Customize Styles
Modify the CSS files in the `assets/css` directory to adjust the appearance of the journal archive and single pages.

## Development

### Enqueue Styles
The child theme enqueues its custom styles in the `enqueue_styles` method of the `Journal_Archive` class.

### Template Overrides
The `single-journal.php` template can be customized to change the layout of single journal entries.

## License

This project is licensed under the [GNU General Public License v3 or later](https://www.gnu.org/licenses/gpl-3.0.html).

## Author

Developed by [Joseph Bassey](https://github.com/Josef-code).
```
