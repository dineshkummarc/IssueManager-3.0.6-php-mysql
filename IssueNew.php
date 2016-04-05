<?php
//Include Common Files @1-EB2C65FB
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "IssueNew.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-3DD2EFDC
include_once(RelativePath . "/Header.php");
//End Include Page implementation

class clsRecordissues { //issues Class @4-CA3823B4

//Variables @4-0DF9B1C2

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

//Class_Initialize Event @4-399DFE9B
    function clsRecordissues($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record issues/Error";
        $this->DataSource = new clsissuesDataSource($this);
        $this->ds = & $this->DataSource;
        $this->InsertAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "issues";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "multipart/form-data";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->issue_name = & new clsControl(ccsTextBox, "issue_name", $CCSLocales->GetText("im_issue_name"), ccsText, "", CCGetRequestParam("issue_name", $Method, NULL), $this);
            $this->issue_name->Required = true;
            $this->issue_desc = & new clsControl(ccsTextArea, "issue_desc", $CCSLocales->GetText("im_issue_description"), ccsMemo, "", CCGetRequestParam("issue_desc", $Method, NULL), $this);
            $this->issue_desc->Required = true;
            $this->priority_id = & new clsControl(ccsListBox, "priority_id", $CCSLocales->GetText("im_priority"), ccsInteger, "", CCGetRequestParam("priority_id", $Method, NULL), $this);
            $this->priority_id->DSType = dsTable;
            list($this->priority_id->BoundColumn, $this->priority_id->TextColumn, $this->priority_id->DBFormat) = array("priority_id", "priority_desc", "");
            $this->priority_id->DataSource = new clsDBIM();
            $this->priority_id->ds = & $this->priority_id->DataSource;
            $this->priority_id->DataSource->SQL = "SELECT * \n" .
"FROM priorities {SQL_Where} {SQL_OrderBy}";
            $this->status_id = & new clsControl(ccsListBox, "status_id", $CCSLocales->GetText("im_status"), ccsInteger, "", CCGetRequestParam("status_id", $Method, NULL), $this);
            $this->status_id->DSType = dsTable;
            list($this->status_id->BoundColumn, $this->status_id->TextColumn, $this->status_id->DBFormat) = array("status_id", "status", "");
            $this->status_id->DataSource = new clsDBIM();
            $this->status_id->ds = & $this->status_id->DataSource;
            $this->status_id->DataSource->SQL = "SELECT * \n" .
"FROM statuses {SQL_Where} {SQL_OrderBy}";
            $this->version = & new clsControl(ccsTextBox, "version", $CCSLocales->GetText("im_version"), ccsText, "", CCGetRequestParam("version", $Method, NULL), $this);
            $this->assigned_to = & new clsControl(ccsListBox, "assigned_to", $CCSLocales->GetText("im_assigned_to"), ccsInteger, "", CCGetRequestParam("assigned_to", $Method, NULL), $this);
            $this->assigned_to->DSType = dsTable;
            list($this->assigned_to->BoundColumn, $this->assigned_to->TextColumn, $this->assigned_to->DBFormat) = array("user_id", "user_name", "");
            $this->assigned_to->DataSource = new clsDBIM();
            $this->assigned_to->ds = & $this->assigned_to->DataSource;
            $this->assigned_to->DataSource->SQL = "SELECT * \n" .
"FROM users {SQL_Where} {SQL_OrderBy}";
            $this->assigned_to->DataSource->Order = "user_name";
            $this->assigned_to->DataSource->Order = "user_name";
            $this->user_name = & new clsControl(ccsLabel, "user_name", "User Id", ccsText, "", CCGetRequestParam("user_name", $Method, NULL), $this);
            $this->date_submitted = & new clsControl(ccsLabel, "date_submitted", "Date Submitted", ccsDate, array("GeneralDate"), CCGetRequestParam("date_submitted", $Method, NULL), $this);
            $this->UploadControls = & new clsPanel("UploadControls", $this);
            $this->attachment = & new clsFileUpload("attachment", $CCSLocales->GetText("im_file"), "temp/", "uploads/", "*", "", 1000000, $this);
            $this->Insert = & new clsButton("Insert", $Method, $this);
            $this->Cancel = & new clsButton("Cancel", $Method, $this);
            $this->date_now = & new clsControl(ccsHidden, "date_now", "date_now", ccsDate, array("GeneralDate"), CCGetRequestParam("date_now", $Method, NULL), $this);
            $this->UploadControls->AddComponent("attachment", $this->attachment);
            if(!$this->FormSubmitted) {
                if(!is_array($this->priority_id->Value) && !strlen($this->priority_id->Value) && $this->priority_id->Value !== false)
                    $this->priority_id->SetText(3);
                if(!is_array($this->status_id->Value) && !strlen($this->status_id->Value) && $this->status_id->Value !== false)
                    $this->status_id->SetText(1);
                if(!is_array($this->date_now->Value) && !strlen($this->date_now->Value) && $this->date_now->Value !== false)
                    $this->date_now->SetValue(time());
            }
            if(!is_array($this->date_submitted->Value) && !strlen($this->date_submitted->Value) && $this->date_submitted->Value !== false)
                $this->date_submitted->SetValue(time());
        }
    }
//End Class_Initialize Event

//Initialize Method @4-6C7B8AC5
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urlissue_id"] = CCGetFromGet("issue_id", NULL);
    }
//End Initialize Method

//Validate Method @4-D4E274DE
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->issue_name->Validate() && $Validation);
        $Validation = ($this->issue_desc->Validate() && $Validation);
        $Validation = ($this->priority_id->Validate() && $Validation);
        $Validation = ($this->status_id->Validate() && $Validation);
        $Validation = ($this->version->Validate() && $Validation);
        $Validation = ($this->assigned_to->Validate() && $Validation);
        $Validation = ($this->attachment->Validate() && $Validation);
        $Validation = ($this->date_now->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->issue_name->Errors->Count() == 0);
        $Validation =  $Validation && ($this->issue_desc->Errors->Count() == 0);
        $Validation =  $Validation && ($this->priority_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->status_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->version->Errors->Count() == 0);
        $Validation =  $Validation && ($this->assigned_to->Errors->Count() == 0);
        $Validation =  $Validation && ($this->attachment->Errors->Count() == 0);
        $Validation =  $Validation && ($this->date_now->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @4-18975927
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->issue_name->Errors->Count());
        $errors = ($errors || $this->issue_desc->Errors->Count());
        $errors = ($errors || $this->priority_id->Errors->Count());
        $errors = ($errors || $this->status_id->Errors->Count());
        $errors = ($errors || $this->version->Errors->Count());
        $errors = ($errors || $this->assigned_to->Errors->Count());
        $errors = ($errors || $this->user_name->Errors->Count());
        $errors = ($errors || $this->date_submitted->Errors->Count());
        $errors = ($errors || $this->attachment->Errors->Count());
        $errors = ($errors || $this->date_now->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @4-8BB91E14
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

        $this->attachment->Upload();

        if($this->FormSubmitted) {
            $this->PressedButton = "Insert";
            if($this->Insert->Pressed) {
                $this->PressedButton = "Insert";
            } else if($this->Cancel->Pressed) {
                $this->PressedButton = "Cancel";
            }
        }
        $Redirect = "Default.php" . "?" . CCGetQueryString("QueryString", array("ccsForm"));
        if($this->PressedButton == "Cancel") {
            if(!CCGetEvent($this->Cancel->CCSEvents, "OnClick", $this->Cancel)) {
                $Redirect = "";
            }
        } else if($this->Validate()) {
            if($this->PressedButton == "Insert") {
                if(!CCGetEvent($this->Insert->CCSEvents, "OnClick", $this->Insert) || !$this->InsertRow()) {
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

//InsertRow Method @4-2DC17A8C
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert", $this);
        if(!$this->InsertAllowed) return false;
        $this->DataSource->issue_name->SetValue($this->issue_name->GetValue(true));
        $this->DataSource->issue_desc->SetValue($this->issue_desc->GetValue(true));
        $this->DataSource->priority_id->SetValue($this->priority_id->GetValue(true));
        $this->DataSource->status_id->SetValue($this->status_id->GetValue(true));
        $this->DataSource->version->SetValue($this->version->GetValue(true));
        $this->DataSource->assigned_to->SetValue($this->assigned_to->GetValue(true));
        $this->DataSource->assigned_to->SetValue($this->assigned_to->GetValue(true));
        $this->DataSource->date_now->SetValue($this->date_now->GetValue(true));
        $this->DataSource->date_now->SetValue($this->date_now->GetValue(true));
        $this->DataSource->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert", $this);
        if($this->DataSource->Errors->Count() == 0) {
            $this->attachment->Move();
        }
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//Show Method @4-70D4A0E1
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->priority_id->Prepare();
        $this->status_id->Prepare();
        $this->assigned_to->Prepare();

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
                    $this->issue_name->SetValue($this->DataSource->issue_name->GetValue());
                    $this->issue_desc->SetValue($this->DataSource->issue_desc->GetValue());
                    $this->priority_id->SetValue($this->DataSource->priority_id->GetValue());
                    $this->status_id->SetValue($this->DataSource->status_id->GetValue());
                    $this->version->SetValue($this->DataSource->version->GetValue());
                    $this->assigned_to->SetValue($this->DataSource->assigned_to->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }
        if (!$this->FormSubmitted) {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->issue_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->issue_desc->Errors->ToString());
            $Error = ComposeStrings($Error, $this->priority_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->status_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->version->Errors->ToString());
            $Error = ComposeStrings($Error, $this->assigned_to->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->date_submitted->Errors->ToString());
            $Error = ComposeStrings($Error, $this->attachment->Errors->ToString());
            $Error = ComposeStrings($Error, $this->date_now->Errors->ToString());
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

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->issue_name->Show();
        $this->issue_desc->Show();
        $this->priority_id->Show();
        $this->status_id->Show();
        $this->version->Show();
        $this->assigned_to->Show();
        $this->user_name->Show();
        $this->date_submitted->Show();
        $this->UploadControls->Show();
        $this->Insert->Show();
        $this->Cancel->Show();
        $this->date_now->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End issues Class @4-FCB6E20C

class clsissuesDataSource extends clsDBIM {  //issuesDataSource Class @4-FEEDA2F4

//DataSource Variables @4-86E0E166
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $InsertParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $issue_name;
    var $issue_desc;
    var $priority_id;
    var $status_id;
    var $version;
    var $assigned_to;
    var $user_name;
    var $date_submitted;
    var $attachment;
    var $date_now;
//End DataSource Variables

//DataSourceClass_Initialize Event @4-9C9FEE6D
    function clsissuesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record issues/Error";
        $this->Initialize();
        $this->issue_name = new clsField("issue_name", ccsText, "");
        $this->issue_desc = new clsField("issue_desc", ccsMemo, "");
        $this->priority_id = new clsField("priority_id", ccsInteger, "");
        $this->status_id = new clsField("status_id", ccsInteger, "");
        $this->version = new clsField("version", ccsText, "");
        $this->assigned_to = new clsField("assigned_to", ccsInteger, "");
        $this->user_name = new clsField("user_name", ccsText, "");
        $this->date_submitted = new clsField("date_submitted", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->attachment = new clsField("attachment", ccsText, "");
        $this->date_now = new clsField("date_now", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));

        $this->InsertFields["issue_name"] = array("Name" => "issue_name", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["issue_desc"] = array("Name" => "issue_desc", "Value" => "", "DataType" => ccsMemo);
        $this->InsertFields["priority_id"] = array("Name" => "priority_id", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["status_id"] = array("Name" => "status_id", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["version"] = array("Name" => "version", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["assigned_to"] = array("Name" => "assigned_to", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["assigned_to_orig"] = array("Name" => "assigned_to_orig", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["date_submitted"] = array("Name" => "date_submitted", "Value" => "", "DataType" => ccsDate);
        $this->InsertFields["user_id"] = array("Name" => "user_id", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["date_modified"] = array("Name" => "date_modified", "Value" => "", "DataType" => ccsDate);
        $this->InsertFields["modified_by"] = array("Name" => "modified_by", "Value" => "", "DataType" => ccsInteger);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @4-B8A68F25
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlissue_id", ccsInteger, "", "", $this->Parameters["urlissue_id"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "issue_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @4-1FE97867
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT * \n\n" .
        "FROM issues {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @4-B34FE7B8
    function SetValues()
    {
        $this->issue_name->SetDBValue($this->f("issue_name"));
        $this->issue_desc->SetDBValue($this->f("issue_desc"));
        $this->priority_id->SetDBValue(trim($this->f("priority_id")));
        $this->status_id->SetDBValue(trim($this->f("status_id")));
        $this->version->SetDBValue($this->f("version"));
        $this->assigned_to->SetDBValue(trim($this->f("assigned_to")));
    }
//End SetValues Method

//Insert Method @4-7C906E6C
    function Insert()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["issue_name"] = new clsSQLParameter("ctrlissue_name", ccsText, "", "", $this->issue_name->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["issue_desc"] = new clsSQLParameter("ctrlissue_desc", ccsMemo, "", "", $this->issue_desc->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["priority_id"] = new clsSQLParameter("ctrlpriority_id", ccsInteger, "", "", $this->priority_id->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["status_id"] = new clsSQLParameter("ctrlstatus_id", ccsInteger, "", "", $this->status_id->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["version"] = new clsSQLParameter("ctrlversion", ccsText, "", "", $this->version->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["assigned_to"] = new clsSQLParameter("ctrlassigned_to", ccsInteger, "", "", $this->assigned_to->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["assigned_to_orig"] = new clsSQLParameter("ctrlassigned_to", ccsInteger, "", "", $this->assigned_to->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["date_submitted"] = new clsSQLParameter("ctrldate_now", ccsDate, array("GeneralDate"), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"), $this->date_now->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_id"] = new clsSQLParameter("sesUserID", ccsInteger, "", "", CCGetSession("UserID", NULL), "", false, $this->ErrorBlock);
        $this->cp["date_modified"] = new clsSQLParameter("ctrldate_now", ccsDate, array("GeneralDate"), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"), $this->date_now->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["modified_by"] = new clsSQLParameter("sesUserID", ccsInteger, "", "", CCGetSession("UserID", NULL), "", false, $this->ErrorBlock);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert", $this->Parent);
        if (!strlen($this->cp["issue_name"]->GetText()) and !is_bool($this->cp["issue_name"]->GetValue())) 
            $this->cp["issue_name"]->SetValue($this->issue_name->GetValue(true));
        if (!strlen($this->cp["issue_desc"]->GetText()) and !is_bool($this->cp["issue_desc"]->GetValue())) 
            $this->cp["issue_desc"]->SetValue($this->issue_desc->GetValue(true));
        if (!strlen($this->cp["priority_id"]->GetText()) and !is_bool($this->cp["priority_id"]->GetValue())) 
            $this->cp["priority_id"]->SetValue($this->priority_id->GetValue(true));
        if (!strlen($this->cp["status_id"]->GetText()) and !is_bool($this->cp["status_id"]->GetValue())) 
            $this->cp["status_id"]->SetValue($this->status_id->GetValue(true));
        if (!strlen($this->cp["version"]->GetText()) and !is_bool($this->cp["version"]->GetValue())) 
            $this->cp["version"]->SetValue($this->version->GetValue(true));
        if (!strlen($this->cp["assigned_to"]->GetText()) and !is_bool($this->cp["assigned_to"]->GetValue())) 
            $this->cp["assigned_to"]->SetValue($this->assigned_to->GetValue(true));
        if (!strlen($this->cp["assigned_to_orig"]->GetText()) and !is_bool($this->cp["assigned_to_orig"]->GetValue())) 
            $this->cp["assigned_to_orig"]->SetValue($this->assigned_to->GetValue(true));
        if (!strlen($this->cp["date_submitted"]->GetText()) and !is_bool($this->cp["date_submitted"]->GetValue())) 
            $this->cp["date_submitted"]->SetValue($this->date_now->GetValue(true));
        if (!strlen($this->cp["user_id"]->GetText()) and !is_bool($this->cp["user_id"]->GetValue())) 
            $this->cp["user_id"]->SetValue(CCGetSession("UserID", NULL));
        if (!strlen($this->cp["date_modified"]->GetText()) and !is_bool($this->cp["date_modified"]->GetValue())) 
            $this->cp["date_modified"]->SetValue($this->date_now->GetValue(true));
        if (!strlen($this->cp["modified_by"]->GetText()) and !is_bool($this->cp["modified_by"]->GetValue())) 
            $this->cp["modified_by"]->SetValue(CCGetSession("UserID", NULL));
        $this->InsertFields["issue_name"]["Value"] = $this->cp["issue_name"]->GetDBValue(true);
        $this->InsertFields["issue_desc"]["Value"] = $this->cp["issue_desc"]->GetDBValue(true);
        $this->InsertFields["priority_id"]["Value"] = $this->cp["priority_id"]->GetDBValue(true);
        $this->InsertFields["status_id"]["Value"] = $this->cp["status_id"]->GetDBValue(true);
        $this->InsertFields["version"]["Value"] = $this->cp["version"]->GetDBValue(true);
        $this->InsertFields["assigned_to"]["Value"] = $this->cp["assigned_to"]->GetDBValue(true);
        $this->InsertFields["assigned_to_orig"]["Value"] = $this->cp["assigned_to_orig"]->GetDBValue(true);
        $this->InsertFields["date_submitted"]["Value"] = $this->cp["date_submitted"]->GetDBValue(true);
        $this->InsertFields["user_id"]["Value"] = $this->cp["user_id"]->GetDBValue(true);
        $this->InsertFields["date_modified"]["Value"] = $this->cp["date_modified"]->GetDBValue(true);
        $this->InsertFields["modified_by"]["Value"] = $this->cp["modified_by"]->GetDBValue(true);
        $this->SQL = CCBuildInsert("issues", $this->InsertFields, $this);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert", $this->Parent);
        }
    }
//End Insert Method

} //End issuesDataSource Class @4-FCB6E20C

//Include Page implementation @3-58DBA1E3
include_once(RelativePath . "/Footer.php");
//End Include Page implementation

//Initialize Page @1-05BB7611
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
$TemplateFileName = "IssueNew.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-946ECC7A
CCSecurityRedirect("1;2;3", "");
//End Authenticate User

//Include events file @1-9BD64AFF
include("./IssueNew_events.php");
//End Include events file

//Initialize Objects @1-A5739BEC
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$Header = & new clsHeader("", "Header", $MainPage);
$Header->Initialize();
$issues = & new clsRecordissues("", $MainPage);
$Footer = & new clsFooter("", "Footer", $MainPage);
$Footer->Initialize();
$MainPage->Header = & $Header;
$MainPage->issues = & $issues;
$MainPage->Footer = & $Footer;
$issues->Initialize();

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

//Execute Components @1-F0B67EA3
$Header->Operations();
$issues->Operation();
$Footer->Operations();
//End Execute Components

//Go to destination page @1-9E757961
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    $Header->Class_Terminate();
    unset($Header);
    unset($issues);
    $Footer->Class_Terminate();
    unset($Footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-E7C67BC9
$Header->Show();
$issues->Show();
$Footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-4C7B5A31
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
$Header->Class_Terminate();
unset($Header);
unset($issues);
$Footer->Class_Terminate();
unset($Footer);
unset($Tpl);
//End Unload Page


?>
