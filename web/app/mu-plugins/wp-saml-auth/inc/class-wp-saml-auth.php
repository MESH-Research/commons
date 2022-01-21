<?php
/**
 * Class WP_SAML_Auth
 *
 * @package WP_SAML_Auth
 */

/**
 * Main controller class for WP SAML Auth
 */
class WP_SAML_Auth {

	/**
	 * Controller instance as a singleton
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * SAML provider instance
	 *
	 * @var object
	 */
	private $provider = null;

	/**
	 * Class name to instantiate for SimpleSAML Auth.
	 * Replaced with namespaced version if available.
	 *
	 * @var string
	 */
	private $simplesamlphp_class = 'SimpleSAML_Auth_Simple';

	/**
	 * Get the controller instance
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WP_SAML_Auth;
			add_action( 'init', array( self::$instance, 'action_init' ) );
		}
		return self::$instance;
	}

	/**
	 * Get a configuration option for this implementation.
	 *
	 * @param string $option_name Configuration option to produce.
	 * @return mixed
	 */
	public static function get_option( $option_name ) {
		return apply_filters( 'wp_saml_auth_option', null, $option_name );
	}

	/**
	 * Get the provider instance for WP_SAML_Auth
	 *
	 * @return mixed
	 */
	public function get_provider() {
		return $this->provider;
	}

	/**
	 * Initialize the controller logic on the 'init' hook
	 */
	public function action_init() {

		$connection_type = self::get_option( 'connection_type' );
		if ( 'internal' === $connection_type ) {
			if ( file_exists( WP_SAML_AUTH_AUTOLOADER ) ) {
				require_once WP_SAML_AUTH_AUTOLOADER;
			}
			if ( ! class_exists( 'OneLogin_Saml2_Auth' ) ) {
				add_action(
					'admin_notices', function() {
						if ( current_user_can( 'manage_options' ) ) {
							// Translators: Links to the WP SAML Auth plugin.
							echo '<div class="message error"><p>' . wp_kses_post( sprintf( __( "WP SAML Auth wasn't able to find the <code>OneLogin_Saml2_Auth</code> class. Please verify your Composer autoloader, or <a href='%s'>visit the plugin page</a> for more information.", 'wp-saml-auth' ), 'https://wordpress.org/plugins/wp-saml-auth/' ) ) . '</p></div>';
						}
					}
				);
				return;
			}
			$auth_config    = self::get_option( 'internal_config' );
			$this->provider = new OneLogin_Saml2_Auth( $auth_config );
		} else {
			$simplesamlphp_path = self::get_option( 'simplesamlphp_autoload' );
			if ( file_exists( $simplesamlphp_path ) ) {
				require_once $simplesamlphp_path;
			}
			if ( class_exists( 'SimpleSAML\Auth\Simple' ) ) {
				$this->simplesamlphp_class = 'SimpleSAML\Auth\Simple';
			}
			if ( ! class_exists( $this->simplesamlphp_class ) ) {
				add_action(
					'admin_notices', function() {
						if ( current_user_can( 'manage_options' ) ) {
							// Translators: Links to the WP SAML Auth plugin.
							echo '<div class="message error"><p>' . wp_kses_post( sprintf( __( "WP SAML Auth wasn't able to find the <code>%1\$s</code> class. Please check the <code>simplesamlphp_autoload</code> configuration option, or <a href='%2\$s'>visit the plugin page</a> for more information.", 'wp-saml-auth' ), $this->simplesamlphp_class, 'https://wordpress.org/plugins/wp-saml-auth/' ) ) . '</p></div>';
						}
					}
				);
				return;
			}
			$this->provider = new $this->simplesamlphp_class( self::get_option( 'auth_source' ) );
		}
		add_action( 'login_head', array( $this, 'action_login_head' ) );
		add_action( 'login_message', array( $this, 'action_login_message' ) );
		add_action( 'wp_logout', array( $this, 'action_wp_logout' ) );
		add_filter( 'login_body_class', array( $this, 'filter_login_body_class' ) );
		add_filter( 'authenticate', array( $this, 'filter_authenticate' ), 21, 3 ); // after wp_authenticate_username_password runs.

	}

	/**
	 * Render CSS on the login screen
	 */
	public function action_login_head() {
		?>
<style>
	#wp-saml-auth-cta {
		background: #fff;
		-webkit-box-shadow: 0 1px 3px rgba(0,0,0,.13);
		box-shadow: 0 1px 3px rgba(0,0,0,.13);
		padding: 26px 24px 26px;
		margin-top: 24px;
		margin-bottom: 24px;
	}
	.wp-saml-auth-deny-wp-login #loginform,
	.wp-saml-auth-deny-wp-login #nav {
		display: none;
	}
</style>
<?php
	}

	/**
	 * Such a hack — use a filter to add the button to sign in with SAML provider
	 *
	 * @param string $message Existing message string.
	 * @return string
	 */
	public function action_login_message( $message ) {
		if ( ! self::get_option( 'permit_wp_login' ) ) {
			return $message;
		}
		$strings = array(
			'title'     => __( 'Use one-click authentication:', 'wp-saml-auth' ),
			'button'    => __( 'Sign In', 'wp-saml-auth' ),
			'alt_title' => __( 'Or, sign in with WordPress:', 'wp-saml-auth' ),
		);

		$query_args  = array(
			'action' => 'wp-saml-auth',
		);
		$redirect_to = filter_input( INPUT_GET, 'redirect_to', FILTER_SANITIZE_URL );
		if ( $redirect_to ) {
			$query_args['redirect_to'] = rawurlencode( $redirect_to );
		}

		/**
		 * Permit login screen text strings to be easily customized.
		 *
		 * @param array $strings Existing text strings.
		 */
		$strings = apply_filters( 'wp_saml_auth_login_strings', $strings );
		echo '<h3><em>' . esc_html( $strings['title'] ) . '</em></h3>';
		echo '<div id="wp-saml-auth-cta"><p><a class="button" href="' . esc_url( add_query_arg( $query_args, wp_login_url() ) ) . '">' . esc_html( $strings['button'] ) . '</a></p></div>';
		echo '<h3><em>' . esc_html( $strings['alt_title'] ) . '</em></h3>';
		return $message;
	}

	/**
	 * Log the user out of the SAML instance when they log out of WordPress
	 */
	public function action_wp_logout() {
		if ( 'internal' === self::get_option( 'connection_type' ) ) {
			$internal_config = self::get_option( 'internal_config' );
			if ( empty( $internal_config['idp']['singleLogoutService']['url'] ) ) {
				return;
			}
		}
		$this->provider->logout( add_query_arg( 'loggedout', true, wp_login_url() ) );
	}

	/**
	 * Add body classes for our specific configuration attributes
	 *
	 * @param array $classes Body CSS classes.
	 * @return array
	 */
	public function filter_login_body_class( $classes ) {

		if ( ! self::get_option( 'permit_wp_login' ) ) {
			$classes[] = 'wp-saml-auth-deny-wp-login';
		}

		return $classes;
	}

	/**
	 * Check if the user is authenticated against the SimpleSAMLphp instance
	 *
	 * @param mixed  $user     WordPress user reference.
	 * @param string $username Username.
	 * @param string $password Password supplied by the user.
	 * @return mixed
	 */
	public function filter_authenticate( $user, $username, $password ) {

		$permit_wp_login = self::get_option( 'permit_wp_login' );
		if ( is_a( $user, 'WP_User' ) && $permit_wp_login ) {
			return $user;
		}

		if ( ! empty( $_POST['SAMLResponse'] ) ) {
			$user = $this->do_saml_authentication();
		} elseif ( ( ! $permit_wp_login && empty( $_GET['loggedout'] ) ) || ( ! empty( $_GET['action'] ) && 'wp-saml-auth' === $_GET['action'] ) ) {
			$user = $this->do_saml_authentication();
		}
		return $user;
	}

	/**
	 * Do the SAML authentication dance
	 */
	public function do_saml_authentication() {

		if ( is_a( $this->provider, 'OneLogin_Saml2_Auth' ) ) {
			if ( ! empty( $_POST['SAMLResponse'] ) ) {
				$this->provider->processResponse();
				if ( ! $this->provider->isAuthenticated() ) {
					// Translators: Includes error reason from OneLogin.
					return new WP_Error( 'wp_saml_auth_unauthenticated', sprintf( __( 'User is not authenticated with SAML IdP. Reason: %s', 'wp-saml-auth' ), $this->provider->getLastErrorReason() ) );
				}
				$attributes  = $this->provider->getAttributes();
				$redirect_to = filter_input( INPUT_POST, 'RelayState', FILTER_SANITIZE_URL );
				if ( $redirect_to && false === stripos( $redirect_to, parse_url( wp_login_url(), PHP_URL_PATH ) ) ) {
					add_filter(
						'login_redirect', function() use ( $redirect_to ) {
							return $redirect_to;
						}, 1
					);
				}
			} else {
				$redirect_to = filter_input( INPUT_GET, 'redirect_to', FILTER_SANITIZE_URL );
				$redirect_to = $redirect_to ? : $_SERVER['REQUEST_URI'];
				$this->provider->login( $redirect_to );
			}
		} elseif ( is_a( $this->provider, $this->simplesamlphp_class ) ) {
			$redirect_to = filter_input( INPUT_GET, 'redirect_to', FILTER_SANITIZE_URL );
			if ( $redirect_to ) {
				$redirect_to = add_query_arg(
					array(
						'redirect_to' => $redirect_to,
						'action'      => 'wp-saml-auth',
					), wp_login_url()
				);
			} else {
				$redirect_to = wp_login_url();
				// Only persist redirect_to when it's not wp-login.php.
				if ( false === stripos( $redirect_to, $_SERVER['REQUEST_URI'] ) ) {
					$redirect_to = add_query_arg( 'redirect_to', $_SERVER['REQUEST_URI'], $redirect_to );
				}
			}
			$this->provider->requireAuth(
				array(
					'ReturnTo' => $redirect_to,
				)
			);
			$attributes = $this->provider->getAttributes();
		} else {
			return new WP_Error( 'wp_saml_auth_invalid_provider', __( 'Invalid provider specified for SAML authentication', 'wp-saml-auth' ) );
		}

		/**
		 * Runs before the SAML authentication dance proceeds
		 *
		 * Can be used to short-circuit the authentication process.
		 *
		 * @param false $short_circuit Return some non-false value to bypass authentication.
		 * @param array $attributes All attributes received from the SAML response.
		 */
		$pre_auth = apply_filters( 'wp_saml_auth_pre_authentication', false, $attributes );
		if ( false !== $pre_auth ) {
			return $pre_auth;
		}

		$get_user_by = self::get_option( 'get_user_by' );
		$attribute   = self::get_option( "user_{$get_user_by}_attribute" );
		if ( empty( $attributes[ $attribute ][0] ) ) {
			// Translators: Communicates how the user is fetched based on the SAML response.
			return new WP_Error( 'wp_saml_auth_missing_attribute', sprintf( esc_html__( '"%1$s" attribute is expected, but missing, in SAML response. Attribute is used to fetch existing user by "%2$s". Please contact your administrator.', 'wp-saml-auth' ), $attribute, $get_user_by ) );
		}

		$existing_user = get_user_by( $get_user_by, $attributes[ $attribute ][0] );
		if ( $existing_user ) {
			/**
			 * Runs after a existing user has been authenticated in WordPress
			 *
			 * @param WP_User $existing_user  The existing user object.
			 * @param array   $attributes     All attributes received from the SAML Response
			 */
			do_action( 'wp_saml_auth_existing_user_authenticated', $existing_user, $attributes );
			return $existing_user;
		}
		if ( ! self::get_option( 'auto_provision' ) ) {
			return new WP_Error( 'wp_saml_auth_auto_provision_disabled', esc_html__( 'No WordPress user exists for your account. Please contact your administrator.', 'wp-saml-auth' ) );
		}

		$user_args = array();
		foreach ( array( 'display_name', 'user_login', 'user_email', 'first_name', 'last_name' ) as $type ) {
			$attribute          = self::get_option( "{$type}_attribute" );
			$user_args[ $type ] = ! empty( $attributes[ $attribute ][0] ) ? $attributes[ $attribute ][0] : '';
		}
		$user_args['role']      = self::get_option( 'default_role' );
		$user_args['user_pass'] = wp_generate_password();
		/**
		 * Runs before a user is created based off a SAML response.
		 *
		 * @param array $user_args Arguments passed to wp_insert_user().
		 * @param array $attributes Attributes from the SAML response.
		 */
		$user_args = apply_filters( 'wp_saml_auth_insert_user', $user_args, $attributes );
		$user_id   = wp_insert_user( $user_args );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$user = get_user_by( 'id', $user_id );

		/**
		 * Runs after the user has been authenticated in WordPress
		 *
		 * @param WP_User $user       The new user object.
		 * @param array   $attributes All attributes received from the SAML Response
		 */
		do_action( 'wp_saml_auth_new_user_authenticated', $user, $attributes );
		return $user;
	}

}
