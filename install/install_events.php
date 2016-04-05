<?php
//BindEvents Method @1-FA0FB5FC
function BindEvents()
{
    global $Panel1;
    global $sql_environment;
    global $CCSEvents;
    $Panel1->CCSEvents["BeforeShow"] = "Panel1_BeforeShow";
    $sql_environment->CCSEvents["OnValidate"] = "sql_environment_OnValidate";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//Panel1_BeforeShow @34-0CB436E9
function Panel1_BeforeShow(& $sender)
{
    $Panel1_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $Panel1; //Compatibility
//End Panel1_BeforeShow

//Custom Code @39-2A29BDB7
// -------------------------
	global $CCSLocales;
	$ErrorCount = 0;

	$StatusOK = "<font color=green>".$CCSLocales->GetText("inst_status_ok")."</font>";
	$StatusFailed  = "<font color=red>".$CCSLocales->GetText("inst_status_failed")."</font>";

	//Check the MySQLs 
	if (function_exists( 'mysql_connect' )) {
		$Container->MySQLCheck->SetValue($StatusOK);
	} else {
		$Container->MySQLCheck->SetValue($StatusFailed);
		$ErrorCount = 1;
	} 	

	//Check the rights to the common file 
	if (is_writable("../Common.php")) {
		if ($fp = @fopen("../Common.php", "a")) {
			$Container->WriteCheck->SetValue($StatusOK);
			fclose($fp);
		} else {
			$Container->WriteCheck->SetValue($StatusFailed);
			$ErrorCount = 2;
		} 	
	} else {
		$Container->WriteCheck->SetValue($StatusFailed);
		$ErrorCount = 2;
	}

	$folder = "uploads";
	$folder2 = "temp";
	if (!is_dir("../$folder")) {
		$Container->FolderCheck->SetValue("<font color=Red><b>" . $CCSLocales->GetText("inst_not_exist_folder", $folder) . "</b></font>");
		$ErrorCount = 3;
	} elseif (!is_dir_writable("../$folder")) {
		$Container->FolderCheck->SetValue("<font color=Red><b>" . $CCSLocales->GetText("inst_folder_not_writable", $folder) . "</b></font>");
		$ErrorCount = 3;
	} elseif (!is_dir("../$folder2")) {
		$Container->FolderCheck->SetValue("<font color=Red><b>" . $CCSLocales->GetText("inst_not_exist_folder", $folder2) . "</b></font>");
		$ErrorCount = 3;
	} elseif (!is_dir_writable("../$folder2")) {
		$Container->FolderCheck->SetValue("<font color=Red><b>" . $CCSLocales->GetText("inst_folder_not_writable", $folder2) . "</b></font>");
		$ErrorCount = 3;
	} else
		$Container->FolderCheck->SetValue($StatusOK);

	if ($ErrorCount > 1)
	{
		$Container->WriteResolution->Visible = true;
	}

	if ($ErrorCount == 0) {
		$Container->InstallLink->Parameters = CCAddParam("", "step", 2);
		$Container->InstallLink->Value = $CCSLocales->GetText("inst_start_config");
	} else {
		$Container->InstallLink->Value = $CCSLocales->GetText("inst_recheck");
	}

// -------------------------
//End Custom Code

//Close Panel1_BeforeShow @34-D21EBA68
    return $Panel1_BeforeShow;
}
//End Close Panel1_BeforeShow

//sql_environment_OnValidate @2-B57369EF
function sql_environment_OnValidate(& $sender)
{
    $sql_environment_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $sql_environment; //Compatibility
//End sql_environment_OnValidate

//Custom Code @10-FDAF69F5
// -------------------------
global $CCSLocales;
global $Redirect;

	$sql_par = array("sql_host"     => trim($sql_environment->sql_host->GetValue()),
					 "sql_db_name"  => trim($sql_environment->sql_db_name->GetValue()),
					 "sql_username" => trim($sql_environment->sql_username->GetValue()),
					 "sql_password" => trim($sql_environment->sql_password->GetValue()) );

	$user_info = array("user_login"        => trim($sql_environment->user_login->GetValue()),
					   "user_password"     => trim($sql_environment->user_password->GetValue()),
					   "user_password_rep" => trim($sql_environment->user_password_rep->GetValue()) );

	$root_info = array("root_login"    => trim($sql_environment->root_username->GetValue()),
					   "root_password" => trim($sql_environment->root_password->GetValue()) );

	$create_db = $sql_environment->DbSource->GetValue() == "create";
	$upgrade_data = $sql_environment->DbSource->GetValue() == "other";
	$sample_data = $sql_environment->SampleData->GetValue();
	$confirm_delete = $sql_environment->ConfirmDelete->GetValue();

	//Check form data
	$ErrorCount = $sql_environment->sql_host->Errors->Count() + 
				$sql_environment->sql_db_name->Errors->Count() + 
				$sql_environment->sql_username->Errors->Count() + 
				$sql_environment->user_login->Errors->Count() + 
				$sql_environment->user_password->Errors->Count(); 

	if ($ErrorCount) {
		return;
	}


	//Check admin login
	if ($create_db && strlen($root_info["root_login"]) == 0 )
	{
		$sql_environment->Errors->addError($CCSLocales->GetText("CCS_RequiredField", $Container->root_username->Caption));
		return;
	}

	//Compare two passwords (if not upgrading)
	if (!$upgrade_data && $user_info["user_password"] != $user_info["user_password_rep"])
	{
		$sql_environment->Errors->addError($CCSLocales->GetText("inst_error_diffpass"));
		return;
	}

	//Create the Database 
	if ($create_db)
	{
		if (!$db = @mysql_connect($sql_par["sql_host"],$root_info["root_login"],$root_info["root_password"])) {
			$sql_environment->Errors->addError($CCSLocales->GetText("inst_sql_connect_error").": ".mysql_error());
			return;
		}
		if (@mysql_select_db($sql_par["sql_db_name"])) {
			$sql_environment->Errors->addError($CCSLocales->GetText("inst_sql_db_exist_error").": ".mysql_error());
			mysql_close($db);
			return;
		}

		$SQL = "CREATE DATABASE " . $sql_par["sql_db_name"] . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci"; 
		$result = mysql_query($SQL);

		if (!$result) {
			$SQL = "CREATE DATABASE " . $sql_par["sql_db_name"]; 
			$result = mysql_query($SQL);
		}

		if (!$result) {
			$sql_environment->Errors->addError($CCSLocales->GetText("inst_sql_create_db_error").": ".mysql_error());
			mysql_close($db);
			return;
		}
		mysql_close($db);
	}

	//Connect to MySQL
	if (!$db = @mysql_connect($sql_par["sql_host"],$sql_par["sql_username"],$sql_par["sql_password"]))
	{
		$sql_environment->Errors->addError($CCSLocales->GetText("inst_sql_connect_error").": ".mysql_error());
		return;
	}

	//Select the Database
	if (!@mysql_select_db($sql_par["sql_db_name"]))
	{
		$sql_environment->Errors->addError($CCSLocales->GetText("inst_sql_database_error").": ".mysql_error());
		mysql_close($db);
		return;
	}

	// Detect database version
	$dbver = 0;
	if (table_exists("issues"))
	{
		if (table_exists("locales"))
			$dbver = 3;
		else
			$dbver = 2;			
	}


	//Open SQL script 
	if ($upgrade_data)
	{
		if ($dbver >= 2)
		{
			// Verify password
			$sql = "SELECT user_id FROM users WHERE login=" . CCToSQL($user_info["user_login"], ccsText) . " AND pass=" . CCToSQL($user_info["user_password"], ccsText);
			$rs = @mysql_query($sql);
			if ($rs && mysql_fetch_array($rs))
			{
				if ($dbver == 2)
					execute_sql_script("IssueManager_MySQL_upgrade.sql");
			}
			else
			{
				$sql_environment->Errors->addError($CCSLocales->GetText("inst_invalid_admin_password_error"));
			}
		}
		else
		{
			$sql_environment->Errors->addError($CCSLocales->GetText("inst_not_2x_database_error"));
		}
	}
	elseif ($dbver == 0 || ($dbver == 3 && $confirm_delete))
	{
		execute_sql_script("IssueManager_MySQL.sql");

		if ($sample_data)
			execute_sql_script("IssueManager_MySQL_sample.sql");
	}
	else
	{
		$sql_environment->Errors->addError($CCSLocales->GetText("inst_data_delete_confirm"));
		$sql_environment->ConfirmDelete->Visible = true;
	}

	if ($sql_environment->Errors->Count())
	{
		return;
	}

	//Update the Admin login and password
	$SQL = "SELECT user_id FROM users WHERE login = '" . str_replace("'", "''", $user_info["user_login"]) . "'";
	$result = mysql_query($SQL);
	if (mysql_num_rows($result)) {
		$SQL = "UPDATE users SET ".
			  " user_name = 'Administrator', " .
			  " pass = '" . str_replace("'", "''", $user_info["user_password"]) . "',".
			  " security_level = 3 ," .
			  " allow_upload = 1 " .
			  " WHERE login = '" . str_replace("'", "''", $user_info["user_login"]) . "'";
	} else {
		$SQL = "INSERT INTO users ( ".
			  "user_name,".
			  "login, ".
			  "pass, ".
			  "security_level ".
			  ") ".
			   "VALUES (" .
			   "'Administrator',".
			   "'" . str_replace("'", "''", $user_info["user_login"]) . "'," .
			   "'" . str_replace("'", "''", $user_info["user_password"]) . "',".
			   "3 ".
			   ")";
	}
	$result = mysql_query($SQL);
	if (mysql_errno())
	{
		$sql_environment->Errors->addError(mysql_error());
	}
	else
	{
		$fcontents = join("", file("../Common.php"));
		if (!$fp = fopen("../Common.php", "w"))
			$sql_environment->Errors->addError($CCSLocales->GetText("inst_file_open_error"));
		else {
			$RegExpMask = "\"Database\"[\t \n]*=>[\t \n]*\"[a-zA-Z0-9_]*\"";
			$NewValue = '"Database" => "' . $sql_par["sql_db_name"] . '"';
			$fcontents = ereg_replace($RegExpMask, $NewValue, $fcontents);

			$RegExpMask = "\"Host\"[\t \n]*=>[\t \n]*\"[a-zA-Z0-9_]*\"";
			$NewValue = '"Host" => "' . $sql_par["sql_host"] . '"';
			$fcontents = ereg_replace($RegExpMask, $NewValue, $fcontents);

			$RegExpMask = "\"User\"[\t \n]*=>[\t \n]*\"[a-zA-Z0-9_]*\"";
			$NewValue = '"User" => "' . $sql_par["sql_username"] . '"';
			$fcontents = ereg_replace($RegExpMask, $NewValue, $fcontents);

			$RegExpMask = "\"Password\"[\t \n]*=>[\t \n]*\"[a-zA-Z0-9_]*\"";
			$NewValue = '"Password" => "' . $sql_par["sql_password"] . '"';
			$fcontents = ereg_replace($RegExpMask, $NewValue, $fcontents);

			$RegExpMask = "[\$]ApplicationIsInstalled[\t \n]*=[\t \n]*false";
			$NewValue = "\$ApplicationIsInstalled = true";
			$fcontents = ereg_replace($RegExpMask, $NewValue, $fcontents);

			fwrite($fp,$fcontents);
			fclose($fp);
			$Redirect = "install.php?step=3";
		}	
	}

// -------------------------
//End Custom Code

//Close sql_environment_OnValidate @2-A07C6C0B
    return $sql_environment_OnValidate;
}
//End Close sql_environment_OnValidate

//Page_AfterInitialize @1-27F6875A
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $install; //Compatibility
//End Page_AfterInitialize

//Custom Code @23-D1696264
// -------------------------
global $sql_environment;
global $ApplicationIsInstalled;

	$Step = CCGetFromGet("step",1);

	if ($ApplicationIsInstalled && $Step != 3) {
		header("Location: ../Default.php");
		exit;
	} else {
		//Set the Default variable
		if (!$sql_environment->FormSubmitted) {
			$sql_environment->sql_host->SetValue("localhost");
			$sql_environment->sql_db_name->SetValue("issuemanager");
			$sql_environment->sql_username->SetValue("root");

			$sql_environment->user_login->SetValue("admin");
		}

		$Container->Panel1->Visible = ($Step == 1);
		$Container->Panel2->Visible = ($Step == 2);
		$Container->Panel3->Visible = ($Step == 3);
	}
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize

function is_dir_writable($dir)
{
	$fp = @fopen("$dir/__temp","w");
	if ($fp)
	{
		@fclose($fp);
		@unlink("$dir/__temp");
		return !file_exists("$dir/__temp");
	}
	else
		return false;
}

function execute_sql_script($fname)
{
	global $sql_environment,$CCSLocales;

	if ($fp = @fopen($fname, "r"))
	{
		$line = 0;
		while (!feof($fp)) {
			$line ++;
			$str="";
			while (!(strpos($str,";") || feof($fp))) 
				$str .= fgets($fp,4096);
			$str = trim($str);
			$str = substr($str, 0, strlen($str)-1);
			if (strlen(trim($str))) {
				mysql_query($str);
				if (mysql_errno())
					$sql_environment->Errors->addError("{$fname} [{$line}]:".mysql_error());
			}
		}
		fclose($fp);
	}
	else
		$sql_environment->Errors->addError($CCSLocales->GetText("inst_sql_file_open_error", $fname));

}

function table_exists($table)
{
	@mysql_query("SELECT * FROM $table");
	return !mysql_errno();
}
?>