<?php
//BindEvents Method @1-FDD3C92A
function BindEvents()
{
    global $issuesSearch;
    global $issues;
    $issuesSearch->CCSEvents["BeforeShow"] = "issuesSearch_BeforeShow";
    $issues->priority->CCSEvents["BeforeShow"] = "issues_priority_BeforeShow";
    $issues->CCSEvents["BeforeShowRow"] = "issues_BeforeShowRow";
    $issues->ds->CCSEvents["BeforeExecuteSelect"] = "issues_ds_BeforeExecuteSelect";
}
//End BindEvents Method

//issuesSearch_BeforeShow @4-24F5E60E
function issuesSearch_BeforeShow(& $sender)
{
    $issuesSearch_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issuesSearch; //Compatibility
//End issuesSearch_BeforeShow

//Custom Code @45-2A29BDB7
// -------------------------
	TranslateListbox($issuesSearch->s_priority_id);
	TranslateListbox($issuesSearch->s_status_id);
	TranslateListbox($issuesSearch->s_notstatus_id);
// -------------------------
//End Custom Code

//Close issuesSearch_BeforeShow @4-87B69EBF
    return $issuesSearch_BeforeShow;
}
//End Close issuesSearch_BeforeShow

//issues_priority_BeforeShow @51-3D92491D
function issues_priority_BeforeShow(& $sender)
{
    $issues_priority_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_priority_BeforeShow

//Translate @61-78A3CEA4
    $Component->SetValue(Translate($Component->GetValue()));
//End Translate

//Close issues_priority_BeforeShow @51-A1CB9B6C
    return $issues_priority_BeforeShow;
}
//End Close issues_priority_BeforeShow

//issues_BeforeShowRow @3-89B38DF3
function issues_BeforeShowRow(& $sender)
{
    $issues_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_BeforeShowRow

//Custom Code @46-2A29BDB7
// -------------------------
	$issues->status_id->SetValue(Translate($issues->status_id->GetValue()));
// -------------------------
//End Custom Code

//Close issues_BeforeShowRow @3-64A5DCC2
    return $issues_BeforeShowRow;
}
//End Close issues_BeforeShowRow

//issues_ds_BeforeExecuteSelect @3-80F25165
function issues_ds_BeforeExecuteSelect(& $sender)
{
    $issues_ds_BeforeExecuteSelect = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_ds_BeforeExecuteSelect

//Custom Code @63-2A29BDB7
// -------------------------
    // Write your own code here.
// -------------------------
//End Custom Code

//Close issues_ds_BeforeExecuteSelect @3-CD716B9B
    return $issues_ds_BeforeExecuteSelect;
}
//End Close issues_ds_BeforeExecuteSelect


?>
