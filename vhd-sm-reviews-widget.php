<?php
/**
 * Plugin Name: Vet Help Direct API Reviews Widget
 * Plugin URI: https://github.com/marina9568/vhd-sm-reviews-widget
 * Description: Displays reviews via Vet Help Direct API
 * Version: 1.0
 * Author: Sokolova Marina
 * Author URI: https://github.com/marina9568
 * License: GPLv2
 
 * Copyright 2015  Sokolova Marina  (email : marina9568@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 */

if ( ! defined( 'VHD_SM_PLUGIN_DIR' ) ) {
    define( 'VHD_SM_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
}


function vhd_sm_reviews_includes(){
    wp_register_script( 'vhd-sm-reviews-js', plugins_url( '/lib/owl.carousel.min.js', __FILE__ ), array( 'jquery' ), '2.2.1' );
    wp_enqueue_script( 'vhd-sm-reviews-js' );
    
    wp_enqueue_style( 'vhd-sm-owl-carousel-css', plugins_url( '/lib/assets/owl.carousel.min.css', __FILE__ ));
    wp_enqueue_style( 'vhd-sm-owl-theme-carousel-css', plugins_url( '/lib/assets/owl.theme.default.min.css', __FILE__ ));
    wp_enqueue_style( 'vhd-sm-reviews-css', plugins_url( '/css/style.css', __FILE__ ));
}

add_action( 'wp_enqueue_scripts', 'vhd_sm_reviews_includes' );

require_once VHD_SM_PLUGIN_DIR . '/vhd-sm-reviews.php';

function vhd_sm_reviews_load_widget() {
    register_widget( 'vhd_sm_reviews_widget' );
}
add_action( 'widgets_init', 'vhd_sm_reviews_load_widget' );