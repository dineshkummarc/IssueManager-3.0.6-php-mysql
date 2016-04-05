<?php
//Include Common Files @1-7E100BD7
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "UserMaint.php");
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

class clsRecordusers { //users Class @4-9BE1AF6F

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

//Class_Initialize Event @4-91699E7F
    function clsRecordusers($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record users/Error";
        $this->DataSource = new clsusersDataSource($this);
        $this->ds = & $this->DataSource;
        $this->InsertAllowed = true;
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "users";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->login = & new clsControl(ccsTextBox, "login", $CCSLocales->GetText("CCS_Login"), ccsText, "", CCGetRequestParam("login", $Method, NULL), $this);
            $this->login->Required = true;
            $this->new_pass = & new clsControl(ccsTextBox, "new_pass", $CCSLocales->GetText("CCS_Password"), ccsText, "", CCGetRequestParam("new_pass", $Method, NULL), $this);
            $this->rep_pass = & new clsControl(ccsTextBox, "rep_pass", $CCSLocales->GetText("CCS_Password"), ccsText, "", CCGetRequestParam("rep_pass", $Method, NULL), $this);
            $this->security_level = & new clsControl(ccsListBox, "security_level", $CCSLocales->GetText("im_security_level"), ccsInteger, "", CCGetRequestParam("security_level", $Method, NULL), $this);
            $this->security_level->DSType = dsListOfValues;
            $this->security_level->Values = array(array("1", $CCSLocales->GetText("im_level_1")), array("2", $CCSLocales->GetText("im_level_2")), array("3", $CCSLocales->GetText("im_level_3")));
            $this->user_name = & new clsControl(ccsTextBox, "user_name", $CCSLocales->GetText("im_name"), ccsText, "", CCGetRequestParam("user_name", $Method, NULL), $this);
            $this->user_name->Required = true;
            $this->email = & new clsControl(ccsTextBox, "email", $CCSLocales->GetText("im_email"), ccsText, "", CCGetRequestParam("email", $Method, NULL), $this);
            $this->email->Required = true;
            $this->allow_upload = & new clsControl(ccsCheckBox, "allow_upload", "Allow Upload", ccsInteger, "", CCGetRequestParam("allow_upload", $Method, NULL), $this);
            $this->allow_upload->CheckedValue = $this->allow_upload->GetParsedValue(1);
            $this->allow_upload->UncheckedValue = $this->allow_upload->GetParsedValue(0);
            $this->notify_new = & new clsControl(ccsCheckBox, "notify_new", "Notify New", ccsInteger, "", CCGetRequestParam("notify_new", $Method, NULL), $this);
            $this->notify_new->CheckedValue = $this->notify_new->GetParsedValue(1);
            $this->notify_new->UncheckedValue = $this->notify_new->GetParsedValue(0);
            $this->notify_original = & new clsControl(ccsCheckBox, "notify_original", "Notify Original", ccsInteger, "", CCGetRequestParam("notify_original", $Method, NULL), $this);
            $this->notify_original->CheckedValue = $this->notify_original->GetParsedValue(1);
            $this->notify_original->UncheckedValue = $this->notify_original->GetParsedValue(0);
            $this->notify_reassignment = & new clsControl(ccsCheckBox, "notify_reassignment", "Notify Reassignment", ccsInteger, "", CCGetRequestParam("notify_reassignment", $Method, NULL), $this);
            $this->notify_reassignment->CheckedValue = $this->notify_reassignment->GetParsedValue(1);
            $this->notify_reassignment->UncheckedValue = $this->notify_reassignment->GetParsedValue(0);
            $this->Insert = & new clsButton("Insert", $Method, $this);
            $this->Update = & new clsButton("Update", $Method, $this);
            $this->Delete = & new clsButton("Delete", $Method, $this);
            $this->Cancel = & new clsButton("Cancel", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @4-53A359F1
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urluser_id"] = CCGetFromGet("user_id", NULL);
    }
//End Initialize Method

//Validate Method @4-90078AB1
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        if($this->EditMode && strlen($this->DataSource->Where))
            $Where = " AND NOT (" . $this->DataSource->Where . ")";
        $this->DataSource->login->SetValue($this->login->GetValue());
        if(CCDLookUp("COUNT(*)", "users", "login=" . $this->DataSource->ToSQL($this->DataSource->login->GetDBValue(), $this->DataSource->login->DataType) . $Where, $this->DataSource) > 0)
            $this->login->Errors->addError($CCSLocales->GetText("CCS_UniqueValue", $CCSLocales->GetText("CCS_Login")));
        if(strlen($this->email->GetText()) && !preg_match ("/^[\w\.-]{1,}\@([\da-zA-Z-]{1,}\.){1,}[\da-zA-Z-]+$/", $this->email->GetText())) {
            $this->email->Errors->addError($CCSLocales->GetText("CCS_MaskValidation", $CCSLocales->GetText("im_email")));
        }
        $Validation = ($this->login->Validate() && $Validation);
        $Validation = ($this->new_pass->Validate() && $Validation);
        $Validation = ($this->rep_pass->Validate() && $Validation);
        $Validation = ($this->security_level->Validate() && $Validation);
        $Validation = ($this->user_name->Validate() && $Validation);
        $Validation = ($this->email->Validate() && $Validation);
        $Validation = ($this->allow_upload->Validate() && $Validation);
        $Validation = ($this->notify_new->Validate() && $Validation);
        $Validation = ($this->notify_original->Validate() && $Validation);
        $Validation = ($this->notify_reassignment->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->login->Errors->Count() == 0);
        $Validation =  $Validation && ($this->new_pass->Errors->Count() == 0);
        $Validation =  $Validation && ($this->rep_pass->Errors->Count() == 0);
        $Validation =  $Validation && ($this->security_level->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_name->Errors->Count() == 0);
        $Validation =  $Validation && ($this->email->Errors->Count() == 0);
        $Validation =  $Validation && ($this->allow_upload->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_new->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_original->Errors->Count() == 0);
        $Validation =  $Validation && ($this->notify_reassignment->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @4-708535C7
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->login->Errors->Count());
        $errors = ($errors || $this->new_pass->Errors->Count());
        $errors = ($errors || $this->rep_pass->Errors->Count());
        $errors = ($errors || $this->security_level->Errors->Count());
        $errors = ($errors || $this->user_name->Errors->Count());
        $errors = ($errors || $this->email->Errors->Count());
        $errors = ($errors || $this->allow_upload->Errors->Count());
        $errors = ($errors || $this->notify_new->Errors->Count());
        $errors = ($errors || $this->notify_original->Errors->Count());
        $errors = ($errors || $this->notify_reassignment->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @4-2971B50C
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
        $Redirect = "UserList.php";
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

//InsertRow Method @4-0E50BA3D
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert", $this);
        if(!$this->InsertAllowed) return false;
        $this->DataSource->login->SetValue($this->login->GetValue(true));
        $this->DataSource->security_level->SetValue($this->security_level->GetValue(true));
        $this->DataSource->user_name->SetValue($this->user_name->GetValue(true));
        $this->DataSource->email->SetValue($this->email->GetValue(true));
        $this->DataSource->allow_upload->SetValue($this->allow_upload->GetValue(true));
        $this->DataSource->notify_new->SetValue($this->notify_new->GetValue(true));
        $this->DataSource->notify_original->SetValue($this->notify_original->GetValue(true));
        $this->DataSource->notify_reassignment->SetValue($this->notify_reassignment->GetValue(true));
        $this->DataSource->new_pass->SetValue($this->new_pass->GetValue(true));
        $this->DataSource->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert", $this);
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//UpdateRow Method @4-DF51D583
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->login->SetValue($this->login->GetValue(true));
        $this->DataSource->new_pass->SetValue($this->new_pass->GetValue(true));
        $this->DataSource->rep_pass->SetValue($this->rep_pass->GetValue(true));
        $this->DataSource->security_level->SetValue($this->security_level->GetValue(true));
        $this->DataSource->user_name->SetValue($this->user_name->GetValue(true));
        $this->DataSource->email->SetValue($this->email->GetValue(true));
        $this->DataSource->allow_upload->SetValue($this->allow_upload->GetValue(true));
        $this->DataSource->notify_new->SetValue($this->notify_new->GetValue(true));
        $this->DataSource->notify_original->SetValue($this->notify_original->GetValue(true));
        $this->DataSource->notify_reassignment->SetValue($this->notify_reassignment->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//DeleteRow Method @4-299D98C3
    function DeleteRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDelete", $this);
        if(!$this->DeleteAllowed) return false;
        $this->DataSource->Delete();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDelete", $this);
        return (!$this->CheckErrors());
    }
//End DeleteRow Method

//Show Method @4-FBD23DAD
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->security_level->Prepare();

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
                    $this->login->SetValue($this->DataSource->login->GetValue());
                    $this->security_level->SetValue($this->DataSource->security_level->GetValue());
                    $this->user_name->SetValue($this->DataSource->user_name->GetValue());
                    $this->email->SetValue($this->DataSource->email->GetValue());
                    $this->allow_upload->SetValue($this->DataSource->allow_upload->GetValue());
                    $this->notify_new->SetValue($this->DataSource->notify_new->GetValue());
                    $this->notify_original->SetValue($this->DataSource->notify_original->GetValue());
                    $this->notify_reassignment->SetValue($this->DataSource->notify_reassignment->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }
        if (!$this->FormSubmitted) {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->login->Errors->ToString());
            $Error = ComposeStrings($Error, $this->new_pass->Errors->ToString());
            $Error = ComposeStrings($Error, $this->rep_pass->Errors->ToString());
            $Error = ComposeStrings($Error, $this->security_level->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->email->Errors->ToString());
            $Error = ComposeStrings($Error, $this->allow_upload->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_new->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_original->Errors->ToString());
            $Error = ComposeStrings($Error, $this->notify_reassignment->Errors->ToString());
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

        $this->login->Show();
        $this->new_pass->Show();
        $this->rep_pass->Show();
        $this->security_level->Show();
        $this->user_name->Show();
        $this->email->Show();
        $this->allow_upload->Show();
        $this->notify_new->Show();
        $this->notify_original->Show();
        $this->notify_reassignment->Show();
        $this->Insert->Show();
        $this->Update->Show();
        $this->Delete->Show();
        $this->Cancel->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End users Class @4-FCB6E20C

class clsusersDataSource extends clsDBIM {  //usersDataSource Class @4-0A435B39

//DataSource Variables @4-5171F182
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
    var $login;
    var $new_pass;
    var $rep_pass;
    var $security_level;
    var $user_name;
    var $email;
    var $allow_upload;
    var $notify_new;
    var $notify_original;
    var $notify_reassignment;
//End DataSource Variables

//DataSourceClass_Initialize Event @4-2F41F0C5
    function clsusersDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record users/Error";
        $this->Initialize();
        $this->login = new clsField("login", ccsText, "");
        $this->new_pass = new clsField("new_pass", ccsText, "");
        $this->rep_pass = new clsField("rep_pass", ccsText, "");
        $this->security_level = new clsField("security_level", ccsInteger, "");
        $this->user_name = new clsField("user_name", ccsText, "");
        $this->email = new clsField("email", ccsText, "");
        $this->allow_upload = new clsField("allow_upload", ccsInteger, "");
        $this->notify_new = new clsField("notify_new", ccsInteger, "");
        $this->notify_original = new clsField("notify_original", ccsInteger, "");
        $this->notify_reassignment = new clsField("notify_reassignment", ccsInteger, "");

        $this->InsertFields["login"] = array("Name" => "login", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["security_level"] = array("Name" => "security_level", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["user_name"] = array("Name" => "user_name", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["email"] = array("Name" => "email", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["allow_upload"] = array("Name" => "allow_upload", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["notify_new"] = array("Name" => "notify_new", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["notify_original"] = array("Name" => "notify_original", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["notify_reassignment"] = array("Name" => "notify_reassignment", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["pass"] = array("Name" => "pass", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["login"] = array("Name" => "login", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["security_level"] = array("Name" => "security_level", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["user_name"] = array("Name" => "user_name", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["email"] = array("Name" => "email", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["allow_upload"] = array("Name" => "allow_upload", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["notify_new"] = array("Name" => "notify_new", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["notify_original"] = array("Name" => "notify_original", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["notify_reassignment"] = array("Name" => "notify_reassignment", "Value" => "", "DataType" => ccsInteger);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @4-B49E291C
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urluser_id", ccsInteger, "", "", $this->Parameters["urluser_id"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "user_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @4-B071412E
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT * \n\n" .
        "FROM users {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @4-F80C51B3
    function SetValues()
    {
        $this->login->SetDBValue($this->f("login"));
        $this->security_level->SetDBValue(trim($this->f("security_level")));
        $this->user_name->SetDBValue($this->f("user_name"));
        $this->email->SetDBValue($this->f("email"));
        $this->allow_upload->SetDBValue(trim($this->f("allow_upload")));
        $this->notify_new->SetDBValue(trim($this->f("notify_new")));
        $this->notify_original->SetDBValue(trim($this->f("notify_original")));
        $this->notify_reassignment->SetDBValue(trim($this->f("notify_reassignment")));
    }
//End SetValues Method

//Insert Method @4-10C39D09
    function Insert()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["login"] = new clsSQLParameter("ctrllogin", ccsText, "", "", $this->login->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["security_level"] = new clsSQLParameter("ctrlsecurity_level", ccsInteger, "", "", $this->security_level->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_name"] = new clsSQLParameter("ctrluser_name", ccsText, "", "", $this->user_name->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["email"] = new clsSQLParameter("ctrlemail", ccsText, "", "", $this->email->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["allow_upload"] = new clsSQLParameter("ctrlallow_upload", ccsInteger, "", "", $this->allow_upload->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["notify_new"] = new clsSQLParameter("ctrlnotify_new", ccsInteger, "", "", $this->notify_new->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["notify_original"] = new clsSQLParameter("ctrlnotify_original", ccsInteger, "", "", $this->notify_original->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["notify_reassignment"] = new clsSQLParameter("ctrlnotify_reassignment", ccsInteger, "", "", $this->notify_reassignment->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["pass"] = new clsSQLParameter("ctrlnew_pass", ccsText, "", "", $this->new_pass->GetValue(true), "", false, $this->ErrorBlock);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert", $this->Parent);
        if (!strlen($this->cp["login"]->GetText()) and !is_bool($this->cp["login"]->GetValue())) 
            $this->cp["login"]->SetValue($this->login->GetValue(true));
        if (!strlen($this->cp["security_level"]->GetText()) and !is_bool($this->cp["security_level"]->GetValue())) 
            $this->cp["security_level"]->SetValue($this->security_level->GetValue(true));
        if (!strlen($this->cp["user_name"]->GetText()) and !is_bool($this->cp["user_name"]->GetValue())) 
            $this->cp["user_name"]->SetValue($this->user_name->GetValue(true));
        if (!strlen($this->cp["email"]->GetText()) and !is_bool($this->cp["email"]->GetValue())) 
            $this->cp["email"]->SetValue($this->email->GetValue(true));
        if (!strlen($this->cp["allow_upload"]->GetText()) and !is_bool($this->cp["allow_upload"]->GetValue())) 
            $this->cp["allow_upload"]->SetValue($this->allow_upload->GetValue(true));
        if (!strlen($this->cp["notify_new"]->GetText()) and !is_bool($this->cp["notify_new"]->GetValue())) 
            $this->cp["notify_new"]->SetValue($this->notify_new->GetValue(true));
        if (!strlen($this->cp["notify_original"]->GetText()) and !is_bool($this->cp["notify_original"]->GetValue())) 
            $this->cp["notify_original"]->SetValue($this->notify_original->GetValue(true));
        if (!strlen($this->cp["notify_reassignment"]->GetText()) and !is_bool($this->cp["notify_reassignment"]->GetValue())) 
            $this->cp["notify_reassignment"]->SetValue($this->notify_reassignment->GetValue(true));
        if (!strlen($this->cp["pass"]->GetText()) and !is_bool($this->cp["pass"]->GetValue())) 
            $this->cp["pass"]->SetValue($this->new_pass->GetValue(true));
        $this->InsertFields["login"]["Value"] = $this->cp["login"]->GetDBValue(true);
        $this->InsertFields["security_level"]["Value"] = $this->cp["security_level"]->GetDBValue(true);
        $this->InsertFields["user_name"]["Value"] = $this->cp["user_name"]->GetDBValue(true);
        $this->InsertFields["email"]["Value"] = $this->cp["email"]->GetDBValue(true);
        $this->InsertFields["allow_upload"]["Value"] = $this->cp["allow_upload"]->GetDBValue(true);
        $this->InsertFields["notify_new"]["Value"] = $this->cp["notify_new"]->GetDBValue(true);
        $this->InsertFields["notify_original"]["Value"] = $this->cp["notify_original"]->GetDBValue(true);
        $this->InsertFields["notify_reassignment"]["Value"] = $this->cp["notify_reassignment"]->GetDBValue(true);
        $this->InsertFields["pass"]["Value"] = $this->cp["pass"]->GetDBValue(true);
        $this->SQL = CCBuildInsert("users", $this->InsertFields, $this);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert", $this->Parent);
        }
    }
//End Insert Method

//Update Method @4-0F28581B
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        $this->UpdateFields["login"]["Value"] = $this->login->GetDBValue(true);
        $this->UpdateFields["security_level"]["Value"] = $this->security_level->GetDBValue(true);
        $this->UpdateFields["user_name"]["Value"] = $this->user_name->GetDBValue(true);
        $this->UpdateFields["email"]["Value"] = $this->email->GetDBValue(true);
        $this->UpdateFields["allow_upload"]["Value"] = $this->allow_upload->GetDBValue(true);
        $this->UpdateFields["notify_new"]["Value"] = $this->notify_new->GetDBValue(true);
        $this->UpdateFields["notify_original"]["Value"] = $this->notify_original->GetDBValue(true);
        $this->UpdateFields["notify_reassignment"]["Value"] = $this->notify_reassignment->GetDBValue(true);
        $this->SQL = CCBuildUpdate("users", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

//Delete Method @4-6BD040D0
    function Delete()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete", $this->Parent);
        $this->SQL = "DELETE FROM users";
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete", $this->Parent);
        }
    }
//End Delete Method

} //End usersDataSource Class @4-FCB6E20C

//Include Page implementation @3-58DBA1E3
include_once(RelativePath . "/Footer.php");
//End Include Page implementation

//Initialize Page @1-F112C068
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
$TemplateFileName = "UserMaint.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-4B0BB954
CCSecurityRedirect("3", "");
//End Authenticate User

//Include events file @1-04ECBD5E
include("./UserMaint_events.php");
//End Include events file

//Initialize Objects @1-9D8C6DC4
$DBIM = new clsDBIM();
$MainPage->Connections["IM"] = & $DBIM;

// Controls
$Header = & new clsHeader("", "Header", $MainPage);
$Header->Initialize();
$AdminMenu = & new clsAdminMenu("", "AdminMenu", $MainPage);
$AdminMenu->Initialize();
$users = & new clsRecordusers("", $MainPage);
$Footer = & new clsFooter("", "Footer", $MainPage);
$Footer->Initialize();
$MainPage->Header = & $Header;
$MainPage->AdminMenu = & $AdminMenu;
$MainPage->users = & $users;
$MainPage->Footer = & $Footer;
$users->Initialize();

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

//Execute Components @1-C6D6B945
$Header->Operations();
$AdminMenu->Operations();
$users->Operation();
$Footer->Operations();
//End Execute Components

//Go to destination page @1-E65E111C
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBIM->close();
    header("Location: " . $Redirect);
    $Header->Class_Terminate();
    unset($Header);
    $AdminMenu->Class_Terminate();
    unset($AdminMenu);
    unset($users);
    $Footer->Class_Terminate();
    unset($Footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-331081B5
$Header->Show();
$AdminMenu->Show();
$users->Show();
$Footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$main_block = CCConvertEncoding($main_block, $FileEncoding, $CCSLocales->GetFormatInfo("Encoding"));
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-11FDB8C0
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBIM->close();
$Header->Class_Terminate();
unset($Header);
$AdminMenu->Class_Terminate();
unset($AdminMenu);
unset($users);
$Footer->Class_Terminate();
unset($Footer);
unset($Tpl);
//End Unload Page


?>
