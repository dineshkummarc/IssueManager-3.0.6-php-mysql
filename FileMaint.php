<?php
//Include Common Files @1-A94D2A70
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "FileMaint.php");
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

class clsRecordfiles { //files Class @3-5092AA06

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

//Class_Initialize Event @3-B87C2D8D
    function clsRecordfiles($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record files/Error";
        $this->DataSource = new clsfilesDataSource($this);
        $this->ds = & $this->DataSource;
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "files";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "multipart/form-data";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->file = & new clsControl(ccsLink, "file", "file", ccsText, "", CCGetRequestParam("file", $Method, NULL), $this);
            $this->file_name = & new clsFileUpload("file_name", $CCSLocales->GetText("im_file_name"), "temp/", "uploads/", "*", "", 1000000, $this);
            $this->file_name->Required = true;
            $this->uploaded_by = & new clsControl(ccsListBox, "uploaded_by", $CCSLocales->GetText("im_uploaded_by"), ccsInteger, "", CCGetRequestParam("uploaded_by", $Method, NULL), $this);
            $this->uploaded_by->DSType = dsTable;
            list($this->uploaded_by->BoundColumn, $this->uploaded_by->TextColumn, $this->uploaded_by->DBFormat) = array("user_id", "user_name", "");
            $this->uploaded_by->DataSource = new clsDBIM();
            $this->uploaded_by->ds = & $this->uploaded_by->DataSource;
            $this->uploaded_by->DataSource->SQL = "SELECT * \n" .
"FROM users {SQL_Where} {SQL_OrderBy}";
            $this->uploaded_by->DataSource->Order = "user_name";
            $this->uploaded_by->DataSource->Order = "user_name";
            $this->uploaded_by->Required = true;
            $this->date_uploaded = & new clsControl(ccsTextBox, "date_uploaded", $CCSLocales->GetText("im_date_uploaded"), ccsDate, array("GeneralDate"), CCGetRequestParam("date_uploaded", $Method, NULL), $this);
            $this->date_uploaded->Required = true;
            $this->date_format = & new clsControl(ccsLabel, "date_format", "date_format", ccsText, "", CCGetRequestParam("date_format", $Method, NULL), $this);
            $this->Update = & new clsButton("Update", $Method, $this);
            $this->Delete = & new clsButton("Delete", $Method, $this);
            $this->Cancel = & new clsButton("Cancel", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @3-DDC000A9
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urlfile_id"] = CCGetFromGet("file_id", NULL);
    }
//End Initialize Method

//Validate Method @3-18E1DD33
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->file_name->Validate() && $Validation);
        $Validation = ($this->uploaded_by->Validate() && $Validation);
        $Validation = ($this->date_uploaded->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->file_name->Errors->Count() == 0);
        $Validation =  $Validation && ($this->uploaded_by->Errors->Count() == 0);
        $Validation =  $Validation && ($this->date_uploaded->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @3-A4A8307F
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->file->Errors->Count());
        $errors = ($errors || $this->file_name->Errors->Count());
        $errors = ($errors || $this->uploaded_by->Errors->Count());
        $errors = ($errors || $this->date_uploaded->Errors->Count());
        $errors = ($errors || $this->date_format->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @3-61EE5222
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

        $this->file_name->Upload();

        if($this->FormSubmitted) {
            $this->PressedButton = $this->EditMode ? "Update" : "Cancel";
            if($this->Update->Pressed) {
                $this->PressedButton = "Update";
            } else if($this->Delete->Pressed) {
                $this->PressedButton = "Delete";
            } else if($this->Cancel->Pressed) {
                $this->PressedButton = "Cancel";
            }
        }
        $Redirect = "IssueMaint.php" . "?" . CCGetQueryString("QueryString", array("ccsForm", "file_id"));
        if($this->PressedButton == "Delete") {
            if(!CCGetEvent($this->Delete->CCSEvents, "OnClick", $this->Delete) || !$this->DeleteRow()) {
                $Redirect = "";
            }
        } else if($this->PressedButton == "Cancel") {
            $Redirect = "IssueMaint.php" . "?" . CCGetQueryString("QueryString", array("ccsForm", "file_id", "file_id"));
            if(!CCGetEvent($this->Cancel->CCSEvents, "OnClick", $this->Cancel)) {
                $Redirect = "";
            }
        } else if($this->Validate()) {
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

//UpdateRow Method @3-EBEAC9BF
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->file->SetValue($this->file->GetValue(true));
        $this->DataSource->file_name->SetValue($this->file_name->GetValue(true));
        $this->DataSource->uploaded_by->SetValue($this->uploaded_by->GetValue(true));
        $this->DataSource->date_uploaded->SetValue($this->date_uploaded->GetValue(true));
        $this->DataSource->date_format->SetValue($this->date_format->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        if($this->DataSource->Errors->Count() == 0) {
            $this->file_name->Move();
        }
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//DeleteRow Method @3-69632B86
    function DeleteRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDelete", $this);
        if(!$this->DeleteAllowed) return false;
        $this->DataSource->Delete();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDelete", $this);
        if($this->DataSource->Errors->Count() == 0) {
            $this->file_name->Delete();
        }
        return (!$this->CheckErrors());
    }
//End DeleteRow Method

//Show Method @3-5F3E171B
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->uploaded_by->Prepare();

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
                $this->file->SetValue($this->DataSource->file->GetValue());
                $this->file->Page = $this->DataSource->f("file_name");
                if(!$this->FormSubmitted){
                    $this->file_name->SetValue($this->DataSource->file_name->GetValue());
                    $this->uploaded_by->SetValue($this->DataSource->uploaded_by->GetValue());
                    $this->date_uploaded->SetValue($this->DataSource->date_uploaded->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->file->Errors->ToString());
            $Error = ComposeStrings($Error, $this->file_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->uploaded_by->Errors->ToString());
            $Error = ComposeStrings($Error, $this->date_uploaded->Errors->ToString());
            $Error = ComposeStrings($Error, $this->date_format->Errors->ToString());
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
        $this->Delete->Visible = $this->EditMode && $this->DeleteAllowed;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->file->Show();
        $this->file_name->Show();
        $this->uploaded_by->Show();
        $this->date_uploaded->Show();
        $this->date_format->Show();
        $this->Update->Show();
        $this->Delete->Show();
        $this->Cancel->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End files Class @3-FCB6E20C

class clsfilesDataSource extends clsDBIM {  //filesDataSource Class @3-50AE40EA

//DataSource Variables @3-E4A86726
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $UpdateParameters;
    var $DeleteParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $file;
    var $file_name;
    var $uploaded_by;
    var $date_uploaded;
    var $date_format;
//End DataSource Variables

//DataSourceClass_Initialize Event @3-B30B72BF
    function clsfilesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record files/Error";
        $this->Initialize();
        $this->file = new clsField("file", ccsText, "");
        $this->file_name = new clsField("file_name", ccsText, "");
        $this->uploaded_by = new clsField("uploaded_by", ccsInteger, "");
        $this->date_uploaded = new clsField("date_uploaded", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->date_format = new clsField("date_format", ccsText, "");

        $this->UpdateFields["file_name"] = array("Name" => "file_name", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["uploaded_by"] = array("Name" => "uploaded_by", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["date_uploaded"] = array("Name" => "date_uploaded", "Value" => "", "DataType" => ccsDate);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @3-53977593
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlfile_id", ccsInteger, "", "", $this->Parameters["urlfile_id"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "file_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @3-69729949
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT * \n\n" .
        "FROM files {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @3-C83F4AB7
    function SetValues()
    {
        $this->file->SetDBValue($this->f("file_name"));
        $this->file_name->SetDBValue($this->f("file_name"));
        $this->uploaded_by->SetDBValue(trim($this->f("uploaded_by")));
        $this->date_uploaded->SetDBValue(trim($this->f("date_uploaded")));
    }
//End SetValues Method

//Update Method @3-82B4B1FA
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        $this->UpdateFields["file_name"]["Value"] = $this->file_name->GetDBValue(true);
        $this->UpdateFields["uploaded_by"]["Value"] = $this->uploaded_by->GetDBValue(true);
        $this->UpdateFields["date_uploaded"]["Value"] = $this->date_uploaded->GetDBValue(true);
        $this->SQL = CCBuildUpdate("files", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

//Delete Method @3-E35DF010
    function Delete()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete", $this->Parent);
        $this->SQL = "DELETE FROM files";
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete", $this->Parent);
        }
    }
//End Delete Method

} //End filesDataSource Class @3-FCB6E20C

//Include Page implementation @11-58DBA1E3
include_once(RelativePath . "/Footer.php");
//End Include Page implementation

//Initialize Page @1-F0E731DE
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
$TemplateFileName = "FileMaint.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-4B0BB954
CCSecurityRedirect("3", "");
//End Authenticate User

//Include events file @1-F63520B0
include("./FileMaint_events.php");
//End Include events file

//Initialize Objects @1-D664B7A9
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$Header = & new clsHeader("", "Header", $MainPage);
$Header->Initialize();
$AdminMenu = & new clsAdminMenu("", "AdminMenu", $MainPage);
$AdminMenu->Initialize();
$files = & new clsRecordfiles("", $MainPage);
$Footer = & new clsFooter("", "Footer", $MainPage);
$Footer->Initialize();
$MainPage->Header = & $Header;
$MainPage->AdminMenu = & $AdminMenu;
$MainPage->files = & $files;
$MainPage->Footer = & $Footer;
$files->Initialize();

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

//Execute Components @1-80BAC452
$Header->Operations();
$AdminMenu->Operations();
$files->Operation();
$Footer->Operations();
//End Execute Components

//Go to destination page @1-3EE7472F
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    $Header->Class_Terminate();
    unset($Header);
    $AdminMenu->Class_Terminate();
    unset($AdminMenu);
    unset($files);
    $Footer->Class_Terminate();
    unset($Footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-700C7B17
$Header->Show();
$AdminMenu->Show();
$files->Show();
$Footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-5EDAE7D9
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
$Header->Class_Terminate();
unset($Header);
$AdminMenu->Class_Terminate();
unset($AdminMenu);
unset($files);
$Footer->Class_Terminate();
unset($Footer);
unset($Tpl);
//End Unload Page


?>
