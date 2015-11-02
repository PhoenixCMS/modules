<?php
/**
 * @author Tomáš Blatný
 */

namespace PhoenixCMS\Modules\Exceptions;

use Exception;
use PhoenixCMS\Modules\IModule;


class ModuleException extends Exception
{
	/** @var IModule */
	private $module;


	/**
	 * @return IModule
	 */
	public function getModule()
	{
		return $this->module;
	}


	/**
	 * @param IModule $module
	 * @return $this
	 */
	public function setModule(IModule $module)
	{
		$this->module = $module;
		return $this;
	}
}
