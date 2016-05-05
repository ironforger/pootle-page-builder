<?php

/**
 * Pootle Page Builder Pro public class
 * @property string $token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class Pootle_Page_Builder_Pro_Public{

	/**
	 * @var 	Pootle_Page_Builder_Pro_Public Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Main Pootle Page Builder Pro Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @since 1.0.0
	 * @return Pootle_Page_Builder_Pro instance
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct() {
		$this->token   =   Pootle_Page_Builder_Pro::$token;
		$this->url     =   Pootle_Page_Builder_Pro::$url;
		$this->path    =   Pootle_Page_Builder_Pro::$path;
		$this->version =   Pootle_Page_Builder_Pro::$version;
	} // End __construct()

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 * @since 1.0.0
	 */
	public function init() {
		$active_addons = get_option( 'ppbpro_active_addons', array() );
		foreach ( $active_addons as $addon => $active ) {
			$file = "$this->path/inc/ext/$addon/init.php";
			if ( file_exists( $file ) && $active ) {
				include $file;
			}
		}
	}
}