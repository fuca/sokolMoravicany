<?php

/**
 * My Application bootstrap file.
 */
use Nette\Application\Routers\Route;


// Load Nette Framework
require LIBS_DIR . '/Nette/loader.php';


// Configure application
$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setProductionMode($configurator::AUTO);
$configurator->setProductionMode(TRUE);
$configurator->enableDebugger(__DIR__ . '/../log');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(TEMP_DIR);
$configurator->createRobotLoader()
	->addDirectory(APP_DIR)
	->addDirectory(LIBS_DIR)
	->register();

// Register dibi extension for compiler
$configurator->onCompile[] = function ($configurator, $compiler) {
    $compiler->addExtension('dibi', new DibiNetteExtension);
};

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();

// Setup router
$container->router[] = new Route('index.php', 'Front:Homepage:default', Route::ONE_WAY);
$container->router[] = new Route('', 'Front:Homepage:default');
$container->router[] = new Route('o-nas', 'Front:Homepage:default');
$container->router[] = new Route('aktuality', 'Front:Article:default');
$container->router[] = new Route('aktuality/clanek/<id>', 'Front:Article:show');
$container->router[] = new Route('oddily', 'Front:Section:default');
$container->router[] = new Route('dokumenty', 'Front:Download:default');
$container->router[] = new Route('galerie', 'Front:Gallery:default');
$container->router[] = new Route('kontakty', 'Front:Contact:default');
$container->router[] = new Route('externi-odkazy', 'Front:Link:default');

$container->router[] = new Route('rss-feed', 'Front:Rss:default');
$container->router[] = new Route('<presenter>/<action>[/<id>]','Front:Homepage:default');



// Configure and run the application!
$container->application->run();
