<?php
//BindEvents Method @1-D930E199
function BindEvents()
{
    global $users;
    $users->CCSEvents["OnValidate"] = "users_OnValidate";
    $users->CCSEvents["BeforeShow"] = "users_BeforeShow";
}
//End BindEvents Method

//users_OnValidate @4-2CD43F71
function users_OnValidate(& $sender)
{
    $users_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users; //Compatibility
//End users_OnValidate

//Custom Code @48-2A29BDB7
// -------------------------
		global $DBIM, $CCSLocales;
		if ($Component->new_pass->GetValue())
		{
			if ($Component->new_pass->GetValue() == $Component->rep_pass->GetValue())
			{
				if (CCGetParam("user_id"))
				{
					$sql = "UPDATE users SET pass=".$DBIM->ToSql($Component->new_pass->GetValue(), ccsText)." WHERE user_id=".$DBIM->ToSql(CCGetParam("user_id"), ccsInteger);
					$DBIM->query($sql);
				}
			}
			else
			{
				$Component->Errors->addError($CCSLocales->GetText("im_passwords_differ"));
			}
		}
		elseif(!$Component->EditMode)
		{
			$Component->Errors->addError($CCSLocales->GetText("CCS_RequiredField", $CCSLocales->GetText("CCS_Password")));
		}
// -------------------------
//End Custom Code

//Close users_OnValidate @4-6FF40A5B
    return $users_OnValidate;
}
//End Close users_OnValidate

//users_BeforeShow @4-84B77ABC
function users_BeforeShow(& $sender)
{
    $users_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users; //Compatibility
//End users_BeforeShow

//Custom Code @58-2A29BDB7
// -------------------------
	$users->new_pass->SetValue("");
	$users->rep_pass->SetValue("");
// -------------------------
//End Custom Code

//Close users_BeforeShow @4-500F6ED2
    return $users_BeforeShow;
}
//End Close users_BeforeShow


?>
