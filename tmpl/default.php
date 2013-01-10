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

if($list):
    $doc    = &JFactory::getDocument();
    $doc -> addStyleSheet('modules/mod_tz_livid/css/jquery.fancybox.css');
    $doc -> addStyleSheet('modules/mod_tz_livid/css/style.css');

    $doc -> addCustomTag('<script type="text/javascript" src="http://www.youtube.com/player_api"></script>');
    $doc -> addCustomTag('<script type="text/javascript" src="modules/mod_tz_livid/js/jquery.fancybox.pack.js"></script>');
    $doc -> addCustomTag('<script type="text/javascript" src="modules/mod_tz_livid/js/jquery.masonry.min.js"></script>');
    $doc -> addCustomTag('
        <script type="text/javascript">// Fires whenever a player has finished loading
        var widthVideo = 640;
        var heightVideo = 360;
        var widthStage;
        var heightStage;
        function onPlayerReady(event) {

            event.target.playVideo();
            //resizeVideo();
        }

        // Fires when the player\'s state changes.
        function onPlayerStateChange(event) {
            // Go to the next video after the current one is finished playing
            if (event.data === 0) {
                jQuery.fancybox.next();
                //jQuery.fancybox.close(  );
            }
        }

        function resizeVideo () {
            widthStage = jQuery (window).width();
            heightStage = jQuery (window).height();

            var escVideoX = widthStage / widthVideo;
            var escVideoY = heightStage / heightVideo;

            var escalaVideo = (escVideoX > escVideoY) ? escVideoX : escVideoY;
            var widthV = widthVideo * escalaVideo;
            var heightV = heightVideo * escalaVideo;

            var posVideoY = 0;
            var posVideoX = 0;

            if (heightV > heightStage) {
                posVideoY = (heightStage - heightV) / 2;
            };

            if (widthV > widthStage) {
                posVideoX = (widthStage - widthV) / 2;
            };


            jQuery(\'.fancybox-inner\').css({ top: posVideoY, left: posVideoX, width: widthV, height: heightV });
            jQuery(\'.fancybox-wrap\').css({width: widthStage-18, height: heightStage, overflow: \'hidden\'});

        };

        // The API will call this function when the page has finished downloading the JavaScript for the player API
        function onYouTubePlayerAPIReady() {

            // Initialise the fancyBox after the DOM is loaded
            jQuery(document).ready(function() {
                jQuery(".mfancybox")
                    .attr(\'rel\', \'gallery\')
                    .fancybox({
                        nextEffect  : \'none\',
                        prevEffect  : \'none\',
                        padding     : 0,
                        margin      : 0,
                        mouseWheel	: false,
                        autoCenter  : false,
                        wrapCSS     : \'TzWrap\',
                        beforeShow  : function() {

                            // Find the iframe ID
                            var id = jQuery.fancybox.inner.find(\'iframe\').attr(\'id\');
                            

                            // Create video player object and add event listeners
                            var player = new YT.Player(id, {
                                height: \'100%\',
                                width: \'100%\',
                                playerVars: {
                                    modestbranding: 1,
                                    controls: 0,
                                    autoplay: 0,
                                    rel: 0,
                                    loop: 0,
                                    showinfo: 0,
                                    wmode: \'transparent\'
                                },
                                events: {
                                    \'onReady\': onPlayerReady,
                                    \'onStateChange\': onPlayerStateChange
                                }
                            });

                        },
                        afterLoad  : function () {
                            switch (this.type) {
                                case \'image\':
                                    jQuery.extend(this, {
                                        aspectRatio : false,
                                        type    : \'html\',
                                        width   : \'100%\',
                                        height  : \'100%\',
                                        content : \'<\div class="fancybox-image" style="background-image:url(\\\'\' + this.href + \'\\\'); background-size: cover; background-position:50% 50%;background-repeat:no-repeat;height:100%;width:100%;" /></div>\'
                                    });
                                    break;
                                case \'iframe\':
                                    jQuery.extend(this, {
                                        aspectRatio : false,
                                        width   : \'100%\',
                                        height  : \'100%\'
                                    });
                                    break;
                            }
                        }
                    });
                jQuery (window).resize (function () {
                    //resizeVideo ();
                });
            });

        }</script>
    ');
?>
    <div class="TzFullImage<?php echo $moduleclass_sfx;?>">
        <div class="TzFullImageInner">
            <?php foreach($list as $item):?>
                <?php if($media = $item -> tzImages):?>
                <div class="TzElement">
                    <div class="TzElementInner">
                        <?php if($media[0] -> type != 'video'):?>
                        <a class="mfancybox" href="<?php echo JUri::root().$media[0] -> imageFull;?>">
                            <img src="<?php echo JUri::root().$media[0] -> images;?>"/>
                        </a>
                        <?php else:?>
                            <?php
                                $url    = null;
                                if($media[0] -> from == 'youtube'):
                                    $url    = 'http://www.youtube.com/embed/'.$media[0] -> images.'?enablejsapi=1&wmode=opaque';
                                endif;
                                if($url):
                            ?>
                            <a class="mfancybox mfancybox.iframe" href="<?php echo $url;?>">
                                <img src="<?php echo JUri::root().$media[0] -> thumb;?>"/>
                            </a>
                            <?php endif;?>
                        <?php endif;?>
                        <?php if($params -> get('show_title',1) OR $params -> get('show_category',0)
                                 OR $params -> get('show_create_date',0) OR $params -> get('show_tags',1)):?>
                        <div class="TzInfo">
                            <div class="TzInfoInner">
                                <?php if($params -> get('show_title',1)):?>
                                <h3 class="TzTitle"><span><?php echo $item -> title;?></span></h3>
                                <?php endif;?>
                                <?php if($params -> get('show_create_date',0)):?>
                                <span class="TzCreated"><?php echo JText::sprintf('MOD_TZ_LIVID_CREATED_DATE_ON',
                                              JHtml::_('date', $item -> created, JText::_('DATE_FORMAT_LC2')))?>
                                </span>
                                <?php endif;?>

                                <?php if($params -> get('show_category',0)):?>
                                <span class="TzCategory">
                                    <?php echo JText::sprintf('MOD_TZ_LIVID_CATEGORY',
                                                              $item -> category_title);?>
                                </span>
                                <?php endif;?>

                                <?php  if($params -> get('show_tags',0)):?>
                                    <?php if($item -> tags):?>
                                    <?php
                                        $tags   = implode('+',$item -> tags);
                                    ?>
                                    <div class="clearfix"></div>
                                    <span class="TzTag"><?php echo JText::sprintf('MOD_TZ_LIVID_TAGS',$tags);?></span>
                                    <?php endif;?>
                                <?php endif;?>
                            </div>
                        </div>
                        <?php endif;?>
                    </div>
                </div>
                <?php endif;?>
            <?php endforeach;?>
        </div>
        <?php
            if($colWidth = $params -> get('column_width',200)):
                if(preg_match('/^[0-9]+px$/',$colWidth)):
                    $colWidth   = (int) str_replace('px','',$colWidth);
                endif;
            endif;
        ?>
        <script type="text/javascript">
            var $m_container = jQuery('.TzFullImageInner');
            function tz_resize(containerWidth){

                var containerWidth = containerWidth,
                        elem    = $m_container.find('.TzElement'),
                        columnWidth     = <?php echo $colWidth;?>,
                        curColCount     = 0,
                        maxColCount     = 0,
                        newColCount     = 0,
                        newColWidth     = 0,
                        totalRowCount   = elem.length,
                        ratio   = 360/640,
                        newRowCount     = 0,
                        newColHeight    = 0;

                curColCount = Math.floor(containerWidth / columnWidth);

                maxColCount = curColCount + 1;
                if((maxColCount - (containerWidth / columnWidth)) > ((containerWidth / columnWidth) - curColCount)){
                    newColCount     = curColCount;
                }
                else{
                    newColCount = maxColCount;
                }

                newColWidth = containerWidth;

                if(newColCount > 1){
                    newColWidth = Math.floor(containerWidth / newColCount);
                }
                newColHeight    = newColWidth * ratio;

                newRowCount = parseInt(totalRowCount / newColCount);

                elem.css({width: newColWidth,height: newColHeight});

                //Replace element image
                elem.each(function(index){
                    var _elem   = jQuery(this).find('.mfancybox');
                    var img = _elem.find('img');
                    if(img.length){
                        var imgRatio    = img.height() /img.width(),
                                bgSize   = null;
                        bgSize  = '100% auto';
                        if(imgRatio < ratio){
                            bgSize  = 'auto 100%';
                        }
                        _elem.css({background: 'url("'+img.attr('src')+'") no-repeat center center',
                            'background-size': bgSize
                        });
                        img.remove();
                    }

                    //Remove with element not enough column count
                    <?php if($params -> get('remove_item',0)):?>
                    if(index >= (newRowCount * newColCount)){
                        jQuery(this).remove();
                    }
                    <?php endif;?>
                });
                return newColWidth;
            }

            $m_container.imagesLoaded(function(){
                tz_resize($m_container.width());
                $m_container.masonry({
                    columnWidth: function( containerWidth ) {
                        return tz_resize(containerWidth);
                    },
                    itemSelector: ".TzElement",
                    isAnimated: true
                });
            });
            
        </script>
    </div>
<?php
endif;