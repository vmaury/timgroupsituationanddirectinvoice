<?php
/* Copyright (C) 2004-2018	Laurent Destailleur			<eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019	Nicolas ZABOURI				<info@inovea-conseil.com>
 * Copyright (C) 2019-2024	Frédéric France				<frederic.france@free.fr>
 * Copyright (C) 2025		Raphael Jube				<rjube@timgroup.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   timgroupsituationanddirectinvoice     Module Timgroupsituationanddirectinvoice
 *  \brief      Timgroupsituationanddirectinvoice module descriptor.
 *
 *  \file       htdocs/timgroupsituationanddirectinvoice/core/modules/modTimgroupsituationanddirectinvoice.class.php
 *  \ingroup    timgroupsituationanddirectinvoice
 *  \brief      Description and activation file for module Timgroupsituationanddirectinvoice
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module Timgroupsituationanddirectinvoice
 */
class modTimgroupsituationanddirectinvoice extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $conf, $langs;

		$this->db = $db;
		$this->numero = 900124;
		$this->rights_class = 'timgroupsituationanddirectinvoice';
		$this->family = "financial";
		$this->module_position = '90';
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = "TimgroupsituationanddirectinvoiceDescription";
		$this->descriptionlong = "TimgroupsituationanddirectinvoiceDescription";
		$this->editor_name = 'Timgroup';
		$this->editor_url = 'https://www.timgroup.fr/';
		$this->editor_squarred_logo = 'timgroupash.png@timgroupsituationanddirectinvoice';
		$this->version = '1.0';
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->picto = 'timgroupash.png@timgroupsituationanddirectinvoice';
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 0,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 1,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
//			'css' => array(
//				'/timgroupsituationanddirectinvoice/assets/css/custom.css',
//			),
//			// Set this to relative path of js file if module must load a js on all pages
//			'js' => array(
//				  '/timgroupsituationanddirectinvoice/assets/js/custom.js',
//			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			/* BEGIN MODULEBUILDER HOOKSCONTEXTS */
			'hooks' => array('invoicecard'),
			/* END MODULEBUILDER HOOKSCONTEXTS */
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
			// Set this to 1 if the module provides a website template into doctemplates/websites/website_template-mytemplate
			'websitetemplates' => 0,
			// Set this to 1 if the module provides a captcha driver
			'captcha' => 0
		);
		$this->dirs = array("/timgroupsituationanddirectinvoice/temp");
		$this->config_page_url = array("setup.php@timgroupsituationanddirectinvoice");
		$this->hidden = getDolGlobalInt('MODULE_TIMGROUPSITUATIONANDDIRECTINVOICE_DISABLED');
		$this->depends = array('modFacture');
		$this->requiredby = array();
		$this->conflictwith = array();
		$this->langfiles = array("timgroupsituationanddirectinvoice@timgroupsituationanddirectinvoice");
		$this->phpmin = array(7, 1);
		$this->need_dolibarr_version = array(19, -3);
		$this->need_javascript_ajax = 0;
		$this->warnings_activation = array();
		$this->warnings_activation_ext = array();
		$this->const = array();
		if (!isModEnabled("timgroupsituationanddirectinvoice")) {
			$conf->timgroupsituationanddirectinvoice = new stdClass();
			$conf->timgroupsituationanddirectinvoice->enabled = 0;
		}
		$this->tabs = array();
		$this->dictionaries = array();
		$this->boxes = array();
		$this->cronjobs = array();
		$this->rights = array();
		$r = 0;
		$this->menu = array();
		$r = 0;
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int<-1,1>          	1 if OK, <=0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;
		
		$langs->loadLangs(array('timgroupsituationanddirectinvoice@timgroupsituationanddirectinvoice'));

		$extrafields = new ExtraFields($this->db);
        $extrafields->addExtraField('dirpaytotht', $langs->trans("TotalDirectPayments"), 'price', 25, null, 'facture', 0, 0, '', 0, 0, '', 1, 0);

		$sql = array();

        $sql[] = "CREATE TABLE IF NOT EXISTS ".MAIN_DB_PREFIX."timgroupsituationanddirectinvoice_directinvoice (
            rowid INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            fk_facture_client INT(11) NOT NULL,
            fk_facture_fournisseur INT(11) NOT NULL,
            date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY fk_client_fourn (fk_facture_client, fk_facture_fournisseur)
        ) ENGINE=innodb DEFAULT CHARSET=utf8mb4;";

        return $this->_init($sql, $options);
	}

	/**
	 *	Function called when module is disabled.
	 *	Remove from database constants, boxes and permissions from Dolibarr database.
	 *	Data directories are not deleted
	 *
	 *	@param	string		$options	Options when enabling module ('', 'noboxes')
	 *	@return	int<-1,1>				1 if OK, <=0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
        $sql[] = "DROP TABLE IF EXISTS ".MAIN_DB_PREFIX."timgroupsituationanddirectinvoice_directinvoice";
        return $this->_remove($sql, $options);
	}
}
