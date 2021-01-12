<p align="center">
<img src="https://raw.githubusercontent.com/crowdsecurity/cs-wordpress-blocker/master/docs/assets/crowdsec_wp.png" alt="CrowdSec" title="CrowdSec" width="280" height="300" />
</p>
<p align="center">
<img src="https://img.shields.io/badge/build-pass-green">
<img src="https://img.shields.io/badge/tests-pass-green">
</p>
<p align="center">
&#x1F4DA; <a href="https://docs.crowdsec.net/blockers/wordpress/installation/">Documentation</a>
&#x1F4A0; <a href="https://hub.crowdsec.net">Hub</a>
&#128172; <a href="https://discourse.crowdsec.net">Discourse </a>
</p>


# CrowdSec Wordpress Blocker (for old CrowdSec versions 0.x, else use the new bouncer)

> Important warning: This bouncer works only with 0.x versions of CrowdSec.
> From 1.x versions, You have to use the [new official WordPress Bouncer](https://github.com/crowdsecurity/cs-wordpress-bouncer).

## Installation

### Prepare the .zip folder

```bash
make zip
```

### Deploy the plugin

```bash
- Go to wordpress backend
- Go to 'Plugins' -> 'Add New' And click on "Upload Plugin" (at the top of the page)
- Choose your zipped plugins file and install it
- Now you can activate it and see a new menu named "Crowdsec"
```

### Settings

##### Crowdsec (Activate by default)

- You can activate or not the CrowdSec data query (you must have a crowdsec instance running on your host)
- If this option is activated, please fill the path to crowdsec database file

:warning: If you disable the Crowdsec decision pull, don't forget to `Refresh Cache`

##### General

 - Activate or not the block on the backend also
 
The cache can be flushed manually by clicking the `Refresh Cache` button. 

### Dashboard

Currently, the dashboard contains only a table with all IPs that are actually stored in cache.


