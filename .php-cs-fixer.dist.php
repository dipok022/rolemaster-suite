<?php
require_once __DIR__ . '/vendor/litonarefin/wp-php-cs-fixer/loader.php';

$finder = PhpCsFixer\Finder::create()
	->exclude( 'node_modules' )
	->exclude( 'vendors' )
	->exclude( 'assets' )
	->in( __DIR__ );

$config = new PhpCsFixer\Config();
$config
	->registerCustomFixers(
		array(
			new Rolemaster_Suite\Fixer\SpaceInsideParenthesisFixer(),
			new Rolemaster_Suite\Fixer\BlankLineAfterClassOpeningFixer(),
		)
	)
	->setRiskyAllowed( true )
	->setUsingCache( false )
	->setRules( Rolemaster_Suite\Fixer\Fixer::rules() )
	->setFinder( $finder );

return $config;
