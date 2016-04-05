<?php
//BindEvents Method @1-F2DD9E10
function BindEvents()
{
    global $settings;
    $settings->CCSEvents["OnValidate"] = "settings_OnValidate";
    $settings->CCSEvents["BeforeShow"] = "settings_BeforeShow";
    $settings->CCSEvents["AfterUpdate"] = "settings_AfterUpdate";
}
//End BindEvents Method

//settings_OnValidate @3-2BEB2B87
function settings_OnValidate(& $sender)
{
    $settings_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $settings; //Compatibility
//End settings_OnValidate

//Custom Code @19-2A29BDB7
// -------------------------
	global $CCSLocales;
	if (!is_dir($settings->file_path->GetValue()) || !is_writable($settings->file_path->GetValue()))
	{
		$settings->Errors->addError($CCSLocales->GetText("im_error_upload_folder"));
	}
// -------------------------
//End Custom Code

//Close settings_OnValidate @3-93EDB1E7
    return $settings_OnValidate;
}
//End Close settings_OnValidate

//settings_BeforeShow @3-9ADD7535
function settings_BeforeShow(& $sender)
{
    $settings_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $settings; //Compatibility
//End settings_BeforeShow

//Custom Code @48-2A29BDB7
// -------------------------
    TranslateListbox($settings->email_component);
// -------------------------
//End Custom Code

//Close settings_BeforeShow @3-AC16D56E
    return $settings_BeforeShow;
}
//End Close settings_BeforeShow

//settings_AfterUpdate @3-2D70B6BA
function settings_AfterUpdate(& $sender)
{
    $settings_AfterUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $settings; //Compatibility
//End settings_AfterUpdate

//Custom Code @49-2A29BDB7
// -------------------------
    // Write your own code here.
// -------------------------
//End Custom Code

//Close settings_AfterUpdate @3-1C22A1CA
    return $settings_AfterUpdate;
}
//End Close settings_AfterUpdate


?>
