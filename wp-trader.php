<?php
/**
 * @package Minances_Kavav
 * @version 1.7.2
 */
/*
Plugin Name: My Plugin by Kavav Digital
Plugin URI: https://kavavdigital.com.ve
Description: Descripcion de Minances
Author: Octavio Martinez
Version: 1.0.0
Author URI: https://wa.me/19104468990
*/

require 'include/classWPTrader.php';

register_activation_hook( __FILE__, array('WP_Trader', 'active') );
register_deactivation_hook( __FILE__, array('WP_Trader', 'deactive') );
register_uninstall_hook(__FILE__, array('WP_Trader', 'uninstall') );

add_action('init', array('WP_Trader', 'init') );



