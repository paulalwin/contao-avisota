<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2010,2011,2012 Tristan Lins
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @copyright  InfinitySoft 2010,2011,2012
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @author     Oliver Hoff <oliver@hofff.com>
 * @package    Avisota
 * @license    LGPL
 * @filesource
 */


/**
 * Back end modules
 */
$GLOBALS['TL_LANG']['MOD']['avisota']              = 'Newsletter';
$GLOBALS['TL_LANG']['MOD']['avisota_mailing_list'] = array('Verteiler', 'Newsletter Verteiler verwalten.');
$GLOBALS['TL_LANG']['MOD']['avisota_recipients']   = array('Abonnenten', 'Newsletter Abonnenten verwalten.');
$GLOBALS['TL_LANG']['MOD']['avisota_newsletter']   = array('Newsletter', 'Newsletter verwalten und versenden.');
$GLOBALS['TL_LANG']['MOD']['avisota_outbox']       = array(
	'Postausgang',
	'Postausgang einsehen und Newsletter versenden.'
);

$GLOBALS['TL_LANG']['MOD']['avisota_settings_group']   = 'Newslettersystem';
$GLOBALS['TL_LANG']['MOD']['avisota_newsletter_draft'] = array('Vorlagen', 'Vorlagen verwalten.');
$GLOBALS['TL_LANG']['MOD']['avisota_update']           = array('Update', 'Avisota Newslettersystem aktualisieren.');
$GLOBALS['TL_LANG']['MOD']['avisota_settings']         = array('Einstellungen', 'Einstellungen verwalten.');
$GLOBALS['TL_LANG']['MOD']['avisota_theme']            = array('Layouts', 'Newsletter Layouts verwalten.');
$GLOBALS['TL_LANG']['MOD']['avisota_recipient_source'] = array(
	'Abonnentenquellen',
	'Quellen für Newsletter Abonnenten verwalten.'
);
$GLOBALS['TL_LANG']['MOD']['avisota_transport']        = array('Transportmodule', 'Transportmodule verwalten.');


/**
 * Front end modules
 */
$GLOBALS['TL_LANG']['FMD']['avisota']              = 'Newslettersystem';
$GLOBALS['TL_LANG']['FMD']['avisota_subscribe']    = array(
	'Newsletter Abonieren (Avisota)',
	'Anmeldung zum Avisota Newslettersystem.'
);
$GLOBALS['TL_LANG']['FMD']['avisota_unsubscribe']  = array(
	'Newsletter Kündigen (Avisota)',
	'Abmeldung vom Avisota Newslettersystem.'
);
$GLOBALS['TL_LANG']['FMD']['avisota_subscription'] = array(
	'Newsletter Abonnement verwalten (Avisota)',
	'An- und Abmeldung zum Avisota Newslettersystem.'
);
$GLOBALS['TL_LANG']['FMD']['avisota_list']         = array(
	'Newsletter-Liste (Avisota)',
	'List aller versendeten Newsletter.'
);
$GLOBALS['TL_LANG']['FMD']['avisota_reader']       = array(
	'Newsletter-Leser (Avisota)',
	'Einen Newsletter innerhalb einer Seite anzeigen.'
);
