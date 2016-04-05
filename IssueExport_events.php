<?php
//BindEvents Method @1-5960BF3C
function BindEvents()
{
    global $issues;
    global $CCSEvents;
    $issues->priority_id->CCSEvents["BeforeShow"] = "issues_priority_id_BeforeShow";
    $issues->assigned_to->CCSEvents["BeforeShow"] = "issues_assigned_to_BeforeShow";
    $issues->CCSEvents["BeforeShow"] = "issues_BeforeShow";
    $issues->CCSEvents["BeforeShowRow"] = "issues_BeforeShowRow";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//issues_priority_id_BeforeShow @7-A0FA63E5
function issues_priority_id_BeforeShow(& $sender)
{
    $issues_priority_id_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_priority_id_BeforeShow

//Custom Code @123-2A29BDB7
// -------------------------
 $issues->priority_id->SetValue("<font color='" . $issues->color->GetValue() . "'>" . Translate($issues->priority_id->GetValue()) . "</font>");
// -------------------------
//End Custom Code

//Close issues_priority_id_BeforeShow @7-5A113DBA
    return $issues_priority_id_BeforeShow;
}
//End Close issues_priority_id_BeforeShow

//issues_assigned_to_BeforeShow @11-60346E17
function issues_assigned_to_BeforeShow(& $sender)
{
    $issues_assigned_to_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_assigned_to_BeforeShow

//Custom Code @124-2A29BDB7
// -------------------------
	if ($issues->assigned_id->GetValue() == CCGetSession("UserID"))
	{
		$issues->assigned_to->SetValue("<font color='red'>" . $issues->assigned_to->GetValue() . "</font>");
	}
// -------------------------
//End Custom Code

//Close issues_assigned_to_BeforeShow @11-45CD9B3A
    return $issues_assigned_to_BeforeShow;
}
//End Close issues_assigned_to_BeforeShow

//issues_BeforeShow @2-67B94C6A
function issues_BeforeShow(& $sender)
{
    $issues_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_BeforeShow

//Custom Code @120-2A29BDB7
// -------------------------
	header("Content-Type: application/x-ms-download");
 	header("Content-Disposition: attachment; filename=Issues.xls");

	global $DBIM,$CCSLocales;

	$param_status = CCGetParam("s_status_id");
	$param_notstatus = CCGetParam("s_notstatus_id");
	if ($param_notstatus == "0") $param_notstatus = "";

	if ($param_notstatus)
	{
	  $issue_look = Translate(CCDLookup("status","statuses","status_id=" . $DBIM->ToSQL($param_notstatus,ccsInteger), $DBIM));
	  $issue_view = $CCSLocales->GetText("im_not_issues_title",$issue_look);
	}
	
	if ($param_status)
	{
	  $issue_look = Translate(CCDLookup("status","statuses","status_id=" . $DBIM->ToSQL($param_status,ccsInteger), $DBIM));
	  $issue_view = $CCSLocales->GetText("im_issues_title",$issue_look);
	}
	
	if (!$param_status && !$param_notstatus)
	{
	  $issue_view = $CCSLocales->GetText("im_all_issues");
	}

	$issues->title->SetValue($issue_view);

	TranslateBoolean($issues);
// -------------------------
//End Custom Code

//Close issues_BeforeShow @2-27817DF2
    return $issues_BeforeShow;
}
//End Close issues_BeforeShow

//issues_BeforeShowRow @2-89B38DF3
function issues_BeforeShowRow(& $sender)
{
    $issues_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_BeforeShowRow

//Custom Code @185-2A29BDB7
// -------------------------
    $issues->status_id->SetValue(Translate($issues->status_id->GetValue()));
// -------------------------
//End Custom Code

//Close issues_BeforeShowRow @2-64A5DCC2
    return $issues_BeforeShowRow;
}
//End Close issues_BeforeShowRow

//Page_AfterInitialize @1-05BBA8D2
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $IssueExport; //Compatibility
//End Page_AfterInitialize

//Custom Code @184-2A29BDB7
// -------------------------
    // Write your own code here.
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize


?>
