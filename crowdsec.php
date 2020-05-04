<?php

session_start();

/**
 * @package CrowdsecPlugin
 * 
 */
/*

Plugin Name: Crowdsec
Plugin URI: https://www.crowdsec.net/
Description: Wordpressp plugin that doesn't allow IP according to crowdwatch or dynafw
Version 1.0.0
Author: JohnDoe
Author URI: https://www.crowdsec.net/
License: MIT
Text Domain: crowdsec-wp
*/


defined ('ABSPATH') or die ('Error');
define('CROWDWATCH_DEFAULT_DB_PATH', '/var/run/crowdsec/crowdwatch.db');

if ( file_exists( dirname(__FILE__) . '/vendor/autoload.php' )) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

require_once dirname( __FILE__ )  . "/inc/Base/CrowdsecController.php";
require_once  dirname( __FILE__ )  . "/captcha.php";



function flush_ip(){
    global $wpdb;

    $sql = "DELETE FROM wp_options WHERE option_name LIKE '%_transient_crowdsec_ip%' ;";
    $result = $wpdb->query($sql);

    update_option("cache_successfully_refreshed", "1");
    header("Location: {$_SERVER['HTTP_REFERER']}");
}
add_action('admin_post_refresh_cache', 'flush_ip');


/**
 * The code that runs during plugin activation
 */
function activate_crowdsec_plugin()
{
    Inc\Base\Activate::activate();
    
    // default option
    add_option("cs_is_activated", 1);
    add_option("crowdwatch_db_file", CROWDWATCH_DEFAULT_DB_PATH);
    add_option("crowdwatch_activate", "1");
    add_option("crowdsec_activate", "0");
    add_option("crowd_activate_on_backend", "0");
    add_option("cache_successfully_refreshed", "1");
    add_option("cache_is_empty", "1");

}   

register_activation_hook( __FILE__, 'activate_crowdsec_plugin');



/**
 * The code that runs during plugin deactivation
 */
function deactivate_crowdsec_plugin()
{
    Inc\Base\Deactivate::deactivate();
    flush_ip();

    // clean option
    delete_option("cs_is_activated");
    delete_option("crowdwatch_db_file");
    delete_option("crowdwatch_activate");
    delete_option("crowdsec_activate");
    delete_option("crowd_activate_on_backend");
    delete_option("crowd_ip_expiration");
    delete_option("crowd_interval_flush");
    delete_option("crowdsec_api_token");


}
register_deactivation_hook( __FILE__, 'deactivate_crowdsec_plugin');


if (is_admin()) {
/**
 * Initialize all the core classes of the plugin
 */
    if ( class_exists( 'Inc\\Init' ) ) {
        Inc\Init::register_services();
    }
}




$activate_on_backend = get_option('crowd_activate_on_backend');
if ($activate_on_backend === "1") {
    add_action('init', "blockIp");
} else {
    add_action('wp', "blockIp");
}

function blockIp() {

    if (isset($_SESSION['phrase'])) {
        error_log("crowdwatch-wp: " . $_SERVER["REMOTE_ADDR"] . " is in captcha mode");
        if (checkCaptcha() === false) {
            echo "Invalid captcha!";
            $captchaOk = setCaptcha();
            $_SESSION["captchaResolved"] = false;
        } else {
            $_SESSION["captchaResolved"] = true;
        }
    }


    // get admin options
    $api_token = get_option("crowdsec_api_token");
    $db_file = get_option("crowdwatch_db_file");
    $activated = get_option("cs_is_activated");
    $crowdwatch_activated = get_option("crowdwatch_activate");
    $crowdsec_activated = get_option("crowdsec_activate");

    $cs_source = array(
        "crowdwatch_db" => array(
            "name" => "crowdwatch_db",
            "args" => array(
                $db_file
            ),
            "active" => ($crowdwatch_activated === "1") ? true : false
        ),
        "crowdsec_api" => array(
            "name" => "crowdsec_api",
            "active" => ($crowdwatch_activated === "1") ? true : false,
            "args" => array(
                $api_token
            )
        )
    );

    $cs_cache = array(
        "wordpress_transient" => array(
            "active" => true
        )
    );
    if ($activated) {
        $csController = new CrowdsecController();
        list($block, $measure) = $csController->blockIp($cs_source, $cs_cache);
        if ($block) {
            if ($measure == "ban") {
                wp_die("Not authorized : " . $_SERVER["REMOTE_ADDR"]);
            } else {
                if (!$_SESSION["captchaResolved"]) {
                    setCaptcha();
                } else {
                    $_SESSION["captchaResolved"] = false; // reset to false for next time visit
                }
            }
        }
    }
}

?>
