<?php if (defined('TL_ROOT')) {
	die('You can not access this file via contao!');
}

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


// run in FE mode
define('TL_MODE', 'FE');

// Define the static URL constants
define('TL_FILES_URL', '');
define('TL_SCRIPT_URL', '');
define('TL_PLUGINS_URL', '');

// initialize contao
include('../../initialize.php');

/**
 * Class AvisotaNewsletterPreview
 *
 * @copyright  InfinitySoft 2010,2011,2012
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Avisota
 */
class AvisotaNewsletterPreview extends Backend
{
	/**
	 * @var AvisotaBase
	 */
	protected $Base;

	/**
	 * @var AvisotaNewsletterContent
	 */
	protected $Content;

	/**
	 * @var AvisotaStatic
	 */
	protected $Static;

	public function __construct()
	{
		$this->import('BackendUser', 'User');
		parent::__construct();
		$this->import('AvisotaBase', 'Base');
		$this->import('AvisotaNewsletterContent', 'Content');
		$this->import('AvisotaStatic', 'Static');

		// force all URLs absolute
		$GLOBALS['TL_CONFIG']['forceAbsoluteDomainLink'] = true;

		// load default translations
		$this->loadLanguageFile('default');

		// HOTFIX Remove isotope frontend hook
		if (isset($GLOBALS['TL_HOOKS']['parseTemplate']) && is_array($GLOBALS['TL_HOOKS']['parseTemplate'])) {
			foreach ($GLOBALS['TL_HOOKS']['parseTemplate'] as $k => $v) {
				if ($v[0] == 'IsotopeFrontend') {
					unset($GLOBALS['TL_HOOKS']['parseTemplate'][$k]);
				}
			}
		}
		// HOTFIX Remove catalog frontend hook
		if (isset($GLOBALS['TL_HOOKS']['parseFrontendTemplate']) && is_array(
			$GLOBALS['TL_HOOKS']['parseFrontendTemplate']
		)
		) {
			foreach ($GLOBALS['TL_HOOKS']['parseFrontendTemplate'] as $k => $v) {
				if ($v[0] == 'CatalogExt') {
					unset($GLOBALS['TL_HOOKS']['parseFrontendTemplate'][$k]);
				}
			}
		}
	}

	public function run()
	{
		// user have to be authenticated
		$this->User->authenticate();

		// read session data
		$sessionData = $this->Session->get('AVISOTA_PREVIEW');

		// get preview mode
		if ($this->Input->get('mode')) {
			$sessionData['mode'] = $this->Input->get('mode');
		}

		// fallback preview mode
		if (!$sessionData['mode']) {
			$sessionData['mode'] = NL_HTML;
		}

		/*
		// get personalized state
		if ($this->Input->get('personalized'))
		{
			$arrSession['personalized'] = $this->Input->get('personalized');
		}

		// fallback personalized state
		if (!$arrSession['personalized'])
		{
			$arrSession['personalized'] = 'anonymous';
		}
		*/

		// store session data
		$this->Session->set('AVISOTA_PREVIEW', $sessionData);

		// find the newsletter
		$id = $this->Input->get('id');

		$newsletter = $this->Database
			->prepare(
			"
						SELECT
							*
						FROM
							tl_avisota_newsletter
						WHERE
							id=?"
		)
			->execute($id);

		if (!$newsletter->next()) {
			$this->log('Newsletter ID ' . $id . ' does not exists!', 'AvisotaNewsletterPreview', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		// find the newsletter category
		$category = $this->Database
			->prepare(
			"
						SELECT
							*
						FROM
							tl_avisota_newsletter_category
						WHERE
							id=?"
		)
			->execute($newsletter->pid);

		if (!$category->next()) {
			$this->log(
				'Category ID ' . $newsletter->pid . ' does not exists!',
				'AvisotaNewsletterPreview',
				TL_ERROR
			);
			$this->redirect('contao/main.php?act=error');
		}

		// build the recipient data array
		$recipientData = $this->Base->getPreviewRecipient();

		$this->Static->set($category, $newsletter, $recipientData);

		// generate the preview
		switch ($sessionData['mode']) {
			case NL_HTML:
				header('Content-Type: text/html; charset=utf-8');
				echo $this->replaceInsertTags(
					$this->Content->prepareBeforeSending(
						$this->Content->generateHtml($newsletter, $category, $sessionData['personalized'])
					)
				);
				exit(0);

			case NL_PLAIN:
				header('Content-Type: text/plain; charset=utf-8');
				echo $this->replaceInsertTags(
					$this->Content->prepareBeforeSending(
						$this->Content->generatePlain($newsletter, $category, $sessionData['personalized'])
					)
				);
				exit(0);

			default:
				$this->log(
					'Unsupported newsletter preview mode ' . var_export($sessionData['mode']) . '!',
					'AvisotaNewsletterPreview',
					TL_ERROR
				);
				$this->redirect('contao/main.php?act=error');
		}
	}
}

try {
	$avisotaNewsletterPreview = new AvisotaNewsletterPreview();
	$avisotaNewsletterPreview->run();
}
catch (Exception $e) {
	header('HTTP/1.0 500 Internal Server Error');
	header('Content-Type: text/plain');
	echo $e->getMessage();
	echo "\n";
	echo $e->getTraceAsString();
}
