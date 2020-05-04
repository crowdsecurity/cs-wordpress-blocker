<?php 
/**
 * @package  CrowdsecPlugin
 */
namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

class AdminCallbacks extends BaseController
{
	public function adminDashboard()
	{
		return require_once( "$this->plugin_path/templates/admin.php" );
	}

	public function settingPage(){
		return require_once( "$this->plugin_path/templates/dashboard.php" );
	}

}