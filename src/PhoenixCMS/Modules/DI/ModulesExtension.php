<?php
/**
 * @author Tomáš Blatný
 */

namespace PhoenixCMS\Modules\DI;

use Nette\DI\CompilerExtension;


class ModulesExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$this->compiler->parseServices($this->getContainerBuilder(), $this->loadFromFile(__DIR__ . '/modules.neon'), $this->name);
	}


	public function beforeCompile()
	{
		//
	}

}
