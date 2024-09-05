<?php 

function conscent_enqueue_amp_scripts() {
    // Register the AMP Project script
    wp_register_script('amp-project-v0', 'https://cdn.ampproject.org/v0.js', array(), '1.0.0', true);
    wp_register_script('amp-mustache', 'https://cdn.ampproject.org/v0/amp-mustache-0.2.js', array(), '1.0.0', true);
    wp_register_script('amp-access', 'https://cdn.ampproject.org/v0/amp-access-0.1.js', array(), '1.0.0', true);
    wp_register_script('amp-analytics', 'https://cdn.ampproject.org/v0/amp-analytics-0.1.js', array(), '1.0.0', true);
    wp_register_script('amp-bind', 'https://cdn.ampproject.org/v0/amp-bind-0.1.js', array(), '1.0.0', true);
    wp_register_script('amp-iframe', 'https://cdn.ampproject.org/v0/amp-iframe-0.1.js', array(), '1.0.0', true);

    // Enqueue the AMP Project script
    wp_enqueue_script('amp-project-v0');
    wp_enqueue_script('amp-mustache');
    wp_enqueue_script('amp-access');
    wp_enqueue_script('amp-analytics');
    wp_enqueue_script('amp-bind');
    wp_enqueue_script('amp-iframe');
}

add_action('wp_enqueue_scripts', 'conscent_enqueue_amp_scripts');


if ( ! function_exists( 'conscent_remove_special_char' ) ) {

    function conscent_remove_special_char($str) {
        $res = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), ' ', $str);
        return $res;
    }

}

if ( ! function_exists( 'conscent_add_header' ) ) {

    function conscent_add_header() {
		global $post;
        if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
            
            $contentId = $post->ID;
            $title = $post->post_title;
            $clientId = CONSCENT_CLIENT_ID;
            $conscent_amp_sdk_url = CONSCENT_AMP_SDK_URL;
            $conscent_amp_api_url = CONSCENT_AMP_API_URL;

            $author_id     = get_post_field ('post_author', $post->ID);
            $display_name  = get_the_author_meta( 'display_name' , $author_id );
            // echo "<pre>"; print_r($display_name); echo "</pre>";
            
            $tags_list = array();	
            foreach(get_the_tags($post->ID) as $tag) $tags_list[] = $tag->name;
            $tags = (!empty($tags_list)) ? implode(" ",$tags_list) : '';
            
            $category_list = array();
            foreach(get_the_category($post->ID) as $category) $category_list[] = $category->cat_name;
            $categories = (!empty($category_list)) ? implode(" ",$category_list) : '';

            $section_list = wp_get_object_terms($post->ID, 'sections', array("fields" => "names"));
            $sections = (!empty($section_list)) ? implode(" ",$section_list) : '';
            $userId = $_COOKIE['id'];
            
    ?>
            
            <script id="amp-access" type="application/json">
                {
                    "authorization": "<?php echo esc_js( $conscent_amp_api_url )?>/content/amp?rid=READER_ID&_=RANDOM&useRid=true&externalUserId=<?php echo esc_js( $userId ) ?>&clientContentId=<?php echo esc_js( $contentId )?>&title=<?php echo esc_js( $title )?>&clientId=<?php echo esc_js( $clientId )?>&categories=<?php echo esc_js( $categories )?>&tags=<?php echo esc_js( $tags )?>&sections=<?php echo esc_js( $sections )?>&authorName=<?php echo esc_js( $display_name )?>&url=SOURCE_URL",
                    "pingback": "https://pub.com/amp-ping?rid=READER_ID&url=SOURCE_URL",
                    "authorizationFallbackResponse": {
                    "granted": false
                    },
                    "noPingback": true
                }
            </script>
        <?php
        }
    }
}

if ( ! function_exists( 'conscent_ampforwp_custom_css' ) ) {

    function conscent_ampforwp_custom_css() { 

        if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
?>
        
            <style amp-boilerplate>
                body {
                    -webkit-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
                    -moz-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
                    -ms-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
                    animation: -amp-start 8s steps(1, end) 0s 1 normal both;
                }
            
                @-webkit-keyframes -amp-start {
                    from {
                    visibility: hidden;
                    }
            
                    to {
                    visibility: visible;
                    }
                }
            
                @-moz-keyframes -amp-start {
                    from {
                    visibility: hidden;
                    }
            
                    to {
                    visibility: visible;
                    }
                }
            
                @-ms-keyframes -amp-start {
                    from {
                    visibility: hidden;
                    }
            
                    to {
                    visibility: visible;
                    }
                }
            
                @-o-keyframes -amp-start {
                    from {
                    visibility: hidden;
                    }
            
                    to {
                    visibility: visible;
                    }
                }
            
                @keyframes -amp-start {
                    from {
                    visibility: hidden;
                    }
            
                    to {
                    visibility: visible;
                    }
                }
            </style>
            <noscript>
            <style amp-boilerplate>
                body {
                -webkit-animation: none;
                -moz-animation: none;
                -ms-animation: none;
                animation: none;
                }
            </style>
            </noscript>
            <style amp-custom>
                h1 {
                    margin: 0px;
                }
            
                .iframe-container-inarticle {
                    width: 550px;
                    max-width: 100vw;
                    height: 520px;
                    position: absolute;
                    top: 200px;
                    text-align: left;
                }
            </style>
<?php 
        }
    }

}

add_action('amp_post_template_head', 'conscent_add_header');
add_action('amp_post_template_css','conscent_ampforwp_custom_css', 11);