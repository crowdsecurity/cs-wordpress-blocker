# crowdsec-wp
Wordpress plugin

## Installation

### Prepare the .zip folder

```make zip```

### Deploy the plugin

```
- Go to wordpress backend
- Go to 'Plugins' -> 'Add New' And click on "Upload Plugin" (at the top of the page)
- Choose your zipped plugins file and install it
- Now you can activate it and see a new menu named "Crowdsec"
```

### Settings

##### CrowdWall (Not activate by default)

- You can activate or not the CrowdWall data query
- If you have activated Crowdsec PULL, please fill your API Token

##### Crowdwatch (Activate by default)

- You can activate or not the CrowdWatch data query (you must have a crowdwatch instance running on your host)
- If this option is activated, please fill the path to crowdwatch database file

##### General

 - Activate or not the block on the backend also
 
The cache can be flushed manually by cliking the `Refresh Cache` button. 

### Dashboard

Currently, the dashboard contains only a table with all IPs that are actually store in cache.

## Test

If you want to contribute or test the plugin, please `cd ./tests/` and run `docker-compose up -d` . 
Then visit localhost:80 to set up wordpress

When the plugins will be uploaded in wordpress, its folder will be located under : `wordpress/wp-content/plugins/crowdsec-wp` 

