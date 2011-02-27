<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	/**
	 * @return void
	 */
	public function _initAutoloader()
    {
        require_once APPLICATION_PATH . '/../library/Doctrine/Common/ClassLoader.php';

        $autoloader = \Zend_Loader_Autoloader::getInstance();

        $bisnaAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
        $autoloader->pushAutoloader(array($bisnaAutoloader, 'loadClass'), 'Bisna');

        $appAutoloader = new \Doctrine\Common\ClassLoader('Inject');
        $autoloader->pushAutoloader(array($appAutoloader, 'loadClass'), 'Inject');


		$entityAutoloader = new \Doctrine\Common\ClassLoader('Entity', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($entityAutoloader, 'loadClass'), 'Entity');

		$providerAutoloader = new \Doctrine\Common\ClassLoader('Logic', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($providerAutoloader, 'loadClass'), 'Logic');

		$pModAutoloader = new \Doctrine\Common\ClassLoader('PMod', APPLICATION_PATH);
		$autoloader->pushAutoloader(array($pModAutoloader, 'loadClass'), 'PMod');
    }

	public function _initInjectionKernel()
	{
		$kernel = new \Inject\Kernel();

		$kernel
			->Bind("EntityManager")
			->ToProvider(new Logic\Provider\EntityManager());

		$kernel
			->Bind("CampRepository")
			->ToProvider(new Logic\Provider\Repository("eCamp\Entity\Camp"));

		$kernel->Bind("SomeService")->ToSelf()->AsSingleton();

		Zend_Registry::set("kernel", $kernel);
	}

	/**
	 * Override the default Zend_View with Ztal support and configure defaults.
	 *
	 * @return void
	 */
	protected function _initZtal()
	{
		//register the Ztal plugin
		$plugin = new Ztal_Controller_Plugin_Ztal($this->getOption('ztal'));
		Zend_Controller_Front::getInstance()->registerPlugin($plugin);
	}


	protected function _initRoutes()
	{


		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'ControllerAction', new Zend_Controller_Router_Route(':controller/:action/*',
			array('controller' => 'index', 'action' => 'index')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'EntityId', new Zend_Controller_Router_Route(':controller/:action/:EntityId/*',
			array('controller' => 'index', 'action' => 'index'),
			array('EntityId' => '\d+')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'CampId', new Zend_Controller_Router_Route(':CampId/:controller/:action/*',
			array('controller' => 'index', 'action' => 'index'),
			array('CampId' => '\d+')));

		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'CampEntityId', new Zend_Controller_Router_Route(':CampId/:controller/:action/:EntityId/*',
			array('controller' => 'index', 'action' => 'index'),
			array('CampId' => '\d+', 'EntityId' => '\d+')));


	}

	protected function _initView()
	{
		$view = new Zend_View();

		$view->setEncoding('UTF-8');
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');

		$view->headLink()->appendStylesheet('/css/blueprint/screen.css', 'screen, projection');
		$view->headLink()->appendStylesheet('/css/blueprint/ie.css', 'screen, projection', 'lt IE 8');
		$view->headLink()->appendStylesheet('/css/blueprint/print.css', 'print');

		$view->headLink()->appendStylesheet('/css/blueprint/plugins/fancy-type/screen.css', 'screen, projection');

		$view->headLink()->appendStylesheet('/css/main.css');


		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);

		return $view;
	}
}
