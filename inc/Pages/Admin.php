<?php 
/**
 * @package  CrowdsecPlugin
 */
namespace Inc\Pages;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Api\Callbacks\ManagerCallbacks;

/**
* 
*/
class Admin extends BaseController
{
	public $settings;

	public $callbacks;
	public $callbacks_mngr;

	public $pages = array();

	public $subpages = array();

	public function register() 
	{
		$this->settings = new SettingsApi();

		$this->callbacks = new AdminCallbacks();
		$this->callbacks_mngr = new ManagerCallbacks();

		$this->setPages();

		$this->setSubpages();

		$this->setSettings();
		$this->setSections();
		$this->setFields();

		$this->settings->addPages( $this->pages )->withSubPage( 'Settings' )->addSubPages( $this->subpages )->register();
	}

	public function setPages() 
	{
		$this->pages = array(
			array(
				'page_title' => 'Crowdsec Plugin', 
				'menu_title' => 'Crowdsec', 
				'capability' => 'manage_options', 
				'menu_slug' => 'crowdsec_plugin', 
				'callback' => array( $this->callbacks, 'adminDashboard' ), 
				'icon_url' => 'dashicons-shield', 
				'position' => 110
			)
		);
	}

	public function setSubpages()
	{
		$this->subpages = array(
			array(
				'parent_slug' => 'crowdsec_plugin', 
				'page_title' => 'Custom Post Types', 
				'menu_title' => 'Dashboard', 
				'capability' => 'manage_options', 
				'menu_slug' => 'crowdsec_settings', 
				'callback' => array( $this->callbacks, 'settingPage' )
			)
		);
	}

	public function setSettings()
	{
		$args = array(
			array(
				'option_group' => 'crowdsec_plugin_settings',
				'option_name' => 'crowdsec_api_token',
				'callback' => array( $this->callbacks_mngr, 'inputboxSanitize' )
            ),
            array(
				'option_group' => 'crowdsec_plugin_settings',
				'option_name' => 'crowdsec_activate',
				'callback' => array( $this->callbacks_mngr, 'checkboxSanitize' )
			),
			array(
				'option_group' => 'crowdsec_plugin_settings',
				'option_name' => 'crowdwatch_activate',
				'callback' => array( $this->callbacks_mngr, 'checkboxSanitize' )
            ),
            array(
				'option_group' => 'crowdsec_plugin_settings',
				'option_name' => 'crowdwatch_db_file',
				'callback' => array( $this->callbacks_mngr, 'SqliteFileSanitize' )
			),
			array(
				'option_group' => 'crowdsec_plugin_settings',
				'option_name' => 'crowd_activate_on_backend',
				'callback' => array( $this->callbacks_mngr, 'checkboxSanitize' )
			)
		);

		$this->settings->setSettings( $args );
	}

	public function setSections()
	{
		$args = array(
			array(
				'id' => 'crowdsec_admin_index',
				'title' => 'Crowdsec configuration',
				'callback' => array( $this->callbacks_mngr, 'crowdsecSectionManager' ),
				'page' => 'crowdsec_settings'
            ),
			array(
				'id' => 'crowdwatch_admin_index',
				'title' => 'Crowdwatch  configuration',
				'callback' => array( $this->callbacks_mngr, 'crowdwatchSectionManager' ),
				'page' => 'crowdsec_settings'
			),
			array(
				'id' => 'crowd_admin_index',
				'title' => 'General configuration',
				'callback' => array( $this->callbacks_mngr, 'generalSectionManager' ),
				'page' => 'crowdsec_settings'
			)
		);

		$this->settings->setSections( $args );
	}

	public function setFields()
	{
		$current_db = get_option("crowdwatch_db_file");
		$api_token = get_option("crowdsec_api_token");
		$args = array(
			array(
				'id' => 'crowdsec_activate',
				'title' => 'Activate Crowdsec PULL',
				'callback' => array( $this->callbacks_mngr, 'checkboxField' ),
				'page' => 'crowdsec_settings',
				'section' => 'crowdsec_admin_index',
				'args' => array(
					'label_for' => 'crowdsec_activate',
					'class' => 'ui-toggle'
				)
			),
			array(
				'id' => 'crowdwatch_activate',
				'title' => 'Activate Crowdwatch decision pull',
				'callback' => array( $this->callbacks_mngr, 'checkboxField' ),
				'page' => 'crowdsec_settings',
				'section' => 'crowdwatch_admin_index',
				'args' => array(
					'label_for' => 'crowdwatch_activate',
					'class' => 'ui-toggle'
				)
			),
			array(
				'id' => 'crowd_activate_on_backend',
				'title' => 'Activate on backend',
				'callback' => array( $this->callbacks_mngr, 'checkboxField' ),
				'page' => 'crowdsec_settings',
				'section' => 'crowd_admin_index',
				'args' => array(
					'label_for' => 'crowd_activate_on_backend',
					'class' => 'ui-toggle'
				)
			),
			array(
				'id' => 'crowdsec_api_token',
				'title' => 'Crowdsec API Token',
				'callback' => array( $this->callbacks_mngr, 'ApiTokenInputboxField' ),
				'page' => 'crowdsec_settings',
				'section' => 'crowdsec_admin_index',
				'args' => array(
					'label_for' => 'crowdsec_api_token',
					'placeholder' => 'Crowdsec API Token'
				)
			),
			array(
				'id' => 'crowdwatch_db_file',
				'title' => 'Crowdwatch db file',
				'callback' => array( $this->callbacks_mngr, 'sqliteInputboxField' ),
				'page' => 'crowdsec_settings',
				'section' => 'crowdwatch_admin_index',
				'args' => array(
					'label_for' => 'crowdwatch_db_file',
					'placeholder' => 'Crowdwatch DB file',
					
				)
			)
		);

		$this->settings->setFields( $args );
	}
}