<?php
//BindEvents Method @1-487B67BA
function BindEvents()
{
    global $users;
    $users->CCSEvents["BeforeShowRow"] = "users_BeforeShowRow";
    $users->CCSEvents["BeforeShow"] = "users_BeforeShow";
}
//End BindEvents Method

//users_BeforeShowRow @3-82C07E8D
function users_BeforeShowRow(& $sender)
{
    $users_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users; //Compatibility
//End users_BeforeShowRow

//Custom Code @17-2A29BDB7
// -------------------------
	global $CCSLocales;
	$users->security_level->SetValue($CCSLocales->GetText("im_level_".$users->security_level->GetValue()));
// -------------------------
//End Custom Code

//Close users_BeforeShowRow @3-370775E0
    return $users_BeforeShowRow;
}
//End Close users_BeforeShowRow

//users_BeforeShow @3-84B77ABC
function users_BeforeShow(& $sender)
{
    $users_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users; //Compatibility
//End users_BeforeShow

//Custom Code @18-2A29BDB7
// -------------------------
    FormatBoolean($users->allow_upload);
// -------------------------
//End Custom Code

//Close users_BeforeShow @3-500F6ED2
    return $users_BeforeShow;
}
//End Close users_BeforeShow


?>
