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
 * Constants
 */
define('AVISOTA_VERSION', '2.0.0');
define('AVISOTA_RELEASE', 'alpha1');
define('NL_HTML', 'html');
define('NL_PLAIN', 'plain');


/**
 * Update check
 */
$avisotaUpdateRequired = AvisotaUpdate::getInstance()
	->hasUpdates();


/**
 * Request starttime
 */
if (!isset($_SERVER['REQUEST_TIME'])) {
	$_SERVER['REQUEST_TIME'] = time();
}


/**
 * Settings
 */
$GLOBALS['TL_CONFIG']['avisota_max_send_time']          = ini_get('max_execution_time') > 0 ? floor(
	0.85 * ini_get('max_execution_time')
) : 120;
$GLOBALS['TL_CONFIG']['avisota_max_send_count']         = 100;
$GLOBALS['TL_CONFIG']['avisota_max_send_timeout']       = 1;
$GLOBALS['TL_CONFIG']['avisota_notification_time']      = 3;
$GLOBALS['TL_CONFIG']['avisota_notification_count']     = 3;
$GLOBALS['TL_CONFIG']['avisota_cleanup_time']           = 14;


/**
 * Salutation
 */
if (!isset($GLOBALS['TL_CONFIG']['avisota_salutations'])) {
	$GLOBALS['TL_CONFIG']['avisota_salutations'][] = array(
		'salutation' => 'Sehr geehrter Herr',
		'title'      => true,
		'firstname'  => true,
		'lastname'   => true
	);
	$GLOBALS['TL_CONFIG']['avisota_salutations'][] = array(
		'salutation' => 'Sehr geehrte Frau',
		'title'      => true,
		'firstname'  => true,
		'lastname'   => true
	);
	$GLOBALS['TL_CONFIG']['avisota_salutations'][] = array(
		'salutation' => 'Sehr geehrte/-r Herr/Frau',
		'title'      => true,
		'firstname'  => true,
		'lastname'   => true
	);
	$GLOBALS['TL_CONFIG']['avisota_salutations'][] = array(
		'salutation' => 'Sehr geehrter Herr',
		'title'      => false,
		'firstname'  => true,
		'lastname'   => true
	);
	$GLOBALS['TL_CONFIG']['avisota_salutations'][] = array(
		'salutation' => 'Sehr geehrte Frau',
		'title'      => false,
		'firstname'  => true,
		'lastname'   => true
	);
	$GLOBALS['TL_CONFIG']['avisota_salutations'][] = array(
		'salutation' => 'Sehr geehrte/-r Herr/Frau',
		'title'      => false,
		'firstname'  => true,
		'lastname'   => true
	);
	$GLOBALS['TL_CONFIG']['avisota_salutations'][] = array(
		'salutation' => 'Sehr geehrter',
		'title'      => false,
		'firstname'  => true,
		'lastname'   => true
	);
	$GLOBALS['TL_CONFIG']['avisota_salutations'][] = array(
		'salutation' => 'Sehr geehrte',
		'title'      => false,
		'firstname'  => true,
		'lastname'   => true
	);
	$GLOBALS['TL_CONFIG']['avisota_salutations'][] = array(
		'salutation' => 'Sehr geehrte/-r',
		'title'      => false,
		'firstname'  => true,
		'lastname'   => true
	);
	$GLOBALS['TL_CONFIG']['avisota_salutations'][] = array(
		'salutation' => 'Hallo',
		'title'      => false,
		'firstname'  => true,
		'lastname'   => false
	);
}
else if (is_string($GLOBALS['TL_CONFIG']['avisota_salutations'])) {
	$GLOBALS['TL_CONFIG']['avisota_salutations'] = deserialize($GLOBALS['TL_CONFIG']['avisota_salutations'], true);
}

/**
 * Page types
 */
$GLOBALS['TL_PTY']['avisota'] = 'PageAvisotaNewsletter';


/**
 * Form fields
 */
$GLOBALS['BE_FFL']['upload']                 = 'UploadField';
$GLOBALS['BE_FFL']['columnAssignmentWizard'] = 'ColumnAssignmentWizard';

/**
 * Build custom back end modules
 */
$customModules = array();
$backendUser          = BackendUser::getInstance();
$database      = Database::getInstance();
if ($database->fieldExists('showInMenu', 'tl_avisota_newsletter_category')) {
	$category = $database->query(
		'SELECT * FROM tl_avisota_newsletter_category WHERE showInMenu=\'1\' ORDER BY title'
	);
	while ($category->next()) {
		$customModules['avisota_newsletter_' . $category->id]          = array(
			'href'       => 'table=tl_avisota_newsletter&amp;id=' . $category->id,
			'tables'     => array(
				'tl_avisota_newsletter_category',
				'tl_avisota_newsletter',
				'tl_avisota_newsletter_content',
				'tl_avisota_newsletter_create_from_draft'
			),
			'send'       => array('Avisota', 'send'),
			'icon'       => $category->menuIcon ? $category->menuIcon
				: 'system/modules/Avisota2/html/newsletter.png',
			'stylesheet' => 'system/modules/Avisota2/html/stylesheet.css'
		);
		$GLOBALS['TL_LANG']['MOD']['avisota_newsletter_' . $category->id] = array($category->title, '');
	}
}

/**
 * Back end modules
 */
$i                 = array_search('design', array_keys($GLOBALS['BE_MOD']));
$GLOBALS['BE_MOD'] = array_merge(
	array_slice($GLOBALS['BE_MOD'], 0, $i),
	array
	(
		'avisota' => array_merge
		(
			array(
				'avisota_outbox'   => array
				(
					'callback'   => 'AvisotaBackendOutbox',
					'icon'       => 'system/modules/Avisota2/html/outbox.png',
					'stylesheet' => 'system/modules/Avisota2/html/stylesheet.css'
				)
			),
			$customModules,
			array(
				'avisota_newsletter' => array
				(
					'tables'     => array(
						'tl_avisota_newsletter_category',
						'tl_avisota_newsletter',
						'tl_avisota_newsletter_content',
						'tl_avisota_newsletter_create_from_draft'
					),
					'send'       => array('Avisota', 'send'),
					'icon'       => 'system/modules/Avisota2/html/newsletter.png',
					'stylesheet' => 'system/modules/Avisota2/html/stylesheet.css'
				),
				'avisota_recipients' => array
				(
					'tables'     => array(
						'tl_avisota_recipient',
						'tl_avisota_recipient_migrate',
						'tl_avisota_recipient_import',
						'tl_avisota_recipient_export',
						'tl_avisota_recipient_remove',
						'tl_avisota_recipient_notify'
					),
					'icon'       => 'system/modules/Avisota2/html/recipients.png',
					'stylesheet' => 'system/modules/Avisota2/html/stylesheet.css',
					'javascript' => 'system/modules/Avisota2/html/backend.js'
				)
			)
		)
	),
	array_slice($GLOBALS['BE_MOD'], $i)
);

$i                 = array_search('system', array_keys($GLOBALS['BE_MOD'])) + 1;
$GLOBALS['BE_MOD'] = array_merge(
	array_slice($GLOBALS['BE_MOD'], 0, $i),
	array(
		'avisota_settings_group' => array
		(
			'avisota_mailing_list'     => array
			(
				'tables'     => array('tl_avisota_mailing_list'),
				'icon'       => 'system/modules/Avisota2/html/mailing_list.png',
				'stylesheet' => 'system/modules/Avisota2/html/stylesheet.css'
			),
			'avisota_newsletter_draft' => array
			(
				'tables'     => array('tl_avisota_newsletter_draft', 'tl_avisota_newsletter_draft_content'),
				'render'     => array('AvisotaBackend', 'renderDraft'),
				'preview'    => array('AvisotaBackend', 'previewDraft'),
				'icon'       => 'system/modules/Avisota2/html/newsletter_draft.png',
				'stylesheet' => 'system/modules/Avisota2/html/stylesheet.css'
			),
			'avisota_settings'         => array
			(
				'tables'     => array('tl_avisota_settings'),
				'icon'       => 'system/modules/Avisota2/html/settings.png',
				'stylesheet' => 'system/modules/Avisota2/html/stylesheet.css'
			),
			'avisota_theme'            => array
			(
				'tables'     => array('tl_avisota_newsletter_theme'),
				'icon'       => 'system/modules/Avisota2/html/theme.png',
				'stylesheet' => 'system/modules/Avisota2/html/stylesheet.css'
			),
			'avisota_recipient_source' => array
			(
				'tables'     => array('tl_avisota_recipient_source'),
				'icon'       => 'system/modules/Avisota2/html/recipient_source.png',
				'stylesheet' => 'system/modules/Avisota2/html/stylesheet.css'
			),
			'avisota_transport'        => array
			(
				'tables'     => array('tl_avisota_transport'),
				'icon'       => 'system/modules/Avisota2/html/transport.png',
				'stylesheet' => 'system/modules/Avisota2/html/stylesheet.css'
			),
			'avisota_update'           => array
			(
				'callback'   => 'AvisotaUpdate',
				'icon'       => 'system/modules/Avisota2/html/update.png',
				'stylesheet' => 'system/modules/Avisota2/html/stylesheet.css'
			)
		)
	),
	array_slice($GLOBALS['BE_MOD'], $i)
);

// TODO gray out outbox if nothink in there!

/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['avisota']['avisota_subscribe']    = 'ModuleAvisotaSubscribe';
$GLOBALS['FE_MOD']['avisota']['avisota_unsubscribe']  = 'ModuleAvisotaUnsubscribe';
$GLOBALS['FE_MOD']['avisota']['avisota_subscription'] = 'ModuleAvisotaSubscription';
$GLOBALS['FE_MOD']['avisota']['avisota_list']         = 'ModuleAvisotaList';
$GLOBALS['FE_MOD']['avisota']['avisota_reader']       = 'ModuleAvisotaReader';


/**
 * Newsletter elements
 */
$GLOBALS['TL_NLE'] = array_merge_recursive(
	array
	(
		'texts'    => array
		(
			'headline' => 'NewsletterHeadline',
			'text'     => 'NewsletterText',
			'list'     => 'NewsletterList',
			'table'    => 'NewsletterTable'
		),
		'links'    => array
		(
			'hyperlink' => 'NewsletterHyperlink'
		),
		'images'   => array
		(
			'image'   => 'NewsletterImage',
			'gallery' => 'NewsletterGallery'
		),
		'includes' => array
		(
			'news'    => 'NewsletterNews',
			'events'  => 'NewsletterEvent',
			'article' => 'NewsletterArticleTeaser'
		)
	),
	is_array($GLOBALS['TL_NLE']) ? $GLOBALS['TL_NLE'] : array()
);


/**
 * Widgets
 */
$GLOBALS['BE_FFL']['eventchooser'] = 'WidgetEventchooser';
$GLOBALS['BE_FFL']['newschooser']  = 'WidgetNewschooser';


/**
 * Recipient sources
 */
$GLOBALS['TL_AVISOTA_RECIPIENT_SOURCE']['integrated'] = 'AvisotaRecipientSourceIntegratedRecipients';
$GLOBALS['TL_AVISOTA_RECIPIENT_SOURCE']['member']     = 'AvisotaRecipientSourceMemberGroup';
$GLOBALS['TL_AVISOTA_RECIPIENT_SOURCE']['csv_file']   = 'AvisotaRecipientSourceCSVFile';


/**
 * Transport modules
 */
$GLOBALS['TL_AVISOTA_TRANSPORT']['swift'] = 'AvisotaTransportSwiftTransport';


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['outputBackendTemplate'][]   = array('AvisotaBackend', 'hookOutputBackendTemplate');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][]       = array('AvisotaInsertTag', 'hookReplaceNewsletterInsertTags');
$GLOBALS['TL_HOOKS']['getEditorStylesLayout'][]   = array('AvisotaBackendEditorStyle', 'hookGetEditorStylesLayout');
$GLOBALS['TL_HOOKS']['mysqlMultiTriggerCreate'][] = array('AvisotaUpdate', 'hookMysqlMultiTriggerCreate');
$GLOBALS['TL_HOOKS']['createNewUser'][]           = array('AvisotaDCA', 'hookCreateNewUser');
$GLOBALS['TL_HOOKS']['activateAccount'][]         = array('AvisotaDCA', 'hookActivateAccount');
$GLOBALS['TL_HOOKS']['updatePersonalData'][]      = array('AvisotaDCA', 'hookUpdatePersonalData');
$GLOBALS['TL_HOOKS']['avisotaMailingListLabel'][] = array('AvisotaBackend', 'hookAvisotaMailingListLabel');
$GLOBALS['TL_HOOKS']['getUserNavigation'][]       = array('AvisotaBackend', 'hookGetUserNavigation');


/**
 * Procedures
 */
$GLOBALS['TL_PROCEDURE']['member_to_mailing_list(IN MEMBER_ID INT, IN LIST_IDS BLOB)'] = '
-- clear the association table
DELETE FROM tl_member_to_mailing_list WHERE member=MEMBER_ID
	AND list NOT IN (SELECT id FROM tl_avisota_mailing_list WHERE FIND_IN_SET(id, LIST_IDS));

-- insert new association
INSERT INTO tl_member_to_mailing_list (member, list)
	SELECT MEMBER_ID, id FROM tl_avisota_mailing_list WHERE FIND_IN_SET(id, LIST_IDS)
		AND id NOT IN (SELECT list FROM tl_member_to_mailing_list WHERE member=MEMBER_ID);
';


/**
 * Multi Triggers
 */
$GLOBALS['TL_TRIGGER']['tl_avisota_recipient']['before']['delete'][]    = 'DELETE FROM tl_avisota_recipient_to_mailing_list WHERE recipient=OLD.id;';
$GLOBALS['TL_TRIGGER']['tl_member']['after']['insert'][]                = 'CALL member_to_mailing_list(NEW.id, NEW.avisota_lists);';
$GLOBALS['TL_TRIGGER']['tl_member']['after']['update'][]                = 'CALL member_to_mailing_list(NEW.id, NEW.avisota_lists);';
$GLOBALS['TL_TRIGGER']['tl_member']['before']['delete'][]               = 'DELETE FROM tl_member_to_mailing_list WHERE member=OLD.id;';
$GLOBALS['TL_TRIGGER']['tl_avisota_mailing_list']['before']['delete'][] = 'DELETE FROM tl_avisota_recipient_to_mailing_list WHERE list=OLD.id;
DELETE FROM tl_member_to_mailing_list WHERE list=OLD.id;';


/**
 * Graphical text support.
 */
if (in_array('graphicaltext', $this->getActiveModules())) {
	$GLOBALS['TL_HOOKS']['parseAvisotaNewsletterTemplate'][] = array(
		'FrontendGraphicalText',
		'replaceGraphicalTextTag'
	);
}


/**
 * Custom user permissions.
 */
$GLOBALS['TL_PERMISSIONS'][] = 'avisota_recipient_lists';
$GLOBALS['TL_PERMISSIONS'][] = 'avisota_recipient_list_permissions';
$GLOBALS['TL_PERMISSIONS'][] = 'avisota_recipient_permissions';
$GLOBALS['TL_PERMISSIONS'][] = 'avisota_newsletter_categories';
$GLOBALS['TL_PERMISSIONS'][] = 'avisota_newsletter_category_permissions';
$GLOBALS['TL_PERMISSIONS'][] = 'avisota_newsletter_permissions';


/**
 * Cron
 */
$GLOBALS['TL_CRON']['daily'][] = array('AvisotaBackend', 'cronCleanupRecipientList');
$GLOBALS['TL_CRON']['daily'][] = array('AvisotaBackend', 'cronNotifyRecipients');


/**
 * folderurl support
 */
$GLOBALS['URL_KEYWORDS'][] = 'item';


/**
 * Hack: Fix ajax load import source tree.
 */
if (($_GET['table'] == 'tl_avisota_recipient_import' || $_GET['table'] == 'tl_avisota_recipient_remove') && ($_GET['isAjax'] || $_POST['isAjax'])) {
	unset($_GET['table']);
}


/**
 * JavaScript inject
 */
if (TL_MODE == 'BE' && $_GET['do'] == 'avisota_recipients') {
	$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/Avisota2/html/tl_avisota_recipient.js.php';
}


/**
 * Compatibility check
 */
if (version_compare(
	Database::getInstance()
		->query('SHOW VARIABLES WHERE Variable_name = \'version\'')->Value,
	'5',
	'<'
)
) {
	$environment = Environment::getInstance();
	if ( // The update controller itself
		strpos($environment->requestUri, 'system/modules/Avisota2/AvisotaCompatibilityController.php') === false
		// Backend login
		&& strpos($environment->requestUri, 'contao/index.php') === false
		// Extension manager
		&& strpos($environment->requestUri, 'contao/main.php?do=repository_manager') === false
		// Install Tool
		&& strpos($environment->requestUri, 'contao/install.php') === false
	) {
		header(
			'Location: ' . $environment->url . $GLOBALS['TL_CONFIG']['websitePath'] . '/system/modules/Avisota2/AvisotaCompatibilityController.php'
		);
		exit;
	}
}

/**
 * Update script
 */
else if (TL_MODE == 'BE') {
	/*
	$objEnvironment = Environment::getInstance();
	if ($blnAvisotaUpdate
		// The update controller itself
		&& strpos($objEnvironment->requestUri, 'contao/main.php?do=avisota_update') === false
		// The system log
		&& strpos($objEnvironment->requestUri, 'contao/main.php?do=log') === false
		// Backend login
		&& strpos($objEnvironment->requestUri, 'contao/index.php') === false
		// Install Tool
		&& strpos($objEnvironment->requestUri, 'contao/install.php') === false
	) {
		header('Location: ' . $objEnvironment->url . $GLOBALS['TL_CONFIG']['websitePath'] . '/contao/main.php?do=avisota_update');
		exit;
	}
	*/
}
