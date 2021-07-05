<?php


if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
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
 * @copyright  Frank Thonak www.thomkit.de
 * @author     Frank Thonak
 * @package    last_change
 * @license    GPL
 * @filesource
 */



/**
 * Class ModuleModify
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Frank Thonak www.thomkit.de
 * @author     Frank Thonak
 * @package    last_change
 */


class lastChange extends System
{

	public $tagArray = array('last_change_page','cache_last_change_page',
				'last_change_article','cache_last_change_article',
				'last_change_ce','cache_last_change_ce',
				'last_change_news','cache_last_change_news',
				'last_change_events','cache_last_change_events',
				'last_change_faqs','cache_last_change_faqs',
				);


	public function getChange($tagStr)
	{
		$tagArr = explode('::',$tagStr);
		if(is_numeric($tagArr[1]))
		{
			switch ($tagArr[0])
			{
			    case 'last_change_page' :
			    case 'cache_last_change_page' :
			    		$retValue = $this->doPage($tagArr);
			    		break;

			    case 'last_change_article' :
			    case 'cache_last_change_article' :
			    		$retValue = $this->doArticle($tagArr);
			    		break;

			    case 'last_change_ce' :
			    case 'cache_last_change_ce' :
			    		$retValue = $this->doCe($tagArr);
			    		break;

			    case 'last_change_news' :
			    case 'cache_last_change_news' :
			    		$retValue = $this->doNews($tagArr);
			    		break;

			    case 'last_change_events' :
			    case 'cache_last_change_events' :
			    		$retValue = $this->doEvents($tagArr);
			    		break;

			    case 'last_change_faqs' :
			    case 'cache_last_change_faqs' :
			    		$retValue = $this->doFaqs($tagArr);
			    		break;
                            default:
                                        return false;
                                        break;  
			}
			return $this->getFormatted($tagArr,$retValue);
		}
		return false;
	}

	public function doPage($tagArr)
	{
		$this->import('Database');
		$retValue = false;
	// Seite
		$sql = $this->Database->prepare("SELECT tstamp FROM tl_page WHERE published=? AND id=?")
								->execute('1',$tagArr[1]);
		if($sql)
		{
			$retValue = $sql->tstamp;
			if(in_array('all',$tagArr))
			{
			// Artikel
				$sql = $this->Database->prepare("SELECT max(tstamp) AS tstamp FROM tl_article WHERE published=? AND tl_article.pid=?")
								->execute('1',$tagArr[1]);
				if($sql)
				{
					if($retValue < $sql->tstamp) $retValue = $sql->tstamp;
			// Contentelemente
					$sql = $this->Database->prepare("SELECT max(tl_content.tstamp) AS tstamp FROM tl_article, tl_content WHERE tl_article.id = tl_content.pid AND tl_content.ptable = 'tl_article' AND tl_content.invisible<>? AND tl_article.pid=?")
									->execute('1',$tagArr[1]);
					if($sql)
					{
						if($retValue < $sql->tstamp) $retValue = $sql->tstamp;
			// News
						$sql = $this->Database->prepare("SELECT tl_module.news_archives AS ids FROM tl_article, tl_content, tl_module WHERE tl_article.id = tl_content.pid AND tl_content.ptable = 'tl_article' AND tl_module.type IN ('newslist','newsreader','newsarchive','newsmenu') AND tl_content.module = tl_module.id AND tl_content.invisible<>? AND tl_article.pid=?")
									->execute('1',$tagArr[1]);
						$stamp = $this->getNewsStamp($this->getIdsFromDb($sql));
						if($stamp)
						{
							if($retValue < $stamp) $retValue = $stamp;
						}
			// Events
						$sql = $this->Database->prepare("SELECT tl_module.cal_calendar AS ids FROM tl_article, tl_content, tl_module WHERE tl_article.id = tl_content.pid AND tl_content.ptable = 'tl_article' AND tl_module.type IN ('calendar','eventreader','eventlist','eventmenu') AND tl_content.module = tl_module.id AND tl_content.invisible<>? AND tl_article.pid=?")
									->execute('1',$tagArr[1]);

						$stamp = $this->getEventsStamp($this->getIdsFromDb($sql));
						if($stamp)
						{
							if($retValue < $stamp) $retValue = $stamp;
						}
			// FAQs
						$sql = $this->Database->prepare("SELECT tl_module.faq_categories AS ids FROM tl_article, tl_content, tl_module WHERE tl_article.id = tl_content.pid AND tl_content.ptable = 'tl_article' AND tl_module.type IN ('faqlist','faqreader','faqpage') AND tl_content.module = tl_module.id AND tl_content.invisible<>? AND tl_article.pid=?")
									->execute('1',$tagArr[1]);
						$stamp = $this->getFaqsStamp($this->getIdsFromDb($sql));
						if($stamp)
						{
							if($retValue < $stamp) $retValue = $stamp;
						}
					}
				}
			}
		}
		return $retValue;
	}

	public function doArticle($tagArr)
	{
		$this->import('Database');
		$retValue = false;
	// Artikel
		$sql = $this->Database->prepare("SELECT tstamp FROM tl_article WHERE published=? AND id=?")
								->execute('1',$tagArr[1]);
		if($sql)
		{
			$retValue = $sql->tstamp;
			if(in_array('all',$tagArr))
			{
			// Contentelemente
				$sql = $this->Database->prepare("SELECT max(tl_content.tstamp) AS tstamp FROM tl_article, tl_content WHERE tl_article.id = tl_content.pid AND tl_content.ptable = 'tl_article' AND tl_content.invisible<>? AND tl_article.id=?")
								->execute('1',$tagArr[1]);
				if($sql)
				{
					if($retValue < $sql->tstamp) $retValue = $sql->tstamp;
			// News
					$sql = $this->Database->prepare("SELECT tl_module.news_archives AS ids FROM tl_article, tl_content, tl_module WHERE tl_article.id = tl_content.pid AND tl_content.ptable = 'tl_article' AND tl_module.type IN ('newslist','newsreader','newsarchive','newsmenu') AND tl_content.module = tl_module.id AND tl_content.invisible<>? AND tl_article.id=?")
								->execute('1',$tagArr[1]);
					$stamp = $this->getNewsStamp($this->getIdsFromDb($sql));
					if($stamp)
					{
						if($retValue < $stamp) $retValue = $stamp;
					}
			// Events
					$sql = $this->Database->prepare("SELECT tl_module.cal_calendar AS ids FROM tl_article, tl_content, tl_module WHERE tl_article.id = tl_content.pid AND tl_content.ptable = 'tl_article' AND tl_module.type IN ('calendar','eventreader','eventlist','eventmenu') AND tl_content.module = tl_module.id AND tl_content.invisible<>? AND tl_article.id=?")
								->execute('1',$tagArr[1]);

					$stamp = $this->getEventsStamp($this->getIdsFromDb($sql));
					if($stamp)
					{
						if($retValue < $stamp) $retValue = $stamp;
					}
			// FAQs
					$sql = $this->Database->prepare("SELECT tl_module.faq_categories AS ids FROM tl_article, tl_content, tl_module WHERE tl_article.id = tl_content.pid AND tl_content.ptable = 'tl_article' AND tl_module.type IN ('faqlist','faqreader','faqpage') AND tl_content.module = tl_module.id AND tl_content.invisible<>? AND tl_article.id=?")
								->execute('1',$tagArr[1]);
					$stamp = $this->getFaqsStamp($this->getIdsFromDb($sql));
					if($stamp)
					{
						if($retValue < $stamp) $retValue = $stamp;
					}
				}
			}
		}
		return $retValue;
	}

	public function doCe($tagArr)
	{
		$this->import('Database');
		$retValue = false;
	// Contentelemente
		$sql = $this->Database->prepare("SELECT tstamp FROM tl_content WHERE invisible<>? AND id=?")
								->execute('1',$tagArr[1]);
		if($sql)
		{
			$retValue = $sql->tstamp;
			if(in_array('all',$tagArr))
			{
			// News
				$sql = $this->Database->prepare("SELECT tl_module.news_archives AS ids FROM tl_content, tl_module WHERE tl_content.ptable = 'tl_article' AND tl_module.type IN ('newslist','newsreader','newsarchive','newsmenu') AND tl_content.module = tl_module.id AND tl_content.invisible<>? AND tl_content.id=?")
							->execute('1',$tagArr[1]);
				$stamp = $this->getNewsStamp($this->getIdsFromDb($sql));
				if($stamp)
				{
					if($retValue < $stamp) $retValue = $stamp;
				}
			// Events
				$sql = $this->Database->prepare("SELECT tl_module.cal_calendar AS ids FROM tl_content, tl_module WHERE tl_content.ptable = 'tl_article' AND tl_module.type IN ('calendar','eventreader','eventlist','eventmenu') AND tl_content.module = tl_module.id AND tl_content.invisible<>? AND tl_content.id=?")
							->execute('1',$tagArr[1]);

				$stamp = $this->getEventsStamp($this->getIdsFromDb($sql));
				if($stamp)
				{
					if($retValue < $stamp) $retValue = $stamp;
				}
			// FAQs
				$sql = $this->Database->prepare("SELECT tl_module.faq_categories AS ids FROM tl_content, tl_module WHERE tl_content.ptable = 'tl_article' AND tl_module.type IN ('faqlist','faqreader','faqpage') AND tl_content.module = tl_module.id AND tl_content.invisible<>? AND tl_content.id=?")
							->execute('1',$tagArr[1]);
				$stamp = $this->getFaqsStamp($this->getIdsFromDb($sql));
				if($stamp)
				{
					if($retValue < $stamp) $retValue = $stamp;
				}
			}
		}
		return $retValue;
	}

	public function doNews($tagArr)
	{
		// News
		return $this->getNewsStamp($tagArr[1]);
	}

	public function doEvents($tagArr)
	{
		// Events
		return $this->getEventsStamp($tagArr[1]);
	}

	public function doFaqs($tagArr)
	{
		// FAQs
		return $this->getFaqsStamp($tagArr[1]);
	}

	public function getIdsFromDb($sql)
	{
		$retValue = '0';
		while($sql->next())
		{
		    $tmp = deserialize($sql->ids, true);
		    if(count($tmp) > 0) $retValue.= implode(',',$tmp);
		}
		return $retValue;
	}

	public function getNewsStamp($ids)
	{
		$this->import('Database');
		$retValue = false;
		$sql = $this->Database->prepare("SELECT max(tl_news.tstamp) AS tstamp FROM tl_news_archive, tl_news WHERE tl_news.pid = tl_news_archive.id AND tl_news.published=? AND tl_news_archive.id IN(?)")
						->execute('1',$ids);
		if($sql) $retValue = $sql->tstamp;
		return $retValue;
	}
	public function getEventsStamp($ids)
	{
		$this->import('Database');
		$retValue = false;
		$sql = $this->Database->prepare("SELECT max(tl_calendar_events.tstamp) AS tstamp FROM tl_calendar, tl_calendar_events WHERE tl_calendar_events.pid = tl_calendar.id AND tl_calendar_events.published=? AND tl_calendar.id IN(?)")
						->execute('1',$ids);
		if($sql) $retValue = $sql->tstamp;
		return $retValue;
	}
	public function getFaqsStamp($ids)
	{
		$this->import('Database');
		$retValue = false;
		$sql = $this->Database->prepare("SELECT max(tl_faq.tstamp) AS tstamp FROM tl_faq_category, tl_faq WHERE tl_faq.pid = tl_faq_category.id AND tl_faq.published=? AND tl_faq_category.id IN(?)")
						->execute('1',$ids);
		if($sql) $retValue = $sql->tstamp;
		return $retValue;
	}

	public function getFormatted($tagArr,$timeStamp)
	{
		// date, datetime, ago
		$retValue = $timeStamp;

		if(in_array('ago',$tagArr))
		{
			$tmpRetVal ='';
			$tmpDiff = time() - $timeStamp;
			$tmpD = 0;
			$tmpH = 0;
			$tmpM = 0;
		// days
			$tsH = $tmpDiff % (60*60*24);
			if($tmpDiff != $tsH) $tmpD = ($tmpDiff - $tsH) / (60*60*24);
		// hours
			$tsM = $tsH % (60*60);
			if($tsH != $tsM) $tmpH = ($tsH - $tsM) / (60*60);
		// minutes
			$tsS = $tsM % (60);
			if($tsM != $tsS) $tmpM = ($tsM - $tsS) / (60);

			if($tmpD != 0) $tmpD == 1 ? $tmpRetVal = $tmpD.' '.$GLOBALS['TL_LANG']['last_change']['day'].' ' :  $tmpRetVal = $tmpD.' '.$GLOBALS['TL_LANG']['last_change']['days'].' ';
			if($tmpRetVal != '' || $tmpH != 0) $tmpH == 1 ? $tmpRetVal .= $tmpH.' '.$GLOBALS['TL_LANG']['last_change']['hour'].' ' :  $tmpRetVal .= $tmpH.' '.$GLOBALS['TL_LANG']['last_change']['hours'].' ';
			if($tmpRetVal != '' || ($tmpM + $tmpS) != 0) $tmpM == 1 ? $tmpRetVal .= $tmpM.' '.$GLOBALS['TL_LANG']['last_change']['minute'].' ' :  $tmpRetVal .= $tmpM.' '.$GLOBALS['TL_LANG']['last_change']['minutes'].' ';

			if($tmpRetVal != '') $retValue = $tmpRetVal;
		}
		else if(in_array('datetime',$tagArr))
		{
			$retValue = date($GLOBALS['TL_LANG']['last_change']['datetimeformat'],$timeStamp);
		}
		else if(in_array('date',$tagArr))
		{
			$retValue = date($GLOBALS['TL_LANG']['last_change']['dateformat'],$timeStamp);
		}

		return $retValue;
	}
}
?>