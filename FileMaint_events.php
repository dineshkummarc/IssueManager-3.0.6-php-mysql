<?php
//BindEvents Method @1-920121F7
function BindEvents()
{
    global $files;
    $files->file->CCSEvents["BeforeShow"] = "files_file_BeforeShow";
    $files->file_name->CCSEvents["BeforeProcessFile"] = "files_file_name_BeforeProcessFile";
    $files->date_format->CCSEvents["BeforeShow"] = "files_date_format_BeforeShow";
    $files->CCSEvents["AfterDelete"] = "files_AfterDelete";
    $files->CCSEvents["BeforeShow"] = "files_BeforeShow";
}
//End BindEvents Method

//files_file_BeforeShow @48-D30D88C4
function files_file_BeforeShow(& $sender)
{
    $files_file_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $files; //Compatibility
//End files_file_BeforeShow

//Get Original Filename @49-10353880
    $control_value = $Component->GetValue();
    $original_filename = CCGetOriginalFileName($control_value);
    $Component->SetValue($original_filename);
//End Get Original Filename

//Close files_file_BeforeShow @48-2B9FAA8E
    return $files_file_BeforeShow;
}
//End Close files_file_BeforeShow

//files_file_name_BeforeProcessFile @13-A4900B48
function files_file_name_BeforeProcessFile(& $sender)
{
    $files_file_name_BeforeProcessFile = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $files; //Compatibility
//End files_file_name_BeforeProcessFile

//Custom Code @16-2A29BDB7
// -------------------------
	SetupUpload($files->file_name);
// -------------------------
//End Custom Code

//Close files_file_name_BeforeProcessFile @13-BA792C97
    return $files_file_name_BeforeProcessFile;
}
//End Close files_file_name_BeforeProcessFile

//files_date_format_BeforeShow @103-877F3294
function files_date_format_BeforeShow(& $sender)
{
    $files_date_format_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $files; //Compatibility
//End files_date_format_BeforeShow

//Print General Date Format @104-BB1D2E46
    $Component->SetValue(GetGeneralDateFormat());
//End Print General Date Format

//Close files_date_format_BeforeShow @103-EE696E03
    return $files_date_format_BeforeShow;
}
//End Close files_date_format_BeforeShow

//files_AfterDelete @3-890CCC85
function files_AfterDelete(& $sender)
{
    $files_AfterDelete = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $files; //Compatibility
//End files_AfterDelete

//Custom Code @14-2A29BDB7
// -------------------------
    // Write your own code here.
// -------------------------
//End Custom Code

//Close files_AfterDelete @3-B47787A5
    return $files_AfterDelete;
}
//End Close files_AfterDelete

//files_BeforeShow @3-88AECC2D
function files_BeforeShow(& $sender)
{
    $files_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $files; //Compatibility
//End files_BeforeShow

//Custom Code @15-2A29BDB7
// -------------------------
	SetupUpload($files->file_name);
	$files->file->SetLink(GetSetting("file_path")."/".$files->file->GetLink());
// -------------------------
//End Custom Code

//Close files_BeforeShow @3-B34F3707
    return $files_BeforeShow;
}
//End Close files_BeforeShow


?>
