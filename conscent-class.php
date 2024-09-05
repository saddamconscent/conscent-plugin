<?php
/*
 *
 * Make content to saparate two paragraph after infix with word 'Coderwolves' into unlocked  * * content.
 *
 */
if ( ! function_exists( 'conscent_prefix_insert_after_paragraph' ) ) {

    function conscent_prefix_insert_after_paragraph( $insertion, $paragraph_id, $content ) {

        $closing_p = '</p>';
        $paragraphs = explode( $closing_p, $content );

        foreach ($paragraphs as $index => $paragraph) {
            if ( trim( $paragraph ) ) {
                $paragraphs[$index] .= $closing_p;
            }
            if ( $paragraph_id == $index + 1 ) {
                $paragraphs[$index] .= $insertion;
            }
        }

        return implode( '', $paragraphs );

    }
}

/*
 *
 *It is used to make previewpages for 'real3dflipbook' 
 *
 */ 
if ( ! function_exists( 'conscent_get_preview_short_code' ) ) {

    function conscent_get_preview_short_code($shortCode) {

        $isReal3dflipbook = strpos($shortCode, 'real3dflipbook');
        $suffixIndex = strpos($shortCode, ']');
        $extraAttribute = 'mode="lightbox" previewpages="true" hidemenu="true"';

        if ($isReal3dflipbook !== false && $suffixIndex !== false) {
            $newShortCode = substr_replace($shortCode, ' ' . $extraAttribute, $suffixIndex, 0);
            return $newShortCode;
        }else {
            return $shortCode;
        }

    }

}

/*
 *
 * Return array of content between locked and unlocked content  
 * 0 index is unlocked content i.e a part of content that should be displated 
 * 1 index is remaining part of content
 * 
 */ 
 
if ( ! function_exists( 'conscent_get_short_content' ) ) {

    function conscent_get_short_content($content) {
        global $wpdb;
		$wpID = get_the_ID();
		$t_ids = get_option('Paywall');
		$cat_meta = get_option("category_$t_ids");

		// Get number of paragraphs to be shown if content is locked
		$content_visibility = get_option('ContentVisibility');

		// Fetch the Conscent data using get_post_meta
		$conscent_data = get_post_meta($wpID, 'Conscent_data', true);

		// Determine the visibility based on the retrieved meta value or options
		if (!empty($conscent_data)) {
			$VISIBLE = $conscent_data;
		} elseif (!empty($content_visibility)) {
			$VISIBLE = $content_visibility;
		} else {
			$VISIBLE = CONTENT_VISIBLE_PERCENT_BEFORE_PAYMENT;
		}

        // Add string after visible content ( by default it is two paragraph ) 
        $development_code = 'Coderwolves';

        // make content after add infix "Coderwolves" into content 
        $dalta = conscent_prefix_insert_after_paragraph( $development_code, $VISIBLE, $content );
        $development = explode($development_code, $dalta);
        return $development;
    }

}
/**
 * This method is called on the_content filter hook and handle the locked and unlocked 
 * condition 
 * addConscentPayWall
 * Adds the ConsCent Pay Wall
 * @param type $content
 * @return string
 */

if ( ! function_exists( 'conscent_add_conscent_paywall' ) ) {

    function conscent_add_conscent_paywall($content) {
        global $template; 
        $Chektemp= basename($template);
        
        if ( is_single() && 'post' == get_post_type() ) {
            $newContent = conscent_get_short_content($content);
            $shortCode = (!empty($newContent[0])) ? $newContent[0] : '';
            $remainingContent = ( !empty($newContent[1]) ) ? $newContent[1] : '';
			$final_content = "";
            if ( function_exists( 'amp_is_request' ) && amp_is_request() ) { 

                $final_content .= '<div amp-access="NOT granted" amp-access-hide class="conscent-iframe-wrapper">
                '.$shortCode.'<template amp-access-template type="amp-mustache">
                    <amp-iframe
                    id="conscentIframe"
                    style="position: relative; bottom: 0px; left: 8px; overflow: hidden; z-index: 2147483599; width: 100vw; height: 100vh; max-width: 820px; display: flex; justify-content: center; align-items: center; background: transparent;"
                    allowfullscreen
                    width="100vh"
                    height="50vh"
                    src="'.CONSCENT_AMP_SDK_URL.'/static/index.html?rid={{rid}}&clientId={{clientId}}&contentId={{contentId}}&journey={{journey}}&URL={{loginRedirectUrl}}&userId={{userId}}"
                    layout="responsive"
                    resizable
                    id="myAmpIframe"
                    sandbox="allow-scripts allow-same-origin allow-popups allow-forms allow-top-navigation allow-modals allow-popups-to-escape-sandbox allow-top-navigation-by-user-activation"
                    >
                    <div style="position: absolute; top: 50%; left: 43%; display: flex; justify-content: center; align-items: center;" placeholder >
                        <amp-img
                        src="https://storage.googleapis.com/bkt-conscent-public-stage/808.gif"
                        placeholder
                        layout="fixed"
                        width="140px"
                        height="20px"
                        ></amp-img>
                    </div>
                    <div overflow="">Read more!</div>
                    </amp-iframe>
                </template>
                </div>';
        
                $final_content .= '<div amp-access="(granted AND meteringActionId)" amp-access-hide class="conscent-iframe-wrapper">
                '.$content.'
                <template amp-access-template type="amp-mustache">
                    <amp-iframe
                    id="conscentIframe"
                    style="position: fixed; bottom: 0px; left: 16px; overflow: hidden; z-index: 2147483599; width: 100vw; height: 100vh; max-height: 210px; display: flex; justify-content: center; align-items: center; background: transparent;"
                    allowfullscreen
                    width="100vh"
                    height="50vh"
                    src="'.CONSCENT_AMP_SDK_URL.'/static/index.html?rid={{rid}}&clientId={{clientId}}&contentId={{contentId}}&journey={{journey}}&URL={{url}}"
                    layout="responsive"
                    resizable
                    id="myAmpIframe"
                    sandbox="allow-scripts allow-same-origin allow-popups allow-forms allow-top-navigation allow-modals allow-popups-to-escape-sandbox allow-top-navigation-by-user-activation"
                    >
                    <div style="position: absolute; top: 50%; left: 43%; display: flex; justify-content: center; align-items: center;" placeholder >
                        <amp-img
                        src="https://storage.googleapis.com/bkt-conscent-public-stage/808.gif"
                        placeholder
                        layout="fixed"
                        width="140px"
                        height="20px"
                        ></amp-img>
                    </div>
                    <div overflow="">Read more!</div>
                    </amp-iframe>
                </template></div>';

                $final_content .= '<div amp-access="(granted AND NOT meteringActionId)" amp-access-hide>
                    <template amp-access-template type="amp-mustache">
                    '.$content.'
                    </template>
                </div>';
                return $final_content;

            } else {

                // $shortCodeWithPreview = conscent_get_preview_short_code($shortCode);
                // $shortContentWithPreview = '<span id="previewPDF">'.$shortCodeWithPreview.'</span>';
                // $shortContent = '<span id="originalPDF" style="display:none">'.$content.'</span>';
                $shortContent = $shortCode;
                $shortContentWithPreview = '';
                $new_content = '<div id="csc-paywall"></div>';
                $final_content = '<div id="conscent_content">' . $shortContent . $shortContentWithPreview . "<span id='conscent-loader'> ... </span></div>" . $new_content;
                return $final_content;

            }

        } else {
            return $content; 	  
        }
    }
 
}

/*

 * This function  will be used for validating objec$h
 * we got from storydetails.js file's success call back function.
 * This file will provides output by calling an ajax call to target this function. 
 * @param: consumptionId

 */

 if ( ! function_exists( 'conscent_confirm_data_from_backend' ) ) {

    function conscent_confirm_data_from_backend() {
    // Check if the request is valid
    if (!isset($_POST['contentId']) || !isset($_POST['consumptionId'])) {
        echo "Invalid Request";
        exit();
    }

    // Sanitize input data
    $contentId = sanitize_text_field($_POST['contentId']);
    $consumptionId = sanitize_text_field($_POST['consumptionId']);

    // Get the post content
    $post_content = get_post_field('post_content', $contentId);

    // Prepare the request
    $checkConsumptionURL = "/content/consumption/$consumptionId";
    $postJsonData = wp_json_encode(array('consumptionId' => $consumptionId));

    // API URL
    $url = CONSCENT_API_URL . $checkConsumptionURL;

    // Make the POST request using wp_remote_post
    $response = wp_remote_post($url, array(
        'method'    => 'POST',
        'body'      => $postJsonData,
        'headers'   => array(
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Basic ' . base64_encode(CONSCENT_API_KEY . ':' . CONSCENT_API_SECRET)
        ),
        'timeout'   => 10,
        'data_format' => 'body',
    ));

    // Check for WP_Error
    if (is_wp_error($response)) {
        echo 'Request failed: ' . esc_html($response->get_error_message());
        exit();
    }

    // Decode the response
    $responseArray = json_decode(wp_remote_retrieve_body($response));

    // Validate the response
    if ($responseArray->consumptionId === $consumptionId && $responseArray->payload->contentId === $contentId && $responseArray->payload->clientId === CONSCENT_CLIENT_ID) {
        $allowed_html = wp_kses_allowed_html('post');
        echo wp_kses(conscent_embed_code($post_content), $allowed_html);
    } else {
        echo "Failed";
    }

    exit();
}


}
add_action('wp_ajax_nopriv_conscent_confirm_data_from_backend', 'conscent_confirm_data_from_backend');
add_action('wp_ajax_conscent_confirm_data_from_backend', 'conscent_confirm_data_from_backend');

add_filter('the_content', 'conscent_add_conscent_paywall');

if ( ! function_exists( 'conscent_embed_code' ) ) {
    
    function conscent_embed_code($post_content) {

        // Regular expression to match URLs
        // $url_pattern = '/(https?:\/\/[^\s]+)/';
        $url_pattern = '/(ht|f)tps?:\/\/[^"]*?(?=<|\s|$)/';

        // Find all URLs in the content
        preg_match_all($url_pattern, $post_content, $matches);

        // Loop through the matched URLs
        foreach ($matches[0] as $url) {

            // Check if the URL is from YouTube, Twitter, Facebook, LinkedIn, Instagram
            if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false || strpos($url, 'twitter.com') !== false || strpos($url, 'facebook.com') !== false || strpos($url, 'linkedin.com') !== false || strpos($url, 'instagram.com') !== false || strpos($url, 'xing.com') !== false) {
                $embed_html = wp_oembed_get($url);
                $post_content = str_replace($url, $embed_html, $post_content);
            }
            
        }
        // Output the modified post content
        return wpautop($post_content);
    }

}