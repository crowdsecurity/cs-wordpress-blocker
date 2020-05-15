<?php 
/**
 * @package  CrowdsecPlugin
 */
namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;



class ManagerCallbacks extends BaseController
{
    public function checkboxSanitize( $input )
    {
        return ( isset($input) ? true : false );
    }
    
    public function inputboxSanitize( $input )
    {
        return $input;
    }


    public function SqliteFileSanitize ( $input ) 
    {
        //var_dump($input);
        $input = esc_attr($input);
        if ( ! file_exists($input)) {
            $crowdwatch_activated = get_option("crowdwatch_activate");
            if ($crowdwatch_activated) {
                add_settings_error("SQL db file", "crowdsec_error", "SQLite database file " . $input . " not found.");
                return $input;
            }
        }
        return $input;
    }

    public function sqliteInputboxField( $args )
    {
        $name = $args["label_for"];
        $placeholder = $args["placeholder"];
        $inputBox = get_option($name);
        $value = esc_attr(get_option("crowdwatch_db_file"));
        if ( ! file_exists($value)) {
            echo "SQLite database file " . $value . " not found.\n";
        }
        echo '<input type="text" class="regular-text" name="' . $name . '" value="'. $value . '" placeholder="' . $placeholder . '">';
    }

    public function ApiTokenInputboxField( $args )
    {
        $name = $args["label_for"];
        $placeholder = $args["placeholder"];
        $inputBox = get_option($name);
        $value = esc_attr(get_option("crowdsec_api_token"));
        echo '<input type="text" class="regular-text" name="' . $name . '" value="' . $value . '" placeholder="' . $placeholder . '">';
    }

    public function crowdsecSectionManager()
    {
        echo 'Manage your crowdsec configuration';
    }
    
    public function crowdwatchSectionManager()
    {
        echo 'Manage your crowdwatch configuration';
    }

    public function generalSectionManager()
    {
        echo "General configuration for your plugin";
    }

    public function backendActivateCheckBoxField( $args )
    {
        $name = $args['label_for'];
        $classes = $args['class'];
        $checkbox = get_option( $name );
        $options = get_option( 'crowd_activate_on_backend' );
        echo '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $name . '" value="' . $options . '" class="" ' . ($checkbox ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
    }

    public function crowdSecCheckboxField( $args )
    {
        $name = $args['label_for'];
        $classes = $args['class'];
        $checkbox = get_option( $name );
        $options = get_option( 'crowdwatch_activate' );
        echo '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $name . '" value="' . $options . '" class="" ' . ($checkbox ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
    }

}