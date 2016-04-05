<?php
//Include Common Files @1-76FC921D
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "UserProfile.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-3DD2EFDC
include_once(RelativePath . "/Header.php");
//End Include Page implementation

class clsRecordusers { //users Class @3-9BE1AF6F

//Variables @3-0DF9B1C2

    // Public variables
    var $ComponentType = "Record";
    var $ComponentName;
    var $Parent;
    var $HTMLFormAction;
    var $PressedButton;
    var $Errors;
    var $ErrorBlock;
    var $FormSubmitted;
    var $FormEnctype;
    var $Visible;
    var $IsEmpty;

    var $CCSEvents = "";
    var $CCSEventResult;

    var $RelativePath = "";

    var $InsertAllowed = false;
    var $UpdateAllowed = false;
    var $DeleteAllowed = false;
    var $ReadAllowed   = false;
    var $EditMode      = false;
    var $ds;
    var $DataSource;
    var $ValidatingControls;
    var $Controls;

    // Class variables
//End Variables

//Class_Initialize Event @3-04BD9227
    function clsRecordusers($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record users/Error";
        $this->DataSource = new clsusersDataSource($this);
        $this->ds = & $this->DataSource;
        $this->UpdateAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "users";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->user_name = & new clsControl(ccsLabel, "user_name", $CCSLocales->GetText("im_name"), ccsText, "", CCGetRequestParam("user_name", $Method, NULL), $this);
            $this->login = & new clsControl(ccsLabel, "login", $CCSLocales->GetText("CCS_Login"), ccsText, "", CCGetRequestParam("login", $Method, NULL), $this);
            $this->old_pass = & new clsControl(ccsTextBox, "old_pass", $CCSLocales->GetText("CCS_Password"), ccsText, "", CCGetRequestParam("old_pass", $Method, NULL), $this);
            $this->new_pass = & new clsControl(ccsTextBox, "new_pass", $CCSLocales->GetText("CCS_Password"), ccsText, "", CCGetRequestParam("new_pass", $Method, NULL), $this);
            $this->rep_pass = & new clsControl(ccsTextBox, "rep_pass", $CCSLocales->GetText("CCS_Password"), ccsText, "", CCGetRequestParam("rep_pass", $Method, NULL), $this);
            $this->email = & new clsControl(ccsTextBox, "email", $CCSLocales->GetText("im_email"), ccsText, "", CCGetRequestParam("email", $Method, NULL), $this);
            $this->email->Required = true;
            $this->notify_new = & new clsControl(ccsCheckBox, "notify_new", "Notify New", ccsInteger, "", CCGetRequestParam("notify_new", $Method, NULL), $this);
            $this->notify_new->CheckedValue = $this->notify_new->GetParsedValue(1);
            $this->notify_new->UncheckedValue = $this->notify_new->GetParsedValue(0);
            $this->notify_original = & new clsControl(ccsCheckBox, "notify_original", "Notify Original", ccsInteger, "", CCGetRequestParam("notify_original", $Method, NULL), $this);
            $this->notify_original->CheckedValue = $this->notify_original->GetParsedValue(1);
            $this->notify_original->UncheckedValue = $this->notify_original->GetParsedValue(0);
            $this->notify_reassignment = & new clsControl(ccsCheckBox, "notify_reassignment", "Notify Reassignment", ccsInteger, "", CCGetRequestParam("notify_reassignment", $Method, NULL), $this);
            $this->notify_reassignment->CheckedValue = $this->notify_reassignment->GetParsedValue(1);
            $this->notify_reassignment->UncheckedValue = $this->notify_reassignment->GetParsedValue(0);
            $this->allow_upload = & new clsControl(ccsLabel, "allow_upload", "Allow Upload", ccsBoolean, array("res:im_yes", "res:im_no", ""), CCGetRequestParam("allow_upload", $Method, NULL), $this);
            $this->security_level = & new clsControl(ccsLabel, "security_level", "Security Level", ccsText, "", CCGetRequestParam("security_level", $Method, NULL), $this);
            $this->Update = & new clsButton("Update", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @3-D3026D7D
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["sesUserID"] = CCGetSession("UserID", NULL);
    }
//End Initialize Method

//Validate Method @3-389A30FA
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        if($this->EditMode && strlen($this->DataSource->Where))
            $Where = " AND NOT (" . $this->DataSource->Where . ")";
        if(strlen($this->email->GetText()) && !preg_match ("/^[\w\.-]{1,}\@([\da-zA-Z-]{1,}\.){1,}[\da-zA-Z-]+$/", $this->email->GetText())) {
            $this->email->Errors->addError($CCSLocales->GetText("CCS_MaskValidation", $CCSLocales->GetText("im_email")));
        }
        $Validation = ($this->old_pass->Validate() && $Validation);
        $Validation = ($this->new_pass->Validate() && $Validation);
        $Validation = ($this->rep_pass->Validate() && $Validation);
        $Validation = ($this->email->Validate() && $Validation);
        $Validation = ($this->notify_new->Validate() && $Validation);
        $Validation = ($this->notify_original->Validate() && $Validation);
        $Validation = ($this->notify_reassignment->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->old_pass->Errors->Count() == 0);
        $Validation =  $Validation && ($this->new_pass->Errors->Count() == 0);
        $Validation =  $Validation && ($this->rep_pass->Errors->Count() == 0);
        $Validation =  $Validation && ($this->email->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_new->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_original->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_reassignment->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @3-A25580A8
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->user_name->Errors->Count());
        $errors = ($errors || $this->login->Errors->Count());
        $errors = ($errors || $this->old_pass->Errors->Count());
        $errors = ($errors || $this->new_pass->Errors->Count());
        $errors = ($errors || $this->rep_pass->Errors->Count());
        $errors = ($errors || $this->email->Errors->Count());
        $errors = ($errors || $this->notify_new->Errors->Count());
        $errors = ($errors || $this->notify_original->Errors->Count());
        $errors = ($errors || $this->notify_reassignment->Errors->Count());
        $errors = ($errors || $this->allow_upload->Errors->Count());
        $errors = ($errors || $this->security_level->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @3-B223C7F5
    function Operation()
    {
        if(!$this->Visible)
            return;

        global $Redirect;
        global $FileName;

        $this->DataSource->Prepare();
        if(!$this->FormSubmitted) {
            $this->EditMode = $this->DataSource->AllParametersSet;
            return;
        }

        if($this->FormSubmitted) {
            $this->PressedButton = $this->EditMode ? "Update" : "";
            if($this->Update->Pressed) {
                $this->PressedButton = "Update";
            }
        }
        $Redirect = "UserProfile.php" . "?" . CCGetQueryString("QueryString", array("ccsForm"));
        if($this->Validate()) {
            if($this->PressedButton == "Update") {
                if(!CCGetEvent($this->Update->CCSEvents, "OnClick", $this->Update) || !$this->UpdateRow()) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
        if ($Redirect)
            $this->DataSource->close();
    }
//End Operation Method

//UpdateRow Method @3-0BA358B7
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->user_name->SetValue($this->user_name->GetValue(true));
        $this->DataSource->login->SetValue($this->login->GetValue(true));
        $this->DataSource->old_pass->SetValue($this->old_pass->GetValue(true));
        $this->DataSource->new_pass->SetValue($this->new_pass->GetValue(true));
        $this->DataSource->rep_pass->SetValue($this->rep_pass->GetValue(true));
        $this->DataSource->email->SetValue($this->email->GetValue(true));
        $this->DataSource->notify_new->SetValue($this->notify_new->GetValue(true));
        $this->DataSource->notify_original->SetValue($this->notify_original->GetValue(true));
        $this->DataSource->notify_reassignment->SetValue($this->notify_reassignment->GetValue(true));
        $this->DataSource->allow_upload->SetValue($this->allow_upload->GetValue(true));
        $this->DataSource->security_level->SetValue($this->security_level->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//Show Method @3-565C9211
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);


        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        $this->EditMode = $this->EditMode && $this->ReadAllowed;
        if($this->EditMode) {
            if($this->DataSource->Errors->Count()){
                $this->Errors->AddErrors($this->DataSource->Errors);
                $this->DataSource->Errors->clear();
            }
            $this->DataSource->Open();
            if($this->DataSource->Errors->Count() == 0 && $this->DataSource->next_record()) {
                $this->DataSource->SetValues();
                $this->user_name->SetValue($this->DataSource->user_name->GetValue());
                $this->login->SetValue($this->DataSource->login->GetValue());
                $this->allow_upload->SetValue($this->DataSource->allow_upload->GetValue());
                $this->security_level->SetValue($this->DataSource->security_level->GetValue());
                if(!$this->FormSubmitted){
                    $this->email->SetValue($this->DataSource->email->GetValue());
                    $this->notify_new->SetValue($this->DataSource->notify_new->GetValue());
                    $this->notify_original->SetValue($this->DataSource->notify_original->GetValue());
                    $this->notify_reassignment->SetValue($this->DataSource->notify_reassignment->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }
        if (!$this->FormSubmitted) {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->user_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->login->Errors->ToString());
            $Error = ComposeStrings($Error, $this->old_pass->Errors->ToString());
            $Error = ComposeStrings($Error, $this->new_pass->Errors->ToString());
            $Error = ComposeStrings($Error, $this->rep_pass->Errors->ToString());
            $Error = ComposeStrings($Error, $this->email->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_new->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_original->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_reassignment->Errors->ToString());
            $Error = ComposeStrings($Error, $this->allow_upload->Errors->ToString());
            $Error = ComposeStrings($Error, $this->security_level->Errors->ToString());
            $Error = ComposeStrings($Error, $this->Errors->ToString());
            $Error = ComposeStrings($Error, $this->DataSource->Errors->ToString());
            $Tpl->SetVar("Error", $Error);
            $Tpl->Parse("Error", false);
        }
        $CCSForm = $this->EditMode ? $this->ComponentName . ":" . "Edit" : $this->ComponentName;
        $this->HTMLFormAction = $FileName . "?" . CCAddParam(CCGetQueryString("QueryString", ""), "ccsForm", $CCSForm);
        $Tpl->SetVar("Action", $this->HTMLFormAction);
        $Tpl->SetVar("HTMLFormName", $this->ComponentName);
        $Tpl->SetVar("HTMLFormEnctype", $this->FormEnctype);
        $this->Update->Visible = $this->EditMode && $this->UpdateAllowed;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->user_name->Show();
        $this->login->Show();
        $this->old_pass->Show();
        $this->new_pass->Show();
        $this->rep_pass->Show();
        $this->email->Show();
        $this->notify_new->Show();
        $this->notify_original->Show();
        $this->notify_reassignment->Show();
        $this->allow_upload->Show();
        $this->security_level->Show();
        $this->Update->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End users Class @3-FCB6E20C

class clsusersDataSource extends clsDBIM {  //usersDataSource Class @3-0A435B39

//DataSource Variables @3-BD621173
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $UpdateParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $user_name;
    var $login;
    var $old_pass;
    var $new_pass;
    var $rep_pass;
    var $email;
    var $notify_new;
    var $notify_original;
    var $notify_reassignment;
    var $allow_upload;
    var $security_level;
//End DataSource Variables

//DataSourceClass_Initialize Event @3-D8E433E5
    function clsusersDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record users/Error";
        $this->Initialize();
        $this->user_name = new clsField("user_name", ccsText, "");
        $this->login = new clsField("login", ccsText, "");
        $this->old_pass = new clsField("old_pass", ccsText, "");
        $this->new_pass = new clsField("new_pass", ccsText, "");
        $this->rep_pass = new clsField("rep_pass", ccsText, "");
        $this->email = new clsField("email", ccsText, "");
        $this->notify_new = new clsField("notify_new", ccsInteger, "");
        $this->notify_original = new clsField("notify_original", ccsInteger, "");
        $this->notify_reassignment = new clsField("notify_reassignment", ccsInteger, "");
        $this->allow_upload = new clsField("allow_upload", ccsBoolean, array(1, 0, ""));
        $this->security_level = new clsField("security_level", ccsText, "");

        $this->UpdateFields["email"] = array("Name" => "email", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["notify_new"] = array("Name" => "notify_new", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["notify_original"] = array("Name" => "notify_original", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["notify_reassignment"] = array("Name" => "notify_reassignment", "Value" => "", "DataType" => ccsInteger);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @3-9BA58B14
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "sesUserID", ccsInteger, "", "", $this->Parameters["sesUserID"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "user_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @3-B071412E
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT * \n\n" .
        "FROM users {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @3-8427FD53
    function SetValues()
    {
        $this->user_name->SetDBValue($this->f("user_name"));
        $this->login->SetDBValue($this->f("login"));
        $this->email->SetDBValue($this->f("email"));
        $this->notify_new->SetDBValue(trim($this->f("notify_new")));
        $this->notify_original->SetDBValue(trim($this->f("notify_original")));
        $this->notify_reassignment->SetDBValue(trim($this->f("notify_reassignment")));
        $this->allow_upload->SetDBValue(trim($this->f("allow_upload")));
        $this->security_level->SetDBValue($this->f("security_level"));
    }
//End SetValues Method

//Update Method @3-1C286A6F
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        $this->UpdateFields["email"]["Value"] = $this->email->GetDBValue(true);
        $this->UpdateFields["notify_new"]["Value"] = $this->notify_new->GetDBValue(true);
        $this->UpdateFields["notify_original"]["Value"] = $this->notify_original->GetDBValue(true);
        $this->UpdateFields["notify_reassignment"]["Value"] = $this->notify_reassignment->GetDBValue(true);
        $this->SQL = CCBuildUpdate("users", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

} //End usersDataSource Class @3-FCB6E20C

//Include Page implementation @15-58DBA1E3
include_once(RelativePath . "/Footer.php");
//End Include Page implementation

//Initialize Page @1-DE30B5A1
// Variables
$FileName = "";
$Redirect = "";
$Tpl = "";
$TemplateFileName = "";
$BlockToParse = "";
$ComponentName = "";

// Events;
$CCSEvents = "";
$CCSEventResult = "";

$FileName = FileName;
$Redirect = "";
$TemplateFileName = "UserProfile.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-946ECC7A
CCSecurityRedirect("1;2;3", "");
//End Authenticate User

//Include events file @1-F303D2B8
include("./UserProfile_events.php");
//End Include events file

//Initialize Objects @1-50C693DE
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$Header = & new clsHeader("", "Header", $MainPage);
$Header->Initialize();
$users = & new clsRecordusers("", $MainPage);
$Footer = & new clsFooter("", "Footer", $MainPage);
$Footer->Initialize();
$MainPage->Header = & $Header;
$MainPage->users = & $users;
$MainPage->Footer = & $Footer;
$users->Initialize();

BindEvents();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize", $MainPage);

$Charset = $Charset ? $Charset : "utf-8";
if ($Charset)
    header("Content-Type: text/html; charset=" . $Charset);
//End Initialize Objects

//Initialize HTML Template @1-885748E0
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView", $MainPage);
$Tpl = new clsTemplate($FileEncoding, $TemplateEncoding);
$Tpl->LoadTemplate(PathToCurrentPage . $TemplateFileName, $BlockToParse, "UTF-8", "replace");
$Tpl->block_path = "/$BlockToParse";
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow", $MainPage);
//End Initialize HTML Template

//Execute Components @1-AB1E45CE
$Header->Operations();
$users->Operation();
$Footer->Operations();
//End Execute Components

//Go to destination page @1-A863E8FB
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    $Header->Class_Terminate();
    unset($Header);
    unset($users);
    $Footer->Class_Terminate();
    unset($Footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-C790D03B
$Header->Show();
$users->Show();
$Footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-3C2BA087
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
$Header->Class_Terminate();
unset($Header);
unset($users);
$Footer->Class_Terminate();
unset($Footer);
unset($Tpl);
//End Unload Page


?>
