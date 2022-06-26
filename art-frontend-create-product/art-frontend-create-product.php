<?php

/**
 * Plugin Name: Art Frontend Create Product
 * Author:Vitalii Diakin
 *
 */




if (!defined('ABSPATH')) {
	exit;
}

define('CP_DIR', plugin_dir_path(__FILE__));
define('CP_URI', plugin_dir_url(__FILE__));



require CP_DIR . 'includes/class-cp-core.php';

function cp()
{
	return CP_Core::instance();
}

cp();
