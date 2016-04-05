<?php
//BindEvents Method @1-9C6DB630
function BindEvents()
{
    global $issue;
    global $files;
    global $issues;
    global $responses1;
    global $CCSEvents;
    $issue->CCSEvents["BeforeShow"] = "issue_BeforeShow";
    $issue->CCSEvents["BeforeShowRow"] = "issue_BeforeShowRow";
    $files->file_name->CCSEvents["BeforeShow"] = "files_file_name_BeforeShow";
    $files->CCSEvents["BeforeShow"] = "files_BeforeShow";
    $issues->Insert->CCSEvents["BeforeShow"] = "issues_Insert_BeforeShow";
    $issues->CCSEvents["BeforeShow"] = "issues_BeforeShow";
    $issues->CCSEvents["AfterInsert"] = "issues_AfterInsert";
    $issues->CCSEvents["BeforeInsert"] = "issues_BeforeInsert";
    $issues->CCSEvents["OnValidate"] = "issues_OnValidate";
    $issues->ds->CCSEvents["BeforeBuildUpdate"] = "issues_ds_BeforeBuildUpdate";
    $responses1->CCSEvents["BeforeShow"] = "responses1_BeforeShow";
    $responses1->CCSEvents["BeforeShowRow"] = "responses1_BeforeShowRow";
    $CCSEvents["BeforeShow"] = "Page_BeforeShow";
}
//End BindEvents Method

//issue_BeforeShow @2-77A6112D
function issue_BeforeShow(& $sender)
{
    $issue_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issue; //Compatibility
//End issue_BeforeShow

//Custom Code @294-2A29BDB7
// -------------------------
	if (!$issue->ds->num_rows())
	{
		header("Location: Default.php");
		exit;
	}

	TranslateBoolean($issue);
// -------------------------
//End Custom Code

//Close issue_BeforeShow @2-D292647A
    return $issue_BeforeShow;
}
//End Close issue_BeforeShow

//issue_BeforeShowRow @2-A4F63C91
function issue_BeforeShowRow(& $sender)
{
    $issue_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issue; //Compatibility
//End issue_BeforeShowRow

//Custom Code @295-2A29BDB7
// -------------------------
    $issue->priority_id->SetValue(Translate($issue->priority_id->GetValue()));
    $issue->status_id->SetValue(Translate($issue->status_id->GetValue()));
// -------------------------
//End Custom Code

//Close issue_BeforeShowRow @2-943D7EC9
    return $issue_BeforeShowRow;
}
//End Close issue_BeforeShowRow

//files_file_name_BeforeShow @235-EEE14E64
function files_file_name_BeforeShow(& $sender)
{
    $files_file_name_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $files; //Compatibility
//End files_file_name_BeforeShow

//Get Original Filename @236-10353880
    $control_value = $Component->GetValue();
    $original_filename = CCGetOriginalFileName($control_value);
    $Component->SetValue($original_filename);
//End Get Original Filename

//Custom Code @243-2A29BDB7
// -------------------------
    $files->file_name->SetLink(GetSetting("file_path") . "/" . $files->file_name->GetLink());
// -------------------------
//End Custom Code

//Close files_file_name_BeforeShow @235-C0AA7F0F
    return $files_file_name_BeforeShow;
}
//End Close files_file_name_BeforeShow

//files_BeforeShow @231-88AECC2D
function files_BeforeShow(& $sender)
{
    $files_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $files; //Compatibility
//End files_BeforeShow

//Custom Code @242-2A29BDB7
// -------------------------
	$files->Visible = $files->ds->num_rows();
// -------------------------
//End Custom Code

//Close files_BeforeShow @231-B34F3707
    return $files_BeforeShow;
}
//End Close files_BeforeShow

//issues_Insert_BeforeShow @43-7837AE8C
function issues_Insert_BeforeShow(& $sender)
{
    $issues_Insert_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_Insert_BeforeShow

//Hide-Show Component @275-F4F52CA6
    $Parameter1 = 1;
    $Parameter2 = 1;
    if (0 == CCCompareValues($Parameter1, $Parameter2, ccsText))
        $Component->Visible = true;
//End Hide-Show Component

//Close issues_Insert_BeforeShow @43-BD497AF9
    return $issues_Insert_BeforeShow;
}
//End Close issues_Insert_BeforeShow

//issues_BeforeShow @34-67B94C6A
function issues_BeforeShow(& $sender)
{
    $issues_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_BeforeShow

//Custom Code @223-2A29BDB7
// -------------------------
 	$issues->UploadControls->Visible = GetSetting("upload_enabled") && CCDLookup("allow_upload","users","user_id=".CCGetSession("UserID"),new clsDBIM());

	TranslateListbox($issues->priority_id);
	TranslateListbox($issues->status_id);
	SetupUpload($issues->attachment);
// -------------------------
//End Custom Code

//Close issues_BeforeShow @34-27817DF2
    return $issues_BeforeShow;
}
//End Close issues_BeforeShow

//issues_AfterInsert @34-E406B0AE
function issues_AfterInsert(& $sender)
{
    $issues_AfterInsert = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_AfterInsert

//Custom Code @228-2A29BDB7
// -------------------------
		$db = new clsDBIM();
 		$issue_id = $db->ToSQL(CCGetParam("issue_id",0), ccsInteger);
		$current = CCGetUserID();
		$user_id = CCDLookup("user_id","issues","issue_id=$issue_id",$db);
		$assigned = CCDLookup("assigned_to", "issues", "issue_id=$issue_id",$db);
		$assigned_new = $issues->assigned_to->GetValue();

		if ($issues->attachment->GetValue())
		{
			$sql = "INSERT INTO files(issue_id, uploaded_by, file_name, date_uploaded) VALUES(".$db->ToSQL($issue_id,ccsInteger).", ".$db->ToSQL($current,ccsInteger).", ".$db->ToSQL($issues->attachment->GetValue(),ccsText).", ".$db->ToSQL(CCGetDateArray(), ccsDate).")";
    		$db->query($sql);
		}

		if ($user_id != $assigned_new && $user_id != $assigned && $user_id != $current && CCDLookup("notify_original","users","user_id=$user_id",$db))
			SendNotification("notify_change", $user_id, $issue_id);

		if ($assigned != $current && CCDLookup("notify_reassignment","users","user_id=$assigned",$db))
			SendNotification("notify_change", $assigned, $issue_id);

		if ($assigned != $assigned_new && $assigned_new != $current && CCDLookup("notify_new","users","user_id=$assigned_new",$db))
			SendNotification("notify_new", $assigned_new, $issue_id);

		$issues->UpdateRow();
// -------------------------
//End Custom Code

//Close issues_AfterInsert @34-2A392882
    return $issues_AfterInsert;
}
//End Close issues_AfterInsert

//issues_BeforeInsert @34-C88056BA
function issues_BeforeInsert(& $sender)
{
    $issues_BeforeInsert = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_BeforeInsert

//Custom Code @292-2A29BDB7
// -------------------------
    // Write your own code here.
// -------------------------
//End Custom Code

//Close issues_BeforeInsert @34-52B36B9A
    return $issues_BeforeInsert;
}
//End Close issues_BeforeInsert

//issues_OnValidate @34-FE77246F
function issues_OnValidate(& $sender)
{
    $issues_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_OnValidate

//Custom Code @293-2A29BDB7
// -------------------------
	global $Redirect;
	if (!$issues->Insert->Pressed) $Redirect = "";
// -------------------------
//End Custom Code

//Close issues_OnValidate @34-187A197B
    return $issues_OnValidate;
}
//End Close issues_OnValidate

//issues_ds_BeforeBuildUpdate @34-CAE2B558
function issues_ds_BeforeBuildUpdate(& $sender)
{
    $issues_ds_BeforeBuildUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_ds_BeforeBuildUpdate

//Custom Code @297-2A29BDB7
// -------------------------
 	if ($issues->ds->status_id->GetValue() == 3)
	{
		$issues->ds->cp["date_resolved"] = new clsSQLParameter("expr286", ccsDate, array("GeneralDate"), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"), $issues->ds->date_now->GetValue(), "", false, $issues->ds->ErrorBlock);
	}
// -------------------------
//End Custom Code

//Close issues_ds_BeforeBuildUpdate @34-651C45C3
    return $issues_ds_BeforeBuildUpdate;
}
//End Close issues_ds_BeforeBuildUpdate

//responses1_BeforeShow @25-6941A6AB
function responses1_BeforeShow(& $sender)
{
    $responses1_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $responses1; //Compatibility
//End responses1_BeforeShow

//Custom Code @226-2A29BDB7
// -------------------------
	$responses1->Visible = $responses1->ds->num_rows();
// -------------------------
//End Custom Code

//Close responses1_BeforeShow @25-06421960
    return $responses1_BeforeShow;
}
//End Close responses1_BeforeShow

//responses1_BeforeShowRow @25-D2F3FB60
function responses1_BeforeShowRow(& $sender)
{
    $responses1_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $responses1; //Compatibility
//End responses1_BeforeShowRow

//Custom Code @296-2A29BDB7
// -------------------------
    $responses1->priority_id->SetValue(Translate($responses1->priority_id->GetValue()));
    $responses1->status_id->SetValue(Translate($responses1->status_id->GetValue()));
// -------------------------
//End Custom Code

//Close responses1_BeforeShowRow @25-99CE8DD4
    return $responses1_BeforeShowRow;
}
//End Close responses1_BeforeShowRow

//Page_BeforeShow @1-154CA9D9
function Page_BeforeShow(& $sender)
{
    $Page_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $IssueChange; //Compatibility
//End Page_BeforeShow

//Custom Code @287-2A29BDB7
// -------------------------
// -------------------------
//End Custom Code

//Close Page_BeforeShow @1-4BC230CD
    return $Page_BeforeShow;
}
//End Close Page_BeforeShow
?>
