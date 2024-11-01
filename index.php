<?php

/**
 * Plugin Name: Slider Ready!
 * Description: Ready! Responsive Slider plugin - the ultimate slideshow solution. Create stunning image and video sliders with professional templates and options.
 * Version: 0.3.7
 * Author: gallery plugin ready
 * Author URI: http://readyshoppingcart.com/product/wordpress-slider-plugin/
 * Text Domain: ready-slider
 **/

require_once dirname(__FILE__) . '/app/ReadySlider.php';

$readySlider = new ReadySlider();
$readySlider->run();
