<?php
/*
Plugin Name: jSnow Falling Snow
Plugin URI: http://joshmccarty.com
Description: Adds falling snow to your site using the jSnow plugin for jQuery.
Version: 0.2
Author: Josh McCarty
Author URI: http://joshmccarty.com
License: GNU General Public License, version 2 (GPL).
*/

/*  Copyright 2011 Josh McCarty  (email : josh@joshmccarty.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function jsnow_load_script() {
    wp_register_script( 'jsnow', plugins_url( 'jquery.jSnow.js', __FILE__ ), array( 'jquery' ), false, true );
    wp_enqueue_script( 'jsnow' );
}
add_action( 'wp_enqueue_scripts', 'jsnow_load_script' );

?>