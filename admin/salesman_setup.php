<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		admin/salesman.php
 * 	\ingroup	salesman
 * 	\brief		This file is an example module setup page
 * 				Put some comments here
 */
// Dolibarr environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
    $res = @include("../../../main.inc.php"); // From "custom" directory
}

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/salesman.lib.php';
require_once DOL_DOCUMENT_ROOT . "/core/class/html.formactions.class.php";

// Translations
$langs->load("salesman@salesman");

// Access control
if (! $user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');

/*
 * Actions
 */
if (preg_match('/set_(.*)/',$action,$reg))
{
	$code=$reg[1];
	if ($code == 'SALESMAN_EVENTTYPE_TO_FILTER_LIST')
	{
		$value = json_encode(GETPOST($code));
		if (empty(GETPOST($code))) dolibarr_del_const($db, $code, 0);
		else if (dolibarr_set_const($db, $code, $value, 'chaine', 0, '', $conf->entity) > 0)
		{
			header("Location: ".$_SERVER["PHP_SELF"]);
			exit;
		}
		else
		{
			dol_print_error($db);
		}
	}
	else if (dolibarr_set_const($db, $code, GETPOST($code), 'chaine', 0, '', $conf->entity) > 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

if (preg_match('/del_(.*)/',$action,$reg))
{
	$code=$reg[1];
	if (dolibarr_del_const($db, $code, 0) > 0)
	{
		Header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

/*
 * View
 */
$page_name = "SalesmanSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'
    . $langs->trans("BackToModuleList") . '</a>';
print load_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = salesmanAdminPrepareHead();
dol_fiche_head(
    $head,
    'settings',
    $langs->trans("Module104025Name"),
    0,
    "salesman@salesman"
);

// Setup page goes here
$form=new Form($db);
$formactions = new FormActions($db);
$var=false;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td>'."\n";
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="center" width="100">'.$langs->trans("Value").'</td>'."\n";


// Example with a yes / no select
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$form->textwithpicto($langs->trans("SALESMAN_GOOGLE_API_KEY"), $langs->trans("SALESMAN_GOOGLE_API_KEY_HELP")).'<br /><a href="https://developers.google.com/maps/documentation/javascript/get-api-key?hl=fr">https://developers.google.com/maps/documentation/javascript/get-api-key?hl=fr</a></td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="300" nowrap="nowrap">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_SALESMAN_GOOGLE_API_KEY">';
print '<input type="text" size="60" name="SALESMAN_GOOGLE_API_KEY" value="'.$conf->global->SALESMAN_GOOGLE_API_KEY .'">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("SALESMAN_EVENTTYPE_TO_FILTER_LIST").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="right" width="300" nowrap="nowrap">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set_SALESMAN_EVENTTYPE_TO_FILTER_LIST">';
print $formactions->select_type_actions(json_decode($conf->global->SALESMAN_EVENTTYPE_TO_FILTER_LIST, true), "SALESMAN_EVENTTYPE_TO_FILTER_LIST", '', -1, 0, 1, 0, 'maxwidth500');
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td></tr>';

print '</table>';

llxFooter();

$db->close();
