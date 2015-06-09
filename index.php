<?php

/**
 * Plugin:   bxSlider (PHP-Struktur basiert auf coinSlider)
 * @author:  BPGS (awardfan@bpgs.de)
 * @version: v1.0.2015-06-05
 * @license: GPL
 * Plugin created by BPGS
 * www.bpgs.de
 * Anmerkungen:
 ** Der Einbau
 *** Dateien einbinden
 <!-- jQuery library (served from Google) -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<!-- bxSlider Javascript file -->
<script src="/js/jquery.bxslider.min.js"></script>
<!-- bxSlider CSS file -->
<link href="/lib/jquery.bxslider.css" rel="stylesheet" />
*** HTML-Code
 <ul class="bxslider">
  <li><img src="/images/pic1.jpg" /></li>
  <li><img src="/images/pic2.jpg" /></li>
  <li><img src="/images/pic3.jpg" /></li>
  <li><img src="/images/pic4.jpg" /></li>
</ul>
*** Standard-Aufruf
$(document).ready(function(){
  $('.bxslider').bxSlider();
});
*** Parameter
Der erste Werte ist der Standardwert
		// GENERAL
		mode: 'horizontal','vertical', 'fade'
		slideSelector: '',
		infiniteLoop: true,false
		hideControlOnEnd: false,true
		speed: 500,
		easing: null,
		slideMargin: 0,
		startSlide: 0,
		randomStart: false,true
		captions: false,true
		ticker: false,true
		tickerHover: false,true
		adaptiveHeight: false,true
		adaptiveHeightSpeed: 500,
		video: false,true
		useCSS: true,false
		preloadImages: 'visible',
		responsive: true,false
		slideZIndex: 50,
		wrapperClass: 'bx-wrapper',

		// TOUCH
		touchEnabled: true,false
		swipeThreshold: 50,
		oneToOneTouch: true,false
		preventDefaultSwipeX: true,false
		preventDefaultSwipeY: false,true

		// PAGER
		pager: true,,false
		pagerType: 'full',
		pagerShortSeparator: ' / ',
		pagerSelector: null,
		buildPager: null,
		pagerCustom: null,

		// CONTROLS
		controls: true,false
		nextText: 'Next',
		prevText: 'Prev',
		nextSelector: null,
		prevSelector: null,
		autoControls: false,
		startText: 'Start',
		stopText: 'Stop',
		autoControlsCombine: false,
		autoControlsSelector: null,

		// AUTO
		auto: false,true
		pause: 4000,
		autoStart: true,false
		autoDirection: 'next',
		autoHover: false,true
		autoDelay: 0,
		autoSlideForOnePage: false,

		// CAROUSEL
		minSlides: 1,
		maxSlides: 1,
		moveSlides: 0,
		slideWidth: 0,

		// CALLBACKS
		onSliderLoad: function() {},
		onSlideBefore: function() {},
		onSlideAfter: function() {},
		onSlideNext: function() {},
		onSlidePrev: function() {},
		onSliderResize: function() {}
 1) wie beim CoinSlider wird die moziloCMS-Standard-Galerie verwendet
**/

if(!defined('IS_CMS')) die();

class bxSlider extends Plugin {

	public $admin_lang;
	private $cms_lang;
	var $GalleryClass;

	function getContent($value) {

		global $CMS_CONF;
		global $syntax;
		global $specialchars;

		$this->cms_lang = new Language(PLUGIN_DIR_REL.'bxSlider/sprachen/cms_language_'.$CMS_CONF->get('cmslanguage').'.txt');

		// get params
		$values = explode('|', $value);

		// id for current bxslider = existing gallery name
		// alte Variante
		// $param_id = rawurlencode(trim($values[0]));
		// neue Variante
		$param_id = trim($values[0]);
		$param_id = trim($specialchars->replacespecialchars($specialchars->getHtmlEntityDecode($param_id),false));
		$params = array(
			// mode Type of transition between slides
			// mode: 'fade', default: 'horizontal' options: 'horizontal', 'vertical', 'fade'
			'mode' => trim($values[1]),
			// captions Include image captions. Captions are derived from the image's title attribute
			// captions default: false
			'captions' => trim($values[2]),
			// speed Slide transition duration (in ms)
			// speed default: 500
			'speed' => trim($values[3]),
			// auto Slides will automatically transition
			// auto default: false
			'auto' => trim($values[4]),
			// autoControls If true, "Start" / "Stop" controls will be added
			// autoControls default: false
			'autoControls' => trim($values[5]),
			// pause The amount of time (in ms) between each auto transition
			// pause default: 4000
			'pause' => trim($values[6]),
			// useCSS default: true, nutzt dann nur CSS-Effekte, wenn false dann wird jquery.easing.1.3.js benötigt, siehe easing
			'useCSS' => trim($values[7]),
			// easing Effekt, useCSS ist Standard true, daher nur CSS-Effekte
            // default: null options: if using CSS: 'linear', 'ease', 'ease-in', 'ease-out', 'ease-in-out', 'cubic-bezier(n,n,n,n)'.
            // Effekt mit useCSS=false siehe
			'easing' => trim($values[8]),
			// effect
			'effect' => '"' . trim($values[9]) . '"',
			// show navigation
			'navigation' => trim($values[10]),
			// show links
			'links' => trim($values[11]),
			// hoverpause
			'hoverPause' => trim($values[12])
		);
//echo $params['pause']."<br>";
//echo $params['easing']."<br>";
		// initialize gallery
		include_once(BASE_DIR.'cms/GalleryClass.php');
		$this->GalleryClass = new GalleryClass();
		$this->GalleryClass->initial_Galleries(false, false, false, true);

		// if gallery exists read pictures, otherwise warn
		if(in_array($param_id,$this->GalleryClass->get_GalleriesArray()))
			$pictures = $this->GalleryClass->get_GalleryImagesArray($param_id);
		else
			return $this->cms_lang->getLanguageValue("error_nogallery", $param_id);
		if(empty($pictures))
			return $this->cms_lang->getLanguageValue("error_nopictures", $param_id);

		// initialize return content
		$content = '';

		// jquery einfuegen
		$syntax->insert_jquery_in_head('jquery');
		$content .= '<script language="JavaScript" src="'.URL_BASE.PLUGIN_DIR_NAME.'/bxSlider/js/jquery.bxslider.min.js"></script>';
        $content .= '<script language="JavaScript" src="'.URL_BASE.PLUGIN_DIR_NAME.'/bxSlider/js/plugins/jquery.easing.1.3.js"></script>';
		// html for container; wichtig param auf params geaendert
		$content .= '<ul id="'.str_replace(' ','_',rawurldecode($param_id)).'" class="bxslider"><!-- id aus Galery-Name -->';
		foreach($pictures as $picture) {
			$content .= '<li>
							<img title="'.$this->GalleryClass->get_ImageDescription($param_id, $picture, false).'" alt="'.$this->GalleryClass->get_ImageDescription($param_id, $picture, false).'" src="'.$this->GalleryClass->get_ImageSrc($param_id, $picture, false).'" />
						</li>';
		}
		$content .= '</ul><!-- Ende id aus Galery-Name -->';

		if ($params['mode']==''):{
			$params['mode']='horizontal';
			}
        endif;
		$mode="mode:'".$params['mode']."', ";

		if ($params['captions']==''):{
			$params['captions']='false';
			}
        endif;
		$captions="captions:".$params['captions'].", ";

		if ($params['speed']==''):{
			$params['speed']='500';
			}
        endif;
		$speed="speed:".$params['speed'].", ";

		if ($params['auto']==''):{
			$params['auto']='false';
			}
        endif;
		$auto="auto:".$params['auto'].", ";

		if ($params['autoControls']==''):{
			$params['autoControls']='false';
			}
        endif;
		$autoControls="autoControls:".$params['autoControls'].", ";

        if ($params['pause']==''):{
			$params['pause']='4000';
			}
        endif;
		$pause="pause:".$params['pause'].", ";

		if ($params['useCSS']==''):{
			$params['useCSS']='false';
			}
        endif;
        $useCSS="useCSS:".$params['useCSS'].", ";

		if ($params['easing']==''){
			// $params['easing']='';
            $easing="easing:'', ";
			} else {
		$easing="easing:'".$params['easing']."', ";
            }
//echo $pause."<br>";
//echo $easing."<br>";

		// call bxslider
		// $('.bxslider').bxSlider({mode: 'fade',captions: true});
		$content .= '<!-- bxSlider Start -->'
		// alte Variante
		// .'<script type="text/javascript">$(document).ready(function() {$(\'.bxslider\').bxSlider({'
		.'<script type="text/javascript">'
		.'$(document).ready(function(){$(\'#'
		.str_replace(' ','_',rawurldecode($param_id))
		.'\').bxSlider({'
		.$mode
		.$captions
		.$speed
		.$auto
		.$autoControls
		.$pause
        .$useCSS
        .$easing
		.'});';

		// remove last commata
		$content = substr($content, 0, -1);
		// alt war $content .= '});});</script>';
		$content .= '});</script>';

		// return bxSlider
		return $content;

	} // function getContent


	function getConfig() {

		$config = array();

		// prev button
		$config['prev']  = array(
			'type' => 'text',
			'description' => $this->admin_lang->getLanguageValue('config_prev'),
			'maxlength' => '100',
			'size' => '4'
		);

		// next button
		$config['next']  = array(
			'type' => 'text',
			'description' => $this->admin_lang->getLanguageValue('config_next'),
			'maxlength' => '100',
			'size' => '4'
		);

		return $config;

	} // function getConfig


	function getInfo() {
		global $ADMIN_CONF;

		$this->admin_lang = new Language(PLUGIN_DIR_REL."bxSlider/sprachen/admin_language_".$ADMIN_CONF->get("language").".txt");

		$info = array(
			// Plugin-Name + Version
			'<b>bxSlider</b> v1.0.2015-06-05',
			// moziloCMS-Version
			'2.0',
			// Kurzbeschreibung nur <span> und <br /> sind erlaubt
			$this->admin_lang->getLanguageValue('description'),
			// Name des Autors
			'BPGS',
			// Docu-URL
			'http://bpgs.de',
			// Platzhalter für die Selectbox in der Editieransicht
			array(
				'{bxSlider|galeriename|options|captions|speed|auto|autoControls|pause|useCSS|easing|effect|navigation|links|hoverPause}' => $this->admin_lang->getLanguageValue('placeholder'),
				'{bxSlider|galeriename||||||||||||}' => $this->admin_lang->getLanguageValue('placeholder')
			)
		);
		// return plugin information
		return $info;

	} // function getInfo

}

?>
