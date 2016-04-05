<?php
//BindEvents Method @1-5C0DEFAA
function BindEvents()
{
    global $users;
    $users->CCSEvents["BeforeShow"] = "users_BeforeShow";
    $users->CCSEvents["OnValidate"] = "users_OnValidate";
}
//End BindEvents Method

//users_BeforeShow @3-84B77ABC
function users_BeforeShow(& $sender)
{
    $users_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users; //Compatibility
//End users_BeforeShow

//Custom Code @19-2A29BDB7
// -------------------------
	global $CCSLocales;
	FormatBoolean($users->allow_upload);
	$users->allow_upload->SetValue($users->allow_upload->GetValue());
	$users->security_level->SetValue($CCSLocales->GetText("im_level_".$users->security_level->GetValue()));	
	$users->old_pass->SetValue("");
	$users->new_pass->SetValue("");
	$users->rep_pass->SetValue("");
// -------------------------
//End Custom Code

//Close users_BeforeShow @3-500F6ED2
    return $users_BeforeShow;
}
//End Close users_BeforeShow

//users_OnValidate @3-2CD43F71
function users_OnValidate(& $sender)
{
    $users_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users; //Compatibility
//End users_OnValidate

//Custom Code @30-2A29BDB7
// -------------------------
		global $DBIM, $CCSLocales;
		if ($Component->new_pass->GetValue())
		{
			if ($Component->new_pass->GetValue() == $Component->rep_pass->GetValue())
			{
				if ($Component->old_pass->GetValue() && CCDLookup("user_id", "users", "login=".$DBIM->ToSQL(CCGetUserLogin(), ccsText)." AND pass=".$DBIM->ToSQL($Component->old_pass->GetValue(), ccsText), $DBIM))
				{
					$sql = "UPDATE users SET pass=".$DBIM->ToSql($Component->new_pass->GetValue(), ccsText)." WHERE user_id=".CCGetUserID();
					$DBIM->query($sql);					
				}
				else
				{
					$Component->Errors->addError($CCSLocales->GetText("im_invalid_password"));
				}
			}
			else
			{
				$Component->Errors->addError($CCSLocales->GetText("im_passwords_differ"));
			}
		}
		elseif ($Component->old_pass->GetValue())
		{
			$Component->Errors->addError($CCSLocales->GetText("im_password_required"));
		}
// -------------------------
//End Custom Code

//Close users_OnValidate @3-6FF40A5B
    return $users_OnValidate;
}
//End Close users_OnValidate


?>
