<?php

    function routes_posts_order_by() {
        register_rest_route("wp/v2", "/posts/view", array(
            "methods" => "GET",
            "callback" => "posts_order_by_views",
        ));
    }
    add_action("rest_api_init", "routes_posts_order_by");

    // Allow sorting by "Views"
    function views_column_orderby($query) {
        if (!is_admin())
            return;

        $orderby = $query->get("orderby");

        if ($orderby === "post_views") {
            $query->set("meta_key", "_custom_views");
            $query->set("orderby", "meta_value_num");
        }
    }
    add_action("pre_get_posts", "views_column_orderby");

    // Get posts with custom sorting
    function posts_order_by_views($request) {
        $params = $request->get_params();
        
        // Standard query arguments
        $args = array(
            "post_type" => "post",
            "posts_per_page" => $params["per_page"] ?? 10, // Number of posts per page
            "paged" => $params["page"] ?? 1, // Page number
        );

        if ($orderby === "post_views") {
            $query->set("meta_key", "_custom_views");
            $query->set("orderby", "meta_value_num");
        }
        
        // Add custom sorting by "Views"
        // /wp-json/wp/v2/posts/view?orderby=views&order=asc
        if (isset($params["orderby"]) && $params["orderby"] === "views") {
            $args["meta_key"] = "_custom_views";
            $args["orderby"] = "meta_value_num";
        }

        // Define the order (ascending or descending)
        if (isset($params["order"]) && strtolower($params["order"]) === "asc") {
            $args["order"] = "ASC";
        } else {
            $args["order"] = "DESC";
        }

        $posts = get_posts($args);
        $data = array();
        
        foreach ($posts as $post) {
            $views = get_post_meta($post->ID, "_custom_views", true);
            $acf = get_fields($post->ID);

            $post_data = array(
                "id" => $post->ID,
                "status" => $post->post_status,
                "title" => $post->post_title,
                "slug" => $post->post_name,
                "views" => (int)$views,
                "author" => $post->post_author,
                "date" => $post->post_date,
                "modified" => $post->post_modified,
                "acf" => $acf,
                "content" => $post->post_content
            );
            
            $data[] = $post_data;
        }

        return $data;
    }

?>