<?php
//BindEvents Method @1-86CC9163
function BindEvents()
{
    global $issues;
    global $CCSEvents;
    $issues->user_name->CCSEvents["BeforeShow"] = "issues_user_name_BeforeShow";
    $issues->Insert->CCSEvents["BeforeShow"] = "issues_Insert_BeforeShow";
    $issues->CCSEvents["AfterInsert"] = "issues_AfterInsert";
    $issues->CCSEvents["BeforeShow"] = "issues_BeforeShow";
    $issues->CCSEvents["BeforeInsert"] = "issues_BeforeInsert";
    $CCSEvents["BeforeShow"] = "Page_BeforeShow";
}
//End BindEvents Method

//issues_user_name_BeforeShow @16-3F24D788
function issues_user_name_BeforeShow(& $sender)
{
    $issues_user_name_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_user_name_BeforeShow

//Retrieve Value for Control @51-26E7449F
    $Component->SetValue(CCGetSession("UserLogin"));
//End Retrieve Value for Control

//Close issues_user_name_BeforeShow @16-B2451632
    return $issues_user_name_BeforeShow;
}
//End Close issues_user_name_BeforeShow

//DEL  // -------------------------
//DEL  	if (!$issues->FormSubmitted)
//DEL  		DropFiles();
//DEL  	
//DEL  	$issues->files->SetValue(PopulateFiles($label));
//DEL  	$issues->AddFiles->SetValue($label);
//DEL  // -------------------------

//issues_Insert_BeforeShow @5-7837AE8C
function issues_Insert_BeforeShow(& $sender)
{
    $issues_Insert_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_Insert_BeforeShow

//Hide-Show Component @68-F4F52CA6
    $Parameter1 = 1;
    $Parameter2 = 1;
    if (0 == CCCompareValues($Parameter1, $Parameter2, ccsText))
        $Component->Visible = true;
//End Hide-Show Component

//Close issues_Insert_BeforeShow @5-BD497AF9
    return $issues_Insert_BeforeShow;
}
//End Close issues_Insert_BeforeShow

//issues_AfterInsert @4-E406B0AE
function issues_AfterInsert(& $sender)
{
    $issues_AfterInsert = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_AfterInsert

//Custom Code @37-2A29BDB7
// -------------------------
	$db = new clsDBIM();

	$current = CCGetUserID();
 	$issue_id = CCDLookUp("MAX(issue_id)","issues","user_id=$current",$db);
	$assigned = CCDLookup("assigned_to", "issues", "issue_id=$issue_id",$db);

	if ($issues->attachment->GetValue())
	{
		$sql = "INSERT INTO files(issue_id, uploaded_by, file_name, date_uploaded) VALUES(".$db->ToSQL($issue_id,ccsInteger).", ".$db->ToSQL($current,ccsInteger).", ".$db->ToSQL($issues->attachment->GetValue(),ccsText).", ".$db->ToSQL(CCGetDateArray(), ccsDate).")";
		$db->query($sql);
	}

	if ($assigned != $current && CCDLookup("notify_new","users","user_id=$assigned",$db))
		SendNotification("notify_new", $assigned, $issue_id);
// -------------------------
//End Custom Code

//Close issues_AfterInsert @4-2A392882
    return $issues_AfterInsert;
}
//End Close issues_AfterInsert

//issues_BeforeShow @4-67B94C6A
function issues_BeforeShow(& $sender)
{
    $issues_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_BeforeShow

//Custom Code @66-2A29BDB7
// -------------------------
 	$issues->UploadControls->Visible = GetSetting("upload_enabled") && CCDLookup("allow_upload","users","user_id=".CCGetSession("UserID"),new clsDBIM());

	TranslateListbox($issues->priority_id);
	TranslateListbox($issues->status_id);
	SetupUpload($issues->attachment);
// -------------------------
//End Custom Code

//Close issues_BeforeShow @4-27817DF2
    return $issues_BeforeShow;
}
//End Close issues_BeforeShow

//issues_BeforeInsert @4-C88056BA
function issues_BeforeInsert(& $sender)
{
    $issues_BeforeInsert = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_BeforeInsert

//Custom Code @67-2A29BDB7
// -------------------------
	$issues->InsertAllowed = $issues->Insert->Pressed;
// -------------------------
//End Custom Code

//Close issues_BeforeInsert @4-52B36B9A
    return $issues_BeforeInsert;
}
//End Close issues_BeforeInsert

//Page_BeforeShow @1-0FB5F371
function Page_BeforeShow(& $sender)
{
    $Page_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $IssueNew; //Compatibility
//End Page_BeforeShow

//Custom Code @65-2A29BDB7
// -------------------------
    // Write your own code here.
// -------------------------
//End Custom Code

//Close Page_BeforeShow @1-4BC230CD
    return $Page_BeforeShow;
}
//End Close Page_BeforeShow
?>
