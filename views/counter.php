<?php

    function views_counter_routes() {
        register_rest_route("wp/v2", "posts/view/(?P<post_id>\d+)", array(
            "methods" => "GET",
            "callback" => "views_counter_increment_view",
        ));
    }
    add_action("rest_api_init", "views_counter_routes");

    // Increment views count
    function views_counter_increment_view($request) {
        // Get post_id from request
        $post_id = $request->get_param("post_id");

        // Check if post exists
        if (!get_post_status($post_id))
            return new WP_Error("invalid_post", "Post not found", array("status" => 404));

        // Get current views count
        $views = get_post_meta($post_id, "_custom_views", true);

        // Increment views count
        $views = intval($views) + 1;
        
        // Update "_custom_views" value
        update_post_meta($post_id, "_custom_views", $views);

        // Get post data
        $post = get_post($post_id);

        // Add the "views" property to the response object
        $post->views = (int)$views;

        return $post->views;
    }

    // Add "views" to REST API response
    function views_counter_add_views_to_rest_response($data, $post) {
        // Get views count
        $views = get_post_meta($post->ID, "_custom_views", true);
        
        // Set views count in the response
        $data->data["views"] = (int)$views;

        return $data;
    }
    add_filter("rest_prepare_post", "views_counter_add_views_to_rest_response", 10, 3);

?>