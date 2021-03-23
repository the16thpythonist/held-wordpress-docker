<?php
/**
 * Pro customizer section.
 *
 * @since  1.0.0
 * @access public
 */
class Venturelite_Customize_Section_Pro extends WP_Customize_Section {

	/**
	 * The type of customize section being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'venture-lite';

	/**
	 * Custom button text to output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $pro_text = '';
		public $pro_text_2 = '';
			public $pro_text_3 = '';
				public $pro_text_4 = '';

	/**
	 * Custom pro button URL.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $pro_url = '';
		public $pro_url_2 = '';
			public $pro_url_3 = '';
				public $pro_url_4 = '';

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function json() {
		$json = parent::json();

		$json['pro_text'] = $this->pro_text;
		$json['pro_url']  = esc_url( $this->pro_url );
		$json['pro_text_2'] = $this->pro_text_2;
		$json['pro_url_2']  = esc_url( $this->pro_url_2 );
		$json['pro_text_3'] = $this->pro_text_3;
		$json['pro_url_3']  = esc_url( $this->pro_url_3 );
		$json['pro_text_4'] = $this->pro_text_4;
		$json['pro_url_4']  = esc_url( $this->pro_url_4 );

		return $json;
	}

	/**
	 * Outputs the Underscore.js template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	protected function render_template() { ?>

		<li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }} cannot-expand">

			<h3 class="accordion-section-title">
				<span class="Venturelite_pro_title">{{ data.title }}</span>
				<# if ( data.pro_text && data.pro_url ) { #>
					<a href="{{ data.pro_url }}" class="button button-venture-lite" target="_blank">{{ data.pro_text }}</a>
				<# } #>
				<# if ( data.pro_text_2 && data.pro_url_2 ) { #>
					<a href="{{ data.pro_url_2 }}" class="button button-venture-lite" target="_blank">{{ data.pro_text_2 }}</a>
				<# } #>
				<# if ( data.pro_text_3 && data.pro_url_3 ) { #>
					<a href="{{ data.pro_url_3 }}" class="button button-venture-lite" target="_blank">{{ data.pro_text_3 }}</a>
				<# } #>
				<# if ( data.pro_text_4 && data.pro_url_4 ) { #>
					<a href="{{ data.pro_url_4 }}" class="button button-venture-lite" target="_blank">{{ data.pro_text_4 }}</a>
				<# } #>
			</h3>
		</li>
	<?php }
}
