<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionFunctions[] = 'wfSetupParserFunctions';
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'Gadgets',
	'version' => '1.1.1',
	'url' => 'http://code.creativecommons.org/viewgit?p=gadgets',
	'author' => 'Asheesh Laroia (built on top of ParserFunctions by Tim Starling)',
	'description' => 'Add tested-safe gadgets to the wiki',
	'descriptionmsg' => 'gadgets_desc',
);

$wgExtensionMessagesFiles['ParserFunctions'] = dirname(__FILE__) . '/ParserFunctions.i18n.php';
$wgHooks['LanguageGetMagic'][]       = 'wfParserFunctionsLanguageGetMagic';

class ExtParserFunctions {
	function registerParser( &$parser ) {
		$parser->setFunctionHook( 'gadget', array(&$this, 'gadget') );

		return true;
	}

	function gadget(&$parser) {
		return array('<script>alert("hi!");</script>', noparse=>true, isHTML=>true);
	}

}

function wfSetupParserFunctions() {
	global $wgParser, $wgExtParserFunctions, $wgHooks;

	$wgExtParserFunctions = new ExtParserFunctions;

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

function wfParserFunctionsLanguageGetMagic( &$magicWords, $langCode ) {
	require_once( dirname( __FILE__ ) . '/ParserFunctions.i18n.magic.php' );
	foreach( efParserFunctionsWords( $langCode ) as $word => $trans )
		$magicWords[$word] = $trans;
	return true;
}

