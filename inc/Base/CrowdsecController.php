<?php
/**
 * @package  CrowdsecPlugin
 */




 class CrowdsecController
{
    public $sqliteDbFile;
    public $remote_addr;
    public $sources_callback;

    public function __construct() {
        $this->remote_addr = $_SERVER["REMOTE_ADDR"];

        // prepare sql queries
        $this->sqliteQuery = "SELECT measure_type, (strftime('%s', until) - strftime('%s', 'now')) as until from ban_applications WHERE ip_text = '" . $this->remote_addr . "' AND deleted_at is NULL AND strftime('%s', until) > strftime('%s', 'now') LIMIT 1;";
        
        // for mysql later if needed
        #$this->mySqlGetIp = 'SELECT measure_type FROM ' . CROWDSEC_TABLE_NAME . ' WHERE ip_addr="%s" LIMIT 1;';
        #$this->mySqlInsertIp = "REPLACE INTO " . CROWDSEC_TABLE_NAME . " (ip_addr,timestamp) VALUES(%s,%d)";

        $this->sources_callback = array(
            "crowdsec_db" => array ($this, 'apply_filter_from_sqlite'),
            "crowdsec_api" => array ($this, 'apply_filter_from_crowdsec_api')
        );

        $this->caches_callback = array(
            "mysql_wordpress" => array(
                "apply" => array ($this,'apply_filter_from_wp_db'),
                "store" => array( $this, 'store_in_wp_db')
            ),
            "wordpress_transient" => array(
                "apply" => array ($this,'apply_filter_from_wp_transient'),
                "store" => array( $this, 'store_in_wp_transient')
            )
        );
    }



    public function blockIp($sources, $caches) {
        global $wpdb;

        // iterate over possible caches
        foreach ($caches as $k => $v) {
            $block = false;
            if ($v["active"]) {
                if (array_key_exists($k, $this->caches_callback)) {
                    if (!empty($v["args"])) {
                        list($block, $measure, $expiration) = call_user_func_array($this->caches_callback[$k]["apply"], $v["args"]);
                    } else {
                        list($block, $measure, $expiration) = call_user_func($this->caches_callback[$k]["apply"]);
                    }
                    if ($block) {
                        return array(true, $measure);
                    }
                }
            }
        }


        // iterate all possible sources of data (ie. crowdsec_db, crowdsec_api ...)
        foreach ($sources as $k => $v) {
            $block = false;
            if ($v["active"]) {
                if (array_key_exists($k, $this->sources_callback)) {
                    list($block, $measure, $expiration) = call_user_func_array($this->sources_callback[$k], $v["args"]);
                    if ($block) {
                        break;
                    }
                }
            }
        }

        if ($block) {
            foreach ($caches as $k => $v) {
                if (array_key_exists($k, $this->caches_callback)) {
                    if (empty($v["args"])) {
                        $v["args"] = array();
                    }
                    array_push($v["args"], $measure);
                    array_push($v["args"], $expiration);
                    call_user_func_array($this->caches_callback[$k]["store"], $v["args"]);
                }
            }
            return array(true, $measure);
        }

        return array(false, "");
    }

    function store_in_wp_transient($measure, $expiration) {
        set_transient("crowdsec_ip_" . $this->remote_addr,$measure, $expiration );
    }


    function apply_filter_from_wp_transient() {
        $found = get_transient("crowdsec_ip_" . $this->remote_addr);
        if (!$found) {
            return array(false, "");
        }
        error_log("crowdsec-wp: ip $this->remote_addr found in wordpress cache");
        return array(true, $found);
    }

    function apply_filter_from_crowdsec_api(){

    }

    function apply_filter_from_sqlite($db_path){
       
        if ( ! isset($db_path)) {
            error_log("crowdsec-wp: not crowdsec database file set");
            return array(false, "");
        }
    
        if ( ! file_exists($db_path)) {
            error_log("crowdsec-wp: crowdsec db file $db_path doesn't exist. Can't take decision");
            return array(false, "");
        }

        $cs_db = new SQLite3($db_path);
        $query = $cs_db->query($this->sqliteQuery);
        $result = $query->fetchArray();
        if ($result) {
            $measure = $result["measure_type"];
            $until = $result["until"];
            error_log("crowdsec-wp: ip $this->remote_addr found in crowdsec db : $measure");
            return array(true, $measure, $until);
        }
        return array(false, "");
    }
}

