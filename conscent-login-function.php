<?php
$is_amp = false;
if ( function_exists( 'amp_is_request' ) && amp_is_request() ) $is_amp = true;

if($is_amp) {

	add_shortcode("ConscentLoginLogout", "conscent_login_link");

	if ( ! function_exists( 'conscent_login_link' ) ) {

		function conscent_login_link() {
?>

			<div amp-access="logoutRedirectUrl" amp-access-hide>
				<div class="logout-container">
					<template amp-access-template type="amp-mustache">
						<button class="logout-button" on="tap:AMP.navigateTo(url='{{logoutRedirectUrl}}'),trackClick">Logout</button>
					</template>
				</div>
			</div>

<?php

		}

	}
}else {
	if ( ! function_exists( 'conscent_login_code' ) ) {

		function conscent_login_code() {
	
		?>
	
		<div id="loginOverlay" style="position: fixed; z-index: 99999; top: 40px; left: 0px; right: 0px; display: block;">
	
				<div class="containerfluid">
	
					<div class="row Custome">
	
						<div class="colmd12">
	
							<div id="csc-login"></div>
	
						</div>
	
					</div>
	
				</div>
	
			</div>
	
		<script language="javascript">
	
		function setCookie(cname,cvalue,exdays){
	
			var d = new Date();
	
			d.setTime(d.getTime() + (exdays*60*60*1000));
	
			var expires = "expires="+ d.toUTCString();	
	
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	
		}
	
		function getCookie(cname) {
	
			var name = cname + "=";
	
			var decodedCookie = decodeURIComponent(document.cookie);
	
			var ca = decodedCookie.split(';');
	
			for(var i = 0; i <ca.length; i++) {
	
				var c = ca[i];
	
				while (c.charAt(0) == ' ') {
	
					c = c.substring(1);
	
				}
	
				if (c.indexOf(name) == 0) {
	
					return c.substring(name.length, c.length);
	
				}
	
			}
	
			return "";
	
		}
	
		function deleteCookie(cookieName) {
	
			document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
	
		}
	
		const subsLogOut = function() {
	
			const cscLogin = window._csc;
	
			cscLogin('logout');
	
			deleteCookie("id");
			deleteCookie("username");
			deleteCookie("phn");
			deleteCookie("name");
			deleteCookie("e_s");
			deleteCookie("login");
			deleteCookie("subscriptions");
			deleteCookie("address");
			deleteCookie("loadded");
            
			// window.location.replace("<?php //echo get_option('AfterLogout'); ?>?Logout=<?php //echo base64_encode(time());?>");
		}
	
		const subsLogin = function() {
	
			document.querySelector("#loginOverlay").style.display = "block";
	
			const cscLogin = window._csc;
	
			cscLogin('login-with-redirect');
	
		}
	
		function conscent_store_user_details() {
			const cscLogin = window._csc;
			cscLogin('get-user-details',{
			successCallbackForUserDetails: async (userDetailsObject) => {
	
				console.log('Success callback received from conscent login', userDetailsObject._id);
				// window.onbeforeunload = function (userDetailsObject) {
				// 	console.log("userDetailsObject: ", userDetailsObject);
				// 	return true;
				// }
				// console.log("userDetailsObject: ");	
				// for (let key in userDetailsObject) {
				// 	console.log(key, userDetailsObject[key]);
				// }
				let getCookieId = getCookie('id');
				if(getCookieId.length == 0 || getCookieId == 'undefined') {
					setCookie('id',userDetailsObject._id,'365');
				}

				let getCookieUsername = getCookie('username');
				if(getCookieUsername.length == 0 || getCookieUsername == 'undefined') {
					setCookie('username',userDetailsObject.email,'365');
				}

				let getCookiePhn = getCookie('phn');
				if(getCookiePhn.length == 0 || getCookiePhn == 'undefined') {
					setCookie('phn',userDetailsObject.phoneNumber,'365');
				}

				let getCookieName = getCookie('name');
				if(getCookieName.length == 0 || getCookieName == 'undefined') {
					setCookie('name',userDetailsObject.name,'365');
				}

				let getCookieE_S = getCookie('e_s');
				if(getCookieE_S.length == 0 || getCookieE_S == 'undefined') {
					setCookie('e_s',userDetailsObject.isSubscriber,'365');
				}

				let getCookieLogin = getCookie('login');
				if(getCookieLogin.length == 0 || getCookieLogin == 'undefined') {
					setCookie('login',userDetailsObject.status,'365');
				}

				var activeSubscriptions = userDetailsObject.activeSubscriptions;
				
				var json_subs = JSON.stringify(activeSubscriptions);

				var address = userDetailsObject.address;
				var json_address = JSON.stringify(address);

				setCookie('address', json_address, '365');
				setCookie('subscriptions', json_subs, '365');
				
				// let loadded = 0;
				let loadded = getCookie('loadded');
				if(loadded.length == 0) {
					setCookie('loadded', 1, '365');
				}

				if(parseInt(getCookie('loadded')) == 1) {
					setCookie('loadded', 2, '365');
					window.location.reload();
				}
			},
			});
	
		}
			
		var profileLogoutBtn = document.getElementById("csc-logout");
		if(profileLogoutBtn) {
			profileLogoutBtn.addEventListener("click", subsLogOut);
		}
		</script>
	
		<?php }
	
	}
	
	add_action('wp_footer','conscent_login_code');
	
	if ( ! function_exists( 'conscent_login_check' ) ) {
	
		function conscent_login_check() {
	?>
			<style>
				.d-none-mobile {
					display: none;
				}
	
				.top-bar .ceiling-side {
					display: contents;
				}

				/* Message display on cancel subscription */
				.success-message {
					position: fixed;
					top: 20px;
					right: 20px;
					padding: 10px 20px;
					background-color: #4caf50;
					color: #fff;
					font-size: 16px;
					border-radius: 5px;
					z-index: 999999;
				}

				.error-message {
					position: fixed;
					top: 20px;
					right: 20px;
					padding: 10px 20px;
					background-color: #c92228;
					color: #fff;
					font-size: 16px;
					border-radius: 5px;
					z-index: 999999;
				}
	
				@media (min-width: 45em) {
					.d-none-mobile {
						display: inline;
					}
	
					.top-bar .ceiling-side {
					display: flex;
				}
				}
			</style>
			<script language="javascript">
	
				const clientId1 = '<?php echo esc_js( get_option('clientId1') ); ?>';
	
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
	
				})(window, document, 'script', '_csc', "<?php echo esc_js( get_option('sdkURL') ); ?>", clientId1);
	
				const cscLogin = window._csc;
				cscLogin('add-auth-state-listener', (userId) => {
					if(userId) {
						conscent_store_user_details();
						// var currentURL = window.location.href;
						// if(currentURL.indexOf("#loaded") == -1){
						// 	conscent_store_user_details();
						// }
					}
				})
			</script>
		<?php
		}
	}
	add_action('wp_head','conscent_login_check');

	// User Dash Redirect to home page in logout
	function conscent_check_and_redirect_user_dashboard() {
		if (empty($_COOKIE['login']) && is_page('user_dashboard')) {
			wp_redirect(home_url());
			exit();
		}
	}
	add_action('template_redirect', 'conscent_check_and_redirect_user_dashboard');
	
	/* create login logout Button*/
	if ( ! function_exists( 'conscent_login_link' ) ) {
	
		function conscent_login_link() {
			
			ob_start();
	
			if (!empty($_COOKIE['login'])) {
	
			?>
	
				<?php if($_COOKIE['username']=='undefined') {
	
					$userNameda= $_COOKIE['phn'];
	
				} else {
	
					$userNameda= $_COOKIE['username'];	
	
				} ?>
	
					<button style="background-color: #F73D92; color: #fff;  border: none; cursor: pointer;">
						<a href="javascript:void(0)" onclick="javascript:subsLogOut()" target="_parent"> <?php echo esc_html($userNameda); ?> ! Logout</a>
					</button>
			<?php 
	
			} else {?>
				<button style="background-color: #F73D92; color: #fff;  border: none; cursor: pointer;" onclick="toggleDropdown()">
					<a href="javascript:void(0)" onclick="javascript:subsLogin()" target="_parent"><?php echo esc_html__( 'Login', 'conscent-paywall' ) ?></a> 
				</button>
	
			<?php } 
	
			return ob_get_clean();
		}
	
	}
	
	
	/*
	 * Add Login Logout Menu Item to navigation at given theme location from admin
	 * 
	 * */
	
	add_filter( 'wp_nav_menu_items', 'conscent_login_logout_menu_link', 10, 2 );
	function conscent_login_logout_menu_link( $items, $args ) {
		$menu_theme_location = get_option('TheamLocation');
	
		if ($args->theme_location == $menu_theme_location) {
	
			if (!empty($_COOKIE['login'])) {
	
				$items .= '<li class="conscent-logout"> <a href="javascript:void(0)" onclick="javascript:subsLogOut()" target="_parent">'.esc_html__( 'Logout', 'conscent-paywall' ).'</a></li>';
	
			} else {
	
				$items .= '<li class="conscent-login"><a href="javascript:void(0)" onclick="javascript:subsLogin()" target="_parent">'.esc_html__( 'Login', 'conscent-paywall' ).'</a></li>';
	
			}
	
		}
	
		return $items;
	
	}
	
	
	
	add_shortcode("ConscentLoginLogout", "conscent_login_link");
	
	if ( ! function_exists( 'conscent_get_user_data' ) ) {
	
		function conscent_get_user_data($params) {
			// Start output buffer
			ob_start();
			
			$key = '';
	
			if(isset($params) && !empty($params['key'])) {
	
				$key = $params['key'];
			}
	
			if(empty($key)) {
				echo esc_html__( 'Please enter correct key', 'conscent-paywall' );
				exit;
			}
	
			if($key == 'name') {
				if(empty($_COOKIE['name']) || $_COOKIE['name'] == 'undefined') {
					echo "--";
				}else {
					echo esc_html( $_COOKIE['name'] );
				}
			}else if($key == 'email') {
				if(empty($_COOKIE['username']) || $_COOKIE['username'] == 'undefined') {
					echo "--";
				}else {
					echo esc_html( $_COOKIE['username'] );
				}
			}else if($key == 'phone') {
				if(empty($_COOKIE['phn']) || $_COOKIE['phn'] == 'undefined') {
					echo "--";
				}else {
					echo esc_html( $_COOKIE['phn'] );
				}
			}else if($key == 'subscriptions') {
				// subscriptionInfo_shortcode();
				if(empty($_COOKIE['subscriptions']) || $_COOKIE['subscriptions'] == 'undefined' || $_COOKIE['subscriptions'] == '[]') {
					echo "--";
				}else {
					$subs_str = "";
					$subs_arr = array();
					$all_subscriptions = json_decode(str_replace('\\', '', $_COOKIE['subscriptions']));
					foreach($all_subscriptions as $subscription) {
						$subs_str = "";
						$expiry_date = date_create($subscription->expiryDate);
						$formatted_expiry_date = date_format($expiry_date, "M d Y H:i:s");
						$subs_str .= "<p><strong>Subsription Plan: </strong>" . $subscription->subscriptionDetails->duration . " Month ";
						$subs_str .= "<strong>Price: </strong>" . $subscription->priceDetails->price . "&nbsp;" . $subscription->priceDetails->currency;
						$subs_str .= "<strong> Expiry Date: </strong>" . $formatted_expiry_date . "&nbsp;&nbsp;&nbsp;&nbsp;<button class='btn btn-primary cancel-subs' onclick='conscent_cancel_subscription_handler(event)' data-id='".$subscription->_id."'>Cancel</button></p>";
						$subs_arr[] = $subs_str;
					}
					$subs_str = implode("<hr><br>", $subs_arr);
					$allowed_html = wp_kses_allowed_html('post');
					echo wp_kses($subs_str, $allowed_html);
				}
				
			}
	
			// End output buffer and return content
			return ob_get_clean();
		}
	}
	add_shortcode('userDetails', 'conscent_get_user_data');


	function conscent_cancel_subscription() {

	?>
	
	<script>
	
		function conscent_cancel_subscription_handler(event) {
			const subsId = event.target.getAttribute('data-id');
			const cscLogin = window._csc;
			cscLogin('cancel-subscription-by-user', {
				subscriptionID: subsId,
				onError:( err ) => {
					console.log(err);
					conscent_cancel_subscription_message_handler('error', 'We apologize, but we are currently unable to cancel your recurring payments. <br>This may be because your subscription has already been cancelled or it is a one-time payment.');
				},
				onSuccess:( data ) => { 
					console.log(data);
					conscent_cancel_subscription_message_handler('success', 'Subscription successfully cancelled.');
				}
			});
		}
	
		function conscent_cancel_subscription_message_handler(type, message) {
			if(type == 'error') {
				var errorMessage = jQuery('<div id="flash-message" class="error-message">' + message + '</div>');
				jQuery('body').append(errorMessage);
			} else if(type == 'success') {
					var successMessage = jQuery('<div id="flash-message" class="success-message">' + message + '</div>');
					jQuery('body').append(successMessage);
			} else {
				return false;
			}
			// Remove flash message after 5 seconds
			var flashMessage = jQuery('#flash-message');
			setTimeout(function() {
				flashMessage.fadeOut(500, function() {
					jQuery(this).remove();
				});
			}, 5000);
		}
	
	</script>
	
	<?php
	
	}
	
	add_action('wp_footer','conscent_cancel_subscription');

} // Standerd Page.