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
 * Class AvisotaInsertTag
 *
 * InsertTag hook class.
 *
 * @copyright  InfinitySoft 2010,2011,2012
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Avisota
 */
class AvisotaInsertTag extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->import('Database');
		$this->import('DomainLink');
		$this->import('AvisotaBase', 'Base');
		$this->import('AvisotaStatic', 'Static');
	}


	public function hookReplaceNewsletterInsertTags($tagName)
	{
		$tagParts = explode('::', $tagName);
		switch ($tagParts[0]) {
			case 'recipient':
				$currentRecipient = $this->Static->getRecipient();

				if ($currentRecipient) {
					switch ($tagParts[1]) {
						default:
							if ($currentRecipient && isset($currentRecipient[$tagParts[1]])) {
								return $currentRecipient[$tagParts[1]];
							}
					}
				}
				return '';

			case 'newsletter':
				$currentRecipient = $this->Static->getRecipient();
				$category         = $this->Static->getCategory();
				$newsletter       = $this->Static->getNewsletter();

				if ($currentRecipient && $category && $newsletter) {
					switch ($tagParts[1]) {
						case 'href':
							$page = $this->Base->getViewOnlinePage($category, $currentRecipient);
							if ($page) {
								return $this->Base->extendURL(
									$this->generateFrontendUrl(
										$page->row(),
										'/item/' . ($newsletter->alias ? $newsletter->alias : $newsletter->id)
									),
									$page
								);
							}
							break;

						case 'unsubscribe':
						case 'unsubscribe_url':
							$alias = false;
							if ($currentRecipient['source'] == 'list') {
								$recipientList = $this->Database
									->prepare("SELECT * FROM tl_avisota_mailing_list WHERE id=?")
									->execute($currentRecipient['sourceID']);
								if (!$recipientList->next()) {
									return '';
								}
								$alias = $recipientList->alias;
								$page  = $this->getPageDetails($recipientList->subscriptionPage);
							}
							else if ($currentRecipient['source'] == 'mgroup') {
								if ($category->subscriptionPage > 0) {
									$page = $this->getPageDetails($category->subscriptionPage);
								}
							}

							if ($page) {
								$url = $this->Base->extendURL(
									$this->generateFrontendUrl($page->row()) . '?' . ($currentRecipient['email']
										? 'email=' . $currentRecipient['email'] : '') . ($alias
										? '&unsubscribetoken=' . $alias : '')
								);
							}
							else {
								$url = $this->Base->extendURL(
									'?' . ($currentRecipient['email'] ? 'email=' . $currentRecipient['email']
										: '') . ($alias ? '&unsubscribetoken=' . $alias : '')
								);
							}

							if ($tagParts[1] == 'unsubscribe_url') {
								return $url;
							}

							switch ($tagParts[2]) {
								case 'html':
									return sprintf(
										'<a href="%s">%s</a>',
										$url,
										$GLOBALS['TL_LANG']['tl_avisota_newsletter']['unsubscribe']
									);

								case 'plain':
									return sprintf(
										"%s\n[%s]",
										$GLOBALS['TL_LANG']['tl_avisota_newsletter']['unsubscribe'],
										$url
									);
							}
							break;
					}
				}
				return '';

			case 'newsletter_latest_link':
			case 'newsletter_latest_url':
				if (strlen($tagParts[1])) {
					$id = "'" . implode("','", trimsplit(',', $tagParts[1])) . "'";
					$newsletter = $this->Database
						->prepare(
						"
						SELECT
							n.*,
							c.`viewOnlinePage`,
							c.`subscriptionPage`
						FROM
							`tl_avisota_newsletter` n
						INNER JOIN
							`tl_avisota_newsletter_category` c
						ON
							c.`id`=n.`pid`
						WHERE
							(	c.`id` IN ($id)
							OR	c.`alias` IN ($id))
							AND n.`sendOn`!=''
						ORDER BY
							n.`sendOn` DESC"
					)
						->limit(1)
						->execute();
					if ($newsletter->next()) {
						if (strlen($tagParts[2])) {
							$page = $this->Database
								->prepare(
								"
								SELECT
									*
								FROM
									`tl_page`
								WHERE
										`id`=?
									OR	`alias`=?"
							)
								->execute($tagParts[2], $tagParts[2]);
							if (!$page->next()) {
								$page = false;
							}
						}
						else {
							$page = $this->Base->getViewOnlinePage($newsletter, false);
						}
						if ($page) {
							$url = $this->Base->extendURL(
								$this->generateFrontendUrl(
									$page->row(),
									'/item/' . ($newsletter->alias ? $newsletter->alias : $newsletter->id)
								),
								$page
							);
							if ($tagParts[0] == 'newsletter_latest_link') {
								$this->loadLanguageFile('avisota');
								return sprintf($GLOBALS['TL_LANG']['avisota']['latest_link'], specialchars($url));
							}
							else {
								return $url;
							}
						}
					}
				}
				return '';
		}
		return false;
	}
}
