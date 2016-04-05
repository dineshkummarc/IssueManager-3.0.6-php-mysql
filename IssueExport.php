<?php
//Include Common Files @1-2432747A
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "IssueExport.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

class clsGridissues { //issues class @2-9E712255

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

//Class_Initialize Event @2-DF1E01F9
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

        $this->issue_id = & new clsControl(ccsLabel, "issue_id", "issue_id", ccsInteger, "", CCGetRequestParam("issue_id", ccsGet, NULL), $this);
        $this->issue_name = & new clsControl(ccsLabel, "issue_name", "issue_name", ccsText, "", CCGetRequestParam("issue_name", ccsGet, NULL), $this);
        $this->issue_desc = & new clsControl(ccsLabel, "issue_desc", "issue_desc", ccsMemo, "", CCGetRequestParam("issue_desc", ccsGet, NULL), $this);
        $this->status_id = & new clsControl(ccsLabel, "status_id", "status_id", ccsText, "", CCGetRequestParam("status_id", ccsGet, NULL), $this);
        $this->color = & new clsControl(ccsLabel, "color", "color", ccsText, "", CCGetRequestParam("color", ccsGet, NULL), $this);
        $this->priority_id = & new clsControl(ccsLabel, "priority_id", "priority_id", ccsText, "", CCGetRequestParam("priority_id", ccsGet, NULL), $this);
        $this->priority_id->HTML = true;
        $this->user_id = & new clsControl(ccsLabel, "user_id", "user_id", ccsText, "", CCGetRequestParam("user_id", ccsGet, NULL), $this);
        $this->date_submitted = & new clsControl(ccsLabel, "date_submitted", "date_submitted", ccsDate, $DefaultDateFormat, CCGetRequestParam("date_submitted", ccsGet, NULL), $this);
        $this->assigned_to_orig = & new clsControl(ccsLabel, "assigned_to_orig", "assigned_to_orig", ccsText, "", CCGetRequestParam("assigned_to_orig", ccsGet, NULL), $this);
        $this->assigned_id = & new clsControl(ccsLabel, "assigned_id", "assigned_id", ccsInteger, "", CCGetRequestParam("assigned_id", ccsGet, NULL), $this);
        $this->assigned_to = & new clsControl(ccsLabel, "assigned_to", "assigned_to", ccsText, "", CCGetRequestParam("assigned_to", ccsGet, NULL), $this);
        $this->assigned_to->HTML = true;
        $this->modified_by = & new clsControl(ccsLabel, "modified_by", "modified_by", ccsText, "", CCGetRequestParam("modified_by", ccsGet, NULL), $this);
        $this->date_modified = & new clsControl(ccsLabel, "date_modified", "date_modified", ccsDate, $DefaultDateFormat, CCGetRequestParam("date_modified", ccsGet, NULL), $this);
        $this->tested = & new clsControl(ccsLabel, "tested", "tested", ccsBoolean, array("res:im_yes", "", ""), CCGetRequestParam("tested", ccsGet, NULL), $this);
        $this->approved = & new clsControl(ccsLabel, "approved", "approved", ccsBoolean, array("res:im_yes", "", ""), CCGetRequestParam("approved", ccsGet, NULL), $this);
        $this->version = & new clsControl(ccsLabel, "version", "version", ccsText, "", CCGetRequestParam("version", ccsGet, NULL), $this);
        $this->title = & new clsControl(ccsLabel, "title", "title", ccsText, "", CCGetRequestParam("title", ccsGet, NULL), $this);
        $this->title->HTML = true;
    }
//End Class_Initialize Event

//Initialize Method @2-75D22D4D
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @2-7C30A16E
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["urls_status_id"] = CCGetFromGet("s_status_id", NULL);
        $this->DataSource->Parameters["urls_notstatus_id"] = CCGetFromGet("s_notstatus_id", NULL);
        $this->DataSource->Parameters["urls_priority_id"] = CCGetFromGet("s_priority_id", NULL);
        $this->DataSource->Parameters["urls_assigned_to"] = CCGetFromGet("s_assigned_to", NULL);
        $this->DataSource->Parameters["urls_issue_desc"] = CCGetFromGet("s_issue_desc", NULL);
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
            $this->ControlsVisible["issue_desc"] = $this->issue_desc->Visible;
            $this->ControlsVisible["status_id"] = $this->status_id->Visible;
            $this->ControlsVisible["color"] = $this->color->Visible;
            $this->ControlsVisible["priority_id"] = $this->priority_id->Visible;
            $this->ControlsVisible["user_id"] = $this->user_id->Visible;
            $this->ControlsVisible["date_submitted"] = $this->date_submitted->Visible;
            $this->ControlsVisible["assigned_to_orig"] = $this->assigned_to_orig->Visible;
            $this->ControlsVisible["assigned_id"] = $this->assigned_id->Visible;
            $this->ControlsVisible["assigned_to"] = $this->assigned_to->Visible;
            $this->ControlsVisible["modified_by"] = $this->modified_by->Visible;
            $this->ControlsVisible["date_modified"] = $this->date_modified->Visible;
            $this->ControlsVisible["tested"] = $this->tested->Visible;
            $this->ControlsVisible["approved"] = $this->approved->Visible;
            $this->ControlsVisible["version"] = $this->version->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->issue_id->SetValue($this->DataSource->issue_id->GetValue());
                $this->issue_name->SetValue($this->DataSource->issue_name->GetValue());
                $this->issue_desc->SetValue($this->DataSource->issue_desc->GetValue());
                $this->status_id->SetValue($this->DataSource->status_id->GetValue());
                $this->color->SetValue($this->DataSource->color->GetValue());
                $this->priority_id->SetValue($this->DataSource->priority_id->GetValue());
                $this->user_id->SetValue($this->DataSource->user_id->GetValue());
                $this->date_submitted->SetValue($this->DataSource->date_submitted->GetValue());
                $this->assigned_to_orig->SetValue($this->DataSource->assigned_to_orig->GetValue());
                $this->assigned_id->SetValue($this->DataSource->assigned_id->GetValue());
                $this->assigned_to->SetValue($this->DataSource->assigned_to->GetValue());
                $this->modified_by->SetValue($this->DataSource->modified_by->GetValue());
                $this->date_modified->SetValue($this->DataSource->date_modified->GetValue());
                $this->tested->SetValue($this->DataSource->tested->GetValue());
                $this->approved->SetValue($this->DataSource->approved->GetValue());
                $this->version->SetValue($this->DataSource->version->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->issue_id->Show();
                $this->issue_name->Show();
                $this->issue_desc->Show();
                $this->status_id->Show();
                $this->color->Show();
                $this->priority_id->Show();
                $this->user_id->Show();
                $this->date_submitted->Show();
                $this->assigned_to_orig->Show();
                $this->assigned_id->Show();
                $this->assigned_to->Show();
                $this->modified_by->Show();
                $this->date_modified->Show();
                $this->tested->Show();
                $this->approved->Show();
                $this->version->Show();
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
        $this->title->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @2-0570F769
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->issue_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->issue_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->issue_desc->Errors->ToString());
        $errors = ComposeStrings($errors, $this->status_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->color->Errors->ToString());
        $errors = ComposeStrings($errors, $this->priority_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_submitted->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_to_orig->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->assigned_to->Errors->ToString());
        $errors = ComposeStrings($errors, $this->modified_by->Errors->ToString());
        $errors = ComposeStrings($errors, $this->date_modified->Errors->ToString());
        $errors = ComposeStrings($errors, $this->tested->Errors->ToString());
        $errors = ComposeStrings($errors, $this->approved->Errors->ToString());
        $errors = ComposeStrings($errors, $this->version->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End issues Class @2-FCB6E20C

class clsissuesDataSource extends clsDBIM {  //issuesDataSource Class @2-FEEDA2F4

//DataSource Variables @2-4A90C687
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
    var $status_id;
    var $color;
    var $priority_id;
    var $user_id;
    var $date_submitted;
    var $assigned_to_orig;
    var $assigned_id;
    var $assigned_to;
    var $modified_by;
    var $date_modified;
    var $tested;
    var $approved;
    var $version;
//End DataSource Variables

//DataSourceClass_Initialize Event @2-F2339482
    function clsissuesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid issues";
        $this->Initialize();
        $this->issue_id = new clsField("issue_id", ccsInteger, "");
        $this->issue_name = new clsField("issue_name", ccsText, "");
        $this->issue_desc = new clsField("issue_desc", ccsMemo, "");
        $this->status_id = new clsField("status_id", ccsText, "");
        $this->color = new clsField("color", ccsText, "");
        $this->priority_id = new clsField("priority_id", ccsText, "");
        $this->user_id = new clsField("user_id", ccsText, "");
        $this->date_submitted = new clsField("date_submitted", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->assigned_to_orig = new clsField("assigned_to_orig", ccsText, "");
        $this->assigned_id = new clsField("assigned_id", ccsInteger, "");
        $this->assigned_to = new clsField("assigned_to", ccsText, "");
        $this->modified_by = new clsField("modified_by", ccsText, "");
        $this->date_modified = new clsField("date_modified", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->tested = new clsField("tested", ccsBoolean, array(1, 0, ""));
        $this->approved = new clsField("approved", ccsBoolean, array(1, 0, ""));
        $this->version = new clsField("version", ccsText, "");

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

//Prepare Method @2-B8E6528D
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urls_status_id", ccsInteger, "", "", $this->Parameters["urls_status_id"], "", false);
        $this->wp->AddParameter("2", "urls_notstatus_id", ccsInteger, "", "", $this->Parameters["urls_notstatus_id"], "", false);
        $this->wp->AddParameter("3", "urls_priority_id", ccsInteger, "", "", $this->Parameters["urls_priority_id"], "", false);
        $this->wp->AddParameter("4", "urls_assigned_to", ccsInteger, "", "", $this->Parameters["urls_assigned_to"], "", false);
        $this->wp->AddParameter("5", "urls_issue_desc", ccsText, "", "", $this->Parameters["urls_issue_desc"], "", false);
        $this->wp->AddParameter("6", "urls_issue_desc", ccsMemo, "", "", $this->Parameters["urls_issue_desc"], "", false);
        $this->wp->AddParameter("7", "urls_assigned_by", ccsInteger, "", "", $this->Parameters["urls_assigned_by"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "issues.status_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opNotEqual, "issues.status_id", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsInteger),false);
        $this->wp->Criterion[3] = $this->wp->Operation(opEqual, "issues.priority_id", $this->wp->GetDBValue("3"), $this->ToSQL($this->wp->GetDBValue("3"), ccsInteger),false);
        $this->wp->Criterion[4] = $this->wp->Operation(opEqual, "assigned_to", $this->wp->GetDBValue("4"), $this->ToSQL($this->wp->GetDBValue("4"), ccsInteger),false);
        $this->wp->Criterion[5] = $this->wp->Operation(opContains, "issue_name", $this->wp->GetDBValue("5"), $this->ToSQL($this->wp->GetDBValue("5"), ccsText),false);
        $this->wp->Criterion[6] = $this->wp->Operation(opContains, "issues.issue_desc", $this->wp->GetDBValue("6"), $this->ToSQL($this->wp->GetDBValue("6"), ccsMemo),false);
        $this->wp->Criterion[7] = $this->wp->Operation(opEqual, "users.user_id", $this->wp->GetDBValue("7"), $this->ToSQL($this->wp->GetDBValue("7"), ccsInteger),false);
        $this->Where = $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]), 
             $this->wp->Criterion[3]), 
             $this->wp->Criterion[4]), $this->wp->opOR(
             true, 
             $this->wp->Criterion[5], 
             $this->wp->Criterion[6])), 
             $this->wp->Criterion[7]);
    }
//End Prepare Method

//Open Method @2-73E4D319
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM (((((issues LEFT JOIN statuses ON\n\n" .
        "statuses.status_id = issues.status_id) LEFT JOIN priorities ON\n\n" .
        "priorities.priority_id = issues.priority_id) LEFT JOIN users ON\n\n" .
        "users.user_id = issues.user_id) LEFT JOIN users users1 ON\n\n" .
        "users1.user_id = issues.assigned_to) LEFT JOIN users users2 ON\n\n" .
        "users2.user_id = issues.assigned_to_orig) LEFT JOIN users users3 ON\n\n" .
        "users3.user_id = issues.modified_by";
        $this->SQL = "SELECT issues.*, status, priority_desc, priority_color, users.user_name AS users_user_name, users1.user_name AS users1_user_name,\n\n" .
        "users2.user_name AS users2_user_name, users3.user_name AS users3_user_name \n\n" .
        "FROM (((((issues LEFT JOIN statuses ON\n\n" .
        "statuses.status_id = issues.status_id) LEFT JOIN priorities ON\n\n" .
        "priorities.priority_id = issues.priority_id) LEFT JOIN users ON\n\n" .
        "users.user_id = issues.user_id) LEFT JOIN users users1 ON\n\n" .
        "users1.user_id = issues.assigned_to) LEFT JOIN users users2 ON\n\n" .
        "users2.user_id = issues.assigned_to_orig) LEFT JOIN users users3 ON\n\n" .
        "users3.user_id = issues.modified_by {SQL_Where} {SQL_OrderBy}";
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

//SetValues Method @2-B510EEA8
    function SetValues()
    {
        $this->issue_id->SetDBValue(trim($this->f("issue_id")));
        $this->issue_name->SetDBValue($this->f("issue_name"));
        $this->issue_desc->SetDBValue($this->f("issue_desc"));
        $this->status_id->SetDBValue($this->f("status"));
        $this->color->SetDBValue($this->f("priority_color"));
        $this->priority_id->SetDBValue($this->f("priority_desc"));
        $this->user_id->SetDBValue($this->f("users_user_name"));
        $this->date_submitted->SetDBValue(trim($this->f("date_submitted")));
        $this->assigned_to_orig->SetDBValue($this->f("users2_user_name"));
        $this->assigned_id->SetDBValue(trim($this->f("assigned_to")));
        $this->assigned_to->SetDBValue($this->f("users1_user_name"));
        $this->modified_by->SetDBValue($this->f("users3_user_name"));
        $this->date_modified->SetDBValue(trim($this->f("date_modified")));
        $this->tested->SetDBValue(trim($this->f("tested")));
        $this->approved->SetDBValue(trim($this->f("approved")));
        $this->version->SetDBValue($this->f("version"));
    }
//End SetValues Method

} //End issuesDataSource Class @2-FCB6E20C

//Initialize Page @1-5DEFD880
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
$TemplateFileName = "IssueExport.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Include events file @1-5A196006
include("./IssueExport_events.php");
//End Include events file

//Initialize Objects @1-03AD2DF2
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$issues = & new clsGridissues("", $MainPage);
$MainPage->issues = & $issues;
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

//Go to destination page @1-6B58B887
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    unset($issues);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-E0BBBB5B
$issues->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-4883296A
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
unset($issues);
unset($Tpl);
//End Unload Page


?>
