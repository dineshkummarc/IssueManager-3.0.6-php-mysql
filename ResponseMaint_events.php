<?php
//BindEvents Method @1-10B34754
function BindEvents()
{
    global $responses;
    $responses->date_format->CCSEvents["BeforeShow"] = "responses_date_format_BeforeShow";
    $responses->CCSEvents["BeforeShow"] = "responses_BeforeShow";
}
//End BindEvents Method

//responses_date_format_BeforeShow @103-512887B9
function responses_date_format_BeforeShow(& $sender)
{
    $responses_date_format_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $responses; //Compatibility
//End responses_date_format_BeforeShow

//Print General Date Format @104-BB1D2E46
    $Component->SetValue(GetGeneralDateFormat());
//End Print General Date Format

//Close responses_date_format_BeforeShow @103-928DC85C
    return $responses_date_format_BeforeShow;
}
//End Close responses_date_format_BeforeShow

//responses_BeforeShow @7-CE9269A4
function responses_BeforeShow(& $sender)
{
    $responses_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $responses; //Compatibility
//End responses_BeforeShow

//Custom Code @20-2A29BDB7
// -------------------------
	TranslateListbox($responses->priority_id);
	TranslateListbox($responses->status_id);
// -------------------------
//End Custom Code

//Close responses_BeforeShow @7-78D367AE
    return $responses_BeforeShow;
}
//End Close responses_BeforeShow


?>
