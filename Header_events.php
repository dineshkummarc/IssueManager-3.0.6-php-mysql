<?php
// //Events @1-F81417CB

//Header_user_BeforeShow @7-47118F61
function Header_user_BeforeShow(& $sender)
{
    $Header_user_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $Header; //Compatibility
//End Header_user_BeforeShow

//Custom Code @9-2A29BDB7
// -------------------------
	global $CCSLocales;
	$user = "";
	if (CCGetUserID()) $user = CCDLookup("user_name","users","user_id=".CCGetUserID(),new clsDBIM());
 	$Header->user->SetValue($CCSLocales->GetText("im_welcome", $user));
// -------------------------
//End Custom Code

//Close Header_user_BeforeShow @7-E59C03A6
    return $Header_user_BeforeShow;
}
//End Close Header_user_BeforeShow

//Header_AdminLink_BeforeShow @17-5EFCF160
function Header_AdminLink_BeforeShow(& $sender)
{
    $Header_AdminLink_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $Header; //Compatibility
//End Header_AdminLink_BeforeShow

//Hide-Show Component @18-20ACD7EF
    $Parameter1 = CCGetSession("GroupID");
    $Parameter2 = "3";
    if (0 != CCCompareValues($Parameter1, $Parameter2, ccsText))
        $Component->Visible = false;
//End Hide-Show Component

//Close Header_AdminLink_BeforeShow @17-E23EF03C
    return $Header_AdminLink_BeforeShow;
}
//End Close Header_AdminLink_BeforeShow
?>
