<?php
/**
 * LianaAutomation admin panel
 *
 * PHP Version 7.4
 *
 * @category Components
 * @package  WordPress
 * @author   Liana Technologies <websites@lianatech.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0-standalone.html GPL-3.0-or-later
 * @link     https://www.lianatech.com
 */

/**
 * LianaAutomation options panel class
 *
 * @category Components
 * @package  WordPress
 * @author   Liana Technologies <websites@lianatech.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0-standalone.html GPL-3.0-or-later
 * @link     https://www.lianatech.com
 */
class LianaAutomation {

	private $_lianaautomation_options;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'lianaAutomationAddPluginPage' ) );
		add_action( 'admin_init', array( $this, 'lianaAutomationPageInit' ) );
	}

	/**
	 * Add an admin page
	 *
	 * @return void
	 */
	public function lianaAutomationAddPluginPage():void {
		global $admin_page_hooks;
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions
			error_log( print_r( $admin_page_hooks, true ) );
			// phpcs:enable
		}

		if ( ! isset( $admin_page_hooks['lianaautomation'] ) ) {
			add_menu_page(
				'LianaAutomation', // page_title.
				'LianaAutomation', // menu_title.
				'manage_options', // capability.
				'lianaautomation', // menu_slug.
				array( $this, 'lianaAutomationCreateAdminPage' ), // function.
				'dashicons-admin-settings', // icon_url.
				65 // position.
			);
		}
		add_submenu_page(
			'lianaautomation',
			'Page Browse',
			'Page Browse',
			'manage_options',
			'lianaautomationpbr',
			array( $this, 'lianaAutomationCreateAdminPage' ), // function.
		);

		// Remove the duplicate of the top level menu item from the sub menu
		// to make things pretty.
		remove_submenu_page( 'lianaautomation', 'lianaautomation' );
	}

	/**
	 * Construct an admin page
	 *
	 * @return null
	 */
	public function lianaAutomationCreateAdminPage() {
		$this->lianaautomation_options = get_option( 'lianaautomation_options' ); ?>
		<div class="wrap">
			<h2>LianaAutomation API Options for Page Browse Tracking</h2>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'lianaautomation_option_group' );
				do_settings_sections( 'lianaautomation_admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Init an admin page
	 *
	 * @return void
	 */
	public function lianaAutomationPageInit():void {
		register_setting(
			'lianaautomation_option_group',
			'lianaautomation_options',
			array( $this, 'lianaAutomationSanitize' )
		);

		add_settings_section(
			'lianaautomation_section',
			'',
			array( $this, 'lianaAutomationSectionInfo' ),
			'lianaautomation_admin'
		);

		add_settings_field(
			'lianaautomation_url',
			'Automation API URL',
			array( $this, 'lianaAutomationURLCallback' ),
			'lianaautomation_admin',
			'lianaautomation_section'
		);

		add_settings_field(
			'lianaautomation_realm',
			'Automation Realm',
			array( $this, 'lianaAutomationRealmCallback' ),
			'lianaautomation_admin',
			'lianaautomation_section'
		);

		add_settings_field(
			'lianaautomation_user',
			'Automation User',
			array( $this, 'lianaAutomationUserCallback' ),
			'lianaautomation_admin',
			'lianaautomation_section'
		);

		add_settings_field(
			'lianaautomation_key',
			'Automation Secret Key',
			array( $this, 'lianaAutomationKeyCallback' ),
			'lianaautomation_admin',
			'lianaautomation_section'
		);

		add_settings_field(
			'lianaautomation_channel',
			'Automation Channel ID',
			array( $this, 'lianaAutomationChannelCallback' ),
			'lianaautomation_admin',
			'lianaautomation_section'
		);

		// Status check.
		add_settings_field(
			'lianaautomation_status_check',
			'LianaAutomation Connection Check',
			array(
				$this,
				'lianaAutomationConnectionCheckCallback',
			),
			'lianaautomation_admin',
			'lianaautomation_section'
		);
	}

	/**
	 * Basic input sanitization function
	 *
	 * @param string $input String to be sanitized.
	 *
	 * @return null
	 */
	public function lianaAutomationSanitize( $input ) {
		$sanitary_values = array();

		if ( isset( $input['lianaautomation_url'] ) ) {
			$sanitary_values['lianaautomation_url']
				= sanitize_text_field( $input['lianaautomation_url'] );
		}
		if ( isset( $input['lianaautomation_realm'] ) ) {
			$sanitary_values['lianaautomation_realm']
				= sanitize_text_field( $input['lianaautomation_realm'] );
		}
		if ( isset( $input['lianaautomation_user'] ) ) {
			$sanitary_values['lianaautomation_user']
				= sanitize_text_field( $input['lianaautomation_user'] );
		}
		if ( isset( $input['lianaautomation_key'] ) ) {
			$sanitary_values['lianaautomation_key']
				= sanitize_text_field( $input['lianaautomation_key'] );
		}
		if ( isset( $input['lianaautomation_channel'] ) ) {
			$sanitary_values['lianaautomation_channel']
				= sanitize_text_field( $input['lianaautomation_channel'] );
		}
		return $sanitary_values;
	}

	/**
	 * Empty section info
	 *
	 * @return void
	 */
	public function lianaAutomationSectionInfo():void {
		// Intentinally left blank.
		// Could be used to generate info text section.
	}

	/**
	 * Automation URL
	 *
	 * @return void
	 */
	public function lianaAutomationURLCallback():void {
		printf(
			'<input class="regular-text" type="text" '
			. 'name="lianaautomation_options[lianaautomation_url]" '
			. 'id="lianaautomation_url" value="%s">',
			isset( $this->lianaautomation_options['lianaautomation_url'] )
				? esc_attr( $this->lianaautomation_options['lianaautomation_url'] )
				: ''
		);
	}

	/**
	 * Automation Realm
	 *
	 * @return void
	 */
	public function lianaAutomationRealmCallback():void {
		printf(
			'<input class="regular-text" type="text" '
			. 'name="lianaautomation_options[lianaautomation_realm]" '
			. 'id="lianaautomation_realm" value="%s">',
			isset( $this->lianaautomation_options['lianaautomation_realm'] )
				? esc_attr( $this->lianaautomation_options['lianaautomation_realm'] )
				: ''
		);
	}
	/**
	 * Automation User
	 *
	 * @return void
	 */
	public function lianaAutomationUserCallback():void {
		printf(
			'<input class="regular-text" type="text" '
			. 'name="lianaautomation_options[lianaautomation_user]" '
			. 'id="lianaautomation_user" value="%s">',
			isset( $this->lianaautomation_options['lianaautomation_user'] )
				? esc_attr( $this->lianaautomation_options['lianaautomation_user'] )
				: ''
		);
	}

	/**
	 * Automation Key
	 *
	 * @return void
	 */
	public function lianaAutomationKeyCallback():void {
		printf(
			'<input class="regular-text" type="text" '
			. 'name="lianaautomation_options[lianaautomation_key]" '
			. 'id="lianaautomation_key" value="%s">',
			isset( $this->lianaautomation_options['lianaautomation_key'] )
				? esc_attr( $this->lianaautomation_options['lianaautomation_key'] )
				: ''
		);
	}

	/**
	 * Automation Channel
	 *
	 * @return void
	 */
	public function lianaAutomationChannelCallback():void {
		printf(
			'<input class="regular-text" type="text" '
			. 'name="lianaautomation_options[lianaautomation_channel]" '
			. 'id="lianaautomation_channel" value="%s">',
			isset( $this->lianaautomation_options['lianaautomation_channel'] )
				? esc_attr( $this->lianaautomation_options['lianaautomation_channel'] )
				: ''
		);
	}

	/**
	 * LianaAutomation API Status check
	 *
	 * @return string
	 */
	public function lianaAutomationConnectionCheckCallback() {

		$return = '💥Fail';
		if ( empty( $this->lianaautomation_options['lianaautomation_user'] ) ) {
			echo $return;
			return null;
		}
		$user
			= $this->lianaautomation_options['lianaautomation_user'];

		if ( empty( $this->lianaautomation_options['lianaautomation_key'] ) ) {
			echo $return;
			return null;
		}
		$secret
			= $this->lianaautomation_options['lianaautomation_key'];

		if ( empty( $this->lianaautomation_options['lianaautomation_realm'] ) ) {
			echo $return;
			return null;
		}
		$realm
			= $this->lianaautomation_options['lianaautomation_realm'];

		if ( empty( $this->lianaautomation_options['lianaautomation_url'] ) ) {
			echo $return;
			return null;
		}
		$url
			= $this->lianaautomation_options['lianaautomation_url'];

		if ( empty( $this->lianaautomation_options['lianaautomation_channel'] ) ) {
			echo $return;
			return null;
		}
		$channel
			= $this->lianaautomation_options['lianaautomation_channel'];

		/**
		* General variables
		*/
		$base_path    = 'rest';             // Base path of the api end points.
		$content_type = 'application/json'; // Content will be send as json.
		$method       = 'POST';             // Method is always POST!

		// Import Data!
		$path = 'v1/pingpong';
		$data = array(
			'ping' => 'pong',
		);

		// Encode our body content data.
		$data = wp_json_encode( $data );
		// Get the current datetime in ISO 8601.
		$date = gmdate( 'c' );
		// md5 hash our body content.
		$content_md5 = md5( $data );
		// Create our signature!
		$signature_content = implode(
			"\n",
			array(
				$method,
				$content_md5,
				$content_type,
				$date,
				$data,
				"/{$base_path}/{$path}",
			),
		);

		$signature = hash_hmac( 'sha256', $signature_content, $secret );
		// Create the authorization header value.
		$auth = "{$realm} {$user}:" . $signature;

		// Create our full stream context with all required headers.
		$ctx = stream_context_create(
			array(
				'http' => array(
					'method'  => $method,
					'header'  => implode(
						"\r\n",
						array(
							"Authorization: {$auth}",
							"Date: {$date}",
							"Content-md5: {$content_md5}",
							"Content-Type: {$content_type}",
						)
					),
					'content' => $data,
				),
			)
		);

		// Build full path, open a data stream, and decode the json response.
		$full_path = "{$url}/{$base_path}/{$path}";

		$fp = fopen( $full_path, 'rb', false, $ctx );

		if ( ! $fp ) {
			// API failed to connect!
			echo $return;
			return null;
		}

		$response = stream_get_contents( $fp );
		$response = json_decode( $response, true );

		if ( ! empty( $response ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				// phpcs:disable WordPress.PHP.DevelopmentFunctions
				error_log( print_r( $response, true ) );
				// phpcs:enable
			}
			if ( ! empty( $response['pong'] ) ) {
				$return = '💚 OK';
			}
		}

		echo $return;
	}


}
if ( is_admin() ) {
	$lianaAutomation = new LianaAutomation();
}
