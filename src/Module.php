<?php
/**
 * @author Tomáš Blatný
 */

namespace PhoenixCMS\Modules;


use Exception;
use Nette\Reflection\ClassType;
use PhoenixCMS\Modules\Exceptions\ModuleInitializeException;
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
	 * @throws ModuleInitializeException
	 */
	public function initialize(ModuleManager $moduleManager)
	{
		try {
			$this->config = $moduleManager->getConfigLoader()->load($this->getConfigFile());
		} catch (Exception $e) {
			throw new ModuleInitializeException('Unable to load configuration: ' . $e->getMessage(), $e->getCode(), $e);
		}
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
		$reflection = ClassType::from($this);
		return dirname($reflection->getFileName()) . '/module.neon';
	}

}
