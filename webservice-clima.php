<?php
/**
 * Plugin Name:       Webservice Clima
 * Description:       Este plugin permite consultar datos del clima por ciudad
 * Version:           1.10.3
 * Author:            Armando Déctor
 * Text Domain:       ws-clima
 */
define('path', plugin_dir_path(__FILE__) );
define('url', plugin_dir_url(__FILE__) );
require_once "class.ws-clima.php";
new WSClima();