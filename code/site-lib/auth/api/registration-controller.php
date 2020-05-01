<?php


namespace Site\Auth\Api;
use Site\Exceptions\Invalid_Argument_Exception;
use Site\Exceptions\User_Registration_Exception;

/**
 * Class Registration_Controller
 * @package Site\Auth\Api
 */
class Registration_Controller
{

	/**
	 * @var array
	 */
	protected $header_options = array(
		'headers' => array(
			'Accept'        => 'application/json',
			'Content-Type'  => 'application/json',
			'Authorization' => 'SSWS ' . OKTA_API_TOKEN
		)
	);

	/**
	 * @var string
	 */
	protected $base_url = OKTA_API_BASE_URL . 'users/';


	/**
	 * Registration_Controller constructor.
	 */
	public function __construct()
	{
		add_action( 'wp_ajax_nopriv_activate_okta_user', array( $this, 'activate_okta_user_action' ) );
	}


	/**
	 * @return void
	 */
	public function activate_okta_user_action()
	{
		$email  = $_POST[ 'email' ];

		$status = array(
			'success' => true,
			'message' => "User has been activated."
		);

		try {
			$this->send_user_activation_link( $email );

		} catch ( \Exception $e ) {
			$status[ 'success' ] = false;
			$status[ 'user' ]    = $email;
			$status[ 'message' ] = $e->getMessage();

			elog( $status );
		}

		echo wp_json_encode( $status );
		exit;
	}


	/**
	 * @param $url
	 * @param string $method
	 *
	 * @throws User_Registration_Exception
	 *
	 * @return array
	 */
	protected function get_response( $url, $method = 'post' )
	{
		$function = 'wp_remote_' . $method;

		$response = $function(
			$url,
			$this->header_options
		);

		$response = json_decode( $response[ 'body' ], true );

		if ( array_key_exists( 'errorCode', $response ) ) {
			throw new User_Registration_Exception( $response[ 'errorSummary' ] );
		}

		return $response;
	}


	/**
	 * @param string $email
	 * @param bool $send_link
	 *
	 * @throws Invalid_Argument_Exception
	 * @throws User_Registration_Exception
	 *
	 * @return void
	 */
	public function send_user_activation_link( $email, $send_link = false )
	{
		$user_id = $this->get_user_id( $email );
		$url     = $this->base_url . $user_id . '/lifecycle/activate';
		$url    .= $send_link ? '?sendEmail=false' : '';

		$this->get_response( $url );
	}


	/**
	 * @param string $email
	 *
	 * @return string|false
	 *
	 * @throws Invalid_Argument_Exception
	 * @throws User_Registration_Exception
	 */
	protected function get_user_id( $email )
	{
		if ( ! is_email( $email ) ) {
			throw new Invalid_Argument_Exception( 'Email is in an invalid format.' );
		}

		$response = $this->get_response( $this->base_url . $email, 'get' );

		if ( array_key_exists( 'id', $response ) ) {
			return $response[ 'id' ];

		} else {
			return false;
		}
	}

}

new Registration_Controller();