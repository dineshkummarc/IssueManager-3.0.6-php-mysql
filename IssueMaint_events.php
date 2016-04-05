<?php
//BindEvents Method @1-4E2656BF
function BindEvents()
{
    global $issues;
    global $files;
    global $responses;
    $issues->date_format->CCSEvents["BeforeShow"] = "issues_date_format_BeforeShow";
    $issues->CCSEvents["BeforeDelete"] = "issues_BeforeDelete";
    $issues->CCSEvents["BeforeShow"] = "issues_BeforeShow";
    $files->file_name->CCSEvents["BeforeShow"] = "files_file_name_BeforeShow";
    $files->CCSEvents["BeforeShow"] = "files_BeforeShow";
    $responses->CCSEvents["BeforeShow"] = "responses_BeforeShow";
    $responses->CCSEvents["BeforeShowRow"] = "responses_BeforeShowRow";
}
//End BindEvents Method

//issues_date_format_BeforeShow @103-44B8125A
function issues_date_format_BeforeShow(& $sender)
{
    $issues_date_format_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_date_format_BeforeShow

//Print General Date Format @104-BB1D2E46
    $Component->SetValue(GetGeneralDateFormat());
//End Print General Date Format

//Close issues_date_format_BeforeShow @103-F7AF9B4C
    return $issues_date_format_BeforeShow;
}
//End Close issues_date_format_BeforeShow

//issues_BeforeDelete @2-DD3D5505
function issues_BeforeDelete(& $sender)
{
    $issues_BeforeDelete = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_BeforeDelete

//Custom Code @81-2A29BDB7
// -------------------------
		$db = new clsDBIM();
	  	$sql = "DELETE FROM responses WHERE issue_id=".$db->ToSQL(CCGetFromGet("issue_id",0),ccsInteger);
		$db->query($sql);

	  	$sql = "SELECT * FROM files WHERE issue_id=".$db->ToSQL(CCGetFromGet("issue_id",0),ccsInteger);
		$db->query($sql);
		$path = GetSetting("file_path");
		while ($db->next_record())
		{
			@unlink("$path/".$db->f("file_name"));
		}

	  	$sql = "DELETE FROM files WHERE issue_id=".$db->ToSQL(CCGetFromGet("issue_id",0),ccsInteger);
		$db->query($sql);

		$db->close();
// -------------------------
//End Custom Code

//Close issues_BeforeDelete @2-01BE0C64
    return $issues_BeforeDelete;
}
//End Close issues_BeforeDelete

//issues_BeforeShow @2-67B94C6A
function issues_BeforeShow(& $sender)
{
    $issues_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $issues; //Compatibility
//End issues_BeforeShow

//Custom Code @89-2A29BDB7
// -------------------------
	TranslateListbox($issues->priority_id);
	TranslateListbox($issues->status_id);
// -------------------------
//End Custom Code

//Close issues_BeforeShow @2-27817DF2
    return $issues_BeforeShow;
}
//End Close issues_BeforeShow

//files_file_name_BeforeShow @34-EEE14E64
function files_file_name_BeforeShow(& $sender)
{
    $files_file_name_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $files; //Compatibility
//End files_file_name_BeforeShow

//Get Original Filename @85-10353880
    $control_value = $Component->GetValue();
    $original_filename = CCGetOriginalFileName($control_value);
    $Component->SetValue($original_filename);
//End Get Original Filename

//Close files_file_name_BeforeShow @34-C0AA7F0F
    return $files_file_name_BeforeShow;
}
//End Close files_file_name_BeforeShow

//files_BeforeShow @30-88AECC2D
function files_BeforeShow(& $sender)
{
    $files_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $files; //Compatibility
//End files_BeforeShow

//Custom Code @90-2A29BDB7
// -------------------------
    $files->Visible = $files->ds->num_rows();
// -------------------------
//End Custom Code

//Close files_BeforeShow @30-B34F3707
    return $files_BeforeShow;
}
//End Close files_BeforeShow

//responses_BeforeShow @20-CE9269A4
function responses_BeforeShow(& $sender)
{
    $responses_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $responses; //Compatibility
//End responses_BeforeShow

//Custom Code @91-2A29BDB7
// -------------------------
    $responses->Visible = $responses->ds->num_rows();
// -------------------------
//End Custom Code

//Close responses_BeforeShow @20-78D367AE
    return $responses_BeforeShow;
}
//End Close responses_BeforeShow

//responses_BeforeShowRow @20-F899D134
function responses_BeforeShowRow(& $sender)
{
    $responses_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $responses; //Compatibility
//End responses_BeforeShowRow

//Custom Code @92-2A29BDB7
// -------------------------
    $responses->priority_id->SetValue(Translate($responses->priority_id->GetValue()));
    $responses->status_id->SetValue(Translate($responses->status_id->GetValue()));
// -------------------------
//End Custom Code

//Close responses_BeforeShowRow @20-344BC14A
    return $responses_BeforeShowRow;
}
//End Close responses_BeforeShowRow


?>
