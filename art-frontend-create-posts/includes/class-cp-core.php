<?php

class CP_Core
{
	private static $instanse;


	public function __construct()
	{

		$this->hooks();

		$this->includes();
	}


	public function hooks()
	{
		add_action('wp_enqueue_scripts', [$this, 'enqueue']);
	}

	public function includes()
	{

		require_once CP_DIR . 'includes/class-cp-shortcode.php';
		new CP_Shortcode();

		require_once CP_DIR . 'includes/class-cp-ajax.php';
		new CP_Ajax();
	}

	public function enqueue()
	{

		wp_register_style(
			'cp-styles',
			CP_URI . 'assets/cp-style.css',
			[],
			filemtime(CP_DIR . 'assets/cp-style.css')
		);

		wp_enqueue_style('cp-styles');

		wp_register_script(
			'cp-script',
			CP_URI . 'assets/cp-script.js',
			['jquery'],
			filemtime(CP_DIR . 'assets/cp-script.js'),
			true
		);

		wp_enqueue_script('cp-script');

		wp_register_script(
			'cp-script-ajax',
			CP_URI . 'assets/cp-ajax.js',
			['jquery'],
			filemtime(CP_DIR . 'assets/cp-ajax.js'),
			true
		);
		wp_localize_script(
			'cp-script-ajax',
			'cp_ajax',
			[
				'url'   => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('cp-ajax-nonce'),
			]
		);
		wp_enqueue_script('cp-script-ajax');

		wp_register_style(
			'cp-select2-style',
			'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css',
			[],
			null
		);

		wp_register_script(
			'cp-select2-script',
			'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js',
			['jquery', 'cp-script'],
			null,
			true
		);
	}






	public static function instance()
	{

		if (is_null(self::$instanse)) {
			self::$instanse = new self();
		}

		return self::$instanse;
	}
}
