<?php
//Include Common Files @1-D1C960D8
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "PriorityList.php");
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

class clsGridpriorities { //priorities class @3-60B21725

//Variables @3-A6151A3A

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
    var $Sorter_priority_desc;
    var $Sorter_priority_color;
    var $Sorter_priority_order;
//End Variables

//Class_Initialize Event @3-507AFD3A
    function clsGridpriorities($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "priorities";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid priorities";
        $this->DataSource = new clsprioritiesDataSource($this);
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
        $this->SorterName = CCGetParam("prioritiesOrder", "");
        $this->SorterDirection = CCGetParam("prioritiesDir", "");

        $this->priority_desc = & new clsControl(ccsLink, "priority_desc", "priority_desc", ccsText, "", CCGetRequestParam("priority_desc", ccsGet, NULL), $this);
        $this->priority_desc->Page = "PriorityList.php";
        $this->priority_transl = & new clsControl(ccsLabel, "priority_transl", "priority_transl", ccsText, "", CCGetRequestParam("priority_transl", ccsGet, NULL), $this);
        $this->priority_color = & new clsControl(ccsLabel, "priority_color", "priority_color", ccsText, "", CCGetRequestParam("priority_color", ccsGet, NULL), $this);
        $this->priority_order = & new clsControl(ccsLabel, "priority_order", "priority_order", ccsInteger, "", CCGetRequestParam("priority_order", ccsGet, NULL), $this);
        $this->Sorter_priority_desc = & new clsSorter($this->ComponentName, "Sorter_priority_desc", $FileName, $this);
        $this->Sorter_priority_color = & new clsSorter($this->ComponentName, "Sorter_priority_color", $FileName, $this);
        $this->Sorter_priority_order = & new clsSorter($this->ComponentName, "Sorter_priority_order", $FileName, $this);
        $this->Navigator = & new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpCentered, $this);
        $this->Link1 = & new clsControl(ccsLink, "Link1", "Link1", ccsText, "", CCGetRequestParam("Link1", ccsGet, NULL), $this);
        $this->Link1->Parameters = CCGetQueryString("QueryString", array("priority_id", "ccsForm"));
        $this->Link1->Page = "PriorityList.php";
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

//Show Method @3-1BC47184
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;


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
            $this->ControlsVisible["priority_desc"] = $this->priority_desc->Visible;
            $this->ControlsVisible["priority_transl"] = $this->priority_transl->Visible;
            $this->ControlsVisible["priority_color"] = $this->priority_color->Visible;
            $this->ControlsVisible["priority_order"] = $this->priority_order->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->priority_desc->SetValue($this->DataSource->priority_desc->GetValue());
                $this->priority_desc->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->priority_desc->Parameters = CCAddParam($this->priority_desc->Parameters, "priority_id", $this->DataSource->f("priority_id"));
                $this->priority_transl->SetValue($this->DataSource->priority_transl->GetValue());
                $this->priority_color->SetValue($this->DataSource->priority_color->GetValue());
                $this->priority_order->SetValue($this->DataSource->priority_order->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->priority_desc->Show();
                $this->priority_transl->Show();
                $this->priority_color->Show();
                $this->priority_order->Show();
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
        $this->Sorter_priority_desc->Show();
        $this->Sorter_priority_color->Show();
        $this->Sorter_priority_order->Show();
        $this->Navigator->Show();
        $this->Link1->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @3-C03F41F0
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->priority_desc->Errors->ToString());
        $errors = ComposeStrings($errors, $this->priority_transl->Errors->ToString());
        $errors = ComposeStrings($errors, $this->priority_color->Errors->ToString());
        $errors = ComposeStrings($errors, $this->priority_order->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End priorities Class @3-FCB6E20C

class clsprioritiesDataSource extends clsDBIM {  //prioritiesDataSource Class @3-FB776828

//DataSource Variables @3-93062C85
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $priority_desc;
    var $priority_transl;
    var $priority_color;
    var $priority_order;
//End DataSource Variables

//DataSourceClass_Initialize Event @3-A61D9DD0
    function clsprioritiesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid priorities";
        $this->Initialize();
        $this->priority_desc = new clsField("priority_desc", ccsText, "");
        $this->priority_transl = new clsField("priority_transl", ccsText, "");
        $this->priority_color = new clsField("priority_color", ccsText, "");
        $this->priority_order = new clsField("priority_order", ccsInteger, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @3-71F5E6BD
    function SetOrder($SorterName, $SorterDirection)
    {
    $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_priority_desc" => array("priority_desc", ""), 
            "Sorter_priority_color" => array("priority_color", ""), 
            "Sorter_priority_order" => array("priority_order", "")));
    }
//End SetOrder Method

//Prepare Method @3-14D6CD9D
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
    }
//End Prepare Method

//Open Method @3-38F39C96
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM priorities";
        $this->SQL = "SELECT * \n\n" .
        "FROM priorities {SQL_Where} {SQL_OrderBy}";
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

//SetValues Method @3-59FC4455
    function SetValues()
    {
        $this->priority_desc->SetDBValue($this->f("priority_desc"));
        $this->priority_transl->SetDBValue($this->f("priority_desc"));
        $this->priority_color->SetDBValue($this->f("priority_color"));
        $this->priority_order->SetDBValue(trim($this->f("priority_order")));
    }
//End SetValues Method

} //End prioritiesDataSource Class @3-FCB6E20C

class clsRecordpriorities1 { //priorities1 Class @48-58B321F3

//Variables @48-0DF9B1C2

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

//Class_Initialize Event @48-1FD1E688
    function clsRecordpriorities1($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record priorities1/Error";
        $this->DataSource = new clspriorities1DataSource($this);
        $this->ds = & $this->DataSource;
        $this->InsertAllowed = true;
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "priorities1";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->priority_desc = & new clsControl(ccsTextBox, "priority_desc", $CCSLocales->GetText("im_priority"), ccsText, "", CCGetRequestParam("priority_desc", $Method, NULL), $this);
            $this->priority_desc->Required = true;
            $this->priority_transl = & new clsControl(ccsLabel, "priority_transl", "priority_transl", ccsText, "", CCGetRequestParam("priority_transl", $Method, NULL), $this);
            $this->priority_color = & new clsControl(ccsTextBox, "priority_color", $CCSLocales->GetText("im_color"), ccsText, "", CCGetRequestParam("priority_color", $Method, NULL), $this);
            $this->priority_order = & new clsControl(ccsTextBox, "priority_order", $CCSLocales->GetText("im_order"), ccsInteger, "", CCGetRequestParam("priority_order", $Method, NULL), $this);
            $this->Insert = & new clsButton("Insert", $Method, $this);
            $this->Update = & new clsButton("Update", $Method, $this);
            $this->Delete = & new clsButton("Delete", $Method, $this);
            $this->Cancel = & new clsButton("Cancel", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @48-A9DEF787
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urlpriority_id"] = CCGetFromGet("priority_id", NULL);
    }
//End Initialize Method

//Validate Method @48-8621E821
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->priority_desc->Validate() && $Validation);
        $Validation = ($this->priority_color->Validate() && $Validation);
        $Validation = ($this->priority_order->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->priority_desc->Errors->Count() == 0);
        $Validation =  $Validation && ($this->priority_color->Errors->Count() == 0);
        $Validation =  $Validation && ($this->priority_order->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @48-7094F45B
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->priority_desc->Errors->Count());
        $errors = ($errors || $this->priority_transl->Errors->Count());
        $errors = ($errors || $this->priority_color->Errors->Count());
        $errors = ($errors || $this->priority_order->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @48-3D5B9ECA
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
        $Redirect = "PriorityList.php";
        if($this->PressedButton == "Delete") {
            $Redirect = "PriorityList.php";
            if(!CCGetEvent($this->Delete->CCSEvents, "OnClick", $this->Delete) || !$this->DeleteRow()) {
                $Redirect = "";
            }
        } else if($this->PressedButton == "Cancel") {
            if(!CCGetEvent($this->Cancel->CCSEvents, "OnClick", $this->Cancel)) {
                $Redirect = "";
            }
        } else if($this->Validate()) {
            if($this->PressedButton == "Insert") {
                $Redirect = "PriorityList.php";
                if(!CCGetEvent($this->Insert->CCSEvents, "OnClick", $this->Insert) || !$this->InsertRow()) {
                    $Redirect = "";
                }
            } else if($this->PressedButton == "Update") {
                $Redirect = "PriorityList.php";
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

//InsertRow Method @48-FEDCF59C
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert", $this);
        if(!$this->InsertAllowed) return false;
        $this->DataSource->priority_desc->SetValue($this->priority_desc->GetValue(true));
        $this->DataSource->priority_transl->SetValue($this->priority_transl->GetValue(true));
        $this->DataSource->priority_color->SetValue($this->priority_color->GetValue(true));
        $this->DataSource->priority_order->SetValue($this->priority_order->GetValue(true));
        $this->DataSource->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert", $this);
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//UpdateRow Method @48-819253E4
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->priority_desc->SetValue($this->priority_desc->GetValue(true));
        $this->DataSource->priority_transl->SetValue($this->priority_transl->GetValue(true));
        $this->DataSource->priority_color->SetValue($this->priority_color->GetValue(true));
        $this->DataSource->priority_order->SetValue($this->priority_order->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//DeleteRow Method @48-299D98C3
    function DeleteRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDelete", $this);
        if(!$this->DeleteAllowed) return false;
        $this->DataSource->Delete();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDelete", $this);
        return (!$this->CheckErrors());
    }
//End DeleteRow Method

//Show Method @48-DD797A40
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
                $this->priority_transl->SetValue($this->DataSource->priority_transl->GetValue());
                if(!$this->FormSubmitted){
                    $this->priority_desc->SetValue($this->DataSource->priority_desc->GetValue());
                    $this->priority_color->SetValue($this->DataSource->priority_color->GetValue());
                    $this->priority_order->SetValue($this->DataSource->priority_order->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->priority_desc->Errors->ToString());
            $Error = ComposeStrings($Error, $this->priority_transl->Errors->ToString());
            $Error = ComposeStrings($Error, $this->priority_color->Errors->ToString());
            $Error = ComposeStrings($Error, $this->priority_order->Errors->ToString());
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

        $this->priority_desc->Show();
        $this->priority_transl->Show();
        $this->priority_color->Show();
        $this->priority_order->Show();
        $this->Insert->Show();
        $this->Update->Show();
        $this->Delete->Show();
        $this->Cancel->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End priorities1 Class @48-FCB6E20C

class clspriorities1DataSource extends clsDBIM {  //priorities1DataSource Class @48-166FAAF8

//DataSource Variables @48-03B04D84
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
    var $priority_desc;
    var $priority_transl;
    var $priority_color;
    var $priority_order;
//End DataSource Variables

//DataSourceClass_Initialize Event @48-1BE6ECBF
    function clspriorities1DataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record priorities1/Error";
        $this->Initialize();
        $this->priority_desc = new clsField("priority_desc", ccsText, "");
        $this->priority_transl = new clsField("priority_transl", ccsText, "");
        $this->priority_color = new clsField("priority_color", ccsText, "");
        $this->priority_order = new clsField("priority_order", ccsInteger, "");

        $this->InsertFields["priority_desc"] = array("Name" => "priority_desc", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["priority_color"] = array("Name" => "priority_color", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["priority_order"] = array("Name" => "priority_order", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["priority_desc"] = array("Name" => "priority_desc", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["priority_color"] = array("Name" => "priority_color", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["priority_order"] = array("Name" => "priority_order", "Value" => "", "DataType" => ccsInteger);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @48-6BEE843F
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlpriority_id", ccsInteger, "", "", $this->Parameters["urlpriority_id"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "priority_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @48-5132DE01
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT * \n\n" .
        "FROM priorities {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @48-59FC4455
    function SetValues()
    {
        $this->priority_desc->SetDBValue($this->f("priority_desc"));
        $this->priority_transl->SetDBValue($this->f("priority_desc"));
        $this->priority_color->SetDBValue($this->f("priority_color"));
        $this->priority_order->SetDBValue(trim($this->f("priority_order")));
    }
//End SetValues Method

//Insert Method @48-2CCCA91D
    function Insert()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert", $this->Parent);
        $this->InsertFields["priority_desc"]["Value"] = $this->priority_desc->GetDBValue(true);
        $this->InsertFields["priority_color"]["Value"] = $this->priority_color->GetDBValue(true);
        $this->InsertFields["priority_order"]["Value"] = $this->priority_order->GetDBValue(true);
        $this->SQL = CCBuildInsert("priorities", $this->InsertFields, $this);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert", $this->Parent);
        }
    }
//End Insert Method

//Update Method @48-C299EDA5
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        $this->UpdateFields["priority_desc"]["Value"] = $this->priority_desc->GetDBValue(true);
        $this->UpdateFields["priority_color"]["Value"] = $this->priority_color->GetDBValue(true);
        $this->UpdateFields["priority_order"]["Value"] = $this->priority_order->GetDBValue(true);
        $this->SQL = CCBuildUpdate("priorities", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

//Delete Method @48-30A5FC26
    function Delete()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete", $this->Parent);
        $this->SQL = "DELETE FROM priorities";
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete", $this->Parent);
        }
    }
//End Delete Method

} //End priorities1DataSource Class @48-FCB6E20C

//Include Page implementation @13-58DBA1E3
include_once(RelativePath . "/Footer.php");
//End Include Page implementation

//Initialize Page @1-BF35E660
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
$TemplateFileName = "PriorityList.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-4B0BB954
CCSecurityRedirect("3", "");
//End Authenticate User

//Include events file @1-1B89F764
include("./PriorityList_events.php");
//End Include events file

//Initialize Objects @1-35543E26
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$Header = & new clsHeader("", "Header", $MainPage);
$Header->Initialize();
$AdminMenu = & new clsAdminMenu("", "AdminMenu", $MainPage);
$AdminMenu->Initialize();
$priorities = & new clsGridpriorities("", $MainPage);
$priorities1 = & new clsRecordpriorities1("", $MainPage);
$Footer = & new clsFooter("", "Footer", $MainPage);
$Footer->Initialize();
$MainPage->Header = & $Header;
$MainPage->AdminMenu = & $AdminMenu;
$MainPage->priorities = & $priorities;
$MainPage->priorities1 = & $priorities1;
$MainPage->Footer = & $Footer;
$priorities->Initialize();
$priorities1->Initialize();

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

//Execute Components @1-8C80895D
$Header->Operations();
$AdminMenu->Operations();
$priorities1->Operation();
$Footer->Operations();
//End Execute Components

//Go to destination page @1-1E175DE5
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    $Header->Class_Terminate();
    unset($Header);
    $AdminMenu->Class_Terminate();
    unset($AdminMenu);
    unset($priorities);
    unset($priorities1);
    $Footer->Class_Terminate();
    unset($Footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-16AA5225
$Header->Show();
$AdminMenu->Show();
$priorities->Show();
$priorities1->Show();
$Footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-58602F96
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
$Header->Class_Terminate();
unset($Header);
$AdminMenu->Class_Terminate();
unset($AdminMenu);
unset($priorities);
unset($priorities1);
$Footer->Class_Terminate();
unset($Footer);
unset($Tpl);
//End Unload Page


?>
