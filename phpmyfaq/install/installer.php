<?php
/**
* $Id: installer.php,v 1.42 2005-12-22 13:05:28 b33blebr0x Exp $
*
* The main phpMyFAQ Installer
*
* This script tests the complete environment, writes the database connection
* paramters into the file data.php and the configuration into the file
* config.php. After that the script creates all MySQL tables and inserts the
* admin user.
*
* @author      Thorsten Rinne <thorsten@phpmyfaq.de>
* @author      Tom Rochester <tom.rochester@gmail.com>
* @author      Johannes Schl�ter <johannes@php.net>
* @since       2002-08-20
* @copyright   (c) 2001-2005 phpMyFAQ Team
* 
* The contents of this file are subject to the Mozilla Public License
* Version 1.1 (the "License"); you may not use this file except in
* compliance with the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
* 
* Software distributed under the License is distributed on an "AS IS"
* basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
* License for the specific language governing rights and limitations
* under the License.
*/

define("VERSION", "1.6.0-dev");
define("COPYRIGHT", "&copy; 2001-2005 <a href=\"http://www.phpmyfaq.de/\">phpMyFAQ-Team</a> | All rights reserved.");
define("SAFEMODE", @ini_get("safe_mode"));
define("PMF_ROOT_DIR", dirname(dirname(__FILE__)));
require_once(PMF_ROOT_DIR."/inc/constants.php");

/**
* db_check()
*
* Lookup for installed database extensions
*
* If the first supported extension is enabled, return true.
*
* @param    array   $supported_databases
* @return   boolean
* @access   public
* @author   Thorsten Rinne <thorsten@phpmyfaq.de>
*/
function db_check($supported_databases)
{
    foreach ($supported_databases as $extension => $database) {
        if (extension_loaded($extension)) {
            return true;
        }
    }
    return false;
}

/**
* phpmyfaq_check()
*
* Checks for an installed phpMyFAQ version
*
* @return   boolean
* @access   public
* @author   Thorsten Rinne <thorsten@phpmyfaq.de>
*/
function phpmyfaq_check()
{
    if (@include('../inc/data.php')) {
        include('../inc/data.php');
        // check for version 1.4.x
        if ((isset($DB["server"]) && $DB["server"] != "") || (isset($DB["user"]) && $DB["user"] != "") || (isset($DB["password"]) && $DB["password"] != "") || (isset($DB["db"]) && $DB["db"] != "") || (isset($DB["prefix"]) && $DB["prefix"] != "")) {
            return false;
        }
        // check for version 1.5.x
        if ((isset($DB["server"]) && $DB["server"] != "") || (isset($DB["user"]) && $DB["user"] != "") || (isset($DB["password"]) && $DB["password"] != "") || (isset($DB["db"]) && $DB["db"] != "") || (isset($DB["prefix"]) && $DB["prefix"] != "")  || (isset($DB["type"]) && $DB["type"] != "")) {
            return false;
        }
        return true;
    }
    return true;
}

/**
* uninstall()
*
* Executes the uninstall queries
*
* @return   void
* @access   public
* @author   Thorsten Rinne <thorsten@phpmyfaq.de>
*/
function uninstall()
{
	global $uninst, $db;
	while ($each_query = each($uninst)) {
		$db->query($each_query[1]);
    }
}

/**
* HTMLFooter()
*
* Executes the uninstall queries
*
* @return   void
* @access   public
* @author   Thorsten Rinne <thorsten@phpmyfaq.de>
*/
function HTMLFooter()
{
	print "<p class=\"center\">".COPYRIGHT."</p>\n</body>\n</html>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>phpMyFAQ <?php print VERSION; ?> Installation</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=iso-8859-1" />
    <script language="javascript" type="text/javascript">
    /*<![CDATA[*/
    <!--
    function select_database(field) {
        switch (field.value) {
            case 'sqlite':
                document.getElementById('dbsqlite').style.display='inline';
                document.getElementById('dbdatafull').style.display='none';
                break;
            default:
                document.getElementById('dbsqlite').style.display='none';
                document.getElementById('dbdatafull').style.display='inline';
                break;
        }
    }
    -->
    /*]]>*/
    </script>
    <style media="screen" type="text/css">
    /*<![CDATA[*/
    <!--
    body {
	    margin: 10px;
	    padding: 0px;
	    font-size: 12px;
	    font-family: "Bitstream Vera Sans", "Trebuchet MS", Geneva, Verdana, Arial, Helvetica, sans-serif;
        background: #ffffff;
        color: #000000;
    }
    #header {
	    margin: auto;
	    padding: 35px;
        background: #0D487A;
        color: #ffffff;
        text-align: center;
    }
    #header h1 {
	    font-family: "Trebuchet MS", Geneva, Verdana, Arial, Helvetica, sans-serif;
	    margin: auto;
        color: #ffffff;
        text-align: center;
    }
    .center {
        text-align: center;
    }
    fieldset.installation {
        margin: auto;
        border: 1px solid black;
        width: 550px;
        margin-bottom: 10px;
        clear: both;
    }
    legend.installation {
        border: 1px solid black;
        background-color: #FCE397;
        padding: 4px 4px 4px 4px;
    }
    .input {
        width: 200px;
        background-color: #f5f5f5;
        border: 1px solid black;
    }
    span.text {
        width: 250px;
        float: left;
        text-align: right;
        padding-right: 10px;
        line-height: 20px;
    }
    #admin {
        line-height: 20px;
        font-weight: bold;
    }
    .help {
        cursor: help;
        border-bottom: 1px dotted Black;
        font-size: 14px;
        font-weight: bold;
        padding-left: 5px;
    }
    .button {
        background-color: #ff7f50;
        border: 1px solid #000000;
        color: #ffffff;
    }
    .error {
        margin: auto;
        margin-top: 20px;
        width: 600px;
        text-align: center;
        padding: 10px;
        line-height: 20px;
        background-color: #f5f5f5;
        border: 1px solid black;
    }
    -->
    /*]]>*/
    </style>
</head>
<body>

<h1 id="header">phpMyFAQ <?php print VERSION; ?> Installation</h1>

<?php
if (version_compare(PHP_VERSION, '4.3.0') == false) {
	print "<p class=\"center\">You need PHP Version 4.3.0 or higher!</p>\n";
	HTMLFooter();
	die();
}
if (db_check($supported_databases) == false) {
	print "<p class=\"center\">No supported database found! Please install one of the following database systems and enable the m 	corresponding PHP extension:</p>\n";
	print "<ul>\n";
    foreach ($supported_databases as $database) {
	    printf('    <li>%s</li>', $database[1]);
    }
	print "</ul>\n";
	HTMLFooter();
	die();
}
if (!phpmyfaq_check()) {
	print "<p class=\"center\">It seems you already running a version of phpMyFAQ.<br />Please use the <a href=\"update.php\">update script</a>.</p>\n";
	HTMLFooter();
	die();
}
$dirs = array('/attachments', '/data', '/images', '/inc', '/pdf', '/xml',);
$faileddirs = array();
foreach ($dirs AS $dir) {
    if (!is_dir(PMF_ROOT_DIR.$dir)) {
        if (!mkdir (PMF_ROOT_DIR.$dir, 0755)) {
            $faileddirs[] = $dir;
        }
    } else if (!is_writable(PMF_ROOT_DIR.$dir)) {
        $faileddirs[] = $dir;
    } else {
        @copy("index.html", PMF_ROOT_DIR.$dir.'/index.html');
    }
}

if (sizeof($faileddirs)) {
    print '<p class="center">The following dirctory/-ies could not be created or are not writable:</p><ul>';
    foreach ($faileddirs AS $dir) {
        print "<li>$dir</li>\n";
    }
    print '</ul><p class="center">Please create it manually and/or change access to chmod 755 (or greater if necessary).</p>';
    HTMLFooter();
    die();
}

if (!isset($_POST["sql_server"]) AND !isset($_POST["sql_user"]) AND !isset($_POST["sql_db"])) {
?>

<p class="center">Your PHP version: <strong>PHP <?php print phpversion(); ?></strong></p>

<?php
    if (SAFEMODE == 1) {
        print "<p class=\"center\">The PHP safe mode is enabled. You may have problems when phpMyFAQ writes in some directories.</p>\n";
    }
?>

<p class="center">You should read the <a href="../docs/documentation.en.html">documentation</a> carefully before installing phpMyFAQ.</p>

<form action="<?php print $_SERVER["PHP_SELF"]; ?>" method="post">
<fieldset class="installation">
<legend class="installation">Database information</legend>
<p>
<span class="text">SQL server:</span>
<select class="input" name="sql_type" size="1" onchange="select_database(this);">
<?php
	// check what extensions are loaded in PHP
    foreach ($supported_databases as $extension => $database) {
        if (extension_loaded($extension) && version_compare(PHP_VERSION, $database[0]) >= 0) {
		    printf('<option value="%s">%s</option>', $extension, $database[1]);
	    }
    }
?>	
</select>
<span class="help" title="Please enter the type of SQL server here.">?</span>
</p>
<div id="dbdatafull">
    <p>
    <span class="text">SQL server host:</span>
    <input class="input" type="text" name="sql_server" />
    <span class="help" title="Please enter the host of your SQL server here.">?</span>
    </p>
    <p>
    <span class="text">SQL username:</span>
    <input class="input" type="text" name="sql_user" />
    <span class="help" title="Please enter your SQL username here.">?</span>
    </p>
    <p>
    <span class="text">SQL password:</span>
    <input class="input" name="sql_passwort" type="password" />
    <span class="help" title="Please enter your SQL password here.">?</span>
    </p>
    <p>
    <span class="text">SQL database:</span>
    <input class="input" type="text" name="sql_db" />
    <span class="help" title="Please enter your SQL database here.">?</span>
    </p>
</div>
<div id="dbsqlite" style="display:none;">
    <p>
    <span class="text">SQLite database file:</span>
    <input class="input" type="text" name="sql_sqlitefile" />
    <span class="help" title="Please enter the full path to your SQLite datafile which should be outside your documentation root.">?</span>
    </p>
</div>
<p>
<span class="text">Table prefix:</span>
<input class="input" type="text" name="sqltblpre" />
<span class="help" title="Please enter a table prefix here if you want to install more phpMyFAQ installations on one database.">?</span>
</p>
</fieldset>
<br />
<?php
    if (extension_loaded('ldap')) {
?>
<fieldset class="installation">
<legend class="installation">LDAP information</legend>
<p>
<span class="text">Do you want to use LDAP?</span>
<input type="checkbox" name="ldap_enabled" value="yes" />
</p>
<p>
<span class="text">LDAP server host:</span>
<input class="input" type="text" name="ldap_server" />
<span class="help" title="Please enter the host of your LDAP server here.">?</span>
</p>
<p>
<span class="text">LDAP server port:</span>
<input class="input" type="text" name="ldap_port" value="389" />
<span class="help" title="Please enter the port of your LDAP server here.">?</span>
</p>
<p>
<span class="text">LDAP username:</span>
<input class="input" type="text" name="ldap_user" />
<span class="help" title="Please enter your specified RDN username here.">?</span>
</p>
<p>
<span class="text">LDAP password:</span>
<input class="input" name="ldap_password" type="password" />
<span class="help" title="Please enter your LDAP password here.">?</span>
</p>
<p>
<span class="text">Distinguished name (dn):</span>
<input class="input" type="text" name="ldap_base" />
<span class="help" title="Please enter your distinguished name, e.g. 'cn=John Smith,ou=Accounts,o=My Company,c=US' here.">?</span>
</p>
</fieldset>
<br />
<?php
    }
?>

<fieldset class="installation">
<legend class="installation">phpMyFAQ information</legend>
<p>
<span class="text">Default language:</span>
<select class="input" name="language" size="1">
<?php
	if ($dir = @opendir(PMF_ROOT_DIR."/lang")) {
		while ($dat = @readdir($dir)) {
			if (substr($dat, -4) == ".php") {
				print "\t\t<option value=\"".$dat."\"";
				if ($dat == "language_en.php") {
					print " selected=\"selected\"";
				}
                print ">".$languageCodes[substr(strtoupper($dat), 9, 2)]."</option>\n";
			}
		}
	} else {
		print "\t\t<option>english</option>";
    }
?>
</select>
<span class="help" title="Please select your default language.">?</span>
</p>
<p>
<span class="text">Administrator's real name:</span>
<input class="input" type="text" name="realname" />
<span class="help" title="Please enter your real name here.">?</span>
</p>
<p>
<span class="text">Administrator's e-mail address:</span>
<input class="input" type="text" name="email" />
<span class="help" title="Please enter your email adress here.">?</span>
</p>
<p>
<span class="text">Administrator's username:</span>
<span id="admin">admin</span>
</p>
<p>
<span class="text">Administrator's password:</span>
<input class="input" type="password" name="password" />
<span class="help" title="Please enter your password for the admin area.">?</span>
</p>
<p>
<span class="text">Retype password:</span>
<input class="input" type="password" name="password_retyped" />
<span class="help" title="Please retype your password for checkup.">?</span>
</p>
<p class="center"><strong>Do not use if you're already running a version of phpMyFAQ!</strong></p>
<p class="center"><input type="submit" value="Install phpMyFAQ" class="button" /></p>
</fieldset>
</form>
<?php
    HTMLFooter();
} else {
    // check database entries
	if (isset($_POST["sql_type"]) && $_POST["sql_type"] != "") {
		$sql_type = trim($_POST["sql_type"]);
        if (file_exists(PMF_ROOT_DIR.'/inc/PMF_DB/'.$sql_type.'.php')) {
            require_once(PMF_ROOT_DIR.'/inc/PMF_DB/'.$sql_type.'.php');
        } else {
            print '<p class="error"><strong>Error:</strong> Invalid server type.</p>';
            HTMLFooter();
            die();
		}
	} else {
        print "<p class=\"error\"><strong>Error:</strong> There's no DB server input.</p>\n";
		HTMLFooter();
		die();
	}

    if (isset($_POST["sql_server"]) && $_POST["sql_server"] != "" || $sql_type == 'sqlite') {
		$sql_server = $_POST["sql_server"];
    } else {
        print "<p class=\"error\"><strong>Error:</strong> There's no DB server input.</p>\n";
		HTMLFooter();
		die();
    }

	if (isset($_POST["sql_user"]) && $_POST["sql_user"] != "" || $sql_type == 'sqlite') {
		$sql_user = $_POST["sql_user"];
    } else {
        print "<p class=\"error\"><strong>Error:</strong> There's no DB username input.</p>\n";
		HTMLFooter();
		die();
    }

    if (isset($_POST["sql_passwort"]) && $_POST["sql_passwort"] != "" || $sql_type == 'sqlite') {
		$sql_passwort = $_POST["sql_passwort"];
    } else {
        $sql_passwort = '';
    }

    if (isset($_POST["sql_db"]) && $_POST["sql_db"] != "" || $sql_type == 'sqlite') {
		$sql_db = $_POST["sql_db"];
    } else {
        print "<p class=\"error\"><strong>Error:</strong> There's no DB database input.</p>\n";
		HTMLFooter();
		die();
    }

    if ($sql_type == 'sqlite') {
        if (isset($_POST["sql_sqlitefile"]) && $_POST["sql_sqlitefile"] != "") {
            $sql_server = $_POST["sql_sqlitefile"]; // We're using $sql_server, too!
        } else {
            print "<p class=\"error\"><strong>Error:</strong> There's no SQLite database filename input.</p>\n";
            HTMLFooter();
            die();
        }
    }

    // check database connection
    require_once(PMF_ROOT_DIR."/inc/db.php");
	$db = db::db_select($sql_type);
	$db->connect($sql_server, $sql_user, $sql_passwort, $sql_db);
	if (!$db) {
		print "<p class=\"error\"><strong>DB Error:</strong> ".$db->error()."</p>\n";
		HTMLFooter();
		die();
    }
    
    // check LDAP if available
    if (extension_loaded('ldap') && isset($_POST['ldap_enabled']) && $_POST['ldap_enabled'] == 'yes') {
        
        // check LDAP entries
        if (isset($_POST["ldap_server"]) && $_POST["ldap_server"] != "") {
            $ldap_server = $_POST["ldap_server"];
        } else {
            print "<p class=\"error\"><strong>Error:</strong> There's no LDAP server input.</p>\n";
            HTMLFooter();
            die();
        }
        if (isset($_POST["ldap_port"]) && $_POST["ldap_port"] != "") {
            $ldap_port = $_POST["ldap_port"];
        } else {
            print "<p class=\"error\"><strong>Error:</strong> There's no LDAP port input.</p>\n";
            HTMLFooter();
            die();
        }
        if (isset($_POST["ldap_user"]) && $_POST["ldap_user"] != "") {
            $ldap_user = $_POST["ldap_user"];
        } else {
            print "<p class=\"error\"><strong>Error:</strong> There's no LDAP username input.</p>\n";
            HTMLFooter();
            die();
        }
        if (isset($_POST["ldap_password"]) && $_POST["ldap_password"] != "") {
            $ldap_password = $_POST["ldap_password"];
        } else {
            print "<p class=\"error\"><strong>Error:</strong> There's no LDAP password input.</p>\n";
            HTMLFooter();
            die();
        }
        if (isset($_POST["ldap_base"]) && $_POST["ldap_base"] != "") {
            $ldap_base = $_POST["ldap_base"];
        } else {
            print "<p class=\"error\"><strong>Error:</strong> There's no distinguished name input for LDAP.</p>\n";
            HTMLFooter();
            die();
        }
        
        // check LDAP connection
        require_once(PMF_ROOT_DIR."/inc/ldap.php");
        $ldap = new LDAP($ldap_server, $ldap_port, $ldap_user, $ldap_base);
        if (!$ldap) {
            print "<p class=\"error\"><strong>LDAP Error:</strong> ".$ldap->error()."</p>\n";
            HTMLFooter();
            die();
        }
    }
    
    // check user entries
    if (isset($_POST["password"]) && $_POST["password"] != "") {
        $password = $_POST["password"];
    } else {
		print "<p class=\"error\"><strong>Error:</strong> There's no password for the administrator's account. Please set your password.</p>\n";
		HTMLFooter();
		die();
    }
    if (isset($_POST["password_retyped"]) && $_POST["password_retyped"] != "") {
        $password_retyped = $_POST["password_retyped"];
    } else {
		print "<p class=\"error\"><strong>Error:</strong> There's no retyped password. Please set your retyped password.</p>\n";
		HTMLFooter();
		die();
    }
    if (strlen($password) <= 5 || strlen($password_retyped) <= 5) {
		print "<p class=\"error\"><strong>Error:</strong> Your password and retyped password are too short. Please set your password and your retyped password with a minimum of 6 characters.</p>\n";
		HTMLFooter();
		die();
    }
    if ($password != $password_retyped) {
		print "<p class=\"error\"><strong>Error:</strong> Your password and retyped password are not equal. Please check your password and your retyped password.</p>\n";
		HTMLFooter();
		die();
    }
    if (isset($_POST["sqltblpre"]) && $_POST["sqltblpre"] != "") {
        $sqltblpre = $_POST["sqltblpre"];
    } else {
        $sqltblpre = "";
    }
    
    if (isset($_POST["language"]) && $_POST["language"] != "") {
        $language = $_POST["language"];
    } else {
        $language = "en";
    }
    if (isset($_POST["realname"]) && $_POST["realname"] != "") {
        $realname = $_POST["realname"];
    } else {
        $realname = "";
    }
    if (isset($_POST["email"]) && $_POST["email"] != "") {
        $email = $_POST["email"];
    } else {
        $email = "";
    }
    
    // Write the DB variables in data.php
	if ($fp = @fopen(PMF_ROOT_DIR."/inc/data.php","w")) {
		@fputs($fp,"<?php\n\$DB[\"server\"] = '".$sql_server."';\n\$DB[\"user\"] = '".$sql_user."';\n\$DB[\"password\"] = '".$sql_passwort."';\n\$DB[\"db\"] = '".$sql_db."';\n\$DB[\"prefix\"] = '".$sqltblpre."';\n\$DB[\"type\"] = '".$sql_type."';\n?>");
		@fclose($fp);
    } else {
		print "<p class=\"error\"><strong>Error:</strong> Cannot write to data.php.</p>";
        HTMLFooter();
		die();
	}
    
    // check LDAP if available
    if (extension_loaded('ldap') && isset($_POST['ldap_enabled']) && $_POST['ldap_enabled'] == 'yes') {
        
        if ($fp = @fopen(PMF_ROOT_DIR."/inc/dataldap.php","w")) {
            @fputs($fp,"<?php\n\$PMF_LDAP[\"ldap_server\"] = '".$ldap_server."';\n\$PMF_LDAP[\"ldap_port\"] = '".$ldap_port."';\n\$PMF_LDAP[\"ldap_user\"] = '".$ldap_user."';\n\$PMF_LDAP[\"ldap_password\"] = '".$ldap_passwort."';\n\$PMF_LDAP[\"ldap_base\"] = '".$ldap_base."';\n;\n?>");
            @fclose($fp);
        } else {
            print "<p class=\"error\"><strong>Error:</strong> Cannot write to dataldap.php.</p>";
            HTMLFooter();
            die();
        }
        
    }
	
    // Create config.php and write the language variables in the file
    if (@file_exists(PMF_ROOT_DIR."/inc/config.php")) {
    	print "<p class=\"center\">A config file was found. Please backup ../inc/config.php and remove the file.</p>\n";
    	HTMLFooter();
    	die();
    }
    if (!@copy(PMF_ROOT_DIR."/inc/config.php.original", PMF_ROOT_DIR."/inc/config.php")) {
    	print "<p class=\"center\">Could not copy the file ../inc/config.php.original to ../inc/config.php.</p>\n";
    	HTMLFooter();
    	die();
    }
	if ($fp = @fopen(PMF_ROOT_DIR."/inc/config.php","r")) {
		$anz = 0;
		while($dat = fgets($fp,1024)) {
			$anz++;
			$inp[$anz] = $dat;
		}
		@fclose($fp);
		for ($h = 1; $h <= $anz; $h++) {
			if (str_replace("\$PMF_CONF[\"language\"] = \"en\";", "", $inp[$h]) != $inp[$h]) {
				$inp[$h] = "\$PMF_CONF[\"language\"] = \"".$language."\";\n";
			}
		}
		if ($fp = @fopen(PMF_ROOT_DIR."/inc/config.php","w")) {
			for ($h = 1; $h <= $anz; $h++) {
				fputs($fp,$inp[$h]);
			}
			@fclose($fp);
		} else {
			print "<p>Cannot write to config.php.</p></td>";
            HTMLFooter();
		    die();
		}
	} else {
		print "<p>Cannot read config.php.</p></td>";
        HTMLFooter();
		die();
	}
	
    // connect to the database using inc/data.php
    require_once(PMF_ROOT_DIR."/inc/data.php");
    require_once(PMF_ROOT_DIR."/inc/db.php");
	$db = db::db_select($sql_type);
	$db->connect($DB["server"], $DB["user"], $DB["password"], $DB["db"]);
	if (!$db) {
		print "<p class=\"error\"><strong>DB Error:</strong> ".$db->error()."</p>\n";
		HTMLFooter();
		die();
	}
	include_once($sql_type.".sql.php");
	print "<p class=\"center\"><strong>";
	while ($each_query = each($query)) {
		$result = $db->query($each_query[1]);
		print "|&nbsp;";
		if (!$result) {
			print "<!-- ".$each_query[1]." --><p class=\"error\"><strong>Error:</strong> Please install your version of phpMyFAQ once again or send us a <a href=\"http://bugs.phpmyfaq.de\" target=\"_blank\">bug report</a>.<br />DB error: ".$db->error()."</p>\n";
			uninstall();
		    HTMLFooter();
		    die();
		}
        usleep(250);
	}
	// add admin account and rights
	/*$query[] = "INSERT INTO ".$sqltblpre."faquser (id, name, pass, realname, email, rights) VALUES (1, 'admin', '".md5($password)."', '".$realname."', '".$email."', '1111111111111111111111111')";*/
	if (!defined('SQLPREFIX'))
        define('SQLPREFIX', $sqltblpre);
	require_once dirname(dirname(__FILE__)).'/inc/PMF_User/User.php';
	$admin = new PMF_User();
	$admin->createUser('admin', $password);
	$admin->setStatus('protected');
	$adminData = array(
	    'display_name' => $realname,
	    'email' => $email
    );
	$admin->setUserData($adminData);
	$adminID = $admin->getUserId();
	// add rights
	$rights = array(
        //1 => "adduser",
        array(
            'name' => 'adduser',
            'description' => 'Right to add user accounts',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //2 => "edituser",
        array(
            'name' => 'edituser',
            'description' => 'Right to edit user accounts',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //3 => "deluser",
        array(
            'name' => 'deluser',
            'description' => 'Right to delete user accounts',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //4 => "addbt",
        array(
            'name' => 'addbt',
            'description' => 'Right to add faq entries',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //5 => "editbt",
        array(
            'name' => 'editbt',
            'description' => 'Right to edit faq entries',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //6 => "delbt",
        array(
            'name' => 'delbt',
            'description' => 'Right to delete faq entries',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //7 => "viewlog",
        array(
            'name' => 'viewlog',
            'description' => 'Right to view logfiles',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //8 => "adminlog",
        array(
            'name' => 'adminlog',
            'description' => 'Right to view admin log',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //9 => "delcomment",
        array(
            'name' => 'delcomment',
            'description' => 'Right to delete comments',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //10 => "addnews",
        array(
            'name' => 'addnews',
            'description' => 'Right to add news',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //11 => "editnews",
        array(
            'name' => 'editnews',
            'description' => 'Right to edit news',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //12 => "delnews",
        array(
            'name' => 'delnews',
            'description' => 'Right to delete news',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //13 => "addcateg",
        array(
            'name' => 'addcateg',
            'description' => 'Right to add categories',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //14 => "editcateg",
        array(
            'name' => 'editcateg',
            'description' => 'Right to edit categories',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //15 => "delcateg",
        array(
            'name' => 'delcateg',
            'description' => 'Right to delete categories',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //16 => "passwd",
        array(
            'name' => 'passwd',
            'description' => 'Right to change passwords',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //17 => "editconfig",
        array(
            'name' => 'editconfig',
            'description' => 'Right to edit configuration',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //18 => "addatt",
        array(
            'name' => 'addatt',
            'description' => 'Right to add attachments',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //19 => "delatt",
        array(
            'name' => 'delatt',
            'description' => 'Right to delete attachments',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //20 => "backup",
        array(
            'name' => 'backup',
            'description' => 'Right to save backups',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //21 => "restore",
        array(
            'name' => 'restore',
            'description' => 'Right to load backups',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //22 => "delquestion",
        array(
            'name' => 'delquestion',
            'description' => 'Right to delete questions',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //23 => 'addglossary',
        array(
            'name' => 'addglossary',
            'description' => 'Right to add glossary entries',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //24 => 'editglossary',
        array(
            'name' => 'editglossary',
            'description' => 'Right to edit glossary entries',
            'for_users' => 1,
            'for_groups' => 1
        ),
        //25 => 'delglossary'
        array(
            'name' => 'delglossary',
            'description' => 'Right to delete glossary entries',
            'for_users' => 1,
            'for_groups' => 1
        ),
    );
    foreach ($rights as $right) {
        $rightID = $admin->perm->addRight($right);
        $admin->perm->grantUserRight($adminID, $rightID);
    }
	print "</strong></p>\n";
	
    print "<p class=\"center\">All tables were created and filled with the data.</p>\n";
    print "<p class=\"center\">Congratulation! Everything seems to be okay.</p>\n";
    print "<p class=\"center\">You can visit <a href=\"../index.php\">your version of phpMyFAQ</a> or</p>\n";
    print "<p class=\"center\">login into your <a href=\"../admin/index.php\">admin section</a>.</p>\n";
    
    if (@unlink(basename($_SERVER["PHP_SELF"]))) {
        print "<p class=\"center\">This file was deleted automatically.</p>\n";
    } else {
        print "<p class=\"center\">Please delete this file manually.</p>\n";
    }
    if (@unlink(dirname($_SERVER["PATH_TRANSLATED"])."/update.php")) {
        print "<p class=\"center\">The file 'update.php' was deleted automatically.</p>\n";
    } else {
        print "<p class=\"center\">Please delete the file 'update.php' manually.</p>\n";
    }
    HTMLFooter();
}
