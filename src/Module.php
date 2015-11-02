<?php
/**
 * @author Tomáš Blatný
 */

namespace PhoenixCMS\Modules;


use PhoenixCMS\Utils\HashMap;


abstract class Module implements IModule
{

	/** @var HashMap */
	private $config;

	/** @var ModuleManager */
	private $moduleManager;


	/**
	 * @return string
	 */
	public function getVendor()
	{
		return $this->config->getString('vendor');
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->config->getString('name');
	}


	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->config->getString('version');
	}


	/**
	 * @return string
	 */
	public function getDependencies()
	{
		return iterator_to_array($this->config->getArray('dependencies'));
	}


	/**
	 * @return HashMap
	 */
	public function getConfig()
	{
		return $this->config;
	}


	/**
	 * @param ModuleManager $moduleManager
	 */
	public function initialize(ModuleManager $moduleManager)
	{
		$this->config = $moduleManager->getConfigLoader()->load($this->getConfigFile());
		$this->moduleManager = $moduleManager;
	}


	public function beforeLoad()
	{

	}


	public function afterLoad()
	{

	}


	/**
	 * @return ModuleManager
	 */
	protected function getModuleManager()
	{
		return $this->moduleManager;
	}


	/**
	 * @return string
	 */
	protected function getConfigFile()
	{
		return __DIR__ . '/module.neon';
	}

}
