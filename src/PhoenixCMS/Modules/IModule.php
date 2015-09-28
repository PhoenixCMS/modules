<?php
/**
 * @author Tomáš Blatný
 */

namespace PhoenixCMS\Modules;

use PhoenixCMS\Utils\HashMap;


interface IModule
{

	/**
	 * @return string
	 */
	function getVendor();


	/**
	 * @return string
	 */
	function getName();


	/**
	 * @return string
	 */
	function getVersion();


	/**
	 * @return string[]
	 */
	function getDependencies();


	/**
	 * @return HashMap
	 */
	function getConfig();


	/**
	 * @param ModuleManager $moduleManager
	 */
	function initialize(ModuleManager $moduleManager);

	function beforeLoad();

	function afterLoad();

}
