<?php
//BindEvents Method @1-4316E920
function BindEvents()
{
    global $issuesSearch;
    global $Link2;
    global $summary;
    global $issues;
    global $CCSEvents;
    $issuesSearch->CCSEvents["BeforeShow"] = "issuesSearch_BeforeShow";
    $Link2->CCSEvents["BeforeShow"] = "Link2_BeforeShow";
    $summary->Label1->CCSEvents["BeforeShow"] = "summary_Label1_BeforeShow";
    $summary->CCSEvents["BeforeShowRow"] = "summary_BeforeShowRow";
    $issues->priority_id->CCSEvents["BeforeShow"] = "issues_priority_id_BeforeShow";
    $issues->assigned_to->CCSEvents["BeforeShow"] = "issues_assigned_to_BeforeShow";
    $issues->priority_id1->CCSEvents["BeforeShow"] = "issues_priority_id1_BeforeShow";
    $issues->assigned_to1->CCSEvents["BeforeShow"] = "issues_assigned_to1_BeforeShow";
    $issues->CCSEvents["BeforeShow"] = "issues_BeforeShow";
    $issues->CCSEvents["BeforeShowRow"] = "issues_BeforeShowRow";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//issuesSearch_BeforeShow @3-24F5E60E
function issuesSearch_BeforeShow(& $sender)
{
    $issuesSearch_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issuesSearch; //Compatibility
//End issuesSearch_BeforeShow

//Custom Code @186-2A29BDB7
// -------------------------
	TranslateListbox($issuesSearch->s_priority_id);
	TranslateListbox($issuesSearch->s_status_id);
	TranslateListbox($issuesSearch->s_notstatus_id);
// -------------------------
//End Custom Code

//Close issuesSearch_BeforeShow @3-87B69EBF
    return $issuesSearch_BeforeShow;
}
//End Close issuesSearch_BeforeShow

//Link2_BeforeShow @36-DA54A9F0
function Link2_BeforeShow(& $sender)
{
    $Link2_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $Link2; //Compatibility
//End Link2_BeforeShow

//Custom Code @191-2A29BDB7
// -------------------------
    // Write your own code here.
// -------------------------
//End Custom Code

//Close Link2_BeforeShow @36-4AE96129
    return $Link2_BeforeShow;
}
//End Close Link2_BeforeShow

//summary_Label1_BeforeShow @41-2A8333C7
function summary_Label1_BeforeShow(& $sender)
{
    $summary_Label1_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $summary; //Compatibility
//End summary_Label1_BeforeShow

//Custom Code @189-2A29BDB7
// -------------------------
    // Write your own code here.
// -------------------------
//End Custom Code

//Close summary_Label1_BeforeShow @41-E49D3791
    return $summary_Label1_BeforeShow;
}
//End Close summary_Label1_BeforeShow

//summary_BeforeShowRow @40-4D0011D9
function summary_BeforeShowRow(& $sender)
{
    $summary_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $summary; //Compatibility
//End summary_BeforeShowRow

//Custom Code @187-2A29BDB7
// -------------------------
    $summary->Label1->SetValue(Translate($summary->Label1->GetValue()));
// -------------------------
//End Custom Code

//Close summary_BeforeShowRow @40-BC0BD67A
    return $summary_BeforeShowRow;
}
//End Close summary_BeforeShowRow

//issues_priority_id_BeforeShow @26-A0FA63E5
function issues_priority_id_BeforeShow(& $sender)
{
    $issues_priority_id_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_priority_id_BeforeShow

//Custom Code @146-2A29BDB7
// -------------------------
 $issues->priority_id->SetValue("<font color='" . $issues->color->GetValue() . "'>" . $issues->priority_id->GetValue() . "</font>");
// -------------------------
//End Custom Code

//Close issues_priority_id_BeforeShow @26-5A113DBA
    return $issues_priority_id_BeforeShow;
}
//End Close issues_priority_id_BeforeShow

//issues_assigned_to_BeforeShow @27-60346E17
function issues_assigned_to_BeforeShow(& $sender)
{
    $issues_assigned_to_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_assigned_to_BeforeShow

//Custom Code @181-2A29BDB7
// -------------------------
	if ($issues->assigned_id->GetValue() == CCGetSession("UserID"))
	{
		$issues->assigned_to->SetValue("<font color='red'>" . $issues->assigned_to->GetValue() . "</font>");
	}
// -------------------------
//End Custom Code

//Close issues_assigned_to_BeforeShow @27-45CD9B3A
    return $issues_assigned_to_BeforeShow;
}
//End Close issues_assigned_to_BeforeShow

//issues_priority_id1_BeforeShow @137-F4961692
function issues_priority_id1_BeforeShow(& $sender)
{
    $issues_priority_id1_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_priority_id1_BeforeShow

//Custom Code @147-2A29BDB7
// -------------------------
 $issues->priority_id1->SetValue("<font color='" . $issues->color1->GetValue() . "'>" . $issues->priority_id1->GetValue() . "</font>");
// -------------------------
//End Custom Code

//Close issues_priority_id1_BeforeShow @137-1CBA0F47
    return $issues_priority_id1_BeforeShow;
}
//End Close issues_priority_id1_BeforeShow

//issues_assigned_to1_BeforeShow @138-DD2D36F5
function issues_assigned_to1_BeforeShow(& $sender)
{
    $issues_assigned_to1_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_assigned_to1_BeforeShow

//Custom Code @182-2A29BDB7
// -------------------------
	if ($issues->assigned_id1->GetValue() == CCGetSession("UserID"))
	{
		$issues->assigned_to1->SetValue("<font color='red'>" . $issues->assigned_to1->GetValue() . "</font>");
	}
// -------------------------
//End Custom Code

//Close issues_assigned_to1_BeforeShow @138-F11D50C1
    return $issues_assigned_to1_BeforeShow;
}
//End Close issues_assigned_to1_BeforeShow

//issues_BeforeShow @2-67B94C6A
function issues_BeforeShow(& $sender)
{
    $issues_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_BeforeShow

//Custom Code @145-2A29BDB7
// -------------------------
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

//Custom Code @188-2A29BDB7
// -------------------------
    if ($issues->IsAltRow)
	{
		$issues->status_id1->SetValue(Translate($issues->status_id1->GetValue()));
		$issues->priority_id1->SetValue(Translate($issues->priority_id1->GetValue()));
	}
	else
	{
		$issues->status_id->SetValue(Translate($issues->status_id->GetValue()));
		$issues->priority_id->SetValue(Translate($issues->priority_id->GetValue()));
	}
// -------------------------
//End Custom Code

//Close issues_BeforeShowRow @2-64A5DCC2
    return $issues_BeforeShowRow;
}
//End Close issues_BeforeShowRow

//Page_AfterInitialize @1-385E061B
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $Default; //Compatibility
//End Page_AfterInitialize

//Custom Code @185-2A29BDB7
// -------------------------
	if (!sizeof($_GET))
	{
		header("Location: Default.php?s_notstatus_id=3");
		exit;
	}
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize


?>
