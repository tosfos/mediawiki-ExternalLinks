<?php
/**
 * Initialization for the ExternalLinks extension.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'External Links',
	'version' => '1.23',
	'author' => array( 'Subfader', '[http://roman-allenstein.de Roman Allenstein]' ),
	'url' => 'http://www.mediawiki.org/wiki/Extension:ExternalLinks',
	'descriptionmsg' => 'el-desc',
);
$wgExtensionMessagesFiles['ExternalLinks'] = __DIR__ . '/ExternalLinks.i18n.php';
$wgExtensionMessagesFiles['ExternalLinksAlias'] = __DIR__ . '/SpecialExternalLinks.alias.php';
$wgAutoloadClasses['ExternalLinks'] = __DIR__ . '/SpecialExternalLinks.php';
$wgSpecialPages['ExternalLinks'] = 'ExternalLinks';

/**
 * Output the toolbox link
 */
$wgHooks['SkinTemplateBuildNavUrlsNav_urlsAfterPermalink'][] = 'efExternalLinksNavigation';
$wgHooks['SkinTemplateToolboxEnd'][] = 'efExternalLinksToolbox';

function efExternalLinksNavigation( SkinTemplate &$skintemplate, &$nav_urls, &$oldid, &$revid ) {
	global $wgELtoolboxLink, $wgELuserRight;
	$title = $skintemplate->getRelevantTitle();
	if ( $wgELtoolboxLink && $skintemplate->getUser()->isAllowed( $wgELuserRight ) && $revid !== 0
		//$skintemplate->getTitle() buggy in MW 1.15 and below
		#&& $skintemplate->getTitle()->isContentPage()
		#&& $skintemplate->getTitle()->exists()
		&& $title->exists()
	) {
		$nav_urls['ELtoolboxLink'] = array(
			'text' => $skintemplate->msg( 'el-toolboxLink' ),
			//$skintemplate->makeSpecialUrl() doesn't respect beautiful URLs, problem on submitting again
			'href' => $skintemplate->makeSpecialUrl( "ExternalLinks",
				"do=filter&checkResponse=on&storeSession=on&pageID=" . $title->getArticleID() )
		);
	}
	return true;
}

function efExternalLinksToolbox( &$monobook ) {
	if ( isset( $monobook->data['nav_urls']['ELtoolboxLink'] ) )
			if ( $monobook->data['nav_urls']['ELtoolboxLink']['href'] == '' ) {
			?><li id="t-iscontributors"><?php echo $monobook->msg( 'el-toolboxLink' ); ?></li><?php
		} else {
			?><li id="t-contributors"><?php
				?><a href="<?php echo htmlspecialchars(
				$monobook->data['nav_urls']['ELtoolboxLink']['href'] )
				?>"><?php
			echo $monobook->msg( 'el-toolboxLink' );
					?></a><?php
					?></li><?php
		}
	return true;
}
