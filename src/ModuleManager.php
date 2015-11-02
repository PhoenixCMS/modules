<?php
/**
 * @author Tomáš Blatný
 */

namespace PhoenixCMS\Modules;

use PhoenixCMS\Config\ILoader;
use PhoenixCMS\Modules\Exceptions\ModuleDependencyException;
use PhoenixCMS\Modules\Exceptions\ModuleException;
use PhoenixCMS\Utils\InvalidVersionException;
use PhoenixCMS\Utils\Version;


class ModuleManager
{

	/** @var ILoader */
	private $loader;

	/** @var IModule[] */
	private $modules = [];

	/** @var ModuleException[] */
	private $errors = [];


	public function __construct(ILoader $loader)
	{
		$this->loader = $loader;
	}


	/**
	 * @param IModule $module
	 */
	public function registerModule(IModule $module)
	{
		try {
			$module->initialize($this);
			try {
				Version::validate($module->getVersion());
			} catch (InvalidVersionException $e) {
				throw new ModuleException($e->getMessage(), $e->getCode(), $e);
			}
		} catch (ModuleException $e) {
			$identifier = $this->getIdentifier($module->getVendor(), $module->getName());
			$this->errors[$identifier] = $e->setModule($module);
			return;
		}
		$identifier = $this->getIdentifier($module->getVendor(), $module->getName());
		$this->modules[$identifier] = $module;
	}


	/**
	 * @param $vendor
	 * @param $name
	 * @return null|IModule
	 */
	public function getModule($vendor, $name)
	{
		$identifier = $this->getIdentifier($vendor, $name);
		return isset($this->modules[$identifier]) ? $this->modules[$identifier] : NULL;
	}


	/**
	 * @return IModule[]
	 */
	public function getModules()
	{
		return $this->modules;
	}


	/**
	 * @return Exceptions\ModuleException[]
	 */
	public function getErrors()
	{
		return $this->errors;
	}


	/**
	 *
	 */
	public function prepareModules()
	{
		foreach ($this->getModules() as $module) {
			try {
				$module->beforeLoad();
			} catch (ModuleException $e) {
				$identifier = $this->getIdentifier($module->getVendor(), $module->getName());
				$this->errors[$identifier] = $e->setModule($module);
				unset($this->modules[$identifier]);
			}
		}
		$this->resolveDependencies();
		foreach ($this->getModules() as $module) {
			$module->afterLoad();
		}
	}


	/**
	 * @return ILoader
	 */
	public function getConfigLoader()
	{
		return $this->loader;
	}


	/**
	 * @param string $vendor
	 * @param string $name
	 * @return string
	 */
	private function getIdentifier($vendor, $name)
	{
		return $vendor . '/' . $name;
	}


	/**
	 * @throws ModuleDependencyException
	 */
	private function resolveDependencies()
	{
		$allClean = FALSE;
		while (!$allClean) {
			$allClean = TRUE;
			foreach ($this->getModules() as $module) {
				foreach ($module->getDependencies() as $dependency => $version) {
					try {
						if (!isset($this->modules[$dependency])) {
							throw new ModuleDependencyException("Dependent module '$dependency' not found or not active.");
						}
						$e = Version::validate($version);
						if ($e instanceof InvalidVersionException) {
							throw new ModuleDependencyException("Dependent module '$dependency' has invalid version string '$version' defined.");
						}
						$dependent = $this->modules[$dependency];
						$compare = Version::compare($dependent->getVersion(), $version);
						if (abs($compare) > 1) {
							throw new ModuleDependencyException("Dependent module '$dependency' required version '$version' not matched its current version '{$dependent->getVersion()}'.");
						}
					} catch (ModuleDependencyException $e) {
						$identifier = $this->getIdentifier($module->getVendor(), $module->getName());
						$this->errors[$identifier] = $e->setModule($module);
						unset ($this->modules[$identifier]);
						$allClean = FALSE;
					}
				}
			}
		}
	}

}
