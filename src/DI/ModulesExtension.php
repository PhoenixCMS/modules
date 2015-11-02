<?php
/**
 * @author Tomáš Blatný
 */

namespace PhoenixCMS\Modules\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use PhoenixCMS\Modules\IModule;
use PhoenixCMS\Modules\ModuleManager;


class ModulesExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$this->compiler->parseServices($this->getContainerBuilder(), $this->loadFromFile(__DIR__ . '/modules.neon'), $this->name);
	}


	public function afterCompile(ClassType $class)
	{
		$initialize = $class->getMethod('initialize');

		$initialize->addBody('$moduleManager = $this->getService(?);', [$this->getContainerBuilder()->getByType(ModuleManager::class)]);
		$initialize->addBody(
			'foreach ($this->findByType(\'' . IModule::class . '\') as $module) {' . "\n" .
			'   $moduleManager->registerModule($this->getService($module));' . "\n" .
			'}'
		);
		$initialize->addBody('$moduleManager->prepareModules();');
	}

}
