<?php
//Include Common Files @1-30E4C810
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "ResponseMaint.php");
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

class clsRecordresponses { //responses Class @7-8897FD58

//Variables @7-0DF9B1C2

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

//Class_Initialize Event @7-A00903A8
    function clsRecordresponses($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record responses/Error";
        $this->DataSource = new clsresponsesDataSource($this);
        $this->ds = & $this->DataSource;
        $this->InsertAllowed = true;
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "responses";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->user_id = & new clsControl(ccsListBox, "user_id", $CCSLocales->GetText("im_user_submitted"), ccsInteger, "", CCGetRequestParam("user_id", $Method, NULL), $this);
            $this->user_id->DSType = dsTable;
            list($this->user_id->BoundColumn, $this->user_id->TextColumn, $this->user_id->DBFormat) = array("user_id", "user_name", "");
            $this->user_id->DataSource = new clsDBIM();
            $this->user_id->ds = & $this->user_id->DataSource;
            $this->user_id->DataSource->SQL = "SELECT * \n" .
"FROM users {SQL_Where} {SQL_OrderBy}";
            $this->user_id->DataSource->Order = "user_name";
            $this->user_id->DataSource->Order = "user_name";
            $this->user_id->Required = true;
            $this->date_response = & new clsControl(ccsTextBox, "date_response", $CCSLocales->GetText("im_date_response"), ccsDate, array("GeneralDate"), CCGetRequestParam("date_response", $Method, NULL), $this);
            $this->date_response->Required = true;
            $this->date_format = & new clsControl(ccsLabel, "date_format", "date_format", ccsText, "", CCGetRequestParam("date_format", $Method, NULL), $this);
            $this->response = & new clsControl(ccsTextArea, "response", $CCSLocales->GetText("im_response"), ccsMemo, "", CCGetRequestParam("response", $Method, NULL), $this);
            $this->response->Required = true;
            $this->assigned_to = & new clsControl(ccsListBox, "assigned_to", $CCSLocales->GetText("im_assigned_to"), ccsInteger, "", CCGetRequestParam("assigned_to", $Method, NULL), $this);
            $this->assigned_to->DSType = dsTable;
            list($this->assigned_to->BoundColumn, $this->assigned_to->TextColumn, $this->assigned_to->DBFormat) = array("user_id", "user_name", "");
            $this->assigned_to->DataSource = new clsDBIM();
            $this->assigned_to->ds = & $this->assigned_to->DataSource;
            $this->assigned_to->DataSource->SQL = "SELECT * \n" .
"FROM users {SQL_Where} {SQL_OrderBy}";
            $this->assigned_to->DataSource->Order = "user_name";
            $this->assigned_to->DataSource->Order = "user_name";
            $this->assigned_to->Required = true;
            $this->priority_id = & new clsControl(ccsListBox, "priority_id", $CCSLocales->GetText("im_priority"), ccsInteger, "", CCGetRequestParam("priority_id", $Method, NULL), $this);
            $this->priority_id->DSType = dsTable;
            list($this->priority_id->BoundColumn, $this->priority_id->TextColumn, $this->priority_id->DBFormat) = array("priority_id", "priority_desc", "");
            $this->priority_id->DataSource = new clsDBIM();
            $this->priority_id->ds = & $this->priority_id->DataSource;
            $this->priority_id->DataSource->SQL = "SELECT * \n" .
"FROM priorities {SQL_Where} {SQL_OrderBy}";
            $this->priority_id->Required = true;
            $this->status_id = & new clsControl(ccsListBox, "status_id", $CCSLocales->GetText("im_status"), ccsInteger, "", CCGetRequestParam("status_id", $Method, NULL), $this);
            $this->status_id->DSType = dsTable;
            list($this->status_id->BoundColumn, $this->status_id->TextColumn, $this->status_id->DBFormat) = array("status_id", "status", "");
            $this->status_id->DataSource = new clsDBIM();
            $this->status_id->ds = & $this->status_id->DataSource;
            $this->status_id->DataSource->SQL = "SELECT * \n" .
"FROM statuses {SQL_Where} {SQL_OrderBy}";
            $this->status_id->Required = true;
            $this->Insert = & new clsButton("Insert", $Method, $this);
            $this->Update = & new clsButton("Update", $Method, $this);
            $this->Delete = & new clsButton("Delete", $Method, $this);
            $this->Cancel = & new clsButton("Cancel", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @7-D14ECCFB
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urlresponse_id"] = CCGetFromGet("response_id", NULL);
    }
//End Initialize Method

//Validate Method @7-E0414B16
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->user_id->Validate() && $Validation);
        $Validation = ($this->date_response->Validate() && $Validation);
        $Validation = ($this->response->Validate() && $Validation);
        $Validation = ($this->assigned_to->Validate() && $Validation);
        $Validation = ($this->priority_id->Validate() && $Validation);
        $Validation = ($this->status_id->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->user_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->date_response->Errors->Count() == 0);
        $Validation =  $Validation && ($this->response->Errors->Count() == 0);
        $Validation =  $Validation && ($this->assigned_to->Errors->Count() == 0);
        $Validation =  $Validation && ($this->priority_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->status_id->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @7-1CA92086
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->user_id->Errors->Count());
        $errors = ($errors || $this->date_response->Errors->Count());
        $errors = ($errors || $this->date_format->Errors->Count());
        $errors = ($errors || $this->response->Errors->Count());
        $errors = ($errors || $this->assigned_to->Errors->Count());
        $errors = ($errors || $this->priority_id->Errors->Count());
        $errors = ($errors || $this->status_id->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @7-6762C2EE
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
            $this->PressedButton = $this->EditMode ? "Update" : "Insert";
            if($this->Insert->Pressed) {
                $this->PressedButton = "Insert";
            } else if($this->Update->Pressed) {
                $this->PressedButton = "Update";
            } else if($this->Delete->Pressed) {
                $this->PressedButton = "Delete";
            } else if($this->Cancel->Pressed) {
                $this->PressedButton = "Cancel";
            }
        }
        $Redirect = "IssueMaint.php" . "?" . CCGetQueryString("QueryString", array("ccsForm", "response_id"));
        if($this->PressedButton == "Delete") {
            if(!CCGetEvent($this->Delete->CCSEvents, "OnClick", $this->Delete) || !$this->DeleteRow()) {
                $Redirect = "";
            }
        } else if($this->PressedButton == "Cancel") {
            if(!CCGetEvent($this->Cancel->CCSEvents, "OnClick", $this->Cancel)) {
                $Redirect = "";
            }
        } else if($this->Validate()) {
            if($this->PressedButton == "Insert") {
                if(!CCGetEvent($this->Insert->CCSEvents, "OnClick", $this->Insert) || !$this->InsertRow()) {
                    $Redirect = "";
                }
            } else if($this->PressedButton == "Update") {
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

//InsertRow Method @7-850CF6CE
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert", $this);
        if(!$this->InsertAllowed) return false;
        $this->DataSource->user_id->SetValue($this->user_id->GetValue(true));
        $this->DataSource->date_response->SetValue($this->date_response->GetValue(true));
        $this->DataSource->date_format->SetValue($this->date_format->GetValue(true));
        $this->DataSource->response->SetValue($this->response->GetValue(true));
        $this->DataSource->assigned_to->SetValue($this->assigned_to->GetValue(true));
        $this->DataSource->priority_id->SetValue($this->priority_id->GetValue(true));
        $this->DataSource->status_id->SetValue($this->status_id->GetValue(true));
        $this->DataSource->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert", $this);
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//UpdateRow Method @7-B02909EA
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->user_id->SetValue($this->user_id->GetValue(true));
        $this->DataSource->date_response->SetValue($this->date_response->GetValue(true));
        $this->DataSource->date_format->SetValue($this->date_format->GetValue(true));
        $this->DataSource->response->SetValue($this->response->GetValue(true));
        $this->DataSource->assigned_to->SetValue($this->assigned_to->GetValue(true));
        $this->DataSource->priority_id->SetValue($this->priority_id->GetValue(true));
        $this->DataSource->status_id->SetValue($this->status_id->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//DeleteRow Method @7-299D98C3
    function DeleteRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDelete", $this);
        if(!$this->DeleteAllowed) return false;
        $this->DataSource->Delete();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDelete", $this);
        return (!$this->CheckErrors());
    }
//End DeleteRow Method

//Show Method @7-3AD68DE8
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->user_id->Prepare();
        $this->assigned_to->Prepare();
        $this->priority_id->Prepare();
        $this->status_id->Prepare();

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
                    $this->user_id->SetValue($this->DataSource->user_id->GetValue());
                    $this->date_response->SetValue($this->DataSource->date_response->GetValue());
                    $this->response->SetValue($this->DataSource->response->GetValue());
                    $this->assigned_to->SetValue($this->DataSource->assigned_to->GetValue());
                    $this->priority_id->SetValue($this->DataSource->priority_id->GetValue());
                    $this->status_id->SetValue($this->DataSource->status_id->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->user_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->date_response->Errors->ToString());
            $Error = ComposeStrings($Error, $this->date_format->Errors->ToString());
            $Error = ComposeStrings($Error, $this->response->Errors->ToString());
            $Error = ComposeStrings($Error, $this->assigned_to->Errors->ToString());
            $Error = ComposeStrings($Error, $this->priority_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->status_id->Errors->ToString());
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
        $this->Insert->Visible = !$this->EditMode && $this->InsertAllowed;
        $this->Update->Visible = $this->EditMode && $this->UpdateAllowed;
        $this->Delete->Visible = $this->EditMode && $this->DeleteAllowed;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->user_id->Show();
        $this->date_response->Show();
        $this->date_format->Show();
        $this->response->Show();
        $this->assigned_to->Show();
        $this->priority_id->Show();
        $this->status_id->Show();
        $this->Insert->Show();
        $this->Update->Show();
        $this->Delete->Show();
        $this->Cancel->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End responses Class @7-FCB6E20C

class clsresponsesDataSource extends clsDBIM {  //responsesDataSource Class @7-5248FCCF

//DataSource Variables @7-5946B2A8
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $InsertParameters;
    var $UpdateParameters;
    var $DeleteParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $user_id;
    var $date_response;
    var $date_format;
    var $response;
    var $assigned_to;
    var $priority_id;
    var $status_id;
//End DataSource Variables

//DataSourceClass_Initialize Event @7-2FDA8BEA
    function clsresponsesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record responses/Error";
        $this->Initialize();
        $this->user_id = new clsField("user_id", ccsInteger, "");
        $this->date_response = new clsField("date_response", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->date_format = new clsField("date_format", ccsText, "");
        $this->response = new clsField("response", ccsMemo, "");
        $this->assigned_to = new clsField("assigned_to", ccsInteger, "");
        $this->priority_id = new clsField("priority_id", ccsInteger, "");
        $this->status_id = new clsField("status_id", ccsInteger, "");

        $this->InsertFields["user_id"] = array("Name" => "user_id", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["date_response"] = array("Name" => "date_response", "Value" => "", "DataType" => ccsDate);
        $this->InsertFields["response"] = array("Name" => "response", "Value" => "", "DataType" => ccsMemo);
        $this->InsertFields["assigned_to"] = array("Name" => "assigned_to", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["priority_id"] = array("Name" => "priority_id", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["status_id"] = array("Name" => "status_id", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["user_id"] = array("Name" => "user_id", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["date_response"] = array("Name" => "date_response", "Value" => "", "DataType" => ccsDate);
        $this->UpdateFields["response"] = array("Name" => "response", "Value" => "", "DataType" => ccsMemo);
        $this->UpdateFields["assigned_to"] = array("Name" => "assigned_to", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["priority_id"] = array("Name" => "priority_id", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["status_id"] = array("Name" => "status_id", "Value" => "", "DataType" => ccsInteger);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @7-643B8355
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlresponse_id", ccsInteger, "", "", $this->Parameters["urlresponse_id"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "response_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @7-68E84934
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT * \n\n" .
        "FROM responses {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @7-69A5242B
    function SetValues()
    {
        $this->user_id->SetDBValue(trim($this->f("user_id")));
        $this->date_response->SetDBValue(trim($this->f("date_response")));
        $this->response->SetDBValue($this->f("response"));
        $this->assigned_to->SetDBValue(trim($this->f("assigned_to")));
        $this->priority_id->SetDBValue(trim($this->f("priority_id")));
        $this->status_id->SetDBValue(trim($this->f("status_id")));
    }
//End SetValues Method

//Insert Method @7-ECE8E394
    function Insert()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert", $this->Parent);
        $this->InsertFields["user_id"]["Value"] = $this->user_id->GetDBValue(true);
        $this->InsertFields["date_response"]["Value"] = $this->date_response->GetDBValue(true);
        $this->InsertFields["response"]["Value"] = $this->response->GetDBValue(true);
        $this->InsertFields["assigned_to"]["Value"] = $this->assigned_to->GetDBValue(true);
        $this->InsertFields["priority_id"]["Value"] = $this->priority_id->GetDBValue(true);
        $this->InsertFields["status_id"]["Value"] = $this->status_id->GetDBValue(true);
        $this->SQL = CCBuildInsert("responses", $this->InsertFields, $this);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert", $this->Parent);
        }
    }
//End Insert Method

//Update Method @7-5840CC3C
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        $this->UpdateFields["user_id"]["Value"] = $this->user_id->GetDBValue(true);
        $this->UpdateFields["date_response"]["Value"] = $this->date_response->GetDBValue(true);
        $this->UpdateFields["response"]["Value"] = $this->response->GetDBValue(true);
        $this->UpdateFields["assigned_to"]["Value"] = $this->assigned_to->GetDBValue(true);
        $this->UpdateFields["priority_id"]["Value"] = $this->priority_id->GetDBValue(true);
        $this->UpdateFields["status_id"]["Value"] = $this->status_id->GetDBValue(true);
        $this->SQL = CCBuildUpdate("responses", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

//Delete Method @7-0BB5A89C
    function Delete()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete", $this->Parent);
        $this->SQL = "DELETE FROM responses";
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete", $this->Parent);
        }
    }
//End Delete Method

} //End responsesDataSource Class @7-FCB6E20C

//Include Page implementation @3-58DBA1E3
include_once(RelativePath . "/Footer.php");
//End Include Page implementation

//Initialize Page @1-04FDEF10
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
$TemplateFileName = "ResponseMaint.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-4B0BB954
CCSecurityRedirect("3", "");
//End Authenticate User

//Include events file @1-911EEE22
include("./ResponseMaint_events.php");
//End Include events file

//Initialize Objects @1-F9F177A7
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$Header = & new clsHeader("", "Header", $MainPage);
$Header->Initialize();
$AdminMenu = & new clsAdminMenu("", "AdminMenu", $MainPage);
$AdminMenu->Initialize();
$responses = & new clsRecordresponses("", $MainPage);
$Footer = & new clsFooter("", "Footer", $MainPage);
$Footer->Initialize();
$MainPage->Header = & $Header;
$MainPage->AdminMenu = & $AdminMenu;
$MainPage->responses = & $responses;
$MainPage->Footer = & $Footer;
$responses->Initialize();

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

//Execute Components @1-764C99ED
$Header->Operations();
$AdminMenu->Operations();
$responses->Operation();
$Footer->Operations();
//End Execute Components

//Go to destination page @1-013C0312
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    $Header->Class_Terminate();
    unset($Header);
    $AdminMenu->Class_Terminate();
    unset($AdminMenu);
    unset($responses);
    $Footer->Class_Terminate();
    unset($Footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-6100701C
$Header->Show();
$AdminMenu->Show();
$responses->Show();
$Footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-2EBF33A8
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
$Header->Class_Terminate();
unset($Header);
$AdminMenu->Class_Terminate();
unset($AdminMenu);
unset($responses);
$Footer->Class_Terminate();
unset($Footer);
unset($Tpl);
//End Unload Page


?>
