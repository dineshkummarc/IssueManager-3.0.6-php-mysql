<?php
//Include Common Files @1-ECEFBE9A
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "IssueMaint.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @28-3DD2EFDC
include_once(RelativePath . "/Header.php");
//End Include Page implementation

//Include Page implementation @93-39F846CD
include_once(RelativePath . "/AdminMenu.php");
//End Include Page implementation

class clsRecordissues { //issues Class @2-CA3823B4

//Variables @2-0DF9B1C2

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

//Class_Initialize Event @2-C30FF361
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
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "issues";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->issue_id = & new clsControl(ccsLabel, "issue_id", "issue_id", ccsText, "", CCGetRequestParam("issue_id", $Method, NULL), $this);
            $this->issue_name = & new clsControl(ccsTextBox, "issue_name", $CCSLocales->GetText("im_issue_name"), ccsText, "", CCGetRequestParam("issue_name", $Method, NULL), $this);
            $this->issue_name->Required = true;
            $this->issue_desc = & new clsControl(ccsTextArea, "issue_desc", $CCSLocales->GetText("im_issue_description"), ccsMemo, "", CCGetRequestParam("issue_desc", $Method, NULL), $this);
            $this->issue_desc->Required = true;
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
            $this->modified_by = & new clsControl(ccsListBox, "modified_by", $CCSLocales->GetText("im_modified_by"), ccsInteger, "", CCGetRequestParam("modified_by", $Method, NULL), $this);
            $this->modified_by->DSType = dsTable;
            list($this->modified_by->BoundColumn, $this->modified_by->TextColumn, $this->modified_by->DBFormat) = array("user_id", "user_name", "");
            $this->modified_by->DataSource = new clsDBIM();
            $this->modified_by->ds = & $this->modified_by->DataSource;
            $this->modified_by->DataSource->SQL = "SELECT * \n" .
"FROM users {SQL_Where} {SQL_OrderBy}";
            $this->modified_by->DataSource->Order = "user_name";
            $this->modified_by->DataSource->Order = "user_name";
            $this->modified_by->Required = true;
            $this->date_submitted = & new clsControl(ccsTextBox, "date_submitted", $CCSLocales->GetText("im_date_submitted"), ccsDate, array("GeneralDate"), CCGetRequestParam("date_submitted", $Method, NULL), $this);
            $this->date_submitted->Required = true;
            $this->date_format = & new clsControl(ccsLabel, "date_format", "date_format", ccsText, "", CCGetRequestParam("date_format", $Method, NULL), $this);
            $this->version = & new clsControl(ccsTextBox, "version", $CCSLocales->GetText("im_version"), ccsText, "", CCGetRequestParam("version", $Method, NULL), $this);
            $this->tested = & new clsControl(ccsCheckBox, "tested", "Tested", ccsInteger, "", CCGetRequestParam("tested", $Method, NULL), $this);
            $this->tested->CheckedValue = $this->tested->GetParsedValue(1);
            $this->tested->UncheckedValue = $this->tested->GetParsedValue(0);
            $this->approved = & new clsControl(ccsCheckBox, "approved", "Approved", ccsInteger, "", CCGetRequestParam("approved", $Method, NULL), $this);
            $this->approved->CheckedValue = $this->approved->GetParsedValue(1);
            $this->approved->UncheckedValue = $this->approved->GetParsedValue(0);
            $this->assigned_to_orig = & new clsControl(ccsListBox, "assigned_to_orig", $CCSLocales->GetText("im_assigned_to_orig"), ccsInteger, "", CCGetRequestParam("assigned_to_orig", $Method, NULL), $this);
            $this->assigned_to_orig->DSType = dsTable;
            list($this->assigned_to_orig->BoundColumn, $this->assigned_to_orig->TextColumn, $this->assigned_to_orig->DBFormat) = array("user_id", "user_name", "");
            $this->assigned_to_orig->DataSource = new clsDBIM();
            $this->assigned_to_orig->ds = & $this->assigned_to_orig->DataSource;
            $this->assigned_to_orig->DataSource->SQL = "SELECT * \n" .
"FROM users {SQL_Where} {SQL_OrderBy}";
            $this->assigned_to_orig->DataSource->Order = "user_name";
            $this->assigned_to_orig->DataSource->Order = "user_name";
            $this->assigned_to_orig->Required = true;
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
            $this->status_id = & new clsControl(ccsListBox, "status_id", $CCSLocales->GetText("im_status"), ccsInteger, "", CCGetRequestParam("status_id", $Method, NULL), $this);
            $this->status_id->DSType = dsTable;
            list($this->status_id->BoundColumn, $this->status_id->TextColumn, $this->status_id->DBFormat) = array("status_id", "status", "");
            $this->status_id->DataSource = new clsDBIM();
            $this->status_id->ds = & $this->status_id->DataSource;
            $this->status_id->DataSource->SQL = "SELECT * \n" .
"FROM statuses {SQL_Where} {SQL_OrderBy}";
            $this->status_id->Required = true;
            $this->priority_id = & new clsControl(ccsListBox, "priority_id", $CCSLocales->GetText("im_priority"), ccsInteger, "", CCGetRequestParam("priority_id", $Method, NULL), $this);
            $this->priority_id->DSType = dsTable;
            list($this->priority_id->BoundColumn, $this->priority_id->TextColumn, $this->priority_id->DBFormat) = array("priority_id", "priority_desc", "");
            $this->priority_id->DataSource = new clsDBIM();
            $this->priority_id->ds = & $this->priority_id->DataSource;
            $this->priority_id->DataSource->SQL = "SELECT * \n" .
"FROM priorities {SQL_Where} {SQL_OrderBy}";
            $this->priority_id->Required = true;
            $this->Insert = & new clsButton("Insert", $Method, $this);
            $this->Update = & new clsButton("Update", $Method, $this);
            $this->Delete = & new clsButton("Delete", $Method, $this);
            $this->Cancel = & new clsButton("Cancel", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @2-6C7B8AC5
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urlissue_id"] = CCGetFromGet("issue_id", NULL);
    }
//End Initialize Method

//Validate Method @2-9016BE03
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->issue_name->Validate() && $Validation);
        $Validation = ($this->issue_desc->Validate() && $Validation);
        $Validation = ($this->user_id->Validate() && $Validation);
        $Validation = ($this->modified_by->Validate() && $Validation);
        $Validation = ($this->date_submitted->Validate() && $Validation);
        $Validation = ($this->version->Validate() && $Validation);
        $Validation = ($this->tested->Validate() && $Validation);
        $Validation = ($this->approved->Validate() && $Validation);
        $Validation = ($this->assigned_to_orig->Validate() && $Validation);
        $Validation = ($this->assigned_to->Validate() && $Validation);
        $Validation = ($this->status_id->Validate() && $Validation);
        $Validation = ($this->priority_id->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->issue_name->Errors->Count() == 0);
        $Validation =  $Validation && ($this->issue_desc->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->modified_by->Errors->Count() == 0);
        $Validation =  $Validation && ($this->date_submitted->Errors->Count() == 0);
        $Validation =  $Validation && ($this->version->Errors->Count() == 0);
        $Validation =  $Validation && ($this->tested->Errors->Count() == 0);
        $Validation =  $Validation && ($this->approved->Errors->Count() == 0);
        $Validation =  $Validation && ($this->assigned_to_orig->Errors->Count() == 0);
        $Validation =  $Validation && ($this->assigned_to->Errors->Count() == 0);
        $Validation =  $Validation && ($this->status_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->priority_id->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @2-3D0E3036
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->issue_id->Errors->Count());
        $errors = ($errors || $this->issue_name->Errors->Count());
        $errors = ($errors || $this->issue_desc->Errors->Count());
        $errors = ($errors || $this->user_id->Errors->Count());
        $errors = ($errors || $this->modified_by->Errors->Count());
        $errors = ($errors || $this->date_submitted->Errors->Count());
        $errors = ($errors || $this->date_format->Errors->Count());
        $errors = ($errors || $this->version->Errors->Count());
        $errors = ($errors || $this->tested->Errors->Count());
        $errors = ($errors || $this->approved->Errors->Count());
        $errors = ($errors || $this->assigned_to_orig->Errors->Count());
        $errors = ($errors || $this->assigned_to->Errors->Count());
        $errors = ($errors || $this->status_id->Errors->Count());
        $errors = ($errors || $this->priority_id->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @2-06A42C6A
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
        $Redirect = "IssueList.php" . "?" . CCGetQueryString("QueryString", array("ccsForm", "issue_id"));
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

//InsertRow Method @2-8315EA0E
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert", $this);
        if(!$this->InsertAllowed) return false;
        $this->DataSource->issue_id->SetValue($this->issue_id->GetValue(true));
        $this->DataSource->issue_name->SetValue($this->issue_name->GetValue(true));
        $this->DataSource->issue_desc->SetValue($this->issue_desc->GetValue(true));
        $this->DataSource->user_id->SetValue($this->user_id->GetValue(true));
        $this->DataSource->modified_by->SetValue($this->modified_by->GetValue(true));
        $this->DataSource->date_submitted->SetValue($this->date_submitted->GetValue(true));
        $this->DataSource->date_format->SetValue($this->date_format->GetValue(true));
        $this->DataSource->version->SetValue($this->version->GetValue(true));
        $this->DataSource->tested->SetValue($this->tested->GetValue(true));
        $this->DataSource->approved->SetValue($this->approved->GetValue(true));
        $this->DataSource->assigned_to_orig->SetValue($this->assigned_to_orig->GetValue(true));
        $this->DataSource->assigned_to->SetValue($this->assigned_to->GetValue(true));
        $this->DataSource->status_id->SetValue($this->status_id->GetValue(true));
        $this->DataSource->priority_id->SetValue($this->priority_id->GetValue(true));
        $this->DataSource->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert", $this);
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//UpdateRow Method @2-8CD987AF
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->issue_id->SetValue($this->issue_id->GetValue(true));
        $this->DataSource->issue_name->SetValue($this->issue_name->GetValue(true));
        $this->DataSource->issue_desc->SetValue($this->issue_desc->GetValue(true));
        $this->DataSource->user_id->SetValue($this->user_id->GetValue(true));
        $this->DataSource->modified_by->SetValue($this->modified_by->GetValue(true));
        $this->DataSource->date_submitted->SetValue($this->date_submitted->GetValue(true));
        $this->DataSource->date_format->SetValue($this->date_format->GetValue(true));
        $this->DataSource->version->SetValue($this->version->GetValue(true));
        $this->DataSource->tested->SetValue($this->tested->GetValue(true));
        $this->DataSource->approved->SetValue($this->approved->GetValue(true));
        $this->DataSource->assigned_to_orig->SetValue($this->assigned_to_orig->GetValue(true));
        $this->DataSource->assigned_to->SetValue($this->assigned_to->GetValue(true));
        $this->DataSource->status_id->SetValue($this->status_id->GetValue(true));
        $this->DataSource->priority_id->SetValue($this->priority_id->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//DeleteRow Method @2-299D98C3
    function DeleteRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDelete", $this);
        if(!$this->DeleteAllowed) return false;
        $this->DataSource->Delete();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDelete", $this);
        return (!$this->CheckErrors());
    }
//End DeleteRow Method

//Show Method @2-C799B7E7
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
        $this->modified_by->Prepare();
        $this->assigned_to_orig->Prepare();
        $this->assigned_to->Prepare();
        $this->status_id->Prepare();
        $this->priority_id->Prepare();

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
                $this->issue_id->SetValue($this->DataSource->issue_id->GetValue());
                if(!$this->FormSubmitted){
                    $this->issue_name->SetValue($this->DataSource->issue_name->GetValue());
                    $this->issue_desc->SetValue($this->DataSource->issue_desc->GetValue());
                    $this->user_id->SetValue($this->DataSource->user_id->GetValue());
                    $this->modified_by->SetValue($this->DataSource->modified_by->GetValue());
                    $this->date_submitted->SetValue($this->DataSource->date_submitted->GetValue());
                    $this->version->SetValue($this->DataSource->version->GetValue());
                    $this->tested->SetValue($this->DataSource->tested->GetValue());
                    $this->approved->SetValue($this->DataSource->approved->GetValue());
                    $this->assigned_to_orig->SetValue($this->DataSource->assigned_to_orig->GetValue());
                    $this->assigned_to->SetValue($this->DataSource->assigned_to->GetValue());
                    $this->status_id->SetValue($this->DataSource->status_id->GetValue());
                    $this->priority_id->SetValue($this->DataSource->priority_id->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->issue_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->issue_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->issue_desc->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->modified_by->Errors->ToString());
            $Error = ComposeStrings($Error, $this->date_submitted->Errors->ToString());
            $Error = ComposeStrings($Error, $this->date_format->Errors->ToString());
            $Error = ComposeStrings($Error, $this->version->Errors->ToString());
            $Error = ComposeStrings($Error, $this->tested->Errors->ToString());
            $Error = ComposeStrings($Error, $this->approved->Errors->ToString());
            $Error = ComposeStrings($Error, $this->assigned_to_orig->Errors->ToString());
            $Error = ComposeStrings($Error, $this->assigned_to->Errors->ToString());
            $Error = ComposeStrings($Error, $this->status_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->priority_id->Errors->ToString());
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

        $this->issue_id->Show();
        $this->issue_name->Show();
        $this->issue_desc->Show();
        $this->user_id->Show();
        $this->modified_by->Show();
        $this->date_submitted->Show();
        $this->date_format->Show();
        $this->version->Show();
        $this->tested->Show();
        $this->approved->Show();
        $this->assigned_to_orig->Show();
        $this->assigned_to->Show();
        $this->status_id->Show();
        $this->priority_id->Show();
        $this->Insert->Show();
        $this->Update->Show();
        $this->Delete->Show();
        $this->Cancel->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End issues Class @2-FCB6E20C

class clsissuesDataSource extends clsDBIM {  //issuesDataSource Class @2-FEEDA2F4

//DataSource Variables @2-0FD85AD2
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
    var $issue_id;
    var $issue_name;
    var $issue_desc;
    var $user_id;
    var $modified_by;
    var $date_submitted;
    var $date_format;
    var $version;
    var $tested;
    var $approved;
    var $assigned_to_orig;
    var $assigned_to;
    var $status_id;
    var $priority_id;
//End DataSource Variables

//DataSourceClass_Initialize Event @2-1440A378
    function clsissuesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record issues/Error";
        $this->Initialize();
        $this->issue_id = new clsField("issue_id", ccsText, "");
        $this->issue_name = new clsField("issue_name", ccsText, "");
        $this->issue_desc = new clsField("issue_desc", ccsMemo, "");
        $this->user_id = new clsField("user_id", ccsInteger, "");
        $this->modified_by = new clsField("modified_by", ccsInteger, "");
        $this->date_submitted = new clsField("date_submitted", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->date_format = new clsField("date_format", ccsText, "");
        $this->version = new clsField("version", ccsText, "");
        $this->tested = new clsField("tested", ccsInteger, "");
        $this->approved = new clsField("approved", ccsInteger, "");
        $this->assigned_to_orig = new clsField("assigned_to_orig", ccsInteger, "");
        $this->assigned_to = new clsField("assigned_to", ccsInteger, "");
        $this->status_id = new clsField("status_id", ccsInteger, "");
        $this->priority_id = new clsField("priority_id", ccsInteger, "");

        $this->InsertFields["issue_name"] = array("Name" => "issue_name", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["issue_desc"] = array("Name" => "issue_desc", "Value" => "", "DataType" => ccsMemo);
        $this->InsertFields["user_id"] = array("Name" => "user_id", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["modified_by"] = array("Name" => "modified_by", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["date_submitted"] = array("Name" => "date_submitted", "Value" => "", "DataType" => ccsDate);
        $this->InsertFields["version"] = array("Name" => "version", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["tested"] = array("Name" => "tested", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["approved"] = array("Name" => "approved", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["assigned_to_orig"] = array("Name" => "assigned_to_orig", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["assigned_to"] = array("Name" => "assigned_to", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["status_id"] = array("Name" => "status_id", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["priority_id"] = array("Name" => "priority_id", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["issue_name"] = array("Name" => "issue_name", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["issue_desc"] = array("Name" => "issue_desc", "Value" => "", "DataType" => ccsMemo);
        $this->UpdateFields["user_id"] = array("Name" => "user_id", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["modified_by"] = array("Name" => "modified_by", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["date_submitted"] = array("Name" => "date_submitted", "Value" => "", "DataType" => ccsDate);
        $this->UpdateFields["version"] = array("Name" => "version", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["tested"] = array("Name" => "tested", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["approved"] = array("Name" => "approved", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["assigned_to_orig"] = array("Name" => "assigned_to_orig", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["assigned_to"] = array("Name" => "assigned_to", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["status_id"] = array("Name" => "status_id", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["priority_id"] = array("Name" => "priority_id", "Value" => "", "DataType" => ccsInteger);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @2-B8A68F25
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

//Open Method @2-1FE97867
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

//SetValues Method @2-E8413E95
    function SetValues()
    {
        $this->issue_id->SetDBValue($this->f("issue_id"));
        $this->issue_name->SetDBValue($this->f("issue_name"));
        $this->issue_desc->SetDBValue($this->f("issue_desc"));
        $this->user_id->SetDBValue(trim($this->f("user_id")));
        $this->modified_by->SetDBValue(trim($this->f("modified_by")));
        $this->date_submitted->SetDBValue(trim($this->f("date_submitted")));
        $this->version->SetDBValue($this->f("version"));
        $this->tested->SetDBValue(trim($this->f("tested")));
        $this->approved->SetDBValue(trim($this->f("approved")));
        $this->assigned_to_orig->SetDBValue(trim($this->f("assigned_to_orig")));
        $this->assigned_to->SetDBValue(trim($this->f("assigned_to")));
        $this->status_id->SetDBValue(trim($this->f("status_id")));
        $this->priority_id->SetDBValue(trim($this->f("priority_id")));
    }
//End SetValues Method

//Insert Method @2-F2EA9F1F
    function Insert()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert", $this->Parent);
        $this->InsertFields["issue_name"]["Value"] = $this->issue_name->GetDBValue(true);
        $this->InsertFields["issue_desc"]["Value"] = $this->issue_desc->GetDBValue(true);
        $this->InsertFields["user_id"]["Value"] = $this->user_id->GetDBValue(true);
        $this->InsertFields["modified_by"]["Value"] = $this->modified_by->GetDBValue(true);
        $this->InsertFields["date_submitted"]["Value"] = $this->date_submitted->GetDBValue(true);
        $this->InsertFields["version"]["Value"] = $this->version->GetDBValue(true);
        $this->InsertFields["tested"]["Value"] = $this->tested->GetDBValue(true);
        $this->InsertFields["approved"]["Value"] = $this->approved->GetDBValue(true);
        $this->InsertFields["assigned_to_orig"]["Value"] = $this->assigned_to_orig->GetDBValue(true);
        $this->InsertFields["assigned_to"]["Value"] = $this->assigned_to->GetDBValue(true);
        $this->InsertFields["status_id"]["Value"] = $this->status_id->GetDBValue(true);
        $this->InsertFields["priority_id"]["Value"] = $this->priority_id->GetDBValue(true);
        $this->SQL = CCBuildInsert("issues", $this->InsertFields, $this);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert", $this->Parent);
        }
    }
//End Insert Method

//Update Method @2-A06F4DBB
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        $this->UpdateFields["issue_name"]["Value"] = $this->issue_name->GetDBValue(true);
        $this->UpdateFields["issue_desc"]["Value"] = $this->issue_desc->GetDBValue(true);
        $this->UpdateFields["user_id"]["Value"] = $this->user_id->GetDBValue(true);
        $this->UpdateFields["modified_by"]["Value"] = $this->modified_by->GetDBValue(true);
        $this->UpdateFields["date_submitted"]["Value"] = $this->date_submitted->GetDBValue(true);
        $this->UpdateFields["version"]["Value"] = $this->version->GetDBValue(true);
        $this->UpdateFields["tested"]["Value"] = $this->tested->GetDBValue(true);
        $this->UpdateFields["approved"]["Value"] = $this->approved->GetDBValue(true);
        $this->UpdateFields["assigned_to_orig"]["Value"] = $this->assigned_to_orig->GetDBValue(true);
        $this->UpdateFields["assigned_to"]["Value"] = $this->assigned_to->GetDBValue(true);
        $this->UpdateFields["status_id"]["Value"] = $this->status_id->GetDBValue(true);
        $this->UpdateFields["priority_id"]["Value"] = $this->priority_id->GetDBValue(true);
        $this->SQL = CCBuildUpdate("issues", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

//Delete Method @2-4271C089
    function Delete()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete", $this->Parent);
        $this->SQL = "DELETE FROM issues";
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete", $this->Parent);
        }
    }
//End Delete Method

} //End issuesDataSource Class @2-FCB6E20C

class clsGridfiles { //files class @30-C7C462F0

//Variables @30-1B46F1E6

    // Public variables
    var $ComponentType = "Grid";
    var $ComponentName;
    var $Visible;
    var $Errors;
    var $ErrorBlock;
    var $ds;
    var $DataSource;
    var $PageSize;
    var $IsEmpty;
    var $SorterName = "";
    var $SorterDirection = "";
    var $PageNumber;
    var $RowNumber;
    var $ControlsVisible = array();

    var $CCSEvents = "";
    var $CCSEventResult;

    var $RelativePath = "";

    // Grid Controls
    var $StaticControls;
    var $RowControls;
    var $Sorter_file_name;
    var $Sorter_uploaded_by;
    var $Sorter_date_uploaded;
//End Variables

//Class_Initialize Event @30-13D72332
    function clsGridfiles($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "files";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid files";
        $this->DataSource = new clsfilesDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 10;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0) $this->PageNumber = 1;
        $this->SorterName = CCGetParam("filesOrder", "");
        $this->SorterDirection = CCGetParam("filesDir", "");

        $this->file_name = & new clsControl(ccsLink, "file_name", "file_name", ccsText, "", CCGetRequestParam("file_name", ccsGet, NULL), $this);
        $this->file_name->Page = "FileMaint.php";
        $this->uploaded_by = & new clsControl(ccsLabel, "uploaded_by", "uploaded_by", ccsText, "", CCGetRequestParam("uploaded_by", ccsGet, NULL), $this);
        $this->date_uploaded = & new clsControl(ccsLabel, "date_uploaded", "date_uploaded", ccsDate, array("GeneralDate"), CCGetRequestParam("date_uploaded", ccsGet, NULL), $this);
        $this->Sorter_file_name = & new clsSorter($this->ComponentName, "Sorter_file_name", $FileName, $this);
        $this->Sorter_uploaded_by = & new clsSorter($this->ComponentName, "Sorter_uploaded_by", $FileName, $this);
        $this->Sorter_date_uploaded = & new clsSorter($this->ComponentName, "Sorter_date_uploaded", $FileName, $this);
        $this->Navigator = & new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpCentered, $this);
    }
//End Class_Initialize Event

//Initialize Method @30-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @30-404519FA
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["urlissue_id"] = CCGetFromGet("issue_id", NULL);

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);


        $this->DataSource->Prepare();
        $this->DataSource->Open();
        $this->IsEmpty = ! $this->DataSource->next_record();

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) return;

        $GridBlock = "Grid " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $GridBlock;


        if (!$this->IsEmpty) {
            $this->IsEmpty = false;
            $this->ControlsVisible["file_name"] = $this->file_name->Visible;
            $this->ControlsVisible["uploaded_by"] = $this->uploaded_by->Visible;
            $this->ControlsVisible["date_uploaded"] = $this->date_uploaded->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->file_name->SetValue($this->DataSource->file_name->GetValue());
                $this->file_name->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->file_name->Parameters = CCAddParam($this->file_name->Parameters, "file_id", $this->DataSource->f("file_id"));
                $this->uploaded_by->SetValue($this->DataSource->uploaded_by->GetValue());
                $this->date_uploaded->SetValue($this->DataSource->date_uploaded->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->file_name->Show();
                $this->uploaded_by->Show();
                $this->date_uploaded->Show();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                $Tpl->parse("Row", true);
            } while (($this->RowNumber < $this->PageSize) && $this->DataSource->next_record());
        }
        else // Show NoRecords block if no records are found
        {
            $this->IsEmpty = true;
            $Tpl->parse("NoRecords", false);
        }

        $errors = $this->GetErrors();
        if(strlen($errors))
        {
            $Tpl->replaceblock("", $errors);
            $Tpl->block_path = $ParentPath;
            return;
        }
        $this->Navigator->PageNumber = $this->DataSource->AbsolutePage;
        if ($this->DataSource->RecordsCount == "CCS not counted")
            $this->Navigator->TotalPages = $this->DataSource->AbsolutePage + ($this->DataSource->next_record() ? 1 : 0);
        else
            $this->Navigator->TotalPages = $this->DataSource->PageCount();
        $this->Sorter_file_name->Show();
        $this->Sorter_uploaded_by->Show();
        $this->Sorter_date_uploaded->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @30-96E21992
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->file_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->uploaded_by->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_uploaded->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End files Class @30-FCB6E20C

class clsfilesDataSource extends clsDBIM {  //filesDataSource Class @30-50AE40EA

//DataSource Variables @30-2B86F4D4
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $file_name;
    var $uploaded_by;
    var $date_uploaded;
//End DataSource Variables

//DataSourceClass_Initialize Event @30-DB87FCA2
    function clsfilesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid files";
        $this->Initialize();
        $this->file_name = new clsField("file_name", ccsText, "");
        $this->uploaded_by = new clsField("uploaded_by", ccsText, "");
        $this->date_uploaded = new clsField("date_uploaded", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @30-FA763628
    function SetOrder($SorterName, $SorterDirection)
    {
    $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_file_name" => array("file_name", ""), 
            "Sorter_uploaded_by" => array("uploaded_by", ""), 
            "Sorter_date_uploaded" => array("date_uploaded", "")));
    }
//End SetOrder Method

//Prepare Method @30-C55515ED
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlissue_id", ccsInteger, "", "", $this->Parameters["urlissue_id"], -1, false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "files.issue_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @30-32E5627A
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM files LEFT JOIN users ON\n\n" .
        "files.uploaded_by = users.user_id";
        $this->SQL = "SELECT files.*, user_name \n\n" .
        "FROM files LEFT JOIN users ON\n\n" .
        "files.uploaded_by = users.user_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @30-C993B170
    function SetValues()
    {
        $this->file_name->SetDBValue($this->f("file_name"));
        $this->uploaded_by->SetDBValue($this->f("user_name"));
        $this->date_uploaded->SetDBValue(trim($this->f("date_uploaded")));
    }
//End SetValues Method

} //End filesDataSource Class @30-FCB6E20C

class clsGridresponses { //responses class @20-36AC8FEE

//Variables @20-663D5B8C

    // Public variables
    var $ComponentType = "Grid";
    var $ComponentName;
    var $Visible;
    var $Errors;
    var $ErrorBlock;
    var $ds;
    var $DataSource;
    var $PageSize;
    var $IsEmpty;
    var $SorterName = "";
    var $SorterDirection = "";
    var $PageNumber;
    var $RowNumber;
    var $ControlsVisible = array();

    var $CCSEvents = "";
    var $CCSEventResult;

    var $RelativePath = "";

    // Grid Controls
    var $StaticControls;
    var $RowControls;
//End Variables

//Class_Initialize Event @20-51ECB20A
    function clsGridresponses($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "responses";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid responses";
        $this->DataSource = new clsresponsesDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 5;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0) $this->PageNumber = 1;

        $this->user_id = & new clsControl(ccsLabel, "user_id", "user_id", ccsText, "", CCGetRequestParam("user_id", ccsGet, NULL), $this);
        $this->date_response = & new clsControl(ccsLabel, "date_response", "date_response", ccsDate, array("GeneralDate"), CCGetRequestParam("date_response", ccsGet, NULL), $this);
        $this->response = & new clsControl(ccsLabel, "response", "response", ccsMemo, "", CCGetRequestParam("response", ccsGet, NULL), $this);
        $this->assigned_to = & new clsControl(ccsLabel, "assigned_to", "assigned_to", ccsText, "", CCGetRequestParam("assigned_to", ccsGet, NULL), $this);
        $this->priority_id = & new clsControl(ccsLabel, "priority_id", "priority_id", ccsText, "", CCGetRequestParam("priority_id", ccsGet, NULL), $this);
        $this->status_id = & new clsControl(ccsLabel, "status_id", "status_id", ccsText, "", CCGetRequestParam("status_id", ccsGet, NULL), $this);
        $this->Link1 = & new clsControl(ccsLink, "Link1", "Link1", ccsText, "", CCGetRequestParam("Link1", ccsGet, NULL), $this);
        $this->Link1->Page = "ResponseMaint.php";
        $this->Navigator = & new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpCentered, $this);
    }
//End Class_Initialize Event

//Initialize Method @20-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @20-284E3467
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["urlissue_id"] = CCGetFromGet("issue_id", NULL);

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);


        $this->DataSource->Prepare();
        $this->DataSource->Open();
        $this->IsEmpty = ! $this->DataSource->next_record();

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) return;

        $GridBlock = "Grid " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $GridBlock;


        if (!$this->IsEmpty) {
            $this->IsEmpty = false;
            $this->ControlsVisible["user_id"] = $this->user_id->Visible;
            $this->ControlsVisible["date_response"] = $this->date_response->Visible;
            $this->ControlsVisible["response"] = $this->response->Visible;
            $this->ControlsVisible["assigned_to"] = $this->assigned_to->Visible;
            $this->ControlsVisible["priority_id"] = $this->priority_id->Visible;
            $this->ControlsVisible["status_id"] = $this->status_id->Visible;
            $this->ControlsVisible["Link1"] = $this->Link1->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->user_id->SetValue($this->DataSource->user_id->GetValue());
                $this->date_response->SetValue($this->DataSource->date_response->GetValue());
                $this->response->SetValue($this->DataSource->response->GetValue());
                $this->assigned_to->SetValue($this->DataSource->assigned_to->GetValue());
                $this->priority_id->SetValue($this->DataSource->priority_id->GetValue());
                $this->status_id->SetValue($this->DataSource->status_id->GetValue());
                $this->Link1->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->Link1->Parameters = CCAddParam($this->Link1->Parameters, "response_id", $this->DataSource->f("response_id"));
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->user_id->Show();
                $this->date_response->Show();
                $this->response->Show();
                $this->assigned_to->Show();
                $this->priority_id->Show();
                $this->status_id->Show();
                $this->Link1->Show();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                $Tpl->parse("Row", true);
            } while (($this->RowNumber < $this->PageSize) && $this->DataSource->next_record());
        }
        else // Show NoRecords block if no records are found
        {
            $this->IsEmpty = true;
            $Tpl->parse("NoRecords", false);
        }

        $errors = $this->GetErrors();
        if(strlen($errors))
        {
            $Tpl->replaceblock("", $errors);
            $Tpl->block_path = $ParentPath;
            return;
        }
        $this->Navigator->PageNumber = $this->DataSource->AbsolutePage;
        if ($this->DataSource->RecordsCount == "CCS not counted")
            $this->Navigator->TotalPages = $this->DataSource->AbsolutePage + ($this->DataSource->next_record() ? 1 : 0);
        else
            $this->Navigator->TotalPages = $this->DataSource->PageCount();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @20-484C1BB7
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->user_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_response->Errors->ToString());
        $errors = ComposeStrings($errors, $this->response->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_to->Errors->ToString());
        $errors = ComposeStrings($errors, $this->priority_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->status_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Link1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End responses Class @20-FCB6E20C

class clsresponsesDataSource extends clsDBIM {  //responsesDataSource Class @20-5248FCCF

//DataSource Variables @20-A14641C6
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $user_id;
    var $date_response;
    var $response;
    var $assigned_to;
    var $priority_id;
    var $status_id;
//End DataSource Variables

//DataSourceClass_Initialize Event @20-549690DC
    function clsresponsesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid responses";
        $this->Initialize();
        $this->user_id = new clsField("user_id", ccsText, "");
        $this->date_response = new clsField("date_response", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->response = new clsField("response", ccsMemo, "");
        $this->assigned_to = new clsField("assigned_to", ccsText, "");
        $this->priority_id = new clsField("priority_id", ccsText, "");
        $this->status_id = new clsField("status_id", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @20-9E1383D1
    function SetOrder($SorterName, $SorterDirection)
    {
    $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @20-EA339402
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlissue_id", ccsInteger, "", "", $this->Parameters["urlissue_id"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "issue_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @20-D5D70DF0
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM (((responses LEFT JOIN statuses ON\n\n" .
        "statuses.status_id = responses.status_id) LEFT JOIN priorities ON\n\n" .
        "priorities.priority_id = responses.priority_id) LEFT JOIN users ON\n\n" .
        "users.user_id = responses.user_id) LEFT JOIN users users1 ON\n\n" .
        "users1.user_id = responses.assigned_to";
        $this->SQL = "SELECT responses.*, status, users.user_name AS users_user_name, users1.user_name AS users1_user_name, priority_desc \n\n" .
        "FROM (((responses LEFT JOIN statuses ON\n\n" .
        "statuses.status_id = responses.status_id) LEFT JOIN priorities ON\n\n" .
        "priorities.priority_id = responses.priority_id) LEFT JOIN users ON\n\n" .
        "users.user_id = responses.user_id) LEFT JOIN users users1 ON\n\n" .
        "users1.user_id = responses.assigned_to {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @20-9AE92514
    function SetValues()
    {
        $this->user_id->SetDBValue($this->f("users_user_name"));
        $this->date_response->SetDBValue(trim($this->f("date_response")));
        $this->response->SetDBValue($this->f("response"));
        $this->assigned_to->SetDBValue($this->f("users1_user_name"));
        $this->priority_id->SetDBValue($this->f("priority_desc"));
        $this->status_id->SetDBValue($this->f("status"));
    }
//End SetValues Method

} //End responsesDataSource Class @20-FCB6E20C

//Include Page implementation @29-58DBA1E3
include_once(RelativePath . "/Footer.php");
//End Include Page implementation

//Initialize Page @1-4E9099B8
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
$TemplateFileName = "IssueMaint.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-4B0BB954
CCSecurityRedirect("3", "");
//End Authenticate User

//Include events file @1-C04C1335
include("./IssueMaint_events.php");
//End Include events file

//Initialize Objects @1-F5B2A8D3
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$Header = & new clsHeader("", "Header", $MainPage);
$Header->Initialize();
$AdminMenu = & new clsAdminMenu("", "AdminMenu", $MainPage);
$AdminMenu->Initialize();
$issues = & new clsRecordissues("", $MainPage);
$files = & new clsGridfiles("", $MainPage);
$responses = & new clsGridresponses("", $MainPage);
$Footer = & new clsFooter("", "Footer", $MainPage);
$Footer->Initialize();
$MainPage->Header = & $Header;
$MainPage->AdminMenu = & $AdminMenu;
$MainPage->issues = & $issues;
$MainPage->files = & $files;
$MainPage->responses = & $responses;
$MainPage->Footer = & $Footer;
$issues->Initialize();
$files->Initialize();
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

//Execute Components @1-8AB1ECF7
$Header->Operations();
$AdminMenu->Operations();
$issues->Operation();
$Footer->Operations();
//End Execute Components

//Go to destination page @1-D820C90E
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    $Header->Class_Terminate();
    unset($Header);
    $AdminMenu->Class_Terminate();
    unset($AdminMenu);
    unset($issues);
    unset($files);
    unset($responses);
    $Footer->Class_Terminate();
    unset($Footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-DA4AC5A1
$Header->Show();
$AdminMenu->Show();
$issues->Show();
$files->Show();
$responses->Show();
$Footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-D51CF743
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
$Header->Class_Terminate();
unset($Header);
$AdminMenu->Class_Terminate();
unset($AdminMenu);
unset($issues);
unset($files);
unset($responses);
$Footer->Class_Terminate();
unset($Footer);
unset($Tpl);
//End Unload Page


?>
