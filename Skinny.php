<?php
/**
 * Skinny
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @ingroup Skins
 */
class SkinSkinny extends SkinTemplate {
	/** Using skinny. */
	function initPage( OutputPage $out ) {
		parent::initPage( $out );
		$this->skinname  = 'skinny';
		$this->stylename = 'skinny';
		$this->template  = 'SkinnyTemplate';
	}

	function setupSkinUserCss( OutputPage $out ) {
		global $wgHandheldStyle;

		parent::setupSkinUserCss( $out );

		# Append to the default screen common & print styles...
		$out->addStyle( 'skinny/main.css', 'screen' );
		$out->addStyle( 'skinny/font-awesome.min.css', 'screen' );
		$out->addStyle( 'skinny/fontello/css/fontello.css', 'screen' );
		
		if( $wgHandheldStyle ) {
			// Currently in testing... try 'chick/main.css'
			$out->addStyle( $wgHandheldStyle, 'handheld' );
		}

		# $out->addStyle( 'monobook/IE50Fixes.css', 'screen', 'lt IE 5.5000' );
		# $out->addStyle( 'monobook/IE55Fixes.css', 'screen', 'IE 5.5000' );
		# $out->addStyle( 'monobook/IE60Fixes.css', 'screen', 'IE 6' );
		# $out->addStyle( 'monobook/IE70Fixes.css', 'screen', 'IE 7' );

		# $out->addStyle( 'monobook/rtl.css', 'screen', '', 'rtl' );
	}
	
	/* Overriding this function so I can eliminate
	   a single space between listed categories and
	   to add an Entypo icon before the word
	   “Categories”. */
	function getCategoryLinks() {
		global $wgOut, $wgUseCategoryBrowser;
		global $wgContLang, $wgUser;

		if( count( $wgOut->mCategoryLinks ) == 0 ) {
			return '';
		}

		# Separator
		$sep = wfMsgExt( 'catseparator', array( 'parsemag', 'escapenoentities' ) );

		// Use Unicode bidi embedding override characters,
		// to make sure links don't smash each other up in ugly ways.
		$dir = $wgContLang->getDir();
		$embed = "<span dir='$dir'>";
		$pop = '</span>';

		$allCats = $wgOut->getCategoryLinks();
		$s = '';
		$colon = wfMsgExt( 'colon-separator', 'escapenoentities' );
		if ( !empty( $allCats['normal'] ) ) {
		    // This is the only line I’ve modified, removing a space between {$pop} and {$sep}. —Joey
			$t = $embed . implode( "{$pop}{$sep} {$embed}" , $allCats['normal'] ) . $pop;

			$msg = wfMsgExt( 'pagecategories', array( 'parsemag', 'escapenoentities' ), count( $allCats['normal'] ) );
			$s .= '<div id="mw-normal-catlinks"><span class="entypo-icon">&#128193;</span> ' .
				$this->link( Title::newFromText( wfMsgForContent( 'pagecategorieslink' ) ), $msg )
				. $colon . $t . '</div>';
		}

		# Hidden categories
		if ( isset( $allCats['hidden'] ) ) {
			if ( $wgUser->getBoolOption( 'showhiddencats' ) ) {
				$class ='mw-hidden-cats-user-shown';
			} elseif ( $this->mTitle->getNamespace() == NS_CATEGORY ) {
				$class = 'mw-hidden-cats-ns-shown';
			} else {
				$class = 'mw-hidden-cats-hidden';
			}
			$s .= "<div id=\"mw-hidden-catlinks\" class=\"$class\">" .
				wfMsgExt( 'hidden-categories', array( 'parsemag', 'escapenoentities' ), count( $allCats['hidden'] ) ) .
				$colon . $embed . implode( "$pop $sep $embed", $allCats['hidden'] ) . $pop .
				'</div>';
		}

		# optional 'dmoz-like' category browser. Will be shown under the list
		# of categories an article belong to
		if( $wgUseCategoryBrowser ) {
			$s .= '<br /><hr />';

			# get a big array of the parents tree
			$parenttree = $this->mTitle->getParentCategoryTree();
			# Skin object passed by reference cause it can not be
			# accessed under the method subfunction drawCategoryBrowser
			$tempout = explode( "\n", Skin::drawCategoryBrowser( $parenttree, $this ) );
			# Clean out bogus first entry and sort them
			unset( $tempout[0] );
			asort( $tempout );
			# Output one per line
			$s .= implode( "<br />\n", $tempout );
		}

		return $s;
	}
}

/**
 * @todo document
 * @ingroup Skins
 */
class SkinnyTemplate extends QuickTemplate {
	var $skin;
	/**
	 * Template filter callback for Skinny skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		global $wgRequest, $wgUser, $wgSitename;
		
		// retrieve site name
		$this->set( 'sitename', $wgSitename );
 
		$this->skin = $skin = $this->data['skin'];
		$action = $wgRequest->getText( 'action' );

		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="<?php $this->text('xhtmldefaultnamespace') ?>" <?php
	foreach($this->data['xhtmlnamespaces'] as $tag => $ns) {
		?>xmlns:<?php echo "{$tag}=\"{$ns}\" ";
	} ?>xml:lang="<?php $this->text('lang') ?>" lang="<?php $this->text('lang') ?>" dir="<?php $this->text('dir') ?>">
	<head>
    	<meta http-equiv="Content-Type" content="<?php $this->text('mimetype') ?>; charset=<?php $this->text('charset') ?>" />
		<?php /* if (strpos($_SERVER['HTTP_USER_AGENT'],"iPhone") > 0): ?>
		    <meta name="viewport" content="width=device-width,maximum-scale=1.0" />
		<?php elseif (strpos($_SERVER['HTTP_USER_AGENT'],"iPad") > 0): ?>
		    <meta name="viewport" content="width=device-width,maximum-scale=1.0" />
		<?php else: */ ?>
		    <meta name="viewport" content="width=device-width" />
		<?php /* endif; */ ?>
		<?php $this->html('headlinks') ?>
        <?php if($this->data['title'] == "Project:Home") { ?>
        <title><?php echo $this->text('sitename'); ?></title>
        <?php } else { ?> 
        <title><?php echo $this->text('pagetitle') ?></title>
        <?php } ?>
		<?php $this->html('csslinks') ?>

		<!--[if lt IE 7]><script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath') ?>/common/IEFixes.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"></script>
		<meta http-equiv="imagetoolbar" content="no" /><![endif]-->

		<?php print Skin::makeGlobalVariablesScript( $this->data ); ?>

		<script type="text/javascript" src="//use.typekit.net/exn2xvh.js"></script>
		<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath' ) ?>/common/wikibits.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"><!-- wikibits js --></script>
		<!-- Head Scripts -->
<?php $this->html('headscripts') ?>
<?php	if($this->data['jsvarurl']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('jsvarurl') ?>"><!-- site js --></script>
<?php	} ?>
<?php	if($this->data['pagecss']) { ?>
		<style type="text/css"><?php $this->html('pagecss') ?></style>
<?php	}
		if($this->data['usercss']) { ?>
		<style type="text/css"><?php $this->html('usercss') ?></style>
<?php	}
		if($this->data['userjs']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('userjs' ) ?>"></script>
<?php	}
		if($this->data['userjsprev']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>"><?php $this->html('userjsprev') ?></script>
<?php	}
		if($this->data['trackbackhtml']) print $this->data['trackbackhtml']; ?>
		<script src="/bibly-hack.js"></script>
		<link href="http://code.bib.ly/bibly.min.css" rel="stylesheet" />
		<script src="/lds-linker.js"></script>
		<script src="/jquery-1.7.2.min.js" type="text/javascript"></script>
		<!-- <script type="text/javascript">
            // When ready...
            window.addEventListener("load",function() {
                // Set a timeout...
                setTimeout(function(){
                    // Hide the address bar in MobileSafari
                    window.scrollTo(0, 1);
                }, 0);
            });
        </script> -->
        <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png?v=2" />
        <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png?v=2" />
        <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png?v=2" />
        <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png?v=2" />
        <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png?v=2" />
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png?v=2" />
        <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png?v=2" />
        <?php /* <script type="text/javascript">
            // update viewport width on orientation change
            function adapt_to_orientation() {
                // determine new screen_width
                var screen_width;
                if (window.orientation == 0 || window.orientation == 180) {
                    // portrait
                    screen_width = 'device-width';
                } else if (window.orientation == 90 || window.orientation == -90) {
                    // landscape
                    screen_width = 'device-height';
                }
                // resize meta viewport
                $('meta[name=viewport]').attr('content', 'width='+screen_width);
            }
            $(document).ready(function() {
                // bind to handler
                $('body').bind('orientationchange', adapt_to_orientation);
                
                // call now
                adapt_to_orientation();
            });
        </script> */ ?>
	</head>
<body<?php if($this->data['body_ondblclick']) { ?> ondblclick="<?php $this->text('body_ondblclick') ?>"<?php } ?>
<?php if($this->data['body_onload']) { ?> onload="<?php $this->text('body_onload') ?>"<?php } ?>
 class="mediawiki <?php $this->text('dir') ?> <?php $this->text('pageclass') ?> <?php $this->text('skinnameclass') ?>">
    <div id="fixed-background-hack"></div>
    <div id="globalWrapper">
		<div id="column-content">
	<div id="content">
		<a name="top" id="top"></a>
		<?php if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>
		<h1 id="firstHeading" class="firstHeading"><?php $this->data['displaytitle']!=""?$this->html('title'):$this->text('title') ?></h1>
		<div id="bodyContent">
			<h3 id="siteSub"><?php $this->msg('tagline') ?></h3>
			<div id="contentSub"><?php $this->html('subtitle') ?></div>
			<?php if($this->data['undelete']) { ?><div id="contentSub2"><?php     $this->html('undelete') ?></div><?php } ?>
			<?php if($this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html('newtalk')  ?></div><?php } ?>
			<?php if($this->data['showjumplinks']) { ?><div id="jump-to-nav"><?php $this->msg('jumpto') ?> <a href="#column-one"><?php $this->msg('jumptonavigation') ?></a>, <a href="#searchInput"><?php $this->msg('jumptosearch') ?></a></div><?php } ?>
			<!-- start content -->
			<?php $this->html('bodytext') ?>
			<?php if($this->data['catlinks']) { $this->html('catlinks'); } ?>
			<!-- end content -->
			<?php if($this->data['dataAfterContent']) { $this->html ('dataAfterContent'); } ?>
			<div class="visualClear"></div>
		</div>
	</div>
		</div>
		<div id="column-one">
        	<div id="p-cactions" class="portlet">
        		<h5><?php $this->msg('views') ?></h5>
        		<div class="pBody">
        			<ul>
        	<?php		foreach($this->data['content_actions'] as $key => $tab) {
        					if ($key != "watch" && $key != "unwatch" && $key != "protect" && $key != "unprotect" && $key != "delete") {
        						echo '
        						<li id="' . Sanitizer::escapeId( "ca-$key" ) . '"';
        						if( $tab['class'] ) {
        							echo ' class="'.htmlspecialchars($tab['class']).'"';
        						}
        						echo'><a href="'.htmlspecialchars($tab['href']).'"';
        						# We don't want to give the watch tab an accesskey if the
        						# page is being edited, because that conflicts with the
        						# accesskey on the watch checkbox.  We also don't want to
        						# give the edit tab an accesskey, because that's fairly su-
        						# perfluous and conflicts with an accesskey (Ctrl-E) often
        						# used for editing in Safari.
        						if( in_array( $action, array( 'edit', 'submit' ) )
        						&& in_array( $key, array( 'edit', 'watch', 'unwatch' ))) {
        							echo $skin->tooltip( "ca-$key" );
        						} else {
        							echo $skin->tooltipAndAccesskeyAttribs( "ca-$key" );
        						}
        						echo '>'.htmlspecialchars($tab['text']).'</a></li>';
        					}
        				} ?>
        			</ul>
        		</div>
        	</div>
        	<div class="portlet" id="p-personal">
        		<h5><?php $this->msg('personaltools') ?></h5>
        		<div class="pBody">
        			<ul>
        <?php 			foreach($this->data['personal_urls'] as $key => $item) {
        					if ($key != "mytalk" && $key != "preferences"  && $key != "watchlist" && $key != "mycontris" && $key != "anonuserpage" && $key != "anontalk" && $key != "logout") { ?>
        						<li id="<?php echo Sanitizer::escapeId( "pt-$key" ) ?>"<?php
        							if ($item['active']) { ?> class="active"<?php } ?>><a href="<?php
        						echo htmlspecialchars($item['href']) ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('pt-'.$key) ?><?php
        						if(!empty($item['class'])) { ?> class="<?php
        						echo htmlspecialchars($item['class']) ?>"<?php } ?>><?php
        						echo htmlspecialchars($item['text']) ?></a></li>
        <?php				}
        				} ?>
        			</ul>
        		</div>
        	</div>
        	<div class="portlet" id="p-logo">
        		<a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>" <?php
        			echo $skin->tooltipAndAccesskeyAttribs('p-logo') ?>><?php echo $this->text('sitename') ?></a>
        	</div>
        	<script type="<?php $this->text('jsmimetype') ?>"> if (window.isMSIE55) fixalpha(); </script>
        	<div id="siderail">
        <?php
        		$sidebar = $this->data['sidebar'];
        		if ( !isset( $sidebar['SEARCH'] ) ) $sidebar['SEARCH'] = true;
        		if ( !isset( $sidebar['TOOLBOX'] ) ) $sidebar['TOOLBOX'] = true;
        		if ( !isset( $sidebar['LANGUAGES'] ) ) $sidebar['LANGUAGES'] = true;
        		foreach ($sidebar as $boxName => $cont) {
        			if ( $boxName == 'SEARCH' ) {
        				$this->searchBox();
        			} elseif ( $boxName == 'TOOLBOX' ) {
        				$this->toolbox();
        			} elseif ( $boxName == 'LANGUAGES' ) {
        				$this->languageBox();
        			} else {
        				$this->customBox( $boxName, $cont );
        			}
        		}
        ?>
        	</div>
		</div><!-- end of the left (by default at least) column -->
			<div class="visualClear"></div>
			<div id="footer">
<?php
        // Define custom messages for use in footer links -- Added by Joey
        $messages['en'] = array(
            'contactme' => 'Contact',
            'contactmepage' => "{{ns:Special}}:Contact",
            'colophon' => 'Colophon',
            'colophonpage' => "{{ns:Project}}:Colophon"
        );

		// Generate additional footer links
		$footerlinks = array(
			'copyright', 'about', 'privacy', 'disclaimer'
		);
		$validFooterLinks = array();
		foreach( $footerlinks as $aLink ) {
			if( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
				$validFooterLinks[] = $aLink;
			}
		}
		if ( count( $validFooterLinks ) > 0 ) {
?>			<ul id="f-list">
<?php
			foreach( $validFooterLinks as $aLink ) {
				if( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
?>					<li id="<?php echo$aLink?>"><?php $this->html($aLink) ?></li>
<?php 			}
			}
?>
			</ul>
<?php	}
?>
		</div>
</div>
<?php $this->html('bottomscripts'); /* JS call to runBodyOnloadHook */ ?>
<?php $this->html('reporttime') ?>
<?php if ( $this->data['debug'] ): ?>
<!-- Debug output:
<?php $this->text( 'debug' ); ?>

-->
<?php endif; ?>
</body></html>

<?php
	wfRestoreWarnings();
	} // end of execute() method

	/*************************************************************************************************/
	function searchBox() {
		global $wgUseTwoButtonsSearchForm;
?>
	<div id="p-search" class="portlet">
		<h5><label for="searchInput"><?php $this->msg('search') ?></label></h5>
		<div id="searchBody" class="pBody">
			<form action="<?php $this->text('wgScript') ?>" id="searchform"><div>
				<input type='hidden' name="title" value="<?php $this->text('searchtitle') ?>"/>
				<input id="searchInput" name="search" type="text"<?php echo $this->skin->tooltipAndAccesskeyAttribs('search');
					if( isset( $this->data['search'] ) ) {
						?> value="<?php $this->text('search') ?>"<?php } ?> />
				<input type='submit' name="go" class="searchButton" id="searchGoButton"	value="<?php $this->msg('searcharticle') ?>"<?php echo $this->skin->tooltipAndAccesskeyAttribs( 'search-go' ); ?> /><?php if ($wgUseTwoButtonsSearchForm) { ?>
				<input type='submit' name="fulltext" class="searchButton" id="mw-searchButton" value="<?php $this->msg('searchbutton') ?>"<?php echo $this->skin->tooltipAndAccesskeyAttribs( 'search-fulltext' ); ?> /><?php } else { ?>

				<div><a href="<?php $this->text('searchaction') ?>" rel="search"><?php $this->msg('powersearch-legend') ?></a></div><?php } ?>

			</div></form>
		</div>
	</div>
<?php
	}

	/*************************************************************************************************/
	function toolbox() {
?>
	<div class="portlet" id="p-tb">
		<h5><?php $this->msg('toolbox') ?></h5>
		<div class="pBody">
			<ul>
<?php
		if($this->data['notspecialpage']) { ?>
				<li id="t-whatlinkshere"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['whatlinkshere']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskeyAttribs('t-whatlinkshere') ?>><?php $this->msg('whatlinkshere') ?></a></li>
<?php
			if( $this->data['nav_urls']['recentchangeslinked'] ) { ?>
				<li id="t-recentchangeslinked"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['recentchangeslinked']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskeyAttribs('t-recentchangeslinked') ?>><?php $this->msg('recentchangeslinked') ?></a></li>
<?php 		}
		}
		if(isset($this->data['nav_urls']['trackbacklink'])) { ?>
			<li id="t-trackbacklink"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['trackbacklink']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskeyAttribs('t-trackbacklink') ?>><?php $this->msg('trackbacklink') ?></a></li>
<?php 	}
		if($this->data['feeds']) { ?>
			<li id="feedlinks"><?php foreach($this->data['feeds'] as $key => $feed) {
					?><a id="<?php echo Sanitizer::escapeId( "feed-$key" ) ?>" href="<?php
					echo htmlspecialchars($feed['href']) ?>" rel="alternate" type="application/<?php echo $key ?>+xml" class="feedlink"<?php echo $this->skin->tooltipAndAccesskeyAttribs('feed-'.$key) ?>><?php echo htmlspecialchars($feed['text'])?></a>&nbsp;
					<?php } ?></li><?php
		}

		foreach( array('contributions', 'log', 'blockip', 'emailuser', 'upload', 'specialpages') as $special ) {

			if($this->data['nav_urls'][$special]) {
				?><li id="t-<?php echo $special ?>"><a href="<?php echo htmlspecialchars($this->data['nav_urls'][$special]['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskeyAttribs('t-'.$special) ?>><?php $this->msg($special) ?></a></li>
<?php		}
		}

		if(!empty($this->data['nav_urls']['print']['href'])) { ?>
				<li id="t-print"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['print']['href'])
				?>" rel="alternate"<?php echo $this->skin->tooltipAndAccesskeyAttribs('t-print') ?>><?php $this->msg('printableversion') ?></a></li><?php
		}

		if(!empty($this->data['nav_urls']['permalink']['href'])) { ?>
				<li id="t-permalink"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['permalink']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskeyAttribs('t-permalink') ?>><?php $this->msg('permalink') ?></a></li><?php
		} elseif ($this->data['nav_urls']['permalink']['href'] === '') { ?>
				<li id="t-ispermalink"<?php echo $this->skin->tooltip('t-ispermalink') ?>><?php $this->msg('permalink') ?></li><?php
		}

		wfRunHooks( 'MonoBookTemplateToolboxEnd', array( &$this ) );
		wfRunHooks( 'SkinTemplateToolboxEnd', array( &$this ) );
?>
			</ul>
		</div>
	</div>
<?php
	}

	/*************************************************************************************************/
	function languageBox() {
		if( $this->data['language_urls'] ) {
?>
	<div id="p-lang" class="portlet">
		<h5><?php $this->msg('otherlanguages') ?></h5>
		<div class="pBody">
			<ul>
<?php		foreach($this->data['language_urls'] as $langlink) { ?>
				<li class="<?php echo htmlspecialchars($langlink['class'])?>"><?php
				?><a href="<?php echo htmlspecialchars($langlink['href']) ?>"><?php echo $langlink['text'] ?></a></li>
<?php		} ?>
			</ul>
		</div>
	</div>
<?php
		}
	}

	/*************************************************************************************************/
	function customBox( $bar, $cont ) {
?>
	<div class='generated-sidebar portlet' id='<?php echo Sanitizer::escapeId( "p-$bar" ) ?>'<?php echo $this->skin->tooltip('p-'.$bar) ?>>
		<h5><?php $out = wfMsg( $bar ); if (wfEmptyMsg($bar, $out)) echo $bar; else echo $out; ?></h5>
		<div class='pBody'>
<?php   if ( is_array( $cont ) ) { ?>
			<ul>
<?php 			foreach($cont as $key => $val) { ?>
				<li id="<?php echo Sanitizer::escapeId($val['id']) ?>"<?php
					if ( $val['active'] ) { ?> class="active" <?php }
				?>><a href="<?php echo htmlspecialchars($val['href']) ?>"<?php echo $this->skin->tooltipAndAccesskeyAttribs($val['id']) ?>><?php echo htmlspecialchars($val['text']) ?></a></li>
<?php			} ?>
			</ul>
<?php   } else {
			# allow raw HTML block to be defined by extensions
			print $cont;
		}
?>
		</div>
	</div>
<?php
	}

} // end of class


