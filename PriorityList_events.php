<?php
//BindEvents Method @1-76049379
function BindEvents()
{
    global $priorities;
    global $priorities1;
    $priorities->CCSEvents["BeforeShowRow"] = "priorities_BeforeShowRow";
    $priorities1->CCSEvents["BeforeShow"] = "priorities1_BeforeShow";
}
//End BindEvents Method

//priorities_BeforeShowRow @3-60A42427
function priorities_BeforeShowRow(& $sender)
{
    $priorities_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $priorities; //Compatibility
//End priorities_BeforeShowRow

//Custom Code @16-2A29BDB7
// -------------------------
	$priorities->priority_transl->SetValue(Translate($priorities->priority_transl->GetValue()));
// -------------------------
//End Custom Code

//Close priorities_BeforeShowRow @3-831E71B6
    return $priorities_BeforeShowRow;
}
//End Close priorities_BeforeShowRow

//priorities1_BeforeShow @48-116B3DC7
function priorities1_BeforeShow(& $sender)
{
    $priorities1_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $priorities1; //Compatibility
//End priorities1_BeforeShow

//Custom Code @57-2A29BDB7
// -------------------------
	$priorities1->priority_transl->SetValue(Translate($priorities1->priority_transl->GetValue()));
// -------------------------
//End Custom Code

//Close priorities1_BeforeShow @48-52515427
    return $priorities1_BeforeShow;
}
//End Close priorities1_BeforeShow


?>
