<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2012-2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_tz_portfolio/helpers/route.php';

jimport('joomla.application.component.model');

JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_tz_portfolio/models', 'TZ_PortfolioModel');

abstract class modTZ_LividNewsHelper{
    function getList(&$params){
        // Get the dbo
		$db = JFactory::getDbo();

		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('Articles', 'TZ_PortfolioModel', array('ignore_request' => true));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
        if($params->get('count', null)){
            $model->setState('list.start', 0);
            $model->setState('list.limit', (int) $params->get('count', null));
        }
		$model->setState('filter.published', 1);

		// Access filter
		$access = !JComponentHelper::getParams('com_tz_portfolio')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));

		// User filter
		$userId = JFactory::getUser()->get('id');
		switch ($params->get('user_id','0'))
		{
			case 'by_me':
				$model->setState('filter.author_id', (int) $userId);
				break;
			case 'not_me':
				$model->setState('filter.author_id', $userId);
				$model->setState('filter.author_id.include', false);
				break;

			case '0':
				break;

			default:
				$model->setState('filter.author_id', (int) $params->get('user_id','0'));
				break;
		}

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

		//  Featured switch
		switch ($params->get('show_featured'))
		{
			case '1':
				$model->setState('filter.featured', 'only');
				break;
			case '0':
				$model->setState('filter.featured', 'hide');
				break;
			default:
				$model->setState('filter.featured', 'show');
				break;
		}

		// Set ordering
		$order_map = array(
			'm_dsc' => 'a.modified DESC, a.created',
			'mc_dsc' => 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END',
			'c_dsc' => 'a.created',
			'p_dsc' => 'a.publish_up',
		);
		$ordering = JArrayHelper::getValue($order_map, $params->get('ordering','c_dsc'), 'a.publish_up');
		$dir = 'DESC';

		$model->setState('list.ordering', $ordering);
		$model->setState('list.direction', $dir);

		if($items = $model->getItems()){
            $model2 = JModelLegacy::getInstance('Media','TZ_PortfolioModel',array('ignore_request' => true));

            //Class Tags
            $model3 = JModelLegacy::getInstance('Tag','TZ_PortfolioModel',array('ignore_request' => true));
            foreach($items  as $item){
                $item -> tzImages   = null;

                if($media  = $model2 -> getMedia($item -> id)){
                    if(count($media)>0){
                        $_media = null;
                        foreach($media as $row){
                            if($row -> type == 'image' || $row -> type == 'video'){
                                if($row -> thumb || $row -> images){
                                    if($row -> images){
                                        $row -> imageFull   = str_replace('.'.JFile::getExt($row -> images),
                                                                      '_'.$params -> get('full_image_size','XL')
                                                                      .'.'.JFile::getExt($row -> images),$row -> images);
                                        $row -> images  = str_replace('.'.JFile::getExt($row -> images),
                                                                      '_'.$params -> get('thumb_image_size','M')
                                                                      .'.'.JFile::getExt($row -> images),$row -> images);
                                    }

                                    if($row -> thumb){
                                        $row -> thumb  = str_replace('.'.JFile::getExt($row -> thumb),
                                                                      '_'.$params -> get('thumb_image_size','M')
                                                                      .'.'.JFile::getExt($row -> thumb),$row -> thumb);
                                    }
                                    if($row -> from != 'vimeo' && $row -> from != 'default'){
                                        $_media[]   = $row;
                                    }
                                }
                            }
                            if($_media){
                                $item -> tzImages   = $_media;
                            }
                        }

                    }
                }

                $item -> tags   = null;
                //Get Tags
                if($model3){
                    $model3 -> setState('article.id',$item -> id);
                    if($tags = $model3 -> getTag()){
                        $_tags  = null;
                        foreach($tags as $tag){
                            $_tags[]    = trim($tag -> name);
                        }
                        if($order = $params -> get('tags_order',null)){
                            if($order == 'n_asc'){
                                sort($_tags);
                            }
                            elseif($order == 'n_desc'){
                                rsort($_tags);
                            }
                        }
                        $item -> tags   = $_tags;
                    }
                }
            }
        }


        return $items;
    }
}