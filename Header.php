<?php
class clsHeader { //Header class @1-CC982CB1

//Variables @1-5DD9E934
    var $ComponentType = "IncludablePage";
    var $Connections = array();
    var $FileName = "";
    var $Redirect = "";
    var $Tpl = "";
    var $TemplateFileName = "";
    var $BlockToParse = "";
    var $ComponentName = "";

    // Events;
    var $CCSEvents = "";
    var $CCSEventResult = "";
    var $RelativePath;
    var $Visible;
    var $Parent;
//End Variables

//Class_Initialize Event @1-5901240E
    function clsHeader($RelativePath, $ComponentName, & $Parent)
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = $ComponentName;
        $this->RelativePath = $RelativePath;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->FileName = "Header.php";
        $this->Redirect = "";
        $this->TemplateFileName = "Header.html";
        $this->BlockToParse = "main";
        $this->TemplateEncoding = "";
    }
//End Class_Initialize Event

//Class_Terminate Event @1-32FD4740
    function Class_Terminate()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUnload", $this);
    }
//End Class_Terminate Event

//BindEvents Method @1-9D6BB5EE
    function BindEvents()
    {
        $this->user->CCSEvents["BeforeShow"] = "Header_user_BeforeShow";
        $this->AdminLink->CCSEvents["BeforeShow"] = "Header_AdminLink_BeforeShow";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInitialize", $this);
    }
//End BindEvents Method

//Operations Method @1-7E2A14CF
    function Operations()
    {
        global $Redirect;
        if(!$this->Visible)
            return "";
    }
//End Operations Method

//Initialize Method @1-9FBC89B7
    function Initialize()
    {
        global $FileName;
        global $CCSLocales;
        if(!$this->Visible)
            return "";
        $this->DBIM = new clsDBIM();
        $this->Connections["IM"] = & $this->DBIM;

        // Create Components
        $this->user = & new clsControl(ccsLabel, "user", "user", ccsText, "", CCGetRequestParam("user", ccsGet, NULL), $this);
        $this->Link1 = & new clsControl(ccsLink, "Link1", "Link1", ccsText, "", CCGetRequestParam("Link1", ccsGet, NULL), $this);
        $this->Link1->Page = $this->RelativePath . "Default.php";
        $this->Link2 = & new clsControl(ccsLink, "Link2", "Link2", ccsText, "", CCGetRequestParam("Link2", ccsGet, NULL), $this);
        $this->Link2->Page = $this->RelativePath . "IssueNew.php";
        $this->Link3 = & new clsControl(ccsLink, "Link3", "Link3", ccsText, "", CCGetRequestParam("Link3", ccsGet, NULL), $this);
        $this->Link3->Page = $this->RelativePath . "UserProfile.php";
        $this->AdminLink = & new clsPanel("AdminLink", $this);
        $this->Link5 = & new clsControl(ccsLink, "Link5", "Link5", ccsText, "", CCGetRequestParam("Link5", ccsGet, NULL), $this);
        $this->Link5->Page = $this->RelativePath . "Administration.php";
        $this->Link4 = & new clsControl(ccsLink, "Link4", "Link4", ccsText, "", CCGetRequestParam("Link4", ccsGet, NULL), $this);
        $this->Link4->Page = $this->RelativePath . "Login.php";
        $this->style = & new clsControl(ccsListBox, "style", "style", ccsText, "", CCGetRequestParam("style", ccsGet, NULL), $this);
        $this->style->DSType = dsTable;
        list($this->style->BoundColumn, $this->style->TextColumn, $this->style->DBFormat) = array("style_name", "style_name", "");
        $this->style->DataSource = new clsDBIM();
        $this->style->ds = & $this->style->DataSource;
        $this->style->DataSource->SQL = "SELECT * \n" .
"FROM styles {SQL_Where} {SQL_OrderBy}";
        $this->locale = & new clsControl(ccsListBox, "locale", "locale", ccsText, "", CCGetRequestParam("locale", ccsGet, NULL), $this);
        $this->locale->DSType = dsTable;
        list($this->locale->BoundColumn, $this->locale->TextColumn, $this->locale->DBFormat) = array("locale_name", "locale_name", "");
        $this->locale->DataSource = new clsDBIM();
        $this->locale->ds = & $this->locale->DataSource;
        $this->locale->DataSource->SQL = "SELECT * \n" .
"FROM locales {SQL_Where} {SQL_OrderBy}";
        $this->AdminLink->AddComponent("Link5", $this->Link5);
        $this->BindEvents();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnInitializeView", $this);
        $this->Link4->Parameters = "";
        $this->Link4->Parameters = CCAddParam($this->Link4->Parameters, "Logout", "True");
    }
//End Initialize Method

//Show Method @1-53D5E17E
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        $block_path = $Tpl->block_path;
        $Tpl->LoadTemplate("/" . $this->TemplateFileName, $this->ComponentName, $this->TemplateEncoding, "remove");
        $Tpl->block_path = $Tpl->block_path . "/" . $this->ComponentName;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $block_path;
            $Tpl->SetVar($this->ComponentName, "");
            return "";
        }
        $this->style->Prepare();
        $this->locale->Prepare();
        $this->style->Show();
        $this->locale->Show();
        $this->user->Show();
        $this->Link1->Show();
        $this->Link2->Show();
        $this->Link3->Show();
        $this->AdminLink->Show();
        $this->Link4->Show();
        $Tpl->Parse();
        $Tpl->block_path = $block_path;
        $Tpl->SetVar($this->ComponentName, $Tpl->GetVar($this->ComponentName));
    }
//End Show Method

} //End Header Class @1-FCB6E20C

//Include Event File @1-0A691A83
include(RelativePath . "/Header_events.php");
//End Include Event File


?>
