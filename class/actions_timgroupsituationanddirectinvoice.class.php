<?php
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';

class ActionsTimgroupsituationanddirectinvoice
{
	function formBuilddocOptions($parameters, &$object, &$action, $hookmanager)
	{
		global $db, $langs, $conf;

		if (get_class($object) != 'Facture') return 0;
		/* @var $object Facture */
		if (empty($object->fk_project)) return 0;

		$langs->load("timgroupsituationanddirectinvoice@timgroupsituationanddirectinvoice");

		print '<div id="timgroup-directinvoices">';
		print '<div class="titre">'.$langs->trans("DirectPaymentTitle").'</div>';

		// Récupère les factures déjà liées
		$linked_ids = [];
		$reslink = $db->query("SELECT fk_facture_fournisseur FROM ".MAIN_DB_PREFIX."timgroupsituationanddirectinvoice_directinvoice WHERE fk_facture_client = ".$object->id);
		if ($reslink) {
			while ($obj = $db->fetch_object($reslink)) {
				$linked_ids[] = (int) $obj->fk_facture_fournisseur;
			}
		}

		$sql = "SELECT f.rowid, f.ref, f.ref_supplier, f.total_ht, f.total_tva, f.total_ttc, f.fk_statut, f.paye, s.nom as supplier_name
        FROM ".MAIN_DB_PREFIX."facture_fourn f
        LEFT JOIN ".MAIN_DB_PREFIX."societe s ON s.rowid = f.fk_soc
        WHERE f.fk_projet = ".((int) $object->fk_project)."
          AND f.entity = ".((int) $conf->entity)."
          AND f.fk_statut IN (1,2)";

		// Exclure les factures déjà liées
		if (!empty($linked_ids)) {
			$sql .= " AND f.rowid NOT IN (".implode(',', $linked_ids).")";
		}

		// Exclure les factures déjà utilisées dans d'autres factures client
		$sql .= " AND f.rowid NOT IN (
            SELECT fk_facture_fournisseur
            FROM ".MAIN_DB_PREFIX."timgroupsituationanddirectinvoice_directinvoice
            WHERE fk_facture_client != ".((int) $object->id)."
            AND fk_facture_fournisseur IS NOT NULL
          )";

		$resql = $db->query($sql);

		print '<form method="POST">';
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<input type="hidden" name="action" value="save_directinvoice">';
		print '<input type="hidden" name="id" value="'.$object->id.'">';

		// --- Select2 ---
		print '<div class="select-wrapper">';
		print '<select id="directinvoice_fourn" name="directinvoice_fourn[]" multiple size="8" style="width:100%;max-width:600px;">';

		// On prépare un objet fournisseur pour utiliser sa méthode LibStatut()
		require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
		$facturefourn = new FactureFournisseur($db);

		if ($resql && $db->num_rows($resql) > 0) {

			while ($objf = $db->fetch_object($resql)) {
				$statut_label = $facturefourn->LibStatut($objf->paye, $objf->fk_statut, 1, 0);
				print '<option value="'.$objf->rowid.'" '.$selected.'>';
				print dol_escape_htmltag($objf->ref).' - '.dol_escape_htmltag($objf->ref_supplier).' - '.price($objf->total_ttc).' € - '.$statut_label;
				print '</option>';
			}
		} else {
			print '<option value="">'.$langs->trans("NoSupplierInvoiceFound").'</option>';
		}

		print '</select>';
		print '<div class="buttons-wrapper">';
		print '<button type="submit" class="butAction">'.$langs->trans("Save").'</button>';
		print '</div>';
		print '</div>';

		// --- Tableau ---
		print '<div id="selected-fourn-table" style="margin-top:1em;">';
		print '<table class="noborder allwidth">';
		print '<thead><tr class="liste_titre">';
		print '<th>'.$langs->trans("Ref").'</th>';
		print '<th>'.$langs->trans("SupplierRef").'</th>';
		print '<th>'.$langs->trans("Supplier").'</th>';
		print '<th>'.$langs->trans("Statut").'</th>';
		print '<th class="right">'.$langs->trans("AmountHT").'</th>';
		print '<th class="right">'.$langs->trans("AmountVAT").'</th>';
		print '<th class="right">'.$langs->trans("AmountTTC").'</th>';
		print '<th class="center">'.$langs->trans("Action").'</th>';
		print '</tr></thead><tbody>';
		$dirpaytotht = 0;
		if (!empty($linked_ids)) {
			$sqlf = "SELECT f.rowid, f.ref, f.ref_supplier, f.paye, f.fk_statut, f.total_ht, f.total_tva, f.total_ttc, s.nom as supplier_name
					FROM ".MAIN_DB_PREFIX."facture_fourn f
					LEFT JOIN ".MAIN_DB_PREFIX."societe s ON s.rowid = f.fk_soc
					WHERE f.rowid IN (".implode(',', $linked_ids).")";
			$resf = $db->query($sqlf);
			while ($objf = $db->fetch_object($resf)) {
				print '<tr data-id="'.$objf->rowid.'">';
				print '<td><a href="'.DOL_URL_ROOT.'/fourn/facture/card.php?id='.$objf->rowid.'" target="_blank">'.dol_escape_htmltag($objf->ref).'</a></td>';
				print '<td>'.$objf->ref_supplier.'</td>';
				print '<td>'.$objf->supplier_name.'</td>';
				print '<td>'.$facturefourn->LibStatut($objf->paye, $objf->fk_statut, 5, 0).'</td>';
				print '<td class="right">'.price($objf->total_ht).'</td>';
				print '<td class="right">'.price($objf->total_tva).'</td>';
				print '<td class="right">'.price($objf->total_ttc).'</td>';
				$delete_link = $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=remove_directinvoice&fk_fourn='.$objf->rowid.'&token='.newToken().'#timgroup-directinvoices';
        		print '<td class="center"><a href="'.$delete_link.'" class="butActionDelete small">'.$langs->trans("Delete").'</a></td>';
				print '</tr>';
				$dirpaytotht += $objf->total_ht;
			}
		} else {
			print '<tr class="oddeven"><td colspan="8" class="center"></td></tr>';
		}
		if ($object->array_options['options_dirpaytotht'] != $dirpaytotht) {
			$object->array_options['options_dirpaytotht'] = price($dirpaytotht);
			$object->updateExtraField('dirpaytotht');
		}

		print '</tbody></table></div>';
		print '</form>';
		print '</div>';

		// ------------------------------
		// Activation de Select2
		// ------------------------------
		print '<script>
			$(document).ready(function() {
				if ($.fn.select2) {
					$("#directinvoice_fourn").select2({
						width: "resolve",
						placeholder: "'.$langs->transnoentities("SelectSupplierInvoices").'",
						allowClear: true,
						language: {
							noResults: function() {
								return "'.$langs->transnoentities("NoSupplierInvoiceFound").'";
							}
						}
					});
				} else {
					console.warn("Select2 n\'est pas disponible.");
				}
			});
		</script>';

		// rajoute une case à cocher permettant de masquer l'historique lors de la génération des factures de situation
		$hidehistofs = getDolGlobalBool('TIMGROUPSITUATIONANDDIRECTINVOICE_HIDEHISTOFSDEF');
		$hidefirstpage = getDolGlobalBool('TIMGROUPSITUATIONANDDIRECTINVOICE_HIDEFIRSTTBLDEF');
        $this->resprints = '<div style="display:inline" id="divhidehistofs">Dans fact. situation : masquer historique '
			. '<input type="checkbox" value="1" name="hidehistofs" id="hidehistofs" '.($hidehistofs ? ' checked="checked" ' : '').' />&nbsp;-&nbsp;'
			. 'masquer 1ère page <input type="checkbox" value="1" name="hidefirstpage" id="hidefirstpage" '.($hidefirstpage ? ' checked="checked" ' : '').' />'
			. '</div>';
		$this->resprints .= '<script>'
				. 'function tglaffhidehistofs() {'
					. 'if ($("select#model").val() != "facture_timgroup_situation") {'
						. '$("#divhidehistofs").hide(); '
					. '} else $("#divhidehistofs").show(); '
				. '};'
				. '$(document).ready(function() {'
					. 'tglaffhidehistofs();'
					. '$("select#model").on("change", function() {'
						. 'tglaffhidehistofs();'
					. '});'
				. '});'
				. '</script>';
		return 0;
	}

	function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $db, $langs;

		if (get_class($object) != 'Facture') return 0;

		if ($action == 'save_directinvoice') {
			$selected = $_POST['directinvoice_fourn'] ?? [];
			$selected = array_map('intval', $selected);
			$db->begin();

			foreach ($selected as $fk_fourn) {
				$query = "INSERT IGNORE INTO ".MAIN_DB_PREFIX."timgroupsituationanddirectinvoice_directinvoice (fk_facture_client, fk_facture_fournisseur) VALUES (".$object->id.", ".$fk_fourn.")";
				$db->query($query);
			}

			$db->commit();

			header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id."#timgroup-directinvoices");
			exit;
		} else if ($action == 'remove_directinvoice') {
			$fk_fourn = (int) ($_GET['fk_fourn'] ?? 0); // GET maintenant
			$token = $_GET['token'] ?? '';

			// Vérification CSRF
			if (!empty($fk_fourn) && !empty($token) && newToken() == $token) {
				$db->begin();
				$db->query("DELETE FROM ".MAIN_DB_PREFIX."timgroupsituationanddirectinvoice_directinvoice
							WHERE fk_facture_client = ".$object->id."
							AND fk_facture_fournisseur = ".$fk_fourn);
				$db->commit();
			}
		    header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id."#timgroup-directinvoices");
			exit;
		}
		return 0;
	}
}
