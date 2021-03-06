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
 * Table tl_avisota_mailing_list
 */
$GLOBALS['TL_DCA']['tl_avisota_mailing_list'] = array
(

	// Config
	'config'       => array
	(
		'dataContainer'    => 'Table',
		'enableVersioning' => true,
		'onload_callback'  => array
		(
			array('tl_avisota_mailing_list', 'checkPermission')
		)
	),
	// List
	'list'         => array
	(
		'sorting'           => array
		(
			'mode'        => 1,
			'flag'        => 1,
			'fields'      => array('title'),
			'panelLayout' => 'search,limit'
		),
		'label'             => array
		(
			'fields'         => array('title'),
			'format'         => '%s',
			'label_callback' => array('tl_avisota_mailing_list', 'getLabel')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations'        => array
		(
			'edit'   => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_avisota_mailing_list']['edit'],
				'href'            => 'act=edit',
				'icon'            => 'edit.gif',
				'button_callback' => array('tl_avisota_mailing_list', 'editList')
			),
			'copy'   => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_avisota_mailing_list']['copy'],
				'href'            => 'act=copy',
				'icon'            => 'copy.gif',
				'attributes'      => 'onclick="Backend.getScrollOffset();"',
				'button_callback' => array('tl_avisota_mailing_list', 'copyCategory')
			),
			'delete' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_avisota_mailing_list']['delete'],
				'href'            => 'act=delete',
				'icon'            => 'delete.gif',
				'attributes'      => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
				'button_callback' => array('tl_avisota_mailing_list', 'deleteCategory')
			),
			'show'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_avisota_mailing_list']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif'
			)
		),
	),
	// Palettes
	'metapalettes' => array
	(
		'default' => array
		(
			'list'   => array('title', 'alias'),
			'expert' => array('integratedRecipientManageSubscriptionPage')
		)
	),
	// Fields
	'fields'       => array
	(
		'title'                                     => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_avisota_mailing_list']['title'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array(
				'mandatory' => true,
				'maxlength' => 255,
				'tl_class'  => 'w50'
			)
		),
		'alias'                                     => array
		(
			'label'         => &$GLOBALS['TL_LANG']['tl_avisota_mailing_list']['alias'],
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
				array('tl_avisota_mailing_list', 'generateAlias')
			)
		),
		'viewOnlinePage'                            => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_avisota_mailing_list']['viewOnlinePage'],
			'exclude'   => true,
			'inputType' => 'pageTree',
			'eval'      => array(
				'fieldType' => 'radio',
				'mandatory' => true
			)
		),
		'integratedRecipientManageSubscriptionPage' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_avisota_mailing_list']['integratedRecipientManageSubscriptionPage'],
			'exclude'   => true,
			'inputType' => 'pageTree',
			'eval'      => array(
				'fieldType' => 'radio',
				'mandatory' => true
			)
		)
	)
);

class tl_avisota_mailing_list extends Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
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
		if (!is_array($this->User->avisota_recipient_lists) || count($this->User->avisota_recipient_lists) < 1) {
			$root = array(0);
		}
		else {
			$root = $this->User->avisota_recipient_lists;
		}

		$GLOBALS['TL_DCA']['tl_avisota_mailing_list']['list']['sorting']['root'] = $root;

		// Check permissions to add recipient lists
		if (!$this->User->hasAccess('create', 'avisota_recipient_list_permissions')) {
			$GLOBALS['TL_DCA']['tl_avisota_mailing_list']['config']['closed'] = true;
		}

		// Check current action
		switch ($this->Input->get('act')) {
			case 'create':
			case 'select':
				// Allow
				break;

			case 'edit':
				// Dynamically add the record to the user profile
				if (!in_array($this->Input->get('id'), $root)) {
					$newRecords = $this->Session->get('new_records');

					if (is_array($newRecords['tl_avisota_mailing_list']) && in_array(
						$this->Input->get('id'),
						$newRecords['tl_avisota_mailing_list']
					)
					) {
						// Add permissions on user level
						if ($this->User->inherit == 'custom' || !$this->User->groups[0]) {
							$user = $this->Database
								->prepare(
								"SELECT avisota_recipient_lists, avisota_recipient_list_permissions FROM tl_user WHERE id=?"
							)
								->limit(1)
								->execute($this->User->id);

							$newsletterCategoryPermissions = deserialize(
								$user->avisota_recipient_list_permissions
							);

							if (is_array($newsletterCategoryPermissions) && in_array(
								'create',
								$newsletterCategoryPermissions
							)
							) {
								$newsletterCategories   = deserialize($user->avisota_recipient_lists);
								$newsletterCategories[] = $this->Input->get('id');

								$this->Database
									->prepare("UPDATE tl_user SET avisota_recipient_lists=? WHERE id=?")
									->execute(serialize($newsletterCategories), $this->User->id);
							}
						}

						// Add permissions on group level
						elseif ($this->User->groups[0] > 0) {
							$group = $this->Database
								->prepare(
								"SELECT avisota_recipient_lists, avisota_recipient_list_permissions FROM tl_user_group WHERE id=?"
							)
								->limit(1)
								->execute($this->User->groups[0]);

							$newsletterCategoryPermissions = deserialize(
								$group->avisota_recipient_list_permissions
							);

							if (is_array($newsletterCategoryPermissions) && in_array(
								'create',
								$newsletterCategoryPermissions
							)
							) {
								$newsletterCategories   = deserialize($group->avisota_recipient_lists);
								$newsletterCategories[] = $this->Input->get('id');

								$this->Database
									->prepare("UPDATE tl_user_group SET avisota_recipient_lists=? WHERE id=?")
									->execute(serialize($newsletterCategories), $this->User->groups[0]);
							}
						}

						// Add new element to the user object
						$root[]                              = $this->Input->get('id');
						$this->User->avisota_recipient_lists = $root;
					}
				}
			// No break;

			case 'copy':
			case 'delete':
			case 'show':
				if (!in_array($this->Input->get('id'), $root) || ($this->Input->get(
					'act'
				) == 'delete' && !$this->User->hasAccess('delete', 'avisota_recipient_list_permissions'))
				) {
					$this->log(
						'Not enough permissions to ' . $this->Input->get(
							'act'
						) . ' avisota newsletter category ID "' . $this->Input->get('id') . '"',
						'tl_avisota_mailing_list checkPermission',
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
					'avisota_recipient_list_permissions'
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
						'Not enough permissions to ' . $this->Input->get('act') . ' avisota newsletter categories',
						'tl_avisota_mailing_list checkPermission',
						TL_ERROR
					);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}


	public function getLabel($rowData, $label, DataContainer $dc)
	{
		$label = '<div style="padding: 3px 0;"><strong>' . $label . '</strong></div>';

		if (isset($GLOBALS['TL_HOOKS']['avisotaMailingListLabel']) && is_array(
			$GLOBALS['TL_HOOKS']['avisotaMailingListLabel']
		)
		) {
			foreach ($GLOBALS['TL_HOOKS']['avisotaMailingListLabel'] as $callback) {
				$this->import($callback[0]);
				$label = $this->$callback[0]->$callback[1]($rowData, $label, $dc);
			}
		}
		return $label;
	}


	/**
	 * Return the edit list button
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
	public function editList($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || count(preg_grep('/^tl_avisota_mailing_list::/', $this->User->alexf)) > 0)
			? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars(
				$title
			) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ' : '';
	}


	/**
	 * Return the copy channel button
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
	public function copyCategory($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('create', 'avisota_recipient_list_permissions'))
			? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars(
				$title
			) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> '
			: $this->generateImage(
				preg_replace('/\.gif$/i', '_.gif', $icon)
			) . ' ';
	}


	/**
	 * Return the delete channel button
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
	public function deleteCategory($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('delete', 'avisota_recipient_list_permissions'))
			? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars(
				$title
			) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> '
			: $this->generateImage(
				preg_replace('/\.gif$/i', '_.gif', $icon)
			) . ' ';
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
			$value  = standardize($dc->activeRecord->title);
		}

		$aliasResultSet = $this->Database
			->prepare("SELECT id FROM tl_avisota_mailing_list WHERE alias=?")
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
