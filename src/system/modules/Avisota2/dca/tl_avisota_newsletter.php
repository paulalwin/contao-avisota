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
 * @package    Avisota
 * @license    LGPL
 * @filesource
 */


/**
 * Table tl_avisota_newsletter
 */
$GLOBALS['TL_DCA']['tl_avisota_newsletter'] = array
(

	// Config
	'config'          => array
	(
		'dataContainer'     => 'Table',
		'ptable'            => 'tl_avisota_newsletter_category',
		'ctable'            => array('tl_avisota_newsletter_content'),
		'switchToEdit'      => true,
		'enableVersioning'  => true,
		'palettes_callback' => array
		(
			array('tl_avisota_newsletter', 'updatePalette')
		),
		'onload_callback'   => array
		(
			array('tl_avisota_newsletter', 'checkPermission')
		)
	),
	// List
	'list'            => array
	(
		'sorting'           => array
		(
			'mode'                  => 4,
			'fields'                => array('sendOn=\'\' DESC', 'sendOn DESC'),
			'panelLayout'           => 'search,limit',
			'headerFields'          => array('title'),
			'header_callback'       => array('tl_avisota_newsletter', 'addHeader'),
			'child_record_callback' => array('tl_avisota_newsletter', 'addNewsletter'),
			'child_record_class'    => 'no_padding',
		),
		'label'             => array
		(
			'group_callback' => array('tl_avisota_newsletter', 'addGroup')
		),
		'global_operations' => array
		(
			'createFromDraft' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['create_from_draft'],
				'href'       => 'table=tl_avisota_newsletter_create_from_draft&amp;act=edit',
				'class'      => 'header_new',
				'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="d"'
			),
			'all'             => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations'        => array
		(
			'edit'       => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['edit'],
				'href'            => 'table=tl_avisota_newsletter_content',
				'icon'            => 'edit.gif',
				'attributes'      => 'class="contextmenu"',
				'button_callback' => array('tl_avisota_newsletter', 'editNewsletter')
			),
			'editheader' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['editheader'],
				'href'            => 'act=edit',
				'icon'            => 'header.gif',
				'attributes'      => 'class="edit-header"',
				'button_callback' => array('tl_avisota_newsletter', 'editHeader')
			),
			'copy'       => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['copy'],
				'href'            => 'act=paste&amp;mode=copy',
				'icon'            => 'copy.gif',
				'attributes'      => 'onclick="Backend.getScrollOffset();"',
				'button_callback' => array('tl_avisota_newsletter', 'copyNewsletter')
			),
			'delete'     => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['delete'],
				'href'            => 'act=delete',
				'icon'            => 'delete.gif',
				'attributes'      => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
				'button_callback' => array('tl_avisota_newsletter', 'deleteNewsletter')
			),
			'show'       => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif'
			),
			'send'       => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['send'],
				'href'            => 'key=send',
				'icon'            => 'system/modules/Avisota2/html/send.png',
				'button_callback' => array('tl_avisota_newsletter', 'sendNewsletter')
			)
		),
	),
	// Palettes
	'metapalettes'    => array
	(
		'default' => array
		(
			'newsletter' => array('subject', 'alias'),
			'meta'       => array('description', 'keywords'),
			'recipient'  => array(),
			'theme'      => array(),
			'transport'  => array(),
			'attachment' => array('addFile'),
			'template'   => array(':hide', 'template_html', 'template_plain')
		),
	),
	// Subpalettes
	'metasubpalettes' => array
	(
		'setRecipients' => array('recipients'),
		'setTheme'      => array('theme'),
		'setTransport'  => array('transport'),
		'addFile'       => array('files')
	),
	// Fields
	'fields'          => array
	(
		'subject'       => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['subject'],
			'exclude'   => true,
			'search'    => true,
			'flag'      => 1,
			'inputType' => 'text',
			'eval'      => array(
				'mandatory'      => true,
				'maxlength'      => 255,
				'tl_class'       => 'w50',
				'decodeEntities' => true
			)
		),
		'alias'         => array
		(
			'label'         => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['alias'],
			'exclude'       => true,
			'search'        => true,
			'inputType'     => 'text',
			'eval'          => array(
				'rgxp'              => 'alnum',
				'unique'            => true,
				'spaceToUnderscore' => true,
				'maxlength'         => 128,
				'tl_class'          => 'w50'
			),
			'save_callback' => array
			(
				array('tl_avisota_newsletter', 'generateAlias')
			)
		),
		'description'   => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['description'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array(
				'maxlength' => 255,
				'tl_class'  => 'w50'
			)
		),
		'keywords'      => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['keywords'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array(
				'maxlength' => 255,
				'tl_class'  => 'w50'
			)
		),
		'setRecipients' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['setRecipients'],
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'clr m12', 'submitOnChange' => true)
		),
		'recipients'    => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['recipients'],
			'inputType'        => 'checkbox',
			'options_callback' => array('AvisotaBackend', 'getRecipients'),
			'eval'             => array(
				'mandatory' => true,
				'multiple'  => true,
				'tl_class'  => 'clr'
			)
		),
		'setTheme'      => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['setTheme'],
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'clr m12 w50', 'submitOnChange' => true)
		),
		'theme'         => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['theme'],
			'inputType'  => 'select',
			'foreignKey' => 'tl_avisota_newsletter_theme.title',
			'eval'       => array(
				'mandatory' => true,
				'tl_class'  => 'w50'
			)
		),
		'setTransport'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['setTransport'],
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'clr m12 w50', 'submitOnChange' => true)
		),
		'transport'     => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['transport'],
			'inputType'  => 'select',
			'foreignKey' => 'tl_avisota_transport.title',
			'eval'       => array(
				'mandatory' => true,
				'tl_class'  => 'w50'
			)
		),
		'addFile'       => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['addFile'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => array('submitOnChange' => true)
		),
		'files'         => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_avisota_newsletter']['files'],
			'exclude'   => true,
			'inputType' => 'fileTree',
			'eval'      => array(
				'fieldType' => 'checkbox',
				'files'     => true,
				'filesOnly' => true,
				'mandatory' => true
			)
		),
		'sendOn'        => array
		(
			'label'   => &$GLOBALS['TL_LANG']['tl_avisota_recipient']['sendOn'],
			'filter'  => true,
			'sorting' => true,
			'flag'    => 7,
			'eval'    => array(
				'rgxp'      => 'datim',
				'doNotCopy' => true,
				'doNotShow' => true
			)
		)
	)
);


class tl_avisota_newsletter extends Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	public function updatePalette()
	{
		if ($this->Input->get('act') == 'edit') {
			$category = $this->Database
				->prepare(
				'SELECT c.*
						   FROM tl_avisota_newsletter_category c
						   INNER JOIN tl_avisota_newsletter n
						   ON c.id=n.pid
						   WHERE n.id=?'
			)
				->execute($this->Input->get('id'));

			if ($category->next()) {
				switch ($category->recipientsMode) {
					case 'byNewsletterOrCategory':
						$GLOBALS['TL_DCA']['tl_avisota_newsletter']['metapalettes']['default']['recipient'][] = 'setRecipients';
						break;

					case 'byNewsletter':
						$GLOBALS['TL_DCA']['tl_avisota_newsletter']['metapalettes']['default']['recipient'][] = 'recipients';
						break;
				}

				switch ($category->themeMode) {
					case 'byNewsletterOrCategory':
						$GLOBALS['TL_DCA']['tl_avisota_newsletter']['metapalettes']['default']['theme'][] = 'setTheme';
						break;

					case 'byNewsletter':
						$GLOBALS['TL_DCA']['tl_avisota_newsletter']['metapalettes']['default']['theme'][] = 'theme';
						break;
				}

				switch ($category->transportMode) {
					case 'byNewsletterOrCategory':
						$GLOBALS['TL_DCA']['tl_avisota_newsletter']['metapalettes']['default']['transport'][] = 'setTransport';
						break;

					case 'byNewsletter':
						$GLOBALS['TL_DCA']['tl_avisota_newsletter']['metapalettes']['default']['transport'][] = 'transport';
						break;
				}
			}
		}
		else {
			$category = $this->Database
				->prepare(
				'SELECT c.*
						   FROM tl_avisota_newsletter_category c
						   WHERE c.id=?'
			)
				->execute($this->Input->get('id'));

			if ($category->next()) {
				switch ($category->recipientsMode) {
					case 'byNewsletterOrCategory':
					case 'byCategory':
						$GLOBALS['TL_DCA']['tl_avisota_newsletter']['list']['sorting']['headerFields'][] = 'recipients';
						break;
				}

				switch ($category->themeMode) {
					case 'byNewsletterOrCategory':
					case 'byCategory':
						$GLOBALS['TL_DCA']['tl_avisota_newsletter']['list']['sorting']['headerFields'][] = 'theme';
						break;
				}

				switch ($category->transportMode) {
					case 'byNewsletterOrCategory':
					case 'byCategory':
						$GLOBALS['TL_DCA']['tl_avisota_newsletter']['list']['sorting']['headerFields'][] = 'transport';
						break;
				}
			}
		}
	}

	/**
	 * Check permissions to edit table tl_newsletter_channel
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin) {
			return;
		}

		// Set root IDs
		if (!is_array($this->User->avisota_newsletter_categories) || count(
			$this->User->avisota_newsletter_categories
		) < 1
		) {
			$root = array(0);
		}
		else {
			$root = $this->User->avisota_newsletter_categories;
		}

		// Check permissions to add channels
		if (!$this->User->hasAccess('create', 'avisota_newsletter_permissions')) {
			$GLOBALS['TL_DCA']['tl_avisota_newsletter']['config']['closed'] = true;
		}

		// Check current action
		switch ($this->Input->get('act')) {
			case 'create':
			case 'select':
				// Allow
				break;

			case 'edit':
			case 'copy':
			case 'paste':
			case 'delete':
			case 'show':
				$pid = -1;
				if ($this->Input->get('id')) {
					$newsletter = $this->Database
						->prepare("SELECT * FROM tl_avisota_newsletter WHERE id=?")
						->execute($this->Input->get('id'));
					if ($newsletter->next()) {
						$pid = $newsletter->pid;
					}
				}
				if (!in_array($pid, $root) || ($this->Input->get('act') == 'delete' && !$this->User->hasAccess(
					'delete',
					'avisota_newsletter_permissions'
				))
				) {
					$this->log(
						'Not enough permissions to ' . $this->Input->get(
							'act'
						) . ' avisota newsletter ID "' . $this->Input->get('id') . '"',
						'tl_avisota_newsletter checkPermission',
						TL_ERROR
					);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
				$session = $this->Session->getData();
				if ($this->Input->get('act') == 'deleteAll' && !$this->User->hasAccess(
					'delete',
					'avisota_newsletter_permissions'
				)
				) {
					$session['CURRENT']['IDS'] = array();
				}
				else {
					$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
				}
				$this->Session->setData($session);
				break;

			default:
				if (strlen($this->Input->get('act'))) {
					$this->log(
						'Not enough permissions to ' . $this->Input->get('act') . ' avisota newsletter',
						'tl_avisota_newsletter checkPermission',
						TL_ERROR
					);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}

	/**
	 * Return the edit button
	 *
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function editNewsletter($row, $href, $label, $title, $icon, $attributes)
	{
		return (!$row['sendOn'] && ($this->User->isAdmin || count(
			preg_grep('/^tl_avisota_newsletter::/', $this->User->alexf)
		) > 0)) ? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars(
			$title
		) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ' : '';
	}

	/**
	 * Return the edit header button
	 *
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function editHeader($row, $href, $label, $title, $icon, $attributes)
	{
		return (!$row['sendOn'] && ($this->User->isAdmin || count(
			preg_grep('/^tl_avisota_newsletter::/', $this->User->alexf)
		) > 0)) ? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars(
			$title
		) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ' : '';
	}


	/**
	 * Return the copy button
	 *
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function copyNewsletter($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('create', 'avisota_newsletter_permissions'))
			? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars(
				$title
			) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> '
			: $this->generateImage(
				preg_replace('/\.gif$/i', '_.gif', $icon)
			) . ' ';
	}


	/**
	 * Return the delete button
	 *
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function deleteNewsletter($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('delete', 'avisota_newsletter_permissions'))
			? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars(
				$title
			) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> '
			: $this->generateImage(
				preg_replace('/\.gif$/i', '_.gif', $icon)
			) . ' ';
	}


	/**
	 * Return the send button
	 *
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function sendNewsletter($row, $href, $label, $title, $icon, $attributes)
	{
		if (!$this->User->isAdmin && !$this->User->hasAccess('send', 'avisota_newsletter_permissions')) {
			$label = $GLOBALS['TL_LANG']['tl_avisota_newsletter']['view_only'][0];
			$title = $GLOBALS['TL_LANG']['tl_avisota_newsletter']['view_only'][1];
		}
		return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars(
			$title
		) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ';
	}

	public function addHeader($add, $dc)
	{
		$key       = $GLOBALS['TL_LANG']['tl_avisota_newsletter_category']['recipients'][0];
		$add[$key] = array();

		$category = AvisotaNewsletterCategory::load($dc->id);

		$fallback = $category->recipientsMode == 'byNewsletterOrCategory';

		$selectedRecipients = $category->getRecipients();

		$recipients = AvisotaBackend::getInstance()
			->getRecipients(true);

		foreach ($recipients as $group => $lists) {
			list($source, $group) = explode(':', $group, 2);
			foreach ($lists as $listKey => $list) {
				if (in_array($listKey, $selectedRecipients)) {
					$add[$key][] = sprintf(
						'<a href="contao/main.php?do=avisota_recipient_source&act=edit&id=%d">%s &raquo; %s</a>%s',
						$source,
						$group,
						$list,
						$fallback ? ' ' . $GLOBALS['TL_LANG']['tl_avisota_newsletter']['fallback'] : ''
					);
				}
			}
		}

		$add[$key] = implode('<br>', $add[$key]);


		if ($category->themeMode == 'byNewsletterOrCategory') {
			$key = $GLOBALS['TL_LANG']['tl_avisota_newsletter_category']['theme'][0];
			$add[$key] .= ' ' . $GLOBALS['TL_LANG']['tl_avisota_newsletter']['fallback'];
		}


		if ($category->transportMode == 'byNewsletterOrCategory') {
			$key = $GLOBALS['TL_LANG']['tl_avisota_newsletter_category']['transport'][0];
			$add[$key] .= ' ' . $GLOBALS['TL_LANG']['tl_avisota_newsletter']['fallback'];
		}


		return $add;
	}

	/**
	 * Add the recipient row.
	 *
	 * @param array
	 */
	public function addNewsletter($newsletterData)
	{
		$icon = $newsletterData['sendOn'] ? 'visible' : 'invisible';

		$label = $newsletterData['subject'];

		if ($row['sendOn']) {
			$label .= ' <span style="color:#b3b3b3; padding-left:3px;">(' . sprintf(
				$GLOBALS['TL_LANG']['tl_avisota_recipient']['sended'],
				$this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $row['sendOn'])
			) . ')</span>';
		}

		return sprintf(
			'<div class="list_icon" style="background-image:url(\'system/themes/%s/images/%s.gif\');">%s</div>',
			$this->getTheme(),
			$icon,
			$label
		);
	}

	public function addGroup($group, $mode, $field, $row, $dc)
	{
		if (!isset($GLOBALS['MAGIC_ADD_GROUP_INDEX'])) {
			$GLOBALS['MAGIC_ADD_GROUP_INDEX'] = 0;
		}
		else {
			$GLOBALS['MAGIC_ADD_GROUP_INDEX']++;
		}

		if ($row[$GLOBALS['MAGIC_ADD_GROUP_INDEX']]['sendOn'] > 0) {
			return $this->parseDate('F Y', $row[$GLOBALS['MAGIC_ADD_GROUP_INDEX']]['sendOn']);
		}
		return $GLOBALS['TL_LANG']['tl_avisota_newsletter']['notSend'];
	}

	/**
	 * Autogenerate a news alias if it has not been set yet
	 *
	 * @param mixed
	 * @param object
	 *
	 * @return string
	 */
	public function generateAlias($value, DataContainer $dc)
	{
		$autoAlias = false;

		// Generate alias if there is none
		if (!strlen($value)) {
			$autoAlias = true;
			$value  = standardize($dc->activeRecord->subject);
		}

		$aliasResultSet = $this->Database
			->prepare("SELECT id FROM tl_avisota_newsletter WHERE alias=?")
			->execute($value);

		// Check whether the news alias exists
		if ($aliasResultSet->numRows > 1 && !$autoAlias) {
			throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $value));
		}

		// Add ID to alias
		if ($aliasResultSet->numRows && $autoAlias) {
			$value .= '-' . $dc->id;
		}

		return $value;
	}
}

