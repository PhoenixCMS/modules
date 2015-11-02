<?php
/**
 * @author Tomáš Blatný
 */

namespace PhoenixCMS\Modules\DI;

use Nette\DI\CompilerExtension;
use PhoenixCMS\Modules\IModule;
use PhoenixCMS\Modules\ModuleManager;


class ModulesExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$this->compiler->parseServices($this->getContainerBuilder(), $this->loadFromFile(__DIR__ . '/modules.neon'), $this->name);
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$modules = $builder->findByType(IModule::class);

		foreach ($modules as $module) {
			$module->addSetup('?->getService(?)->registerModule($service)', ['@container', $builder->getByType(ModuleManager::class)]);
		}
	}

}
