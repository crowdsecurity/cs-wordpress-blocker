<?php
/**
 * @package  CrowdsecPlugin
 */
namespace Inc\Base;

class Filter
{
	public function __construct() {
        $this->api_token = "";
        $this->db_file = "";
        $this->activated = 0;
	}
}