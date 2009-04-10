<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionFunctions[] = 'wfSetupGadgets';
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'Gadgets',
	'version' => '1.1.1',
	'url' => 'http://code.creativecommons.org/viewgit?p=gadgets',
	'author' => 'Asheesh Laroia (built on top of ParserFunctions by Tim Starling)',
	'description' => 'Add tested-safe gadgets to the wiki',
	'descriptionmsg' => 'gadgets_desc',
);

$wgExtensionMessagesFiles['Gadgets'] = dirname(__FILE__) . '/Gadgets.i18n.php';
$wgHooks['LanguageGetMagic'][]       = 'wfGadgetsLanguagesGetMagic';

class ExtGadgets {
	function registerParser( &$parser ) {
		$parser->setFunctionHook( 'gadget', array(&$this, 'gadget') );

		return true;
	}

	function gadget(&$parser) {
		return array('<script>alert("hi!");</script>', noparse=>true, isHTML=>true);
	}

}

function wfSetupGadgets() {
	global $wgParser, $wgExtParserFunctions, $wgHooks;

	$wgExtParserFunctions = new ExtGadgets;

	// Check for SFH_OBJECT_ARGS capability
	if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
		$wgHooks['ParserFirstCallInit'][] = array( &$wgExtParserFunctions, 'registerParser' );
	} else {
		if ( class_exists( 'StubObject' ) && !StubObject::isRealObject( $wgParser ) ) {
			$wgParser->_unstub();
		}
		$wgExtParserFunctions->registerParser( $wgParser );
	}

}

function wfGadgetsLanguagesGetMagic( &$magicWords, $langCode ) {
	require_once( dirname( __FILE__ ) . '/ParserFunctions.i18n.magic.php' );
	foreach( efGadgetsWords( $langCode ) as $word => $trans )
		$magicWords[$word] = $trans;
	return true;
}
