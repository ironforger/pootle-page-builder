<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 25/6/15
 * Time: 11:32 PM
 * @since 0.1.0
 */
final class Pootle_Page_Builder_Front_Css_Js extends Pootle_Page_Builder_Abstract {
	/**
	 * @var Pootle_Page_Builder_Front_Css_Js
	 * @access protected
	 * @since 0.1.0
	 */
	protected static $instance;

	/** @var array $styles The styles array */
	protected $styles = array();

	/**
	 * Magic __construct
	 * @since 0.1.0
	 */
	protected function __construct() {
		$this->hooks();
	}

	/**
	 * Adds the actions and filter hooks for plugin functioning
	 * @since 0.1.0
	 */
	private function hooks() {
		add_filter( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 0 );
		add_filter( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 0 );
	}

	/**
	 * Adds style entry to Pootle_Page_Builder_Front_Css_Js::$style property
	 * @param string $style Style to apply to element
	 * @param string $lmn The element selector
	 * @param int $res Resolution
	 */
	private function css( $style, $lmn, $res=1920 ) {

		if ( empty( $this->styles[ $res ] ) ) {
			$this->styles[ $res ] = array();
		}

		if ( empty( $this->styles[ $res ][ $style ] ) ) {
			$this->styles[ $res ][ $style ] = array();
		}

		$this->styles[ $res ][ $style ][] = $lmn;

	}

	/**
	 * Generate the actual CSS.
	 *
	 * @param int|string $post_id
	 * @param array $panels_data
	 * @since 0.1.0
	 */
	function panels_generate_css( $post_id, $panels_data ) {
		// Exit if we don't have panels data
		if ( empty( $panels_data ) || empty( $panels_data['grids'] ) ) {
			return;
		}

		$settings = pootlepb_settings();

		$panels_mobile_width  = $settings['mobile-width'];
		$panels_margin_bottom = $settings['margin-bottom'];

		// Add the grid sizing
		$this->grid_styles( $settings, $panels_data, $post_id );

		$panel_grid_cell_css = 'box-sizing: border-box !important; display: inline-block !important; vertical-align: top !important;';

		$this->css(  $panel_grid_cell_css , '.panel-grid-cell' );

		$this->css(  'font-size: 0;' , '.panel-grid-cell-container' );

		if ( $settings['responsive'] ) {
			// Add CSS to prevent overflow on mobile resolution.
			$panel_grid_css      = 'margin-left: 0 !important; margin-right: 0 !important;';
			$panel_grid_cell_css = 'padding: 0 !important; width: 100% !important;';

			$this->css(  $panel_grid_css , '.panel-grid', $panels_mobile_width );
			$this->css(  $panel_grid_cell_css , '.panel-grid-cell', $panels_mobile_width );
		} else {
			$panel_grid_cell_css = 'display: inline-block !important; vertical-align: top !important;';

			$this->css(  $panel_grid_cell_css , '.panel-grid-cell', $panels_mobile_width );
		}

		//Margin and padding
		$this->grid_elements_margin_padding( $settings, $panels_margin_bottom );

		/**
		 * Filter the unprocessed CSS array
		 * @since 0.1.0
		 */
		$this->styles = apply_filters( 'pootlepb_css', $this->styles );

		// Build the CSS
		return $this->grid_build_css();
	}

	/**
	 * Outputs style for rows and cells
	 * @param $settings
	 * @param $panels_data
	 * @param $post_id
	 * @since 0.1.0
	 */
	public function grid_styles( $settings, $panels_data, $post_id ) {
		$ci = 0;
		foreach ( $panels_data['grids'] as $gi => $grid ) {
			$cell_count = intval( $grid['cells'] );

			$this->col_widths( $ci, $gi, $post_id, $cell_count, $panels_data );

			$this->row_bottom_margin( $settings, $gi, $post_id, $panels_data );

			$this->mobile_styles( $settings, $gi, $post_id, $cell_count );

			$ci++;
		}

	}

	/**
	 * Outputs column width css
	 * @param int $ci Cell Index
	 * @param int $gi Grid Index
	 * @param int $post_id
	 * @param int $cell_count
	 * @param array $panels_data
	 * @since 0.1.0
	 */
	private function col_widths( $ci, $gi, $post_id, $cell_count, $panels_data ) {
		for ( $i = 0; $i < $cell_count; $i ++ ) {
			$cell = $panels_data['grid_cells'][ $ci ];

			if ( $cell_count > 1 ) {
				$css_new = 'width:' . round( $cell['weight'] * 100, 3 ) . '%';
				$this->css(  $css_new , '#pgc-' . $post_id . '-' . $gi . '-' . $i );
			}
		}
	}

	/**
	 * Outputs margin bottom style for rows
	 * @param array $settings PPB settings
	 * @param int $gi Grid Index
	 * @param int $post_id
	 * @param array $panels_data
	 * @since 0.1.0
	 */
	private function row_bottom_margin( $settings, $gi, $post_id, $panels_data ) {

		$panels_margin_bottom = $settings['margin-bottom'];

		// Add the bottom margin to any grids that aren't the last
		if ( $gi != count( $panels_data['grids'] ) - 1 ) {
			$this->css(  'margin-bottom: ' . $panels_margin_bottom . 'px' , '#pg-' . $post_id . '-' . $gi );
		}
	}

	private function mobile_styles( $settings, $gi, $post_id, $cell_count ) {

		$panels_margin_bottom = $settings['margin-bottom'];
		$panels_mobile_width  = $settings['mobile-width'];

		if ( $settings['responsive'] ) {
			// Mobile Responsive

			$this->css(  'float:none' , '#pg-' . $post_id . '-' . $gi . ' .panel-grid-cell', $panels_mobile_width );
			$this->css(  'width:auto' , '#pg-' . $post_id . '-' . $gi . ' .panel-grid-cell', $panels_mobile_width );

			for ( $i = 0; $i < $cell_count; $i ++ ) {
				if ( $i != $cell_count - 1 ) {
					$css_new = 'margin-bottom:' . $panels_margin_bottom . 'px';
					$this->css(  $css_new , '#pgc-' . $post_id . '-' . $gi . '-' . $i, $panels_mobile_width );
				}
			}
		}
	}

	/**
	 * Margin padding for rows and columns
	 * @param array $settings
	 * @param array $panels_margin_bottom
	 * @since 0.1.0
	 */
	public function grid_elements_margin_padding( $settings, $panels_margin_bottom ) {

		// Add the bottom margin
		$bottom_margin      = 'margin-bottom: ' . $panels_margin_bottom . 'px';
		$bottom_margin_last = 'margin-bottom: 0 !important';

		$this->css(  $bottom_margin , '.panel-grid-cell .panel' );
		$this->css(  $bottom_margin_last , '.panel-grid-cell .panel:last-child' );

		// This is for the side margins
		$magin_half    = $settings['margin-sides'] / 2;
		$side_paddings = "padding: 0 {$magin_half}px 0";

		$this->css(  $side_paddings , '.panel-grid-cell' );

		if ( ! defined( 'POOTLEPB_OLD_V' ) ) {

			$this->css( 'padding: 10px', '.panel' );
			$this->css( 'padding: 5px', '.panel', 768 );

		}

	}

	/**
	 * Decodes array css to string
	 * @return string
	 * @since 0.1.0
	 */
	public function grid_build_css() {
		$css_text = '';
		krsort( $this->styles );
		foreach ( $this->styles as $res => $def ) {
			if ( empty( $def ) ) {
				continue;
			}

			if ( $res < 1920 ) {
				$css_text .= '@media ( max-width:' . $res . 'px ) { ';
			}

			foreach ( $def as $property => $selector ) {
				$selector = array_unique( $selector );
				$css_text .= implode( ' , ', $selector ) . ' { ' . $property . ' } ';
			}

			if ( $res < 1920 ) {
				$css_text .= ' } ';
			}
		}

		return $css_text;
	}

	/**
	 * Enqueue the required styles
	 * @since 0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'ppb-panels-front', POOTLEPB_URL . 'css/front.css', array(), POOTLEPB_VERSION );
	}

	/**
	 * Enqueue the required scripts
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'pootle-page-builder-front-js', POOTLEPB_URL . '/js/front-end.js', array( 'jquery' ) );
	}
}

//Instantiating Pootle_Page_Builder_Front_Css_Js class
Pootle_Page_Builder_Front_Css_Js::instance();