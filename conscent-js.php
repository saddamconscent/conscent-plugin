<?php 
if ( ! function_exists( 'conscent_csc_paywall_code' ) ) {

    function conscent_csc_paywall_code() {
        global $post;
        if ( is_single() && 'post' == get_post_type() ) {
            $author_id     = get_post_field ('post_author', $post->ID);
            $display_name  = get_the_author_meta( 'first_name' , $author_id );

            
			$tags_list = array();	
			foreach(get_the_tags($post->ID) as $tag) {
				$tags_list[] = $tag->name;
			}
			$tags=wp_json_encode($tags_list);
			$cat_name_array = array();
			$category_detail=get_the_category($post->ID);//$post->ID
			foreach($category_detail as $cd){
				$cat_name_array[] = $cd->cat_name;
			}
			$cat_name=wp_json_encode($cat_name_array);
			$sections_names = array();
			$sections_names = wp_get_object_terms($post->ID,'sections',  array("fields" => "names")); 
			$merge_sections_names = array_merge($sections_names, $cat_name_array);
			foreach($merge_sections_names as $merge_sections_name) {
				$trimmed_sections_names[] = str_replace(' ', '', $merge_sections_name);
			}
			$sectionslist = wp_json_encode($merge_sections_names);
			//echo "<pre>sectionslist: "; print_r($merge_sections_names); echo "</pre>";
            ?>
            <script>

            const clientId = '<?php echo esc_js( CONSCENT_CLIENT_ID ); ?>';
            var sdkURL = '<?php echo esc_js( CONSCENT_SDK_URL ); ?>';

            (function (w, d, s, o, f, cid) {
                if (!w[o]) {
                    w[o] = function () {
                        w[o].q.push(arguments);
                    };
                    w[o].q = [];
                }
                (js = d.createElement(s)), (fjs = d.getElementsByTagName(s)[0]);
                js.id = o;
                js.src = f;
                js.async = 1;
                js.title = cid;
                fjs.parentNode.insertBefore(js, fjs);
            })(window, document, 'script', '_csc', sdkURL, clientId);

            const csc = window._csc;
            var contentId = <?php echo esc_js( $post->ID );?>;
            csc('show');
            csc('init', {
            debug: true, // can be set to false to remove sdk non-error log output
            contentId: contentId,
            clientId: clientId,
            title:"<?php echo esc_js( $post->post_title );?>",
            categories: "<?php echo esc_js( $cat_name );?>",
            tags:  "<?php echo esc_js( $tags );?>",
            sections: "<?php echo esc_js( $sectionslist );?>",
            authorName: "<?php echo esc_js( $display_name );?>",
            successCallback: yourSuccessCallbackFunction,
            wrappingElementId: 'csc-paywall',
            fullScreenMode: 'false' // if set to true, the entire screen will be covered,

            });

            async function yourSuccessCallbackFunction(validationObject) {
                var ajaxurl = "<?php echo esc_js( admin_url('admin-ajax.php') ); ?>";
                jQuery.ajax({
                    type: "POST",
                    dataType: "text",
                    url: ajaxurl,
                    data: {consumptionId: validationObject.consumptionId, action: "conscent_confirm_data_from_backend", contentId: contentId},
                    beforeSend: function() {
                        jQuery('#conscent-loader').html('<div id="conscent-loader-text"></div>');
                    },
                    success: function (response) {
                        if (response.trim() !== "Failed") {
                            jQuery('#conscent_content').html(response); 
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) { 
                        console.log("Status: ", textStatus); 
                        console.log("Error: ", errorThrown); 
                    },
                    complete: function(){
                        jQuery('#conscent-loader-text').remove();
                    }
                });
            }
            </script>
<?php
        }
    }
}

add_action('wp_footer','conscent_csc_paywall_code');