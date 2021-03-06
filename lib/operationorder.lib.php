<?php
/* Copyright (C) 2020 ATM Consulting <support@atm-consulting.fr>
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
 *	\file		lib/operationorder.lib.php
 *	\ingroup	operationorder
 *	\brief		This file is an example module library
 *				Put some comments here
 */

/**
 * @return array
 */
function operationorderAdminPrepareHead()
{
    global $langs, $conf;

    $langs->load('operationorder@operationorder');

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/operationorder/admin/operationorder_setup.php", 1);
    $head[$h][1] = $langs->trans("Parameters");
    $head[$h][2] = 'settings';
    $h++;
    $head[$h][0] = dol_buildpath("/operationorder/admin/operationorder_extrafields.php", 1);
    $head[$h][1] = $langs->trans("ExtraFields");
    $head[$h][2] = 'extrafields';
	$h++;

	if (!empty($conf->multicompany->enabled))
	{
		$head[$h][0] = dol_buildpath("/operationorder/admin/multicompany_sharing.php", 1);
		$head[$h][1] = $langs->trans("multicompanySharing");
		$head[$h][2] = 'multicompanySharing';
		$h++;
	}

    $head[$h][0] = dol_buildpath("/operationorder/admin/operationorder_about.php", 1);
    $head[$h][1] = $langs->trans("About");
    $head[$h][2] = 'about';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    //$this->tabs = array(
    //	'entity:+tabname:Title:@operationorder:/operationorder/mypage.php?id=__ID__'
    //); // to add new tab
    //$this->tabs = array(
    //	'entity:-tabname:Title:@operationorder:/operationorder/mypage.php?id=__ID__'
    //); // to remove a tab
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'operationorder');

    return $head;
}


/**
 * @return array
 */
function operationorderStatusAdminPrepareHead()
{
	global $langs, $conf, $db;

	$object = new OperationOrderStatus($db);

	$langs->load('operationorder@operationorder');

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/operationorder/admin/operationorderstatus_setup.php", 1);
	$head[$h][1] = $langs->trans("Parameters");
	$head[$h][2] = 'settings';
	$h++;


	complete_head_from_modules($conf, $langs, $object, $head, $h, 'operationorderstatus');

	return $head;
}


/**
 * Return array of tabs to used on pages for third parties cards.
 *
 * @param 	OperationOrder	$object		Object company shown
 * @return 	array				Array of tabs
 */
function operationorder_prepare_head(OperationOrder $object)
{
    global $db, $langs, $conf;

    $langs->load("operationorder@operationorder");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/operationorder/operationorder_card.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans("OperationOrderCard");
    $head[$h][2] = 'card';
    $h++;

    if (isset($object->fields['note_public']) || isset($object->fields['note_private']))
    {
        $nbNote = 0;
        if (!empty($object->note_private)) $nbNote++;
        if (!empty($object->note_public)) $nbNote++;
        $head[$h][0] = dol_buildpath('/operationorder/note.php', 1).'?id='.$object->id;
        $head[$h][1] = $langs->trans('Notes');
        if ($nbNote > 0) $head[$h][1].= '<span class="badge marginleftonlyshort">'.$nbNote.'</span>';
        $head[$h][2] = 'note';
        $h++;
    }

    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
    $upload_dir = $conf->operationorder->dir_output . "/operationorder/" . dol_sanitizeFileName($object->ref);
    $nbFiles = count(dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$'));
    $nbLinks=Link::count($db, $object->element, $object->id);
    $head[$h][0] = dol_buildpath("/operationorder/document.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans('Documents');
    if (($nbFiles+$nbLinks) > 0) $head[$h][1].= '<span class="badge marginleftonlyshort">'.($nbFiles+$nbLinks).'</span>';
    $head[$h][2] = 'document';
    $h++;

    $head[$h][0] = dol_buildpath("/operationorder/operationorder_agenda.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans("Events");
    $head[$h][2] = 'agenda';
    $h++;

    $head[$h][0] = dol_buildpath("/operationorder/operationorder_info.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans("OperationOrderInfo");
    $head[$h][2] = 'info';
    $h++;


    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    //$this->tabs = array(
    //	'entity:+tabname:Title:@operationorder:/operationorder/mypage.php?id=__ID__'
    //); // to add new tab
    //$this->tabs = array(
    //	'entity:-tabname:Title:@operationorder:/operationorder/mypage.php?id=__ID__'
    //); // to remove a tab
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'operationorder@operationorder');

    complete_head_from_modules($conf, $langs, $object, $head, $h, 'operationorder@operationorder', 'remove');

    return $head;
}

/**
 * @param Form      $form       Form object
 * @param OperationOrder  $object     OperationOrder object
 * @param string    $action     Triggered action
 * @return string
 */
function getFormConfirmOperationOrder($form, $object, $action)
{
    global $langs, $user;

    $formconfirm = '';

    if ($action === 'setStatus' && !empty($user->rights->operationorder->write))
    {

		$fk_status = GETPOST('fk_status' , 'int');

		if(!empty($fk_status)){
			// vérification des droits
			$statusAllowed = new OperationOrderStatus($object->db);
			$res = $statusAllowed->fetch($fk_status);
			if($res>0 && $statusAllowed->userCan($user, 'changeToThisStatus')){
				$body = $langs->trans('ConfirmValidateOperationOrderStatusBody', $object->ref, $statusAllowed->label);
				$formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id.'&fk_status='.$fk_status, $langs->trans('ConfirmValidateOperationOrderStatusTitle', $statusAllowed->label), $body, 'confirm_setStatus', '', 0, 1);
			}else{
				setEventMessage($langs->trans('SetStatusStatusNotAllowed'), 'errors');
			}
		}
		else{
			setEventMessage($langs->trans('SetStatusStatusNotAllowed'), 'errors');
		}


         }
    elseif ($action === 'close' && !empty($user->rights->operationorder->write))
    {
        $body = $langs->trans('ConfirmCloseOperationOrderBody');
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmCloseOperationOrderTitle'), $body, 'confirm_close', '', 0, 1);
    }
    elseif ($action === 'modify' && !empty($user->rights->operationorder->write))
    {
        $body = $langs->trans('ConfirmModifyOperationOrderBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmModifyOperationOrderTitle'), $body, 'confirm_modify', '', 0, 1);
    }
    elseif ($action === 'delete' && !empty($user->rights->operationorder->write))
    {
        $body = $langs->trans('ConfirmDeleteOperationOrderBody');
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmDeleteOperationOrderTitle'), $body, 'confirm_delete', '', 0, 1);
    }
    elseif ($action === 'clone' && !empty($user->rights->operationorder->write))
    {
        $body = $langs->trans('ConfirmCloneOperationOrderBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmCloneOperationOrderTitle'), $body, 'confirm_clone', '', 0, 1);
    }
    elseif ($action == 'ask_deleteline')
    {
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.GETPOST('lineid'), $langs->trans('DeleteProductLine'), $langs->trans('ConfirmDeleteProductLine'), 'confirm_deleteline', '', 0, 1);
    }

    return $formconfirm;
}



/**
 * Return array of tabs to used on pages for third parties cards.
 *
 * @param 	OperationOrderStatus	$object		Object company shown
 * @return 	array				Array of tabs
 */
function operationOrderStatusPrepareHead(OperationOrderStatus $object)
{
	global $db, $langs, $conf;

	$langs->load("operationorder@operationorder");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/operationorder/operationorderstatus_card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("OperationOrderStatusCard");
	$head[$h][2] = 'card';
	$h++;

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'operationorder@operationorder');

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'operationorder@operationorder', 'remove');

	return $head;
}



/**
 * @param Form      $form       Form object
 * @param OperationOrder  $object     OperationOrder object
 * @param string    $action     Triggered action
 * @return string
 */
function getFormConfirmOperationOrderStatus($form, $object, $action)
{
	global $langs, $user;

	$formconfirm = '';

	if ($action === 'valid' && !empty($user->rights->operationorder->write))
	{
		$body = $langs->trans('ConfirmValidateOperationOrderBody', $object->getRef());
		$formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmValidateOperationOrderTitle'), $body, 'confirm_validate', '', 0, 1);
	}
	elseif ($action === 'modify' && !empty($user->rights->operationorder->write))
	{
		$body = $langs->trans('ConfirmModifyOperationOrderBody', $object->ref);
		$formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmModifyOperationOrderTitle'), $body, 'confirm_modify', '', 0, 1);
	}
	elseif ($action === 'delete' && !empty($user->rights->operationorder->write))
	{
		$body = $langs->trans('ConfirmDeleteOperationOrderBody');
		$formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmDeleteOperationOrderTitle'), $body, 'confirm_delete', '', 0, 1);
	}


	return $formconfirm;
}


/**
 * Return an object
 *
 * @param 	string	$objecttype		Type of object ('invoice', 'order', 'expedition_bon', 'myobject@mymodule', ...)
 * @param 	int		$withpicto		Picto to show
 * @param 	string	$option			More options
 * @return	Commonobject			object id/type
 */
function OperationOrderObjectAutoLoad($objecttype, &$db)
{
	global $conf, $langs;

	$ret = -1;
	$regs = array();

	// Parse $objecttype (ex: project_task)
	$module = $myobject = $objecttype;

	// If we ask an resource form external module (instead of default path)
	if (preg_match('/^([^@]+)@([^@]+)$/i', $objecttype, $regs)) {
		$myobject = $regs[1];
		$module = $regs[2];
	}


	if (preg_match('/^([^_]+)_([^_]+)/i', $objecttype, $regs))
	{
		$module = $regs[1];
		$myobject = $regs[2];
	}

	// Generic case for $classpath
	$classpath = $module.'/class';

	// Special cases, to work with non standard path
	if ($objecttype == 'facture' || $objecttype == 'invoice') {
		$classpath = 'compta/facture/class';
		$module='facture';
		$myobject='facture';
	}
	elseif ($objecttype == 'commande' || $objecttype == 'order') {
		$classpath = 'commande/class';
		$module='commande';
		$myobject='commande';
	}
	elseif ($objecttype == 'propal')  {
		$classpath = 'comm/propal/class';
	}
	elseif ($objecttype == 'supplier_proposal')  {
		$classpath = 'supplier_proposal/class';
	}
	elseif ($objecttype == 'shipping') {
		$classpath = 'expedition/class';
		$myobject = 'expedition';
		$module = 'expedition_bon';
	}
	elseif ($objecttype == 'delivery') {
		$classpath = 'livraison/class';
		$myobject = 'livraison';
		$module = 'livraison_bon';
	}
	elseif ($objecttype == 'contract') {
		$classpath = 'contrat/class';
		$module='contrat';
		$myobject='contrat';
	}
	elseif ($objecttype == 'member') {
		$classpath = 'adherents/class';
		$module='adherent';
		$myobject='adherent';
	}
	elseif ($objecttype == 'cabinetmed_cons') {
		$classpath = 'cabinetmed/class';
		$module='cabinetmed';
		$myobject='cabinetmedcons';
	}
	elseif ($objecttype == 'fichinter') {
		$classpath = 'fichinter/class';
		$module='ficheinter';
		$myobject='fichinter';
	}
	elseif ($objecttype == 'task') {
		$classpath = 'projet/class';
		$module='projet';
		$myobject='task';
	}
	elseif ($objecttype == 'stock') {
		$classpath = 'product/stock/class';
		$module='stock';
		$myobject='stock';
	}
	elseif ($objecttype == 'inventory') {
		$classpath = 'product/inventory/class';
		$module='stock';
		$myobject='inventory';
	}
	elseif ($objecttype == 'mo') {
		$classpath = 'mrp/class';
		$module='mrp';
		$myobject='mo';
	}

	// Generic case for $classfile and $classname
	$classfile = strtolower($myobject); $classname = ucfirst($myobject);
	//print "objecttype=".$objecttype." module=".$module." subelement=".$subelement." classfile=".$classfile." classname=".$classname;

	if ($objecttype == 'invoice_supplier') {
		$classfile = 'fournisseur.facture';
		$classname = 'FactureFournisseur';
		$classpath = 'fourn/class';
		$module = 'fournisseur';
	}
	elseif ($objecttype == 'order_supplier') {
		$classfile = 'fournisseur.commande';
		$classname = 'CommandeFournisseur';
		$classpath = 'fourn/class';
		$module = 'fournisseur';
	}
	elseif ($objecttype == 'stock') {
		$classpath = 'product/stock/class';
		$classfile = 'entrepot';
		$classname = 'Entrepot';
	}

	if (!empty($conf->$module->enabled))
	{

		$res = dol_include_once('/'.$classpath.'/'.$classfile.'.class.php');
		if ($res)
		{
			if (class_exists($classname)) {
				return new $classname($db);
			}
		}
	}
	return $ret;
}
