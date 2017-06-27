<?php
/**
 * flexiurl.php - Main plugin file
 *
 * @version     1.0.0
 * @package     plg_system_flexiurl
 * @author      Lyquix
 * @copyright   Copyright (C) 2017 Lyquix
 * @license     GNU General Public License version 2 or later
 * @link        https://github.com/Lyquix/plg_system_flexiurl
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

class plgContentFlexiurl extends JPlugin { 
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context === 'com_finder.indexer')
		{
			return true;
		}

		require_once (JPATH_SITE.DS.'components'.DS.'com_flexicontent'.DS.'helpers'.DS.'route.php');

		// Expression to search for URLs in href and src attributes
		$regex = '/(href|src)="([\S]+)"/i';

		// Find all URLs
		preg_match_all($regex, $article->text, $matches);

		// No matches, skip this
		if ($matches)
		{
			foreach($matches[2] as $match)
			{
				
				$parsed = parse_url($match);
				if(array_key_exists('query', $parsed))
				{
					parse_str(html_entity_decode($parsed['query']), $query);
					if(array_key_exists('option', $query) && $query['option'] == 'com_content')
					{
						if(array_key_exists('view', $query))
						{
							if($query['view'] == 'article') $article -> text = str_replace($match, JRoute::_(FlexicontentHelperRoute::getItemRoute($query['id'], $query['catid'])), $article -> text);
							if($query['view'] == 'category') $article -> text = str_replace($match, JRoute::_(FlexicontentHelperRoute::getCategoryRoute($query['id'])), $article -> text);
						}
					}
				}
				
			}
		}
	}
}