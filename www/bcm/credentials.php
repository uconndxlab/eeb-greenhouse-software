<?php

### Credentials for MYSQL Database logins
$user="ghstaff";
$password="argus";
$database="bcm";

### Global Directory Variables
# Directory for PHP Code
$codebase='/var/www/bcm/'; ### !!! directory should be password protected to prevent malicious access
# Directory for Main Website
$webdir='/var/www/';
$rootdir='/var/www/'; #included for compatibility with older programs - should rewrite for consistency
$imagedir = '/var/www/images/';
$weburl='http://florawww.eeb.uconn.edu/';

### Misc Global Variables
$facilityname='UConn EEB Greenhouse';

$bloom_interval=14; #number of days to keep plant on flowering list
$inventory_interval=28; #flag accessions that have not been confirmed in this interval
$scout_interval=28; #scouting interval
$project_default="GEN_COLL"; # For new accessions
$banner_default='logos/aergclogo.jpg';
date_default_timezone_set('America/New_York');
?>
