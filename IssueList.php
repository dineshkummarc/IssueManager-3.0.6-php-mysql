<?php
//Include Common Files @1-45E83AFA
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "IssueList.php");
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

class clsRecordissuesSearch { //issuesSearch Class @4-FE328556

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

//Class_Initialize Event @4-A1EB1255
    function clsRecordissuesSearch($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record issuesSearch/Error";
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "issuesSearch";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->s_issue_name = & new clsControl(ccsTextBox, "s_issue_name", $CCSLocales->GetText("im_keyword"), ccsText, "", CCGetRequestParam("s_issue_name", $Method, NULL), $this);
            $this->s_priority_id = & new clsControl(ccsListBox, "s_priority_id", $CCSLocales->GetText("im_priority"), ccsInteger, "", CCGetRequestParam("s_priority_id", $Method, NULL), $this);
            $this->s_priority_id->DSType = dsTable;
            list($this->s_priority_id->BoundColumn, $this->s_priority_id->TextColumn, $this->s_priority_id->DBFormat) = array("priority_id", "priority_desc", "");
            $this->s_priority_id->DataSource = new clsDBIM();
            $this->s_priority_id->ds = & $this->s_priority_id->DataSource;
            $this->s_priority_id->DataSource->SQL = "SELECT * \n" .
"FROM priorities {SQL_Where} {SQL_OrderBy}";
            $this->s_status_id = & new clsControl(ccsListBox, "s_status_id", $CCSLocales->GetText("im_status"), ccsInteger, "", CCGetRequestParam("s_status_id", $Method, NULL), $this);
            $this->s_status_id->DSType = dsTable;
            list($this->s_status_id->BoundColumn, $this->s_status_id->TextColumn, $this->s_status_id->DBFormat) = array("status_id", "status", "");
            $this->s_status_id->DataSource = new clsDBIM();
            $this->s_status_id->ds = & $this->s_status_id->DataSource;
            $this->s_status_id->DataSource->SQL = "SELECT * \n" .
"FROM statuses {SQL_Where} {SQL_OrderBy}";
            $this->s_notstatus_id = & new clsControl(ccsListBox, "s_notstatus_id", $CCSLocales->GetText("im_status_is_not"), ccsText, "", CCGetRequestParam("s_notstatus_id", $Method, NULL), $this);
            $this->s_notstatus_id->DSType = dsTable;
            list($this->s_notstatus_id->BoundColumn, $this->s_notstatus_id->TextColumn, $this->s_notstatus_id->DBFormat) = array("status_id", "status", "");
            $this->s_notstatus_id->DataSource = new clsDBIM();
            $this->s_notstatus_id->ds = & $this->s_notstatus_id->DataSource;
            $this->s_notstatus_id->DataSource->SQL = "SELECT * \n" .
"FROM statuses {SQL_Where} {SQL_OrderBy}";
            $this->s_assigned_to = & new clsControl(ccsListBox, "s_assigned_to", $CCSLocales->GetText("im_assigned_to"), ccsInteger, "", CCGetRequestParam("s_assigned_to", $Method, NULL), $this);
            $this->s_assigned_to->DSType = dsTable;
            list($this->s_assigned_to->BoundColumn, $this->s_assigned_to->TextColumn, $this->s_assigned_to->DBFormat) = array("user_id", "user_name", "");
            $this->s_assigned_to->DataSource = new clsDBIM();
            $this->s_assigned_to->ds = & $this->s_assigned_to->DataSource;
            $this->s_assigned_to->DataSource->SQL = "SELECT * \n" .
"FROM users {SQL_Where} {SQL_OrderBy}";
            $this->s_assigned_to->DataSource->Order = "user_name";
            $this->s_assigned_to->DataSource->Order = "user_name";
            $this->DoSearch = & new clsButton("DoSearch", $Method, $this);
        }
    }
//End Class_Initialize Event

//Validate Method @4-F85AFDAB
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->s_issue_name->Validate() && $Validation);
        $Validation = ($this->s_priority_id->Validate() && $Validation);
        $Validation = ($this->s_status_id->Validate() && $Validation);
        $Validation = ($this->s_notstatus_id->Validate() && $Validation);
        $Validation = ($this->s_assigned_to->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->s_issue_name->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_priority_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_status_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_notstatus_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_assigned_to->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @4-0B5B4693
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->s_issue_name->Errors->Count());
        $errors = ($errors || $this->s_priority_id->Errors->Count());
        $errors = ($errors || $this->s_status_id->Errors->Count());
        $errors = ($errors || $this->s_notstatus_id->Errors->Count());
        $errors = ($errors || $this->s_assigned_to->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @4-FD24C723
    function Operation()
    {
        if(!$this->Visible)
            return;

        global $Redirect;
        global $FileName;

        if(!$this->FormSubmitted) {
            return;
        }

        if($this->FormSubmitted) {
            $this->PressedButton = "DoSearch";
            if($this->DoSearch->Pressed) {
                $this->PressedButton = "DoSearch";
            }
        }
        $Redirect = "IssueList.php" . "?" . CCGetQueryString("Form", array("ccsForm"));
        if($this->Validate()) {
            if($this->PressedButton == "DoSearch") {
                $Redirect = "IssueList.php" . "?" . CCMergeQueryStrings(CCGetQueryString("Form", array("DoSearch", "DoSearch_x", "DoSearch_y")));
                if(!CCGetEvent($this->DoSearch->CCSEvents, "OnClick", $this->DoSearch)) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//Show Method @4-15236481
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->s_priority_id->Prepare();
        $this->s_status_id->Prepare();
        $this->s_notstatus_id->Prepare();
        $this->s_assigned_to->Prepare();

        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        $this->EditMode = $this->EditMode && $this->ReadAllowed;
        if (!$this->FormSubmitted) {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->s_issue_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->s_priority_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->s_status_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->s_notstatus_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->s_assigned_to->Errors->ToString());
            $Error = ComposeStrings($Error, $this->Errors->ToString());
            $Tpl->SetVar("Error", $Error);
            $Tpl->Parse("Error", false);
        }
        $CCSForm = $this->EditMode ? $this->ComponentName . ":" . "Edit" : $this->ComponentName;
        $this->HTMLFormAction = $FileName . "?" . CCAddParam(CCGetQueryString("QueryString", ""), "ccsForm", $CCSForm);
        $Tpl->SetVar("Action", $this->HTMLFormAction);
        $Tpl->SetVar("HTMLFormName", $this->ComponentName);
        $Tpl->SetVar("HTMLFormEnctype", $this->FormEnctype);

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->s_issue_name->Show();
        $this->s_priority_id->Show();
        $this->s_status_id->Show();
        $this->s_notstatus_id->Show();
        $this->s_assigned_to->Show();
        $this->DoSearch->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

} //End issuesSearch Class @4-FCB6E20C

class clsGridissues { //issues class @3-9E712255

//Variables @3-C58271D9

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
    var $Sorter_issue_id;
    var $Sorter_issue_name;
    var $Sorter1;
    var $Sorter_status_id;
    var $Sorter_assigned_to;
    var $Sorter_date_submitted;
    var $Sorter_date_modified;
    var $Sorter_date_resolved;
//End Variables

//Class_Initialize Event @3-293A3C70
    function clsGridissues($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "issues";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid issues";
        $this->DataSource = new clsissuesDataSource($this);
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
        $this->SorterName = CCGetParam("issuesOrder", "");
        $this->SorterDirection = CCGetParam("issuesDir", "");

        $this->issue_id = & new clsControl(ccsLabel, "issue_id", "issue_id", ccsInteger, "", CCGetRequestParam("issue_id", ccsGet, NULL), $this);
        $this->issue_name = & new clsControl(ccsLink, "issue_name", "issue_name", ccsText, "", CCGetRequestParam("issue_name", ccsGet, NULL), $this);
        $this->issue_name->Page = "IssueMaint.php";
        $this->priority = & new clsControl(ccsLabel, "priority", "priority", ccsText, "", CCGetRequestParam("priority", ccsGet, NULL), $this);
        $this->status_id = & new clsControl(ccsLabel, "status_id", "status_id", ccsText, "", CCGetRequestParam("status_id", ccsGet, NULL), $this);
        $this->assigned_to = & new clsControl(ccsLabel, "assigned_to", "assigned_to", ccsText, "", CCGetRequestParam("assigned_to", ccsGet, NULL), $this);
        $this->date_submitted = & new clsControl(ccsLabel, "date_submitted", "date_submitted", ccsDate, array("GeneralDate"), CCGetRequestParam("date_submitted", ccsGet, NULL), $this);
        $this->date_modified = & new clsControl(ccsLabel, "date_modified", "date_modified", ccsDate, array("GeneralDate"), CCGetRequestParam("date_modified", ccsGet, NULL), $this);
        $this->date_resolved = & new clsControl(ccsLabel, "date_resolved", "date_resolved", ccsDate, array("GeneralDate"), CCGetRequestParam("date_resolved", ccsGet, NULL), $this);
        $this->Sorter_issue_id = & new clsSorter($this->ComponentName, "Sorter_issue_id", $FileName, $this);
        $this->Sorter_issue_name = & new clsSorter($this->ComponentName, "Sorter_issue_name", $FileName, $this);
        $this->Sorter1 = & new clsSorter($this->ComponentName, "Sorter1", $FileName, $this);
        $this->Sorter_status_id = & new clsSorter($this->ComponentName, "Sorter_status_id", $FileName, $this);
        $this->Sorter_assigned_to = & new clsSorter($this->ComponentName, "Sorter_assigned_to", $FileName, $this);
        $this->Sorter_date_submitted = & new clsSorter($this->ComponentName, "Sorter_date_submitted", $FileName, $this);
        $this->Sorter_date_modified = & new clsSorter($this->ComponentName, "Sorter_date_modified", $FileName, $this);
        $this->Sorter_date_resolved = & new clsSorter($this->ComponentName, "Sorter_date_resolved", $FileName, $this);
        $this->Navigator = & new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpCentered, $this);
    }
//End Class_Initialize Event

//Initialize Method @3-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @3-EE165F48
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["urls_issue_name"] = CCGetFromGet("s_issue_name", NULL);
        $this->DataSource->Parameters["urls_priority_id"] = CCGetFromGet("s_priority_id", NULL);
        $this->DataSource->Parameters["urls_status_id"] = CCGetFromGet("s_status_id", NULL);
        $this->DataSource->Parameters["urls_notstatus_id"] = CCGetFromGet("s_notstatus_id", NULL);
        $this->DataSource->Parameters["urls_assigned_to"] = CCGetFromGet("s_assigned_to", NULL);

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
            $this->ControlsVisible["issue_id"] = $this->issue_id->Visible;
            $this->ControlsVisible["issue_name"] = $this->issue_name->Visible;
            $this->ControlsVisible["priority"] = $this->priority->Visible;
            $this->ControlsVisible["status_id"] = $this->status_id->Visible;
            $this->ControlsVisible["assigned_to"] = $this->assigned_to->Visible;
            $this->ControlsVisible["date_submitted"] = $this->date_submitted->Visible;
            $this->ControlsVisible["date_modified"] = $this->date_modified->Visible;
            $this->ControlsVisible["date_resolved"] = $this->date_resolved->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->issue_id->SetValue($this->DataSource->issue_id->GetValue());
                $this->issue_name->SetValue($this->DataSource->issue_name->GetValue());
                $this->issue_name->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->issue_name->Parameters = CCAddParam($this->issue_name->Parameters, "issue_id", $this->DataSource->f("issue_id"));
                $this->priority->SetValue($this->DataSource->priority->GetValue());
                $this->status_id->SetValue($this->DataSource->status_id->GetValue());
                $this->assigned_to->SetValue($this->DataSource->assigned_to->GetValue());
                $this->date_submitted->SetValue($this->DataSource->date_submitted->GetValue());
                $this->date_modified->SetValue($this->DataSource->date_modified->GetValue());
                $this->date_resolved->SetValue($this->DataSource->date_resolved->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->issue_id->Show();
                $this->issue_name->Show();
                $this->priority->Show();
                $this->status_id->Show();
                $this->assigned_to->Show();
                $this->date_submitted->Show();
                $this->date_modified->Show();
                $this->date_resolved->Show();
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
        $this->Sorter_issue_id->Show();
        $this->Sorter_issue_name->Show();
        $this->Sorter1->Show();
        $this->Sorter_status_id->Show();
        $this->Sorter_assigned_to->Show();
        $this->Sorter_date_submitted->Show();
        $this->Sorter_date_modified->Show();
        $this->Sorter_date_resolved->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @3-C9927ED5
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->issue_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->issue_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->priority->Errors->ToString());
        $errors = ComposeStrings($errors, $this->status_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_to->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_submitted->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_modified->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_resolved->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End issues Class @3-FCB6E20C

class clsissuesDataSource extends clsDBIM {  //issuesDataSource Class @3-FEEDA2F4

//DataSource Variables @3-900B7EC7
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $issue_id;
    var $issue_name;
    var $priority;
    var $status_id;
    var $assigned_to;
    var $date_submitted;
    var $date_modified;
    var $date_resolved;
//End DataSource Variables

//DataSourceClass_Initialize Event @3-4B80C0A6
    function clsissuesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid issues";
        $this->Initialize();
        $this->issue_id = new clsField("issue_id", ccsInteger, "");
        $this->issue_name = new clsField("issue_name", ccsText, "");
        $this->priority = new clsField("priority", ccsText, "");
        $this->status_id = new clsField("status_id", ccsText, "");
        $this->assigned_to = new clsField("assigned_to", ccsText, "");
        $this->date_submitted = new clsField("date_submitted", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->date_modified = new clsField("date_modified", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->date_resolved = new clsField("date_resolved", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @3-F3033F8F
    function SetOrder($SorterName, $SorterDirection)
    {
    $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_issue_id" => array("issue_id", ""), 
            "Sorter_issue_name" => array("issue_name", ""), 
            "Sorter1" => array("priority_order", ""), 
            "Sorter_status_id" => array("issues.status_id", ""), 
            "Sorter_assigned_to" => array("assigned_to", ""), 
            "Sorter_date_submitted" => array("date_submitted", ""), 
            "Sorter_date_modified" => array("date_modified", ""), 
            "Sorter_date_resolved" => array("date_resolved", "")));
    }
//End SetOrder Method

//Prepare Method @3-451A37F4
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urls_issue_name", ccsText, "", "", $this->Parameters["urls_issue_name"], "", false);
        $this->wp->AddParameter("2", "urls_priority_id", ccsInteger, "", "", $this->Parameters["urls_priority_id"], "", false);
        $this->wp->AddParameter("3", "urls_status_id", ccsInteger, "", "", $this->Parameters["urls_status_id"], "", false);
        $this->wp->AddParameter("4", "urls_notstatus_id", ccsInteger, "", "", $this->Parameters["urls_notstatus_id"], "", false);
        $this->wp->AddParameter("5", "urls_assigned_to", ccsInteger, "", "", $this->Parameters["urls_assigned_to"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opContains, "issues.issue_name", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsText),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opEqual, "issues.priority_id", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsInteger),false);
        $this->wp->Criterion[3] = $this->wp->Operation(opEqual, "issues.status_id", $this->wp->GetDBValue("3"), $this->ToSQL($this->wp->GetDBValue("3"), ccsInteger),false);
        $this->wp->Criterion[4] = $this->wp->Operation(opNotEqual, "issues.status_id", $this->wp->GetDBValue("4"), $this->ToSQL($this->wp->GetDBValue("4"), ccsInteger),false);
        $this->wp->Criterion[5] = $this->wp->Operation(opEqual, "issues.assigned_to", $this->wp->GetDBValue("5"), $this->ToSQL($this->wp->GetDBValue("5"), ccsInteger),false);
        $this->Where = $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]), 
             $this->wp->Criterion[3]), 
             $this->wp->Criterion[4]), 
             $this->wp->Criterion[5]);
    }
//End Prepare Method

//Open Method @3-10C047AC
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM ((issues LEFT JOIN statuses ON\n\n" .
        "issues.status_id = statuses.status_id) LEFT JOIN users ON\n\n" .
        "users.user_id = issues.assigned_to) LEFT JOIN priorities ON\n\n" .
        "issues.priority_id = priorities.priority_id";
        $this->SQL = "SELECT * \n\n" .
        "FROM ((issues LEFT JOIN statuses ON\n\n" .
        "issues.status_id = statuses.status_id) LEFT JOIN users ON\n\n" .
        "users.user_id = issues.assigned_to) LEFT JOIN priorities ON\n\n" .
        "issues.priority_id = priorities.priority_id {SQL_Where} {SQL_OrderBy}";
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

//SetValues Method @3-A1674EF4
    function SetValues()
    {
        $this->issue_id->SetDBValue(trim($this->f("issue_id")));
        $this->issue_name->SetDBValue($this->f("issue_name"));
        $this->priority->SetDBValue($this->f("priority_desc"));
        $this->status_id->SetDBValue($this->f("status"));
        $this->assigned_to->SetDBValue($this->f("user_name"));
        $this->date_submitted->SetDBValue(trim($this->f("date_submitted")));
        $this->date_modified->SetDBValue(trim($this->f("date_modified")));
        $this->date_resolved->SetDBValue(trim($this->f("date_resolved")));
    }
//End SetValues Method

} //End issuesDataSource Class @3-FCB6E20C

//Include Page implementation @39-58DBA1E3
include_once(RelativePath . "/Footer.php");
//End Include Page implementation

//Initialize Page @1-DF73FFF4
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
$TemplateFileName = "IssueList.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-4B0BB954
CCSecurityRedirect("3", "");
//End Authenticate User

//Include events file @1-37430893
include("./IssueList_events.php");
//End Include events file

//Initialize Objects @1-962FFBAF
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$Header = & new clsHeader("", "Header", $MainPage);
$Header->Initialize();
$AdminMenu = & new clsAdminMenu("", "AdminMenu", $MainPage);
$AdminMenu->Initialize();
$issuesSearch = & new clsRecordissuesSearch("", $MainPage);
$issues = & new clsGridissues("", $MainPage);
$Footer = & new clsFooter("", "Footer", $MainPage);
$Footer->Initialize();
$MainPage->Header = & $Header;
$MainPage->AdminMenu = & $AdminMenu;
$MainPage->issuesSearch = & $issuesSearch;
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

//Execute Components @1-57C5B480
$Header->Operations();
$AdminMenu->Operations();
$issuesSearch->Operation();
$Footer->Operations();
//End Execute Components

//Go to destination page @1-2953456E
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    $Header->Class_Terminate();
    unset($Header);
    $AdminMenu->Class_Terminate();
    unset($AdminMenu);
    unset($issuesSearch);
    unset($issues);
    $Footer->Class_Terminate();
    unset($Footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-235DD945
$Header->Show();
$AdminMenu->Show();
$issuesSearch->Show();
$issues->Show();
$Footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-D4852F01
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
$Header->Class_Terminate();
unset($Header);
$AdminMenu->Class_Terminate();
unset($AdminMenu);
unset($issuesSearch);
unset($issues);
$Footer->Class_Terminate();
unset($Footer);
unset($Tpl);
//End Unload Page


?>
