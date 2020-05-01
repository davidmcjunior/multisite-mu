<?php

namespace Site\Controllers;

/**
 * Class Settings_Page_Controller
 * @package Site\Controllers
 */
class Settings_Page_Controller
{

	/**
	 * @var string
	 */
	protected $plugin;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $template;


	/**
	 * @var callable
	 *
	 * This is a reference to the function that provides the template's
	 * data (available in the template file as $data.)
	 */
	protected $callback;


	/**
	 * Settings_Page_Controller constructor.
	 *
	 * @param array $params
	 */
	public function __construct( $params = array() )
	{
		foreach ( array( 'name', 'title', 'template', 'callback' ) as $param ) {
			if ( ! array_key_exists( $param, $params ) ) return;
		}

		$this->title    = $params[ 'title' ];
		$this->plugin   = $params[ 'name' ];
		$this->template = $params[ 'template' ];
		$this->callback = $params[ 'callback' ];

		add_action( 'init', array( $this, 'init' ) );
	}


	/**
	 * @return void
	 */
	public function get_settings_page()
	{
		$data = call_user_func( $this->callback );

		$this->get_template( $data );
	}


	/**
	 * @return void
	 */
	public function init()
	{
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_filter( "plugin_action_links_{$this->plugin}/index.php", array( $this, 'plugin_action_links' ) );
	}


	/**
	 * @return void
	 */
	public function add_plugin_page()
	{
		add_options_page(
			$this->title,
			$this->title,
			'manage_options',
			$this->plugin,
			array( $this, 'get_settings_page' )
		);
	}


	/**
	 * @param $data
	 *
	 * @return void
	 */
	protected function get_template( $data )
	{
		require_once $this->template;
	}


	/**
	 * @param $links
	 *
	 * @return mixed
	 */
	public function plugin_action_links( $links )
	{
		$settings_link = admin_url( 'options-general.php?page=' . $this->plugin );
		$settings_link = '<a href=" '  . $settings_link . '">Settings</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

}
