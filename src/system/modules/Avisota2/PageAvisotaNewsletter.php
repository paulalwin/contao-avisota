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
 * Class PageAvisotaNewsletter
 *
 *
 * @copyright  InfinitySoft 2010,2011,2012
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Avisota
 */
class PageAvisotaNewsletter extends Frontend
{
	/**
	 * @var AvisotaNewsletterContent
	 */
	protected $Content;

	/**
	 * Generate a newsletter
	 *
	 * @param object
	 */
	public function generate(Database_Result $pageResultSet)
	{
		// Define the static URL constants
		define('TL_FILES_URL', ($pageResultSet->staticFiles != '' && !$GLOBALS['TL_CONFIG']['debugMode'])
			? $pageResultSet->staticFiles . TL_PATH . '/' : '');
		define('TL_SCRIPT_URL', ($pageResultSet->staticSystem != '' && !$GLOBALS['TL_CONFIG']['debugMode'])
			? $pageResultSet->staticSystem . TL_PATH . '/' : '');
		define('TL_PLUGINS_URL', ($pageResultSet->staticPlugins != '' && !$GLOBALS['TL_CONFIG']['debugMode'])
			? $pageResultSet->staticPlugins . TL_PATH . '/' : '');

		$this->import('AvisotaNewsletterContent', 'Content');

		// force all URLs absolute
		$GLOBALS['TL_CONFIG']['forceAbsoluteDomainLink'] = true;

		$newsletterId = $this->Input->get('item') ? $this->Input->get('item') : $this->Input->get('items');
		$newsletterContent = $this->Content->generateOnlineNewsletter($newsletterId);

		if ($newsletterContent) {
			header('Content-Type: text/html; charset=utf-8');
			echo $newsletterContent;
			exit;
		}

		$this->redirect(
			$this->generateFrontendUrl(
				$this
					->getPageDetails($pageResultSet->jumpBack ? $pageResultSet->jumpBack : $pageResultSet->pid)
					->row()
			)
		);
	}
}
