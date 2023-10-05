<?php

    // Add the "Views" column to the list of posts in the admin panel
    function add_views_column($columns) {
        // Add the new "Views" column
        $columns["post_views"] = "Views";

        return $columns;
    }
    add_filter("manage_posts_columns", "add_views_column");

    // Fill the "Views" column with the value "_custom_views"
    function fill_column_views($column_name, $post_id) {
        if ($column_name === "post_views") {
            // Get the view count
            $custom_views = get_post_meta($post_id, "_custom_views", true);

            // Display the view count
            echo (int)$custom_views;
        }
    }
    add_action("manage_posts_custom_column", "fill_column_views", 10, 2);

    // Make the "Views" column sortable
    function make_views_column_sortable($columns) {
        $columns["post_views"] = "post_views";
        return $columns;
    }
    add_filter("manage_edit-post_sortable_columns", "make_views_column_sortable");

?>