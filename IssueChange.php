<?php
//Include Common Files @1-B2B30361
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "IssueChange.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @225-3DD2EFDC
include_once(RelativePath . "/Header.php");
//End Include Page implementation

class clsGridissue { //issue class @2-14060E68

//Variables @2-663D5B8C

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

//Class_Initialize Event @2-8A6E269D
    function clsGridissue($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "issue";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid issue";
        $this->DataSource = new clsissueDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 1;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0) $this->PageNumber = 1;

        $this->issue_id = & new clsControl(ccsLabel, "issue_id", "issue_id", ccsText, "", CCGetRequestParam("issue_id", ccsGet, NULL), $this);
        $this->issue_name = & new clsControl(ccsLabel, "issue_name", "issue_name", ccsText, "", CCGetRequestParam("issue_name", ccsGet, NULL), $this);
        $this->issue_desc = & new clsControl(ccsLabel, "issue_desc", "issue_desc", ccsMemo, "", CCGetRequestParam("issue_desc", ccsGet, NULL), $this);
        $this->user_id = & new clsControl(ccsLabel, "user_id", "user_id", ccsText, "", CCGetRequestParam("user_id", ccsGet, NULL), $this);
        $this->date_submitted = & new clsControl(ccsLabel, "date_submitted", "date_submitted", ccsDate, array("GeneralDate"), CCGetRequestParam("date_submitted", ccsGet, NULL), $this);
        $this->version = & new clsControl(ccsLabel, "version", "version", ccsText, "", CCGetRequestParam("version", ccsGet, NULL), $this);
        $this->tested = & new clsControl(ccsLabel, "tested", "tested", ccsBoolean, array("res:im_yes", "res:im_no", ""), CCGetRequestParam("tested", ccsGet, NULL), $this);
        $this->approved = & new clsControl(ccsLabel, "approved", "approved", ccsBoolean, array("res:im_yes", "res:im_no", ""), CCGetRequestParam("approved", ccsGet, NULL), $this);
        $this->assigned_to_orig = & new clsControl(ccsLabel, "assigned_to_orig", "assigned_to_orig", ccsText, "", CCGetRequestParam("assigned_to_orig", ccsGet, NULL), $this);
        $this->assigned_to = & new clsControl(ccsLabel, "assigned_to", "assigned_to", ccsText, "", CCGetRequestParam("assigned_to", ccsGet, NULL), $this);
        $this->priority_id = & new clsControl(ccsLabel, "priority_id", "priority_id", ccsText, "", CCGetRequestParam("priority_id", ccsGet, NULL), $this);
        $this->status_id = & new clsControl(ccsLabel, "status_id", "status_id", ccsText, "", CCGetRequestParam("status_id", ccsGet, NULL), $this);
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

//Show Method @2-C4D76023
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
            $this->ControlsVisible["issue_id"] = $this->issue_id->Visible;
            $this->ControlsVisible["issue_name"] = $this->issue_name->Visible;
            $this->ControlsVisible["issue_desc"] = $this->issue_desc->Visible;
            $this->ControlsVisible["user_id"] = $this->user_id->Visible;
            $this->ControlsVisible["date_submitted"] = $this->date_submitted->Visible;
            $this->ControlsVisible["version"] = $this->version->Visible;
            $this->ControlsVisible["tested"] = $this->tested->Visible;
            $this->ControlsVisible["approved"] = $this->approved->Visible;
            $this->ControlsVisible["assigned_to_orig"] = $this->assigned_to_orig->Visible;
            $this->ControlsVisible["assigned_to"] = $this->assigned_to->Visible;
            $this->ControlsVisible["priority_id"] = $this->priority_id->Visible;
            $this->ControlsVisible["status_id"] = $this->status_id->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->issue_id->SetValue($this->DataSource->issue_id->GetValue());
                $this->issue_name->SetValue($this->DataSource->issue_name->GetValue());
                $this->issue_desc->SetValue($this->DataSource->issue_desc->GetValue());
                $this->user_id->SetValue($this->DataSource->user_id->GetValue());
                $this->date_submitted->SetValue($this->DataSource->date_submitted->GetValue());
                $this->version->SetValue($this->DataSource->version->GetValue());
                $this->tested->SetValue($this->DataSource->tested->GetValue());
                $this->approved->SetValue($this->DataSource->approved->GetValue());
                $this->assigned_to_orig->SetValue($this->DataSource->assigned_to_orig->GetValue());
                $this->assigned_to->SetValue($this->DataSource->assigned_to->GetValue());
                $this->priority_id->SetValue($this->DataSource->priority_id->GetValue());
                $this->status_id->SetValue($this->DataSource->status_id->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->issue_id->Show();
                $this->issue_name->Show();
                $this->issue_desc->Show();
                $this->user_id->Show();
                $this->date_submitted->Show();
                $this->version->Show();
                $this->tested->Show();
                $this->approved->Show();
                $this->assigned_to_orig->Show();
                $this->assigned_to->Show();
                $this->priority_id->Show();
                $this->status_id->Show();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                $Tpl->parse("Row", true);
            } while (($this->RowNumber < $this->PageSize) && $this->DataSource->next_record());
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

//GetErrors Method @2-C10CED48
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->issue_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->issue_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->issue_desc->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_submitted->Errors->ToString());
        $errors = ComposeStrings($errors, $this->version->Errors->ToString());
        $errors = ComposeStrings($errors, $this->tested->Errors->ToString());
        $errors = ComposeStrings($errors, $this->approved->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_to_orig->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_to->Errors->ToString());
        $errors = ComposeStrings($errors, $this->priority_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->status_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End issue Class @2-FCB6E20C

class clsissueDataSource extends clsDBIM {  //issueDataSource Class @2-EAE0EACB

//DataSource Variables @2-D7C1A03E
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
    var $issue_desc;
    var $user_id;
    var $date_submitted;
    var $version;
    var $tested;
    var $approved;
    var $assigned_to_orig;
    var $assigned_to;
    var $priority_id;
    var $status_id;
//End DataSource Variables

//DataSourceClass_Initialize Event @2-CB4AB28B
    function clsissueDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid issue";
        $this->Initialize();
        $this->issue_id = new clsField("issue_id", ccsText, "");
        $this->issue_name = new clsField("issue_name", ccsText, "");
        $this->issue_desc = new clsField("issue_desc", ccsMemo, "");
        $this->user_id = new clsField("user_id", ccsText, "");
        $this->date_submitted = new clsField("date_submitted", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->version = new clsField("version", ccsText, "");
        $this->tested = new clsField("tested", ccsBoolean, array(1, 0, ""));
        $this->approved = new clsField("approved", ccsBoolean, array(1, 0, ""));
        $this->assigned_to_orig = new clsField("assigned_to_orig", ccsText, "");
        $this->assigned_to = new clsField("assigned_to", ccsText, "");
        $this->priority_id = new clsField("priority_id", ccsText, "");
        $this->status_id = new clsField("status_id", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @2-9E1383D1
    function SetOrder($SorterName, $SorterDirection)
    {
    $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @2-D7D2D64D
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlissue_id", ccsInteger, "", "", $this->Parameters["urlissue_id"], 74, false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "issue_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @2-4F9EF596
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM ((((issues LEFT JOIN priorities ON\n\n" .
        "priorities.priority_id = issues.priority_id) LEFT JOIN statuses ON\n\n" .
        "statuses.status_id = issues.status_id) LEFT JOIN users users1 ON\n\n" .
        "users1.user_id = issues.assigned_to_orig) LEFT JOIN users users2 ON\n\n" .
        "users2.user_id = issues.assigned_to) LEFT JOIN users ON\n\n" .
        "users.user_id = issues.user_id";
        $this->SQL = "SELECT issues.*, priority_desc, status, users.user_name AS users_user_name, users1.user_name AS users1_user_name, users2.user_name AS users2_user_name \n\n" .
        "FROM ((((issues LEFT JOIN priorities ON\n\n" .
        "priorities.priority_id = issues.priority_id) LEFT JOIN statuses ON\n\n" .
        "statuses.status_id = issues.status_id) LEFT JOIN users users1 ON\n\n" .
        "users1.user_id = issues.assigned_to_orig) LEFT JOIN users users2 ON\n\n" .
        "users2.user_id = issues.assigned_to) LEFT JOIN users ON\n\n" .
        "users.user_id = issues.user_id {SQL_Where} {SQL_OrderBy}";
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

//SetValues Method @2-D7C777E6
    function SetValues()
    {
        $this->issue_id->SetDBValue($this->f("issue_id"));
        $this->issue_name->SetDBValue($this->f("issue_name"));
        $this->issue_desc->SetDBValue($this->f("issue_desc"));
        $this->user_id->SetDBValue($this->f("users_user_name"));
        $this->date_submitted->SetDBValue(trim($this->f("date_submitted")));
        $this->version->SetDBValue($this->f("version"));
        $this->tested->SetDBValue(trim($this->f("tested")));
        $this->approved->SetDBValue(trim($this->f("approved")));
        $this->assigned_to_orig->SetDBValue($this->f("users1_user_name"));
        $this->assigned_to->SetDBValue($this->f("users2_user_name"));
        $this->priority_id->SetDBValue($this->f("priority_desc"));
        $this->status_id->SetDBValue($this->f("status"));
    }
//End SetValues Method

} //End issueDataSource Class @2-FCB6E20C

class clsGridfiles { //files class @231-C7C462F0

//Variables @231-663D5B8C

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

//Class_Initialize Event @231-8CCCED7C
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

        $this->file_name = & new clsControl(ccsLink, "file_name", "file_name", ccsText, "", CCGetRequestParam("file_name", ccsGet, NULL), $this);
        $this->uploaded_by = & new clsControl(ccsLabel, "uploaded_by", "uploaded_by", ccsText, "", CCGetRequestParam("uploaded_by", ccsGet, NULL), $this);
        $this->date_uploaded = & new clsControl(ccsLabel, "date_uploaded", "date_uploaded", ccsDate, array("GeneralDate"), CCGetRequestParam("date_uploaded", ccsGet, NULL), $this);
    }
//End Class_Initialize Event

//Initialize Method @231-75D22D4D
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @231-AF7AEFCA
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
                $this->file_name->Page = $this->DataSource->f("file_name");
                $this->uploaded_by->SetValue($this->DataSource->uploaded_by->GetValue());
                $this->date_uploaded->SetValue($this->DataSource->date_uploaded->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->file_name->Show();
                $this->uploaded_by->Show();
                $this->date_uploaded->Show();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                $Tpl->parse("Row", true);
            } while ($this->DataSource->next_record());
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

//GetErrors Method @231-96E21992
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

} //End files Class @231-FCB6E20C

class clsfilesDataSource extends clsDBIM {  //filesDataSource Class @231-50AE40EA

//DataSource Variables @231-2B86F4D4
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

//DataSourceClass_Initialize Event @231-DB87FCA2
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

//SetOrder Method @231-9E1383D1
    function SetOrder($SorterName, $SorterDirection)
    {
    $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @231-06600929
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlissue_id", ccsInteger, "", "", $this->Parameters["urlissue_id"], -1, false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "issue_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @231-32E5627A
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

//SetValues Method @231-C993B170
    function SetValues()
    {
        $this->file_name->SetDBValue($this->f("file_name"));
        $this->uploaded_by->SetDBValue($this->f("user_name"));
        $this->date_uploaded->SetDBValue(trim($this->f("date_uploaded")));
    }
//End SetValues Method

} //End filesDataSource Class @231-FCB6E20C

class clsRecordissues { //issues Class @34-CA3823B4

//Variables @34-0DF9B1C2

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

//Class_Initialize Event @34-9A1208FE
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
        $this->ReadAllowed = true;
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
            $this->issue_resp = & new clsControl(ccsTextArea, "issue_resp", $CCSLocales->GetText("im_response"), ccsMemo, "", CCGetRequestParam("issue_resp", $Method, NULL), $this);
            $this->issue_resp->Required = true;
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
            $this->priority_id->DataSource->Order = "priority_order";
            $this->priority_id->DataSource->Order = "priority_order";
            $this->priority_id->Required = true;
            $this->status_id = & new clsControl(ccsListBox, "status_id", $CCSLocales->GetText("im_status"), ccsInteger, "", CCGetRequestParam("status_id", $Method, NULL), $this);
            $this->status_id->DSType = dsTable;
            list($this->status_id->BoundColumn, $this->status_id->TextColumn, $this->status_id->DBFormat) = array("status_id", "status", "");
            $this->status_id->DataSource = new clsDBIM();
            $this->status_id->ds = & $this->status_id->DataSource;
            $this->status_id->DataSource->SQL = "SELECT * \n" .
"FROM statuses {SQL_Where} {SQL_OrderBy}";
            $this->status_id->DataSource->Order = "status";
            $this->status_id->DataSource->Order = "status";
            $this->status_id->Required = true;
            $this->version = & new clsControl(ccsTextBox, "version", $CCSLocales->GetText("im_version"), ccsText, "", CCGetRequestParam("version", $Method, NULL), $this);
            $this->tested = & new clsControl(ccsCheckBox, "tested", "Tested", ccsInteger, "", CCGetRequestParam("tested", $Method, NULL), $this);
            $this->tested->CheckedValue = $this->tested->GetParsedValue(1);
            $this->tested->UncheckedValue = $this->tested->GetParsedValue(0);
            $this->approved = & new clsControl(ccsCheckBox, "approved", "Approved", ccsInteger, "", CCGetRequestParam("approved", $Method, NULL), $this);
            $this->approved->CheckedValue = $this->approved->GetParsedValue(1);
            $this->approved->UncheckedValue = $this->approved->GetParsedValue(0);
            $this->UploadControls = & new clsPanel("UploadControls", $this);
            $this->attachment = & new clsFileUpload("attachment", $CCSLocales->GetText("im_file"), "temp/", "uploads/", "*", "", 1000000, $this);
            $this->Insert = & new clsButton("Insert", $Method, $this);
            $this->FormAction = & new clsControl(ccsHidden, "FormAction", "FormAction", ccsText, "", CCGetRequestParam("FormAction", $Method, NULL), $this);
            $this->date_now = & new clsControl(ccsHidden, "date_now", "date_now", ccsDate, array("GeneralDate"), CCGetRequestParam("date_now", $Method, NULL), $this);
            $this->UploadControls->AddComponent("attachment", $this->attachment);
            if(!$this->FormSubmitted) {
                if(!is_array($this->tested->Value) && !strlen($this->tested->Value) && $this->tested->Value !== false)
                    $this->tested->SetValue(true);
                if(!is_array($this->approved->Value) && !strlen($this->approved->Value) && $this->approved->Value !== false)
                    $this->approved->SetValue(false);
                if(!is_array($this->date_now->Value) && !strlen($this->date_now->Value) && $this->date_now->Value !== false)
                    $this->date_now->SetValue(time());
            }
        }
    }
//End Class_Initialize Event

//Initialize Method @34-6C7B8AC5
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urlissue_id"] = CCGetFromGet("issue_id", NULL);
    }
//End Initialize Method

//Validate Method @34-4F2FA32C
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->issue_resp->Validate() && $Validation);
        $Validation = ($this->assigned_to->Validate() && $Validation);
        $Validation = ($this->priority_id->Validate() && $Validation);
        $Validation = ($this->status_id->Validate() && $Validation);
        $Validation = ($this->version->Validate() && $Validation);
        $Validation = ($this->tested->Validate() && $Validation);
        $Validation = ($this->approved->Validate() && $Validation);
        $Validation = ($this->attachment->Validate() && $Validation);
        $Validation = ($this->FormAction->Validate() && $Validation);
        $Validation = ($this->date_now->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->issue_resp->Errors->Count() == 0);
        $Validation =  $Validation && ($this->assigned_to->Errors->Count() == 0);
        $Validation =  $Validation && ($this->priority_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->status_id->Errors->Count() == 0);
        $Validation =  $Validation && ($this->version->Errors->Count() == 0);
        $Validation =  $Validation && ($this->tested->Errors->Count() == 0);
        $Validation =  $Validation && ($this->approved->Errors->Count() == 0);
        $Validation =  $Validation && ($this->attachment->Errors->Count() == 0);
        $Validation =  $Validation && ($this->FormAction->Errors->Count() == 0);
        $Validation =  $Validation && ($this->date_now->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @34-689F6AEF
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->issue_resp->Errors->Count());
        $errors = ($errors || $this->assigned_to->Errors->Count());
        $errors = ($errors || $this->priority_id->Errors->Count());
        $errors = ($errors || $this->status_id->Errors->Count());
        $errors = ($errors || $this->version->Errors->Count());
        $errors = ($errors || $this->tested->Errors->Count());
        $errors = ($errors || $this->approved->Errors->Count());
        $errors = ($errors || $this->attachment->Errors->Count());
        $errors = ($errors || $this->FormAction->Errors->Count());
        $errors = ($errors || $this->date_now->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @34-84FCCC58
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
            $this->PressedButton = $this->EditMode ? "" : "Insert";
            if($this->Insert->Pressed) {
                $this->PressedButton = "Insert";
            }
        }
        $Redirect = "Default.php" . "?" . CCGetQueryString("QueryString", array("ccsForm", "issue_id"));
        if($this->Validate()) {
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

//InsertRow Method @34-A9C8CC5D
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert", $this);
        if(!$this->InsertAllowed) return false;
        $this->DataSource->date_now->SetValue($this->date_now->GetValue(true));
        $this->DataSource->issue_resp->SetValue($this->issue_resp->GetValue(true));
        $this->DataSource->assigned_to->SetValue($this->assigned_to->GetValue(true));
        $this->DataSource->priority_id->SetValue($this->priority_id->GetValue(true));
        $this->DataSource->status_id->SetValue($this->status_id->GetValue(true));
        $this->DataSource->version->SetValue($this->version->GetValue(true));
        $this->DataSource->tested->SetValue($this->tested->GetValue(true));
        $this->DataSource->approved->SetValue($this->approved->GetValue(true));
        $this->DataSource->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert", $this);
        if($this->DataSource->Errors->Count() == 0) {
            $this->attachment->Move();
        }
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//UpdateRow Method @34-947729D0
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->assigned_to->SetValue($this->assigned_to->GetValue(true));
        $this->DataSource->priority_id->SetValue($this->priority_id->GetValue(true));
        $this->DataSource->status_id->SetValue($this->status_id->GetValue(true));
        $this->DataSource->version->SetValue($this->version->GetValue(true));
        $this->DataSource->tested->SetValue($this->tested->GetValue(true));
        $this->DataSource->approved->SetValue($this->approved->GetValue(true));
        $this->DataSource->date_now->SetValue($this->date_now->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        if($this->DataSource->Errors->Count() == 0) {
            $this->attachment->Move();
        }
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//Show Method @34-A67FC8BE
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

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
                    $this->assigned_to->SetValue($this->DataSource->assigned_to->GetValue());
                    $this->priority_id->SetValue($this->DataSource->priority_id->GetValue());
                    $this->status_id->SetValue($this->DataSource->status_id->GetValue());
                    $this->version->SetValue($this->DataSource->version->GetValue());
                    $this->tested->SetValue($this->DataSource->tested->GetValue());
                    $this->approved->SetValue($this->DataSource->approved->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }
        if (!$this->FormSubmitted) {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->issue_resp->Errors->ToString());
            $Error = ComposeStrings($Error, $this->assigned_to->Errors->ToString());
            $Error = ComposeStrings($Error, $this->priority_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->status_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->version->Errors->ToString());
            $Error = ComposeStrings($Error, $this->tested->Errors->ToString());
            $Error = ComposeStrings($Error, $this->approved->Errors->ToString());
            $Error = ComposeStrings($Error, $this->attachment->Errors->ToString());
            $Error = ComposeStrings($Error, $this->FormAction->Errors->ToString());
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

        $this->issue_resp->Show();
        $this->assigned_to->Show();
        $this->priority_id->Show();
        $this->status_id->Show();
        $this->version->Show();
        $this->tested->Show();
        $this->approved->Show();
        $this->UploadControls->Show();
        $this->Insert->Show();
        $this->FormAction->Show();
        $this->date_now->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End issues Class @34-FCB6E20C

class clsissuesDataSource extends clsDBIM {  //issuesDataSource Class @34-FEEDA2F4

//DataSource Variables @34-869FC092
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $InsertParameters;
    var $UpdateParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $issue_resp;
    var $assigned_to;
    var $priority_id;
    var $status_id;
    var $version;
    var $tested;
    var $approved;
    var $attachment;
    var $FormAction;
    var $date_now;
//End DataSource Variables

//DataSourceClass_Initialize Event @34-33ED3ED9
    function clsissuesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record issues/Error";
        $this->Initialize();
        $this->issue_resp = new clsField("issue_resp", ccsMemo, "");
        $this->assigned_to = new clsField("assigned_to", ccsInteger, "");
        $this->priority_id = new clsField("priority_id", ccsInteger, "");
        $this->status_id = new clsField("status_id", ccsInteger, "");
        $this->version = new clsField("version", ccsText, "");
        $this->tested = new clsField("tested", ccsInteger, "");
        $this->approved = new clsField("approved", ccsInteger, "");
        $this->attachment = new clsField("attachment", ccsText, "");
        $this->FormAction = new clsField("FormAction", ccsText, "");
        $this->date_now = new clsField("date_now", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));

        $this->InsertFields["user_id"] = array("Name" => "user_id", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["issue_id"] = array("Name" => "issue_id", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["date_response"] = array("Name" => "date_response", "Value" => "", "DataType" => ccsDate);
        $this->InsertFields["response"] = array("Name" => "response", "Value" => "", "DataType" => ccsMemo);
        $this->InsertFields["assigned_to"] = array("Name" => "assigned_to", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["priority_id"] = array("Name" => "priority_id", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["status_id"] = array("Name" => "status_id", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["version"] = array("Name" => "version", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["tested"] = array("Name" => "tested", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["approved"] = array("Name" => "approved", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["assigned_to"] = array("Name" => "assigned_to", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["priority_id"] = array("Name" => "priority_id", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["status_id"] = array("Name" => "status_id", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["version"] = array("Name" => "version", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["tested"] = array("Name" => "tested", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["approved"] = array("Name" => "approved", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["date_modified"] = array("Name" => "date_modified", "Value" => "", "DataType" => ccsDate);
        $this->UpdateFields["modified_by"] = array("Name" => "modified_by", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["date_resolved"] = array("Name" => "date_resolved", "Value" => "", "DataType" => ccsDate);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @34-B8A68F25
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

//Open Method @34-1FE97867
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

//SetValues Method @34-FDCC0085
    function SetValues()
    {
        $this->assigned_to->SetDBValue(trim($this->f("assigned_to")));
        $this->priority_id->SetDBValue(trim($this->f("priority_id")));
        $this->status_id->SetDBValue(trim($this->f("status_id")));
        $this->version->SetDBValue($this->f("version"));
        $this->tested->SetDBValue(trim($this->f("tested")));
        $this->approved->SetDBValue(trim($this->f("approved")));
    }
//End SetValues Method

//Insert Method @34-09E73D1A
    function Insert()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["user_id"] = new clsSQLParameter("sesUserID", ccsInteger, "", "", CCGetSession("UserID", NULL), "", false, $this->ErrorBlock);
        $this->cp["issue_id"] = new clsSQLParameter("urlissue_id", ccsInteger, "", "", CCGetFromGet("issue_id", NULL), "", false, $this->ErrorBlock);
        $this->cp["date_response"] = new clsSQLParameter("ctrldate_now", ccsDate, array("GeneralDate"), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"), $this->date_now->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["response"] = new clsSQLParameter("ctrlissue_resp", ccsMemo, "", "", $this->issue_resp->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["assigned_to"] = new clsSQLParameter("ctrlassigned_to", ccsInteger, "", "", $this->assigned_to->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["priority_id"] = new clsSQLParameter("ctrlpriority_id", ccsInteger, "", "", $this->priority_id->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["status_id"] = new clsSQLParameter("ctrlstatus_id", ccsInteger, "", "", $this->status_id->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["version"] = new clsSQLParameter("ctrlversion", ccsText, "", "", $this->version->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["tested"] = new clsSQLParameter("ctrltested", ccsInteger, "", "", $this->tested->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["approved"] = new clsSQLParameter("ctrlapproved", ccsInteger, "", "", $this->approved->GetValue(true), "", false, $this->ErrorBlock);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert", $this->Parent);
        if (!strlen($this->cp["user_id"]->GetText()) and !is_bool($this->cp["user_id"]->GetValue())) 
            $this->cp["user_id"]->SetValue(CCGetSession("UserID", NULL));
        if (!strlen($this->cp["issue_id"]->GetText()) and !is_bool($this->cp["issue_id"]->GetValue())) 
            $this->cp["issue_id"]->SetText(CCGetFromGet("issue_id", NULL));
        if (!strlen($this->cp["date_response"]->GetText()) and !is_bool($this->cp["date_response"]->GetValue())) 
            $this->cp["date_response"]->SetValue($this->date_now->GetValue(true));
        if (!strlen($this->cp["response"]->GetText()) and !is_bool($this->cp["response"]->GetValue())) 
            $this->cp["response"]->SetValue($this->issue_resp->GetValue(true));
        if (!strlen($this->cp["assigned_to"]->GetText()) and !is_bool($this->cp["assigned_to"]->GetValue())) 
            $this->cp["assigned_to"]->SetValue($this->assigned_to->GetValue(true));
        if (!strlen($this->cp["priority_id"]->GetText()) and !is_bool($this->cp["priority_id"]->GetValue())) 
            $this->cp["priority_id"]->SetValue($this->priority_id->GetValue(true));
        if (!strlen($this->cp["status_id"]->GetText()) and !is_bool($this->cp["status_id"]->GetValue())) 
            $this->cp["status_id"]->SetValue($this->status_id->GetValue(true));
        if (!strlen($this->cp["version"]->GetText()) and !is_bool($this->cp["version"]->GetValue())) 
            $this->cp["version"]->SetValue($this->version->GetValue(true));
        if (!strlen($this->cp["tested"]->GetText()) and !is_bool($this->cp["tested"]->GetValue())) 
            $this->cp["tested"]->SetValue($this->tested->GetValue(true));
        if (!strlen($this->cp["approved"]->GetText()) and !is_bool($this->cp["approved"]->GetValue())) 
            $this->cp["approved"]->SetValue($this->approved->GetValue(true));
        $this->InsertFields["user_id"]["Value"] = $this->cp["user_id"]->GetDBValue(true);
        $this->InsertFields["issue_id"]["Value"] = $this->cp["issue_id"]->GetDBValue(true);
        $this->InsertFields["date_response"]["Value"] = $this->cp["date_response"]->GetDBValue(true);
        $this->InsertFields["response"]["Value"] = $this->cp["response"]->GetDBValue(true);
        $this->InsertFields["assigned_to"]["Value"] = $this->cp["assigned_to"]->GetDBValue(true);
        $this->InsertFields["priority_id"]["Value"] = $this->cp["priority_id"]->GetDBValue(true);
        $this->InsertFields["status_id"]["Value"] = $this->cp["status_id"]->GetDBValue(true);
        $this->InsertFields["version"]["Value"] = $this->cp["version"]->GetDBValue(true);
        $this->InsertFields["tested"]["Value"] = $this->cp["tested"]->GetDBValue(true);
        $this->InsertFields["approved"]["Value"] = $this->cp["approved"]->GetDBValue(true);
        $this->SQL = CCBuildInsert("responses", $this->InsertFields, $this);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert", $this->Parent);
        }
    }
//End Insert Method

//Update Method @34-CFC4E818
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["assigned_to"] = new clsSQLParameter("ctrlassigned_to", ccsInteger, "", "", $this->assigned_to->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["priority_id"] = new clsSQLParameter("ctrlpriority_id", ccsInteger, "", "", $this->priority_id->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["status_id"] = new clsSQLParameter("ctrlstatus_id", ccsInteger, "", "", $this->status_id->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["version"] = new clsSQLParameter("ctrlversion", ccsText, "", "", $this->version->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["tested"] = new clsSQLParameter("ctrltested", ccsInteger, "", "", $this->tested->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["approved"] = new clsSQLParameter("ctrlapproved", ccsInteger, "", "", $this->approved->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["date_modified"] = new clsSQLParameter("ctrldate_now", ccsDate, array("GeneralDate"), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"), $this->date_now->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["modified_by"] = new clsSQLParameter("sesUserID", ccsInteger, "", "", CCGetSession("UserID", NULL), "", false, $this->ErrorBlock);
        $this->cp["date_resolved"] = new clsSQLParameter("expr286", ccsDate, array("GeneralDate"), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"), "", "", false, $this->ErrorBlock);
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urlissue_id", ccsInteger, "", "", CCGetFromGet("issue_id", NULL), "", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        if (!strlen($this->cp["assigned_to"]->GetText()) and !is_bool($this->cp["assigned_to"]->GetValue())) 
            $this->cp["assigned_to"]->SetValue($this->assigned_to->GetValue(true));
        if (!strlen($this->cp["priority_id"]->GetText()) and !is_bool($this->cp["priority_id"]->GetValue())) 
            $this->cp["priority_id"]->SetValue($this->priority_id->GetValue(true));
        if (!strlen($this->cp["status_id"]->GetText()) and !is_bool($this->cp["status_id"]->GetValue())) 
            $this->cp["status_id"]->SetValue($this->status_id->GetValue(true));
        if (!strlen($this->cp["version"]->GetText()) and !is_bool($this->cp["version"]->GetValue())) 
            $this->cp["version"]->SetValue($this->version->GetValue(true));
        if (!strlen($this->cp["tested"]->GetText()) and !is_bool($this->cp["tested"]->GetValue())) 
            $this->cp["tested"]->SetValue($this->tested->GetValue(true));
        if (!strlen($this->cp["approved"]->GetText()) and !is_bool($this->cp["approved"]->GetValue())) 
            $this->cp["approved"]->SetValue($this->approved->GetValue(true));
        if (!strlen($this->cp["date_modified"]->GetText()) and !is_bool($this->cp["date_modified"]->GetValue())) 
            $this->cp["date_modified"]->SetValue($this->date_now->GetValue(true));
        if (!strlen($this->cp["modified_by"]->GetText()) and !is_bool($this->cp["modified_by"]->GetValue())) 
            $this->cp["modified_by"]->SetValue(CCGetSession("UserID", NULL));
        if (!strlen($this->cp["date_resolved"]->GetText()) and !is_bool($this->cp["date_resolved"]->GetValue())) 
            $this->cp["date_resolved"]->SetValue("");
        $wp->Criterion[1] = $wp->Operation(opEqual, "issue_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = 
             $wp->Criterion[1];
        $this->UpdateFields["assigned_to"]["Value"] = $this->cp["assigned_to"]->GetDBValue(true);
        $this->UpdateFields["priority_id"]["Value"] = $this->cp["priority_id"]->GetDBValue(true);
        $this->UpdateFields["status_id"]["Value"] = $this->cp["status_id"]->GetDBValue(true);
        $this->UpdateFields["version"]["Value"] = $this->cp["version"]->GetDBValue(true);
        $this->UpdateFields["tested"]["Value"] = $this->cp["tested"]->GetDBValue(true);
        $this->UpdateFields["approved"]["Value"] = $this->cp["approved"]->GetDBValue(true);
        $this->UpdateFields["date_modified"]["Value"] = $this->cp["date_modified"]->GetDBValue(true);
        $this->UpdateFields["modified_by"]["Value"] = $this->cp["modified_by"]->GetDBValue(true);
        $this->UpdateFields["date_resolved"]["Value"] = $this->cp["date_resolved"]->GetDBValue(true);
        $this->SQL = CCBuildUpdate("issues", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

} //End issuesDataSource Class @34-FCB6E20C

class clsGridresponses1 { //responses1 class @25-58C78346

//Variables @25-663D5B8C

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

//Class_Initialize Event @25-6B27E3E9
    function clsGridresponses1($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "responses1";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid responses1";
        $this->DataSource = new clsresponses1DataSource($this);
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

        $this->response = & new clsControl(ccsLabel, "response", "response", ccsMemo, "", CCGetRequestParam("response", ccsGet, NULL), $this);
        $this->user_id = & new clsControl(ccsLabel, "user_id", "user_id", ccsText, "", CCGetRequestParam("user_id", ccsGet, NULL), $this);
        $this->date_response = & new clsControl(ccsLabel, "date_response", "date_response", ccsDate, array("GeneralDate"), CCGetRequestParam("date_response", ccsGet, NULL), $this);
        $this->assigned_to = & new clsControl(ccsLabel, "assigned_to", "assigned_to", ccsText, "", CCGetRequestParam("assigned_to", ccsGet, NULL), $this);
        $this->priority_id = & new clsControl(ccsLabel, "priority_id", "priority_id", ccsText, "", CCGetRequestParam("priority_id", ccsGet, NULL), $this);
        $this->status_id = & new clsControl(ccsLabel, "status_id", "status_id", ccsText, "", CCGetRequestParam("status_id", ccsGet, NULL), $this);
        $this->Navigator = & new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpCentered, $this);
    }
//End Class_Initialize Event

//Initialize Method @25-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @25-BFD9892B
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
            $this->ControlsVisible["response"] = $this->response->Visible;
            $this->ControlsVisible["user_id"] = $this->user_id->Visible;
            $this->ControlsVisible["date_response"] = $this->date_response->Visible;
            $this->ControlsVisible["assigned_to"] = $this->assigned_to->Visible;
            $this->ControlsVisible["priority_id"] = $this->priority_id->Visible;
            $this->ControlsVisible["status_id"] = $this->status_id->Visible;
            do {
                // Parse Separator
                if($this->RowNumber)
                    $Tpl->parseto("Separator", true, "Row");
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->response->SetValue($this->DataSource->response->GetValue());
                $this->user_id->SetValue($this->DataSource->user_id->GetValue());
                $this->date_response->SetValue($this->DataSource->date_response->GetValue());
                $this->assigned_to->SetValue($this->DataSource->assigned_to->GetValue());
                $this->priority_id->SetValue($this->DataSource->priority_id->GetValue());
                $this->status_id->SetValue($this->DataSource->status_id->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->response->Show();
                $this->user_id->Show();
                $this->date_response->Show();
                $this->assigned_to->Show();
                $this->priority_id->Show();
                $this->status_id->Show();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                $Tpl->parse("Row", true);
            } while (($this->RowNumber < $this->PageSize) && $this->DataSource->next_record());
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

//GetErrors Method @25-E27B5044
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->response->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_response->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_to->Errors->ToString());
        $errors = ComposeStrings($errors, $this->priority_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->status_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End responses1 Class @25-FCB6E20C

class clsresponses1DataSource extends clsDBIM {  //responses1DataSource Class @25-28A8E2B7

//DataSource Variables @25-77FA6783
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $response;
    var $user_id;
    var $date_response;
    var $assigned_to;
    var $priority_id;
    var $status_id;
//End DataSource Variables

//DataSourceClass_Initialize Event @25-93C97895
    function clsresponses1DataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid responses1";
        $this->Initialize();
        $this->response = new clsField("response", ccsMemo, "");
        $this->user_id = new clsField("user_id", ccsText, "");
        $this->date_response = new clsField("date_response", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->assigned_to = new clsField("assigned_to", ccsText, "");
        $this->priority_id = new clsField("priority_id", ccsText, "");
        $this->status_id = new clsField("status_id", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @25-84E18093
    function SetOrder($SorterName, $SorterDirection)
    {
    $this->Order = "date_response desc";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @25-06600929
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlissue_id", ccsInteger, "", "", $this->Parameters["urlissue_id"], -1, false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "issue_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @25-E54AC83C
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM (((responses LEFT JOIN users ON\n\n" .
        "users.user_id = responses.user_id) LEFT JOIN users users1 ON\n\n" .
        "users1.user_id = responses.assigned_to) LEFT JOIN priorities ON\n\n" .
        "priorities.priority_id = responses.priority_id) LEFT JOIN statuses ON\n\n" .
        "statuses.status_id = responses.status_id";
        $this->SQL = "SELECT responses.*, users.user_name AS users_user_name, users1.user_name AS users1_user_name, priority_desc, status \n\n" .
        "FROM (((responses LEFT JOIN users ON\n\n" .
        "users.user_id = responses.user_id) LEFT JOIN users users1 ON\n\n" .
        "users1.user_id = responses.assigned_to) LEFT JOIN priorities ON\n\n" .
        "priorities.priority_id = responses.priority_id) LEFT JOIN statuses ON\n\n" .
        "statuses.status_id = responses.status_id {SQL_Where} {SQL_OrderBy}";
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

//SetValues Method @25-B8C1BE8B
    function SetValues()
    {
        $this->response->SetDBValue($this->f("response"));
        $this->user_id->SetDBValue($this->f("users_user_name"));
        $this->date_response->SetDBValue(trim($this->f("date_response")));
        $this->assigned_to->SetDBValue($this->f("users1_user_name"));
        $this->priority_id->SetDBValue($this->f("priority_desc"));
        $this->status_id->SetDBValue($this->f("status"));
    }
//End SetValues Method

} //End responses1DataSource Class @25-FCB6E20C

//Include Page implementation @227-58DBA1E3
include_once(RelativePath . "/Footer.php");
//End Include Page implementation

//Initialize Page @1-4F3EDCAA
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
$TemplateFileName = "IssueChange.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-946ECC7A
CCSecurityRedirect("1;2;3", "");
//End Authenticate User

//Include events file @1-EAC8404C
include("./IssueChange_events.php");
//End Include events file

//Initialize Objects @1-F269FC60
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$Header = & new clsHeader("", "Header", $MainPage);
$Header->Initialize();
$issue = & new clsGridissue("", $MainPage);
$files = & new clsGridfiles("", $MainPage);
$issues = & new clsRecordissues("", $MainPage);
$responses1 = & new clsGridresponses1("", $MainPage);
$Footer = & new clsFooter("", "Footer", $MainPage);
$Footer->Initialize();
$MainPage->Header = & $Header;
$MainPage->issue = & $issue;
$MainPage->files = & $files;
$MainPage->issues = & $issues;
$MainPage->responses1 = & $responses1;
$MainPage->Footer = & $Footer;
$issue->Initialize();
$files->Initialize();
$issues->Initialize();
$responses1->Initialize();

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

//Go to destination page @1-9D26E9EF
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    $Header->Class_Terminate();
    unset($Header);
    unset($issue);
    unset($files);
    unset($issues);
    unset($responses1);
    $Footer->Class_Terminate();
    unset($Footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-D58DE9FE
$Header->Show();
$issue->Show();
$files->Show();
$issues->Show();
$responses1->Show();
$Footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-5431DA61
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
$Header->Class_Terminate();
unset($Header);
unset($issue);
unset($files);
unset($issues);
unset($responses1);
$Footer->Class_Terminate();
unset($Footer);
unset($Tpl);
//End Unload Page


?>
