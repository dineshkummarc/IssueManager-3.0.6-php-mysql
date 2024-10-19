<?php
//Include Common Files @1-49F79CB5
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "Default.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @122-3DD2EFDC
include_once(RelativePath . "/Header.php");
//End Include Page implementation

class clsRecordissuesSearch { //issuesSearch Class @3-FE328556

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

//Class_Initialize Event @3-BFC2ECC9
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
            $this->s_issue_desc = new clsControl(ccsTextBox, "s_issue_desc", "s_issue_desc", ccsText, "", CCGetRequestParam("s_issue_desc", $Method, NULL), $this);
            $this->s_priority_id = new clsControl(ccsListBox, "s_priority_id", "s_priority_id", ccsInteger, "", CCGetRequestParam("s_priority_id", $Method, NULL), $this);
            $this->s_priority_id->DSType = dsTable;
            list($this->s_priority_id->BoundColumn, $this->s_priority_id->TextColumn, $this->s_priority_id->DBFormat) = array("priority_id", "priority_desc", "");
            $this->s_priority_id->DataSource = new clsDBIM();
            $this->s_priority_id->ds = & $this->s_priority_id->DataSource;
            $this->s_priority_id->DataSource->SQL = "SELECT * \n" .
"FROM priorities {SQL_Where} {SQL_OrderBy}";
            $this->s_status_id = new clsControl(ccsListBox, "s_status_id", "s_status_id", ccsInteger, "", CCGetRequestParam("s_status_id", $Method, NULL), $this);
            $this->s_status_id->DSType = dsTable;
            list($this->s_status_id->BoundColumn, $this->s_status_id->TextColumn, $this->s_status_id->DBFormat) = array("status_id", "status", "");
            $this->s_status_id->DataSource = new clsDBIM();
            $this->s_status_id->ds = & $this->s_status_id->DataSource;
            $this->s_status_id->DataSource->SQL = "SELECT * \n" .
"FROM statuses {SQL_Where} {SQL_OrderBy}";
            $this->s_notstatus_id = new clsControl(ccsListBox, "s_notstatus_id", "s_notstatus_id", ccsInteger, "", CCGetRequestParam("s_notstatus_id", $Method, NULL), $this);
            $this->s_notstatus_id->DSType = dsTable;
            list($this->s_notstatus_id->BoundColumn, $this->s_notstatus_id->TextColumn, $this->s_notstatus_id->DBFormat) = array("status_id", "status", "");
            $this->s_notstatus_id->DataSource = new clsDBIM();
            $this->s_notstatus_id->ds = & $this->s_notstatus_id->DataSource;
            $this->s_notstatus_id->DataSource->SQL = "SELECT * \n" .
"FROM statuses {SQL_Where} {SQL_OrderBy}";
            $this->s_assigned_to = new clsControl(ccsListBox, "s_assigned_to", "s_assigned_to", ccsInteger, "", CCGetRequestParam("s_assigned_to", $Method, NULL), $this);
            $this->s_assigned_to->DSType = dsTable;
            list($this->s_assigned_to->BoundColumn, $this->s_assigned_to->TextColumn, $this->s_assigned_to->DBFormat) = array("user_id", "user_name", "");
            $this->s_assigned_to->DataSource = new clsDBIM();
            $this->s_assigned_to->ds = & $this->s_assigned_to->DataSource;
            $this->s_assigned_to->DataSource->SQL = "SELECT * \n" .
"FROM users {SQL_Where} {SQL_OrderBy}";
            $this->s_assigned_to->DataSource->Order = "user_name";
            $this->s_assigned_to->DataSource->Order = "user_name";
            $this->DoSearch = new clsButton("DoSearch", $Method, $this);
            if(!$this->FormSubmitted) {
                if(!is_array($this->s_notstatus_id->Value) && !strlen($this->s_notstatus_id->Value) && $this->s_notstatus_id->Value !== false)
                    $this->s_notstatus_id->SetText(3);
            }
        }
    }
//End Class_Initialize Event

//Validate Method @3-452C1DC7
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->s_issue_desc->Validate() && $Validation);
        $Validation = ($this->s_priority_id->Validate() && $Validation);
        $Validation = ($this->s_status_id->Validate() && $Validation);
        $Validation = ($this->s_notstatus_id->Validate() && $Validation);
        $Validation = ($this->s_assigned_to->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->s_issue_desc->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_priority_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_status_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_notstatus_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_assigned_to->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @3-9A2CF9E5
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->s_issue_desc->Errors->Count());
        $errors = ($errors || $this->s_priority_id->Errors->Count());
        $errors = ($errors || $this->s_status_id->Errors->Count());
        $errors = ($errors || $this->s_notstatus_id->Errors->Count());
        $errors = ($errors || $this->s_assigned_to->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @3-427543B9
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
        $Redirect = "Default.php" . "?" . CCGetQueryString("Form", array("ccsForm"));
        if($this->Validate()) {
            if($this->PressedButton == "DoSearch") {
                $Redirect = "Default.php" . "?" . CCMergeQueryStrings(CCGetQueryString("Form", array("DoSearch", "DoSearch_x", "DoSearch_y")));
                if(!CCGetEvent($this->DoSearch->CCSEvents, "OnClick", $this->DoSearch)) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//Show Method @3-236AFEEB
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
            $Error = ComposeStrings($Error, $this->s_issue_desc->Errors->ToString());
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

        $this->s_issue_desc->Show();
        $this->s_priority_id->Show();
        $this->s_status_id->Show();
        $this->s_notstatus_id->Show();
        $this->s_assigned_to->Show();
        $this->DoSearch->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

} //End issuesSearch Class @3-FCB6E20C

class clsGridsummary { //summary class @40-553BABF3

//Variables @40-663D5B8C

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

//Class_Initialize Event @40-556BAA44
    function clsGridsummary($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "summary";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid summary";
        $this->DataSource = new clssummaryDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = 20;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0) $this->PageNumber = 1;

        $this->Label1 = new clsControl(ccsLink, "Label1", "Label1", ccsText, "", CCGetRequestParam("Label1", ccsGet, NULL), $this);
        $this->Label1->Page = "Default.php";
        $this->Label2 = new clsControl(ccsLabel, "Label2", "Label2", ccsText, "", CCGetRequestParam("Label2", ccsGet, NULL), $this);
    }
//End Class_Initialize Event

//Initialize Method @40-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @40-924578D2
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["urls_issue_desc"] = CCGetFromGet("s_issue_desc", NULL);
        $this->DataSource->Parameters["urls_assigned_by"] = CCGetFromGet("s_assigned_by", NULL);
        $this->DataSource->Parameters["urls_priority_id"] = CCGetFromGet("s_priority_id", NULL);
        $this->DataSource->Parameters["urls_status_id"] = CCGetFromGet("s_status_id", NULL);
        $this->DataSource->Parameters["urls_assigned_to"] = CCGetFromGet("s_assigned_to", NULL);
        $this->DataSource->Parameters["urls_notstatus_id"] = CCGetFromGet("s_notstatus_id", NULL);

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
            $this->ControlsVisible["Label1"] = $this->Label1->Visible;
            $this->ControlsVisible["Label2"] = $this->Label2->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->Label1->SetValue($this->DataSource->Label1->GetValue());
                $this->Label1->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->Label1->Parameters = CCAddParam($this->Label1->Parameters, "s_status_id", $this->DataSource->f("statuses_status_id"));
                $this->Label2->SetValue($this->DataSource->Label2->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->Label1->Show();
                $this->Label2->Show();
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
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @40-CAC11AAF
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->Label1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Label2->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End summary Class @40-FCB6E20C

class clssummaryDataSource extends clsDBIM {  //summaryDataSource Class @40-69B30EBD

//DataSource Variables @40-AA3215B8
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $Label1;
    var $Label2;
//End DataSource Variables

//DataSourceClass_Initialize Event @40-34861FF7
    function clssummaryDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid summary";
        $this->Initialize();
        $this->Label1 = new clsField("Label1", ccsText, "");
        $this->Label2 = new clsField("Label2", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @40-C2AB6D95
    function SetOrder($SorterName, $SorterDirection)
    {
    $this->Order = "statuses.status_id";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @40-A3FCE5CB
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urls_issue_desc", ccsText, "", "", $this->Parameters["urls_issue_desc"], "", false);
        $this->wp->AddParameter("2", "urls_issue_desc", ccsMemo, "", "", $this->Parameters["urls_issue_desc"], "", false);
        $this->wp->AddParameter("3", "urls_assigned_by", ccsInteger, "", "", $this->Parameters["urls_assigned_by"], "", false);
        $this->wp->AddParameter("4", "urls_priority_id", ccsInteger, "", "", $this->Parameters["urls_priority_id"], "", false);
        $this->wp->AddParameter("5", "urls_status_id", ccsInteger, "", "", $this->Parameters["urls_status_id"], "", false);
        $this->wp->AddParameter("6", "urls_assigned_to", ccsInteger, "", "", $this->Parameters["urls_assigned_to"], "", false);
        $this->wp->AddParameter("7", "urls_notstatus_id", ccsInteger, "", "", $this->Parameters["urls_notstatus_id"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opContains, "issues.issue_name", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsText),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opContains, "issues.issue_desc", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsMemo),false);
        $this->wp->Criterion[3] = $this->wp->Operation(opEqual, "issues.user_id", $this->wp->GetDBValue("3"), $this->ToSQL($this->wp->GetDBValue("3"), ccsInteger),false);
        $this->wp->Criterion[4] = $this->wp->Operation(opEqual, "issues.priority_id", $this->wp->GetDBValue("4"), $this->ToSQL($this->wp->GetDBValue("4"), ccsInteger),false);
        $this->wp->Criterion[5] = $this->wp->Operation(opEqual, "issues.status_id", $this->wp->GetDBValue("5"), $this->ToSQL($this->wp->GetDBValue("5"), ccsInteger),false);
        $this->wp->Criterion[6] = $this->wp->Operation(opEqual, "issues.assigned_to", $this->wp->GetDBValue("6"), $this->ToSQL($this->wp->GetDBValue("6"), ccsInteger),false);
        $this->wp->Criterion[7] = $this->wp->Operation(opNotEqual, "issues.status_id", $this->wp->GetDBValue("7"), $this->ToSQL($this->wp->GetDBValue("7"), ccsInteger),false);
        $this->Where = $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opOR(
             true, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]), 
             $this->wp->Criterion[3]), 
             $this->wp->Criterion[4]), 
             $this->wp->Criterion[5]), 
             $this->wp->Criterion[6]), 
             $this->wp->Criterion[7]);
    }
//End Prepare Method

//Open Method @40-E7608707
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT COUNT(issue_id) AS issues_count, statuses.status_id AS statuses_status_id, status \n\n" .
        "FROM issues INNER JOIN statuses ON\n\n" .
        "issues.status_id = statuses.status_id {SQL_Where}\n\n" .
        "GROUP BY statuses.status_id, status {SQL_OrderBy}";
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

//SetValues Method @40-F2D75655
    function SetValues()
    {
        $this->Label1->SetDBValue($this->f("status"));
        $this->Label2->SetDBValue($this->f("issues_count"));
    }
//End SetValues Method

} //End summaryDataSource Class @40-FCB6E20C

class clsGridissues { //issues class @2-9E712255

//Variables @2-FA99D63B

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
    var $AltRowControls;
    var $IsAltRow;
    var $Sorter_issue_id;
    var $Sorter_issue_name;
    var $Sorter_status_id;
    var $Sorter_priority_id;
    var $Sorter_assigned_to;
    var $Sorter_date_submitted;
    var $Sorter_date_modified;
    var $Sorter_tested;
    var $Sorter_approved;
    var $Sorter_version;
//End Variables

//Class_Initialize Event @2-11A80A9E
    function clsGridissues($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "issues";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->IsAltRow = false;
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

        $this->issue_id = new clsControl(ccsLabel, "issue_id", "issue_id", ccsInteger, "", CCGetRequestParam("issue_id", ccsGet, NULL), $this);
        $this->issue_name = new clsControl(ccsLink, "issue_name", "issue_name", ccsText, "", CCGetRequestParam("issue_name", ccsGet, NULL), $this);
        $this->issue_name->Page = "IssueChange.php";
        $this->status_id = new clsControl(ccsLabel, "status_id", "status_id", ccsText, "", CCGetRequestParam("status_id", ccsGet, NULL), $this);
        $this->color = new clsControl(ccsLabel, "color", "color", ccsText, "", CCGetRequestParam("color", ccsGet, NULL), $this);
        $this->priority_id = new clsControl(ccsLabel, "priority_id", "priority_id", ccsText, "", CCGetRequestParam("priority_id", ccsGet, NULL), $this);
        $this->priority_id->HTML = true;
        $this->assigned_id = new clsControl(ccsLabel, "assigned_id", "assigned_id", ccsInteger, "", CCGetRequestParam("assigned_id", ccsGet, NULL), $this);
        $this->assigned_to = new clsControl(ccsLabel, "assigned_to", "assigned_to", ccsText, "", CCGetRequestParam("assigned_to", ccsGet, NULL), $this);
        $this->assigned_to->HTML = true;
        $this->date_submitted = new clsControl(ccsLabel, "date_submitted", "date_submitted", ccsDate, array("GeneralDate"), CCGetRequestParam("date_submitted", ccsGet, NULL), $this);
        $this->date_modified = new clsControl(ccsLabel, "date_modified", "date_modified", ccsDate, array("GeneralDate"), CCGetRequestParam("date_modified", ccsGet, NULL), $this);
        $this->tested = new clsControl(ccsLabel, "tested", "tested", ccsBoolean, array("res:im_yes", "", ""), CCGetRequestParam("tested", ccsGet, NULL), $this);
        $this->approved = new clsControl(ccsLabel, "approved", "approved", ccsBoolean, array("res:im_yes", "", ""), CCGetRequestParam("approved", ccsGet, NULL), $this);
        $this->version = new clsControl(ccsLabel, "version", "version", ccsText, "", CCGetRequestParam("version", ccsGet, NULL), $this);
        $this->issue_id1 = new clsControl(ccsLabel, "issue_id1", "issue_id1", ccsInteger, "", CCGetRequestParam("issue_id1", ccsGet, NULL), $this);
        $this->issue_name1 = new clsControl(ccsLink, "issue_name1", "issue_name1", ccsText, "", CCGetRequestParam("issue_name1", ccsGet, NULL), $this);
        $this->issue_name1->Page = "IssueChange.php";
        $this->status_id1 = new clsControl(ccsLabel, "status_id1", "status_id1", ccsText, "", CCGetRequestParam("status_id1", ccsGet, NULL), $this);
        $this->status_id1->HTML = true;
        $this->color1 = new clsControl(ccsLabel, "color1", "color1", ccsText, "", CCGetRequestParam("color1", ccsGet, NULL), $this);
        $this->priority_id1 = new clsControl(ccsLabel, "priority_id1", "priority_id1", ccsText, "", CCGetRequestParam("priority_id1", ccsGet, NULL), $this);
        $this->priority_id1->HTML = true;
        $this->assigned_id1 = new clsControl(ccsLabel, "assigned_id1", "assigned_id1", ccsInteger, "", CCGetRequestParam("assigned_id1", ccsGet, NULL), $this);
        $this->assigned_to1 = new clsControl(ccsLabel, "assigned_to1", "assigned_to1", ccsText, "", CCGetRequestParam("assigned_to1", ccsGet, NULL), $this);
        $this->assigned_to1->HTML = true;
        $this->date_submitted1 = new clsControl(ccsLabel, "date_submitted1", "date_submitted1", ccsDate, array("GeneralDate"), CCGetRequestParam("date_submitted1", ccsGet, NULL), $this);
        $this->date_modified1 = new clsControl(ccsLabel, "date_modified1", "date_modified1", ccsDate, array("GeneralDate"), CCGetRequestParam("date_modified1", ccsGet, NULL), $this);
        $this->tested1 = new clsControl(ccsLabel, "tested1", "tested1", ccsBoolean, array("res:im_yes", "", ""), CCGetRequestParam("tested1", ccsGet, NULL), $this);
        $this->approved1 = new clsControl(ccsLabel, "approved1", "approved1", ccsBoolean, array("res:im_yes", "", ""), CCGetRequestParam("approved1", ccsGet, NULL), $this);
        $this->version1 = new clsControl(ccsLabel, "version1", "version1", ccsText, "", CCGetRequestParam("version1", ccsGet, NULL), $this);
        $this->title = new clsControl(ccsLabel, "title", "title", ccsText, "", CCGetRequestParam("title", ccsGet, NULL), $this);
        $this->title->HTML = true;
        $this->Sorter_issue_id = new clsSorter($this->ComponentName, "Sorter_issue_id", $FileName, $this);
        $this->Sorter_issue_name = new clsSorter($this->ComponentName, "Sorter_issue_name", $FileName, $this);
        $this->Sorter_status_id = new clsSorter($this->ComponentName, "Sorter_status_id", $FileName, $this);
        $this->Sorter_priority_id = new clsSorter($this->ComponentName, "Sorter_priority_id", $FileName, $this);
        $this->Sorter_assigned_to = new clsSorter($this->ComponentName, "Sorter_assigned_to", $FileName, $this);
        $this->Sorter_date_submitted = new clsSorter($this->ComponentName, "Sorter_date_submitted", $FileName, $this);
        $this->Sorter_date_modified = new clsSorter($this->ComponentName, "Sorter_date_modified", $FileName, $this);
        $this->Sorter_tested = new clsSorter($this->ComponentName, "Sorter_tested", $FileName, $this);
        $this->Sorter_approved = new clsSorter($this->ComponentName, "Sorter_approved", $FileName, $this);
        $this->Sorter_version = new clsSorter($this->ComponentName, "Sorter_version", $FileName, $this);
        $this->Navigator = new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpCentered, $this);
        $this->Link6 = new clsControl(ccsLink, "Link6", "Link6", ccsText, "", CCGetRequestParam("Link6", ccsGet, NULL), $this);
        $this->Link6->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
        $this->Link6->Page = "IssueNew.php";
        $this->Link7 = new clsControl(ccsLink, "Link7", "Link7", ccsText, "", CCGetRequestParam("Link7", ccsGet, NULL), $this);
        $this->Link7->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
        $this->Link7->Page = "IssueExport.php";
    }
//End Class_Initialize Event

//Initialize Method @2-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @2-0203C247
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["urls_issue_desc"] = CCGetFromGet("s_issue_desc", NULL);
        $this->DataSource->Parameters["urls_priority_id"] = CCGetFromGet("s_priority_id", NULL);
        $this->DataSource->Parameters["urls_status_id"] = CCGetFromGet("s_status_id", NULL);
        $this->DataSource->Parameters["urls_notstatus_id"] = CCGetFromGet("s_notstatus_id", NULL);
        $this->DataSource->Parameters["urls_assigned_to"] = CCGetFromGet("s_assigned_to", NULL);
        $this->DataSource->Parameters["urls_assigned_by"] = CCGetFromGet("s_assigned_by", NULL);

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
            $this->ControlsVisible["status_id"] = $this->status_id->Visible;
            $this->ControlsVisible["color"] = $this->color->Visible;
            $this->ControlsVisible["priority_id"] = $this->priority_id->Visible;
            $this->ControlsVisible["assigned_id"] = $this->assigned_id->Visible;
            $this->ControlsVisible["assigned_to"] = $this->assigned_to->Visible;
            $this->ControlsVisible["date_submitted"] = $this->date_submitted->Visible;
            $this->ControlsVisible["date_modified"] = $this->date_modified->Visible;
            $this->ControlsVisible["tested"] = $this->tested->Visible;
            $this->ControlsVisible["approved"] = $this->approved->Visible;
            $this->ControlsVisible["version"] = $this->version->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                if(!$this->IsAltRow)
                {
                    $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                    $this->issue_id->SetValue($this->DataSource->issue_id->GetValue());
                    $this->issue_name->SetValue($this->DataSource->issue_name->GetValue());
                    $this->issue_name->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                    $this->issue_name->Parameters = CCAddParam($this->issue_name->Parameters, "issue_id", $this->DataSource->f("issue_id"));
                    $this->status_id->SetValue($this->DataSource->status_id->GetValue());
                    $this->color->SetValue($this->DataSource->color->GetValue());
                    $this->priority_id->SetValue($this->DataSource->priority_id->GetValue());
                    $this->assigned_id->SetValue($this->DataSource->assigned_id->GetValue());
                    $this->assigned_to->SetValue($this->DataSource->assigned_to->GetValue());
                    $this->date_submitted->SetValue($this->DataSource->date_submitted->GetValue());
                    $this->date_modified->SetValue($this->DataSource->date_modified->GetValue());
                    $this->tested->SetValue($this->DataSource->tested->GetValue());
                    $this->approved->SetValue($this->DataSource->approved->GetValue());
                    $this->version->SetValue($this->DataSource->version->GetValue());
                    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                    $this->issue_id->Show();
                    $this->issue_name->Show();
                    $this->status_id->Show();
                    $this->color->Show();
                    $this->priority_id->Show();
                    $this->assigned_id->Show();
                    $this->assigned_to->Show();
                    $this->date_submitted->Show();
                    $this->date_modified->Show();
                    $this->tested->Show();
                    $this->approved->Show();
                    $this->version->Show();
                    $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                    $Tpl->parse("Row", true);
                }
                else
                {
                    $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/AltRow";
                    $this->issue_id1->SetValue($this->DataSource->issue_id1->GetValue());
                    $this->issue_name1->SetValue($this->DataSource->issue_name1->GetValue());
                    $this->issue_name1->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                    $this->issue_name1->Parameters = CCAddParam($this->issue_name1->Parameters, "issue_id", $this->DataSource->f("issue_id"));
                    $this->status_id1->SetValue($this->DataSource->status_id1->GetValue());
                    $this->color1->SetValue($this->DataSource->color1->GetValue());
                    $this->priority_id1->SetValue($this->DataSource->priority_id1->GetValue());
                    $this->assigned_id1->SetValue($this->DataSource->assigned_id1->GetValue());
                    $this->assigned_to1->SetValue($this->DataSource->assigned_to1->GetValue());
                    $this->date_submitted1->SetValue($this->DataSource->date_submitted1->GetValue());
                    $this->date_modified1->SetValue($this->DataSource->date_modified1->GetValue());
                    $this->tested1->SetValue($this->DataSource->tested1->GetValue());
                    $this->approved1->SetValue($this->DataSource->approved1->GetValue());
                    $this->version1->SetValue($this->DataSource->version1->GetValue());
                    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                    $this->issue_id1->Show();
                    $this->issue_name1->Show();
                    $this->status_id1->Show();
                    $this->color1->Show();
                    $this->priority_id1->Show();
                    $this->assigned_id1->Show();
                    $this->assigned_to1->Show();
                    $this->date_submitted1->Show();
                    $this->date_modified1->Show();
                    $this->tested1->Show();
                    $this->approved1->Show();
                    $this->version1->Show();
                    $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                    $Tpl->parseto("AltRow", true, "Row");
                }
                $this->IsAltRow = (!$this->IsAltRow);
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
        $this->title->Show();
        $this->Sorter_issue_id->Show();
        $this->Sorter_issue_name->Show();
        $this->Sorter_status_id->Show();
        $this->Sorter_priority_id->Show();
        $this->Sorter_assigned_to->Show();
        $this->Sorter_date_submitted->Show();
        $this->Sorter_date_modified->Show();
        $this->Sorter_tested->Show();
        $this->Sorter_approved->Show();
        $this->Sorter_version->Show();
        $this->Navigator->Show();
        $this->Link6->Show();
        $this->Link7->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @2-C8C6B396
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->issue_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->issue_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->status_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->color->Errors->ToString());
        $errors = ComposeStrings($errors, $this->priority_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_to->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_submitted->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_modified->Errors->ToString());
        $errors = ComposeStrings($errors, $this->tested->Errors->ToString());
        $errors = ComposeStrings($errors, $this->approved->Errors->ToString());
        $errors = ComposeStrings($errors, $this->version->Errors->ToString());
        $errors = ComposeStrings($errors, $this->issue_id1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->issue_name1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->status_id1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->color1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->priority_id1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_id1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_to1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_submitted1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_modified1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->tested1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->approved1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->version1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End issues Class @2-FCB6E20C

class clsissuesDataSource extends clsDBIM {  //issuesDataSource Class @2-FEEDA2F4

//DataSource Variables @2-2BAA2103
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
    var $status_id;
    var $color;
    var $priority_id;
    var $assigned_id;
    var $assigned_to;
    var $date_submitted;
    var $date_modified;
    var $tested;
    var $approved;
    var $version;
    var $issue_id1;
    var $issue_name1;
    var $status_id1;
    var $color1;
    var $priority_id1;
    var $assigned_id1;
    var $assigned_to1;
    var $date_submitted1;
    var $date_modified1;
    var $tested1;
    var $approved1;
    var $version1;
//End DataSource Variables

//DataSourceClass_Initialize Event @2-9135889C
    function clsissuesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid issues";
        $this->Initialize();
        $this->issue_id = new clsField("issue_id", ccsInteger, "");
        $this->issue_name = new clsField("issue_name", ccsText, "");
        $this->status_id = new clsField("status_id", ccsText, "");
        $this->color = new clsField("color", ccsText, "");
        $this->priority_id = new clsField("priority_id", ccsText, "");
        $this->assigned_id = new clsField("assigned_id", ccsInteger, "");
        $this->assigned_to = new clsField("assigned_to", ccsText, "");
        $this->date_submitted = new clsField("date_submitted", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->date_modified = new clsField("date_modified", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->tested = new clsField("tested", ccsBoolean, array(1, 0, ""));
        $this->approved = new clsField("approved", ccsBoolean, array(1, 0, ""));
        $this->version = new clsField("version", ccsText, "");
        $this->issue_id1 = new clsField("issue_id1", ccsInteger, "");
        $this->issue_name1 = new clsField("issue_name1", ccsText, "");
        $this->status_id1 = new clsField("status_id1", ccsText, "");
        $this->color1 = new clsField("color1", ccsText, "");
        $this->priority_id1 = new clsField("priority_id1", ccsText, "");
        $this->assigned_id1 = new clsField("assigned_id1", ccsInteger, "");
        $this->assigned_to1 = new clsField("assigned_to1", ccsText, "");
        $this->date_submitted1 = new clsField("date_submitted1", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->date_modified1 = new clsField("date_modified1", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->tested1 = new clsField("tested1", ccsBoolean, array(1, 0, ""));
        $this->approved1 = new clsField("approved1", ccsBoolean, array(1, 0, ""));
        $this->version1 = new clsField("version1", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @2-DF075FF9
    function SetOrder($SorterName, $SorterDirection)
    {
    $this->Order = "date_modified desc";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_issue_id" => array("issue_id", ""), 
            "Sorter_issue_name" => array("issue_name", ""), 
            "Sorter_status_id" => array("issues.status_id", ""), 
            "Sorter_priority_id" => array("priority_order", ""), 
            "Sorter_assigned_to" => array("assigned_to", ""), 
            "Sorter_date_submitted" => array("date_submitted", ""), 
            "Sorter_date_modified" => array("date_modified", ""), 
            "Sorter_tested" => array("tested", ""), 
            "Sorter_approved" => array("approved", ""), 
            "Sorter_version" => array("version", "")));
    }
//End SetOrder Method

//Prepare Method @2-4E7782DE
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urls_issue_desc", ccsText, "", "", $this->Parameters["urls_issue_desc"], "", false);
        $this->wp->AddParameter("2", "urls_issue_desc", ccsMemo, "", "", $this->Parameters["urls_issue_desc"], "", false);
        $this->wp->AddParameter("3", "urls_priority_id", ccsInteger, "", "", $this->Parameters["urls_priority_id"], "", false);
        $this->wp->AddParameter("4", "urls_status_id", ccsInteger, "", "", $this->Parameters["urls_status_id"], "", false);
        $this->wp->AddParameter("5", "urls_notstatus_id", ccsInteger, "", "", $this->Parameters["urls_notstatus_id"], "", false);
        $this->wp->AddParameter("6", "urls_assigned_to", ccsInteger, "", "", $this->Parameters["urls_assigned_to"], "", false);
        $this->wp->AddParameter("7", "urls_assigned_by", ccsInteger, "", "", $this->Parameters["urls_assigned_by"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opContains, "issues.issue_name", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsText),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opContains, "issues.issue_desc", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsMemo),false);
        $this->wp->Criterion[3] = $this->wp->Operation(opEqual, "issues.priority_id", $this->wp->GetDBValue("3"), $this->ToSQL($this->wp->GetDBValue("3"), ccsInteger),false);
        $this->wp->Criterion[4] = $this->wp->Operation(opEqual, "issues.status_id", $this->wp->GetDBValue("4"), $this->ToSQL($this->wp->GetDBValue("4"), ccsInteger),false);
        $this->wp->Criterion[5] = $this->wp->Operation(opNotEqual, "issues.status_id", $this->wp->GetDBValue("5"), $this->ToSQL($this->wp->GetDBValue("5"), ccsInteger),false);
        $this->wp->Criterion[6] = $this->wp->Operation(opEqual, "issues.assigned_to", $this->wp->GetDBValue("6"), $this->ToSQL($this->wp->GetDBValue("6"), ccsInteger),false);
        $this->wp->Criterion[7] = $this->wp->Operation(opEqual, "issues.user_id", $this->wp->GetDBValue("7"), $this->ToSQL($this->wp->GetDBValue("7"), ccsInteger),false);
        $this->Where = $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opOR(
             true, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]), 
             $this->wp->Criterion[3]), 
             $this->wp->Criterion[4]), 
             $this->wp->Criterion[5]), 
             $this->wp->Criterion[6]), 
             $this->wp->Criterion[7]);
    }
//End Prepare Method

//Open Method @2-E3C6C0BA
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM ((issues LEFT JOIN priorities ON\n\n" .
        "issues.priority_id = priorities.priority_id) LEFT JOIN statuses ON\n\n" .
        "issues.status_id = statuses.status_id) LEFT JOIN users ON\n\n" .
        "issues.assigned_to = users.user_id";
        $this->SQL = "SELECT issues.*, priority_desc, status, priority_color, user_name \n\n" .
        "FROM ((issues LEFT JOIN priorities ON\n\n" .
        "issues.priority_id = priorities.priority_id) LEFT JOIN statuses ON\n\n" .
        "issues.status_id = statuses.status_id) LEFT JOIN users ON\n\n" .
        "issues.assigned_to = users.user_id {SQL_Where} {SQL_OrderBy}";
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

//SetValues Method @2-8FEEE97B
    function SetValues()
    {
        $this->issue_id->SetDBValue(trim($this->f("issue_id")));
        $this->issue_name->SetDBValue($this->f("issue_name"));
        $this->status_id->SetDBValue($this->f("status"));
        $this->color->SetDBValue($this->f("priority_color"));
        $this->priority_id->SetDBValue($this->f("priority_desc"));
        $this->assigned_id->SetDBValue(trim($this->f("assigned_to")));
        $this->assigned_to->SetDBValue($this->f("user_name"));
        $this->date_submitted->SetDBValue(trim($this->f("date_submitted")));
        $this->date_modified->SetDBValue(trim($this->f("date_modified")));
        $this->tested->SetDBValue(trim($this->f("tested")));
        $this->approved->SetDBValue(trim($this->f("approved")));
        $this->version->SetDBValue($this->f("version"));
        $this->issue_id1->SetDBValue(trim($this->f("issue_id")));
        $this->issue_name1->SetDBValue($this->f("issue_name"));
        $this->status_id1->SetDBValue($this->f("status"));
        $this->color1->SetDBValue($this->f("priority_color"));
        $this->priority_id1->SetDBValue($this->f("priority_desc"));
        $this->assigned_id1->SetDBValue(trim($this->f("assigned_to")));
        $this->assigned_to1->SetDBValue($this->f("user_name"));
        $this->date_submitted1->SetDBValue(trim($this->f("date_submitted")));
        $this->date_modified1->SetDBValue(trim($this->f("date_modified")));
        $this->tested1->SetDBValue(trim($this->f("tested")));
        $this->approved1->SetDBValue(trim($this->f("approved")));
        $this->version1->SetDBValue($this->f("version"));
    }
//End SetValues Method

} //End issuesDataSource Class @2-FCB6E20C

//Include Page implementation @124-58DBA1E3
include_once(RelativePath . "/Footer.php");
//End Include Page implementation

//Initialize Page @1-E22CEB68
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
$TemplateFileName = "Default.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-946ECC7A
CCSecurityRedirect("1;2;3", "");
//End Authenticate User

//Include events file @1-89CE63AA
include("./Default_events.php");
//End Include events file

//Initialize Objects @1-824528FF
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$Header = new clsHeader("", "Header", $MainPage);
$Header->Initialize();
$issuesSearch = new clsRecordissuesSearch("", $MainPage);
$Link1 = new clsControl(ccsLink, "Link1", "Link1", ccsText, "", CCGetRequestParam("Link1", ccsGet, NULL), $MainPage);
$Link1->Page = "Default.php";
$Link2 = new clsControl(ccsLink, "Link2", "Link2", ccsText, "", CCGetRequestParam("Link2", ccsGet, NULL), $MainPage);
$Link2->Page = "Default.php";
$Link4 = new clsControl(ccsLink, "Link4", "Link4", ccsText, "", CCGetRequestParam("Link4", ccsGet, NULL), $MainPage);
$Link4->Parameters = CCAddParam($Link4->Parameters, "s_assigned_by", CCGetSession("UserID", NULL));
$Link4->Page = "Default.php";
$Link5 = new clsControl(ccsLink, "Link5", "Link5", ccsText, "", CCGetRequestParam("Link5", ccsGet, NULL), $MainPage);
$Link5->Parameters = CCAddParam($Link5->Parameters, "s_assigned_to", CCGetSession("UserID", NULL));
$Link5->Page = "Default.php";
$summary = new clsGridsummary("", $MainPage);
$issues = new clsGridissues("", $MainPage);
$Footer = new clsFooter("", "Footer", $MainPage);
$Footer->Initialize();
$MainPage->Header = & $Header;
$MainPage->issuesSearch = & $issuesSearch;
$MainPage->Link1 = & $Link1;
$MainPage->Link2 = & $Link2;
$MainPage->Link4 = & $Link4;
$MainPage->Link5 = & $Link5;
$MainPage->summary = & $summary;
$MainPage->issues = & $issues;
$MainPage->Footer = & $Footer;
$Link1->Parameters = "";
$Link1->Parameters = CCAddParam($Link1->Parameters, "s_notstatus_id", 0);
$Link2->Parameters = "";
$Link2->Parameters = CCAddParam($Link2->Parameters, "s_notstatus_id", 3);
$summary->Initialize();
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

//Execute Components @1-AFAD5F2E
$Header->Operations();
$issuesSearch->Operation();
$Footer->Operations();
//End Execute Components

//Go to destination page @1-3CC20BD8
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    $Header->Class_Terminate();
    unset($Header);
    unset($issuesSearch);
    unset($summary);
    unset($issues);
    $Footer->Class_Terminate();
    unset($Footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-51F558C3
$Header->Show();
$issuesSearch->Show();
$summary->Show();
$issues->Show();
$Footer->Show();
$Link1->Show();
$Link2->Show();
$Link4->Show();
$Link5->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-B2AFAA49
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
$Header->Class_Terminate();
unset($Header);
unset($issuesSearch);
unset($summary);
unset($issues);
$Footer->Class_Terminate();
unset($Footer);
unset($Tpl);
//End Unload Page


?>
