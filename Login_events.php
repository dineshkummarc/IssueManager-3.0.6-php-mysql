<?php
//BindEvents Method @1-A06222C7
function BindEvents()
{
    global $Login;
    global $CCSEvents;
    $Login->DoLogin->CCSEvents["OnClick"] = "Login_DoLogin_OnClick";
    $CCSEvents["OnInitializeView"] = "Page_OnInitializeView";
}
//End BindEvents Method

//Login_DoLogin_OnClick @3-19493798
function Login_DoLogin_OnClick(& $sender)
{
    $Login_DoLogin_OnClick = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $Login; //Compatibility
//End Login_DoLogin_OnClick

//Login @4-1C6B4BA5
    global $CCSLocales;
    if ( !CCLoginUser( $Container->login->Value, $Container->password->Value)) {
        $Container->Errors->addError($CCSLocales->GetText("CCS_LoginError"));
        $Container->password->SetValue("");
        $Login_DoLogin_OnClick = 0;
    } else {
        global $Redirect;
        $Redirect = CCGetParam("ret_link", $Redirect);
        $Login_DoLogin_OnClick = 1;
    }
//End Login

//Close Login_DoLogin_OnClick @3-FDDBE2C9
    return $Login_DoLogin_OnClick;
}
//End Close Login_DoLogin_OnClick

//Page_OnInitializeView @1-16421D3D
function Page_OnInitializeView(& $sender)
{
    $Page_OnInitializeView = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $Login; //Compatibility
//End Page_OnInitializeView

//Logout @9-F4222219
    if(strlen(CCGetParam("Logout", ""))) 
    {
        CCLogoutUser();
        global $Redirect;
        $Redirect = "Login.php";
    }
//End Logout

//Close Page_OnInitializeView @1-81DF8332
    return $Page_OnInitializeView;
}
//End Close Page_OnInitializeView


?>
