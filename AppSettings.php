<?php
//Include Common Files @1-38A246D6
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "AppSettings.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-3DD2EFDC
include_once(RelativePath . "/Header.php");
//End Include Page implementation

//Include Page implementation @47-39F846CD
include_once(RelativePath . "/AdminMenu.php");
//End Include Page implementation

class clsRecordsettings { //settings Class @3-5FAC65B7

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

//Class_Initialize Event @3-7D1A8B4E
    function clsRecordsettings($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record settings/Error";
        $this->DataSource = new clssettingsDataSource($this);
        $this->ds = & $this->DataSource;
        $this->UpdateAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "settings";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->upload_enabled = & new clsControl(ccsCheckBox, "upload_enabled", "Upload Enabled", ccsInteger, "", CCGetRequestParam("upload_enabled", $Method, NULL), $this);
            $this->upload_enabled->CheckedValue = $this->upload_enabled->GetParsedValue(1);
            $this->upload_enabled->UncheckedValue = $this->upload_enabled->GetParsedValue(0);
            $this->file_extensions = & new clsControl(ccsTextBox, "file_extensions", $CCSLocales->GetText("im_file_ext"), ccsMemo, "", CCGetRequestParam("file_extensions", $Method, NULL), $this);
            $this->file_path = & new clsControl(ccsTextBox, "file_path", $CCSLocales->GetText("im_file_path"), ccsMemo, "", CCGetRequestParam("file_path", $Method, NULL), $this);
            $this->notify_new_from = & new clsControl(ccsTextBox, "notify_new_from", $CCSLocales->GetText("im_notify_new_from"), ccsText, "", CCGetRequestParam("notify_new_from", $Method, NULL), $this);
            $this->notify_new_subject = & new clsControl(ccsTextBox, "notify_new_subject", $CCSLocales->GetText("im_notify_new_subject"), ccsMemo, "", CCGetRequestParam("notify_new_subject", $Method, NULL), $this);
            $this->notify_new_body = & new clsControl(ccsTextArea, "notify_new_body", $CCSLocales->GetText("im_notify_new_body"), ccsMemo, "", CCGetRequestParam("notify_new_body", $Method, NULL), $this);
            $this->notify_change_from = & new clsControl(ccsTextBox, "notify_change_from", $CCSLocales->GetText("im_notify_change_from"), ccsText, "", CCGetRequestParam("notify_change_from", $Method, NULL), $this);
            $this->notify_change_subject = & new clsControl(ccsTextBox, "notify_change_subject", $CCSLocales->GetText("im_notify_change_subject"), ccsMemo, "", CCGetRequestParam("notify_change_subject", $Method, NULL), $this);
            $this->notify_change_body = & new clsControl(ccsTextArea, "notify_change_body", $CCSLocales->GetText("im_notify_change_body"), ccsMemo, "", CCGetRequestParam("notify_change_body", $Method, NULL), $this);
            $this->email_component = & new clsControl(ccsListBox, "email_component", $CCSLocales->GetText("im_email_component"), ccsText, "", CCGetRequestParam("email_component", $Method, NULL), $this);
            $this->email_component->DSType = dsTable;
            list($this->email_component->BoundColumn, $this->email_component->TextColumn, $this->email_component->DBFormat) = array("component_id", "component_name", "");
            $this->email_component->DataSource = new clsDBIM();
            $this->email_component->ds = & $this->email_component->DataSource;
            $this->email_component->DataSource->SQL = "SELECT * \n" .
"FROM email_components {SQL_Where} {SQL_OrderBy}";
            $this->smtp_host = & new clsControl(ccsTextBox, "smtp_host", "smtp_host", ccsText, "", CCGetRequestParam("smtp_host", $Method, NULL), $this);
            $this->Update = & new clsButton("Update", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @3-25B54A51
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urlsettings_id"] = CCGetFromGet("settings_id", NULL);
    }
//End Initialize Method

//Validate Method @3-840DAC03
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->upload_enabled->Validate() && $Validation);
        $Validation = ($this->file_extensions->Validate() && $Validation);
        $Validation = ($this->file_path->Validate() && $Validation);
        $Validation = ($this->notify_new_from->Validate() && $Validation);
        $Validation = ($this->notify_new_subject->Validate() && $Validation);
        $Validation = ($this->notify_new_body->Validate() && $Validation);
        $Validation = ($this->notify_change_from->Validate() && $Validation);
        $Validation = ($this->notify_change_subject->Validate() && $Validation);
        $Validation = ($this->notify_change_body->Validate() && $Validation);
        $Validation = ($this->email_component->Validate() && $Validation);
        $Validation = ($this->smtp_host->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->upload_enabled->Errors->Count() == 0);
        $Validation =  $Validation && ($this->file_extensions->Errors->Count() == 0);
        $Validation =  $Validation && ($this->file_path->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_new_from->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_new_subject->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_new_body->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_change_from->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_change_subject->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_change_body->Errors->Count() == 0);
        $Validation =  $Validation && ($this->email_component->Errors->Count() == 0);
        $Validation =  $Validation && ($this->smtp_host->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @3-61238A2E
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->upload_enabled->Errors->Count());
        $errors = ($errors || $this->file_extensions->Errors->Count());
        $errors = ($errors || $this->file_path->Errors->Count());
        $errors = ($errors || $this->notify_new_from->Errors->Count());
        $errors = ($errors || $this->notify_new_subject->Errors->Count());
        $errors = ($errors || $this->notify_new_body->Errors->Count());
        $errors = ($errors || $this->notify_change_from->Errors->Count());
        $errors = ($errors || $this->notify_change_subject->Errors->Count());
        $errors = ($errors || $this->notify_change_body->Errors->Count());
        $errors = ($errors || $this->email_component->Errors->Count());
        $errors = ($errors || $this->smtp_host->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @3-2CB76345
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
        $Redirect = "Administration.php" . "?" . CCGetQueryString("QueryString", array("ccsForm"));
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

//UpdateRow Method @3-86B13E2C
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->upload_enabled->SetValue($this->upload_enabled->GetValue(true));
        $this->DataSource->file_extensions->SetValue($this->file_extensions->GetValue(true));
        $this->DataSource->file_path->SetValue($this->file_path->GetValue(true));
        $this->DataSource->notify_new_from->SetValue($this->notify_new_from->GetValue(true));
        $this->DataSource->notify_new_subject->SetValue($this->notify_new_subject->GetValue(true));
        $this->DataSource->notify_new_body->SetValue($this->notify_new_body->GetValue(true));
        $this->DataSource->notify_change_from->SetValue($this->notify_change_from->GetValue(true));
        $this->DataSource->notify_change_subject->SetValue($this->notify_change_subject->GetValue(true));
        $this->DataSource->notify_change_body->SetValue($this->notify_change_body->GetValue(true));
        $this->DataSource->email_component->SetValue($this->email_component->GetValue(true));
        $this->DataSource->smtp_host->SetValue($this->smtp_host->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//Show Method @3-B41199BF
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->email_component->Prepare();

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
                if(!$this->FormSubmitted){
                    $this->upload_enabled->SetValue($this->DataSource->upload_enabled->GetValue());
                    $this->file_extensions->SetValue($this->DataSource->file_extensions->GetValue());
                    $this->file_path->SetValue($this->DataSource->file_path->GetValue());
                    $this->notify_new_from->SetValue($this->DataSource->notify_new_from->GetValue());
                    $this->notify_new_subject->SetValue($this->DataSource->notify_new_subject->GetValue());
                    $this->notify_new_body->SetValue($this->DataSource->notify_new_body->GetValue());
                    $this->notify_change_from->SetValue($this->DataSource->notify_change_from->GetValue());
                    $this->notify_change_subject->SetValue($this->DataSource->notify_change_subject->GetValue());
                    $this->notify_change_body->SetValue($this->DataSource->notify_change_body->GetValue());
                    $this->email_component->SetValue($this->DataSource->email_component->GetValue());
                    $this->smtp_host->SetValue($this->DataSource->smtp_host->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->upload_enabled->Errors->ToString());
            $Error = ComposeStrings($Error, $this->file_extensions->Errors->ToString());
            $Error = ComposeStrings($Error, $this->file_path->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_new_from->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_new_subject->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_new_body->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_change_from->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_change_subject->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_change_body->Errors->ToString());
            $Error = ComposeStrings($Error, $this->email_component->Errors->ToString());
            $Error = ComposeStrings($Error, $this->smtp_host->Errors->ToString());
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

        $this->upload_enabled->Show();
        $this->file_extensions->Show();
        $this->file_path->Show();
        $this->notify_new_from->Show();
        $this->notify_new_subject->Show();
        $this->notify_new_body->Show();
        $this->notify_change_from->Show();
        $this->notify_change_subject->Show();
        $this->notify_change_body->Show();
        $this->email_component->Show();
        $this->smtp_host->Show();
        $this->Update->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End settings Class @3-FCB6E20C

class clssettingsDataSource extends clsDBIM {  //settingsDataSource Class @3-27598144

//DataSource Variables @3-251FCE80
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $UpdateParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $upload_enabled;
    var $file_extensions;
    var $file_path;
    var $notify_new_from;
    var $notify_new_subject;
    var $notify_new_body;
    var $notify_change_from;
    var $notify_change_subject;
    var $notify_change_body;
    var $email_component;
    var $smtp_host;
//End DataSource Variables

//DataSourceClass_Initialize Event @3-2DA2E4FD
    function clssettingsDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record settings/Error";
        $this->Initialize();
        $this->upload_enabled = new clsField("upload_enabled", ccsInteger, "");
        $this->file_extensions = new clsField("file_extensions", ccsMemo, "");
        $this->file_path = new clsField("file_path", ccsMemo, "");
        $this->notify_new_from = new clsField("notify_new_from", ccsText, "");
        $this->notify_new_subject = new clsField("notify_new_subject", ccsMemo, "");
        $this->notify_new_body = new clsField("notify_new_body", ccsMemo, "");
        $this->notify_change_from = new clsField("notify_change_from", ccsText, "");
        $this->notify_change_subject = new clsField("notify_change_subject", ccsMemo, "");
        $this->notify_change_body = new clsField("notify_change_body", ccsMemo, "");
        $this->email_component = new clsField("email_component", ccsText, "");
        $this->smtp_host = new clsField("smtp_host", ccsText, "");

        $this->UpdateFields["upload_enabled"] = array("Name" => "upload_enabled", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["file_extensions"] = array("Name" => "file_extensions", "Value" => "", "DataType" => ccsMemo);
        $this->UpdateFields["file_path"] = array("Name" => "file_path", "Value" => "", "DataType" => ccsMemo);
        $this->UpdateFields["notify_new_from"] = array("Name" => "notify_new_from", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["notify_new_subject"] = array("Name" => "notify_new_subject", "Value" => "", "DataType" => ccsMemo);
        $this->UpdateFields["notify_new_body"] = array("Name" => "notify_new_body", "Value" => "", "DataType" => ccsMemo);
        $this->UpdateFields["notify_change_from"] = array("Name" => "notify_change_from", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["notify_change_subject"] = array("Name" => "notify_change_subject", "Value" => "", "DataType" => ccsMemo);
        $this->UpdateFields["notify_change_body"] = array("Name" => "notify_change_body", "Value" => "", "DataType" => ccsMemo);
        $this->UpdateFields["email_component"] = array("Name" => "email_component", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["smtp_host"] = array("Name" => "smtp_host", "Value" => "", "DataType" => ccsText);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @3-A2B17DEF
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlsettings_id", ccsInteger, "", "", $this->Parameters["urlsettings_id"], 1, false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "settings_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @3-04984454
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT * \n\n" .
        "FROM settings {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @3-BC5A6DB9
    function SetValues()
    {
        $this->upload_enabled->SetDBValue(trim($this->f("upload_enabled")));
        $this->file_extensions->SetDBValue($this->f("file_extensions"));
        $this->file_path->SetDBValue($this->f("file_path"));
        $this->notify_new_from->SetDBValue($this->f("notify_new_from"));
        $this->notify_new_subject->SetDBValue($this->f("notify_new_subject"));
        $this->notify_new_body->SetDBValue($this->f("notify_new_body"));
        $this->notify_change_from->SetDBValue($this->f("notify_change_from"));
        $this->notify_change_subject->SetDBValue($this->f("notify_change_subject"));
        $this->notify_change_body->SetDBValue($this->f("notify_change_body"));
        $this->email_component->SetDBValue($this->f("email_component"));
        $this->smtp_host->SetDBValue($this->f("smtp_host"));
    }
//End SetValues Method

//Update Method @3-32E9356E
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        $this->UpdateFields["upload_enabled"]["Value"] = $this->upload_enabled->GetDBValue(true);
        $this->UpdateFields["file_extensions"]["Value"] = $this->file_extensions->GetDBValue(true);
        $this->UpdateFields["file_path"]["Value"] = $this->file_path->GetDBValue(true);
        $this->UpdateFields["notify_new_from"]["Value"] = $this->notify_new_from->GetDBValue(true);
        $this->UpdateFields["notify_new_subject"]["Value"] = $this->notify_new_subject->GetDBValue(true);
        $this->UpdateFields["notify_new_body"]["Value"] = $this->notify_new_body->GetDBValue(true);
        $this->UpdateFields["notify_change_from"]["Value"] = $this->notify_change_from->GetDBValue(true);
        $this->UpdateFields["notify_change_subject"]["Value"] = $this->notify_change_subject->GetDBValue(true);
        $this->UpdateFields["notify_change_body"]["Value"] = $this->notify_change_body->GetDBValue(true);
        $this->UpdateFields["email_component"]["Value"] = $this->email_component->GetDBValue(true);
        $this->UpdateFields["smtp_host"]["Value"] = $this->smtp_host->GetDBValue(true);
        $this->SQL = CCBuildUpdate("settings", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

} //End settingsDataSource Class @3-FCB6E20C

//Include Page implementation @17-58DBA1E3
include_once(RelativePath . "/Footer.php");
//End Include Page implementation

//Initialize Page @1-C9BBA8A7
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
$TemplateFileName = "AppSettings.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-4B0BB954
CCSecurityRedirect("3", "");
//End Authenticate User

//Include events file @1-8B6A18CC
include("./AppSettings_events.php");
//End Include events file

//Initialize Objects @1-817C6053
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$Header = & new clsHeader("", "Header", $MainPage);
$Header->Initialize();
$AdminMenu = & new clsAdminMenu("", "AdminMenu", $MainPage);
$AdminMenu->Initialize();
$settings = & new clsRecordsettings("", $MainPage);
$Footer = & new clsFooter("", "Footer", $MainPage);
$Footer->Initialize();
$MainPage->Header = & $Header;
$MainPage->AdminMenu = & $AdminMenu;
$MainPage->settings = & $settings;
$MainPage->Footer = & $Footer;
$settings->Initialize();

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

//Execute Components @1-EC16D3DB
$Header->Operations();
$AdminMenu->Operations();
$settings->Operation();
$Footer->Operations();
//End Execute Components

//Go to destination page @1-83121AE9
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    $Header->Class_Terminate();
    unset($Header);
    $AdminMenu->Class_Terminate();
    unset($AdminMenu);
    unset($settings);
    $Footer->Class_Terminate();
    unset($Footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-9FF7F19F
$Header->Show();
$AdminMenu->Show();
$settings->Show();
$Footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-029A42C2
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
$Header->Class_Terminate();
unset($Header);
$AdminMenu->Class_Terminate();
unset($AdminMenu);
unset($settings);
$Footer->Class_Terminate();
unset($Footer);
unset($Tpl);
//End Unload Page


?>
