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

	function gadget(&$parser, $argument='') {
		/* Remove leading and trailing whitespace. */
		$argument = trim($argument);

		/* Require $wgGadgetsAllowed  at all */
		if (! isset($wgGadgetsAllowed)) {
			return "'''You tried to use a gadget but have not declared $wgGadgetsAllowed.'''";
		}
		if (! isset($wgGadgetsAllowed[$argument])) {
			return "'''You tried to use an undefined gadget.'''";
		}
		return array($wgGadgetsAllowed[$argument],
			     noparse=>true, isHTML=>true);
	}


}

function wfSetupGadgets() {
	global $wgParser, $wgExtGadgets, $wgHooks;

	$wgExtGadgets = new ExtGadgets;

	// Check for SFH_OBJECT_ARGS capability
	if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
		$wgHooks['ParserFirstCallInit'][] = array( &$wgExtGadgets, 'registerParser' );
	} else {
		if ( class_exists( 'StubObject' ) && !StubObject::isRealObject( $wgParser ) ) {
			$wgParser->_unstub();
		}
		$wgExtGadgets->registerParser( $wgParser );
	}

}

function wfGadgetsLanguagesGetMagic( &$magicWords, $langCode ) {
	require_once( dirname( __FILE__ ) . '/Gadgets.i18n.magic.php' );
	foreach( efGadgetsWords( $langCode ) as $word => $trans )
		$magicWords[$word] = $trans;
	return true;
}

