<?php
//BindEvents Method @1-9AFE44E6
function BindEvents()
{
    global $statuses;
    global $statuses1;
    $statuses->status_transl->CCSEvents["BeforeShow"] = "statuses_status_transl_BeforeShow";
    $statuses1->status_transl->CCSEvents["BeforeShow"] = "statuses1_status_transl_BeforeShow";
}
//End BindEvents Method

//statuses_status_transl_BeforeShow @11-65E3324F
function statuses_status_transl_BeforeShow(& $sender)
{
    $statuses_status_transl_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $statuses; //Compatibility
//End statuses_status_transl_BeforeShow

//Custom Code @12-2A29BDB7
// -------------------------
    $statuses->status_transl->SetValue(Translate($statuses->status_transl->GetValue()));
// -------------------------
//End Custom Code

//Close statuses_status_transl_BeforeShow @11-43F8CE08
    return $statuses_status_transl_BeforeShow;
}
//End Close statuses_status_transl_BeforeShow

//statuses1_status_transl_BeforeShow @49-B3E97E1D
function statuses1_status_transl_BeforeShow(& $sender)
{
    $statuses1_status_transl_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $statuses1; //Compatibility
//End statuses1_status_transl_BeforeShow

//Custom Code @50-2A29BDB7
// -------------------------
    $statuses1->status_transl->SetValue(Translate($statuses1->status_transl->GetValue()));
// -------------------------
//End Custom Code

//Close statuses1_status_transl_BeforeShow @49-AD5DAE92
    return $statuses1_status_transl_BeforeShow;
}
//End Close statuses1_status_transl_BeforeShow


?>
