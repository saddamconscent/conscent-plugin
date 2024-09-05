<?php

if ( ! function_exists( 'conscent_content_id_exist' ) ) {

    function conscent_content_id_exist($postArray) {
        $postJsonData = array(
            'clientContentId' => (string)$postArray['ID'],
            'clientId' => (string)CONSCENT_CLIENT_ID,
        );

        $url = add_query_arg($postJsonData, CONSCENT_API_URL . "/api/v2/content/");
        $response = wp_remote_get($url, array(
            'timeout' => 10,
            'redirection' => 10,
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => array(),
            'cookies' => array(),
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $responseBody = wp_remote_retrieve_body($response);
        $responseArray = json_decode($responseBody);
    
        if (isset($responseArray->statusCode)) {
            return false;
        } else {
            return true;
        }
    }

}
/***/

if ( ! function_exists( 'conscent_edit_content_on_conscent' ) ) {

    function conscent_edit_content_on_conscent($postArray) {
        $contentId = (string)$postArray['ID'];
        $title = (string)get_post_field('post_title', $postArray['ID']);
        $price = (int)$postArray['conscent_price'];
        $duration = (int)(($postArray['conscent_duration'] == '' || $postArray['conscent_duration'] < CONSCENT_DEFAULT_STORY_DURATION) ? CONSCENT_DEFAULT_STORY_DURATION : $postArray['conscent_duration']);
        $authorId = get_post_field('post_author', $postArray['ID']);
        $authorName = get_the_author_meta('first_name', $authorId);
        
        $tags_list = [];
        $tags = get_the_tags($contentId);
        if ($tags) {
            foreach ($tags as $tag) {
                $tags_list[] = $tag->name;
            }
        }
        $tagslist = wp_json_encode($tags_list);
    
        $cat_name = [];
        $category_detail = get_the_category($contentId);
        if ($category_detail) {
            foreach ($category_detail as $cd) {
                $cat_name[] = $cd->cat_name;
            }
        }
        $catname = wp_json_encode($cat_name);
    
        $url = get_permalink($postArray['ID']);
        
        $sections_names = wp_get_object_terms($postArray['ID'], 'sections', array("fields" => "names"));
        $sectionslist = wp_json_encode($sections_names);
        
        $postJsonData = array(
            'contentId' => $contentId,
            'title' => $title,
            'authorName' => $authorName,
            'authorId' => $authorId,
            'url' => $url,
            'categories' => json_decode($catname),
            'sections' => json_decode($sectionslist),
            'tags' => json_decode($tagslist),
            'price' => $price,
            'duration' => $duration
        );
    
        $response = wp_remote_request(
            CONSCENT_API_URL . "/api/v2/content/" . $contentId,
            array(
                'method' => 'PATCH',
                'body' => wp_json_encode($postJsonData),
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($apiKeySecret),
                    'Content-Type' => 'application/json'
                ),
                'timeout' => 10,
                'redirection' => 10,
                'blocking' => true,
                'httpversion' => '1.1',
                'sslverify' => true,
            )
        );
    
        if (is_wp_error($response)) {
            // Handle error
            return false;
        }
    
        $responseBody = wp_remote_retrieve_body($response);
        $responseArray = json_decode($responseBody);
    
        if (isset($responseArray->statusCode)) {
            return false;
        } else {
            return true;
        }
    }

}
/***/

if ( ! function_exists( 'conscent_add_content_on_conscent' ) ) {

    function conscent_add_content_on_conscent($postArray) {
        $contentId = (string)$postArray['ID'];
        $title = (string)get_post_field('post_title', $postArray['ID']);
        $price = (int)$postArray['conscent_price'];
        $duration = (int)(($postArray['conscent_duration'] == '' || $postArray['conscent_duration'] < CONSCENT_DEFAULT_STORY_DURATION) ? CONSCENT_DEFAULT_STORY_DURATION : $postArray['conscent_duration']);
        $authorId = get_post_field('post_author', $postArray['ID']);
        $authorName = get_the_author_meta('first_name', $authorId);
        
        $tags_list = [];
        $tags = get_the_tags($contentId);
        if ($tags) {
            foreach ($tags as $tag) {
                $tags_list[] = $tag->name;
            }
        }
        $tagslist = wp_json_encode($tags_list);
    
        $cat_name = [];
        $category_detail = get_the_category($contentId);
        if ($category_detail) {
            foreach ($category_detail as $cd) {
                $cat_name[] = $cd->cat_name;
            }
        }
        $catname = wp_json_encode($cat_name);
    
        $sections_names = wp_get_object_terms($postArray['ID'], 'sections', array("fields" => "names"));
        $sectionslist = wp_json_encode($sections_names);
    
        $url = get_permalink($postArray['ID']);
        
        $postJsonData = array(
            'contentId' => $contentId,
            'title' => $title,
            'price' => $price,
            'duration' => $duration,
            'authorId' => $authorId,
            'authorName' => $authorName,
            'url' => $url,
            'contentType' => 'STORY',
            'categories' => json_decode($catname),
            'tags' => json_decode($tagslist),
            'sections' => json_decode($sectionslist),
            'currency' => 'INR'
        );
    
        $apiKeySecret = base64_encode(CONSCENT_API_KEY . ":" . CONSCENT_API_SECRET);
    
        $response = wp_remote_post(
            CONSCENT_API_URL . "/api/v2/content/",
            array(
                'body' => wp_json_encode($postJsonData),
                'headers' => array(
                    'Authorization' => 'Basic ' . $apiKeySecret,
                    'Content-Type' => 'application/json'
                ),
                'timeout' => 10,
                'redirection' => 10,
                'blocking' => true,
                'httpversion' => '1.1',
                'sslverify' => true,
            )
        );
    
        if (is_wp_error($response)) {
            // Handle error
            return false;
        }
    
        $responseBody = wp_remote_retrieve_body($response);
        $responseArray = json_decode($responseBody);
    
        if (isset($responseArray->statusCode)) {
            return false;
        } else {
            return true;
        }
    }    
    
}
?>