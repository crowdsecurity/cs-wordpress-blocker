<?php
/**
 * @package  CrowdsecPlugin
 */
namespace Inc\Base;

use Dababase\DatabaseManager;

class Activate
{
	public static function activate() {
		flush_rewrite_rules();
	}
}