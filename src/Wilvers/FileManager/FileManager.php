<?php

namespace Wilvers\FileManager;

use Wilvers\FileManager\Config\ConfigurationInterface;
use Wilvers\FileManager\Controller\FileManagerController;
use Wilvers\FileManager\Controller\ExecuteController;
use Wilvers\FileManager\Controller\AjaxController;
use Wilvers\FileManager\Controller\UploadController;
use Wilvers\FileManager\Tools\Response;
use Wilvers\PhpDebugBar\PhpDebugBar;
use Wilvers\PhpDebugBar\Storage\CustomFileStorage;
use Wilvers\PhpDebugBar\DataCollector\GenericCollector;

/**
 * Description of FileManager
 *
 * @author pwilvers
 */
class FileManager {

    /**
     * provide config for fm
     * need to be class that implement configInterface
     */
    protected $_config;

    /**
     * array with translation
     * @var type
     */
    protected $_translation;
    protected $_debugBar;
    protected $_debugBarRenderer;

    /**
     *
     */
    public function __construct() {
//        if ($_SESSION['RF']["verify"] != "RESPONSIVEfilemanager") {
//            $r = new Response('forbiden', 403);
//            $r->send();
//            exit;
//        }
        //
        $this->_debugBar = new PhpDebugBar();
        $st = new CustomFileStorage('/logs/filemanager/');
        $st->setCollectorsToSave(array('Users', 'Ip', 'exceptions', 'memory', 'request', 'messages', '__meta', 'trace', 'config'));
        $this->_debugBar->setStorage($st);
        $this->_debugBar->addCollector(new GenericCollector('trace'));
        $this->_debugBar->addCollector(new GenericCollector('config'));

        $this->_debugBarRenderer = $this->_debugBar
                ->getJavascriptRenderer()
                ->setEnableJqueryNoConflict(false);
        $this->_debugBarRenderer->setOpenHandlerUrl('open.php');
//        $debugbar['messages']->addMessage('hello from redirect');
    }

    /**
     *
     * GETTER AND SETTER
     *
     */
    public function getConfig() {
        return $this->_config;
    }

    public function setConfig(ConfigurationInterface $config) {
        $this->_config = $config;
        return $this;
    }

    public function getTranslation() {
        return $this->_translation;
    }

    public function setTranslation(ConfigurationInterface $translation) {
        $this->_translation = $translation;
        return $this;
    }

    public function render() {
        $ctrl = new FileManagerController($this->_debugBar);
        //return $ctrl->render($this->_config, $this->_translation);
        $dbHead = $this->_debugBarRenderer->renderHead();
        $dbHtml = $this->_debugBarRenderer->render();
        $renderParams = array($dbHead, $dbHtml);
        $search = array('<!--[[head-replace]]-->', '<!--[[body-bottom-replace]]-->');

        $html = $ctrl->render($this->_config, $this->_translation);
        return str_replace($search, $renderParams, $html);
    }

    public function execute() {

        if (strpos($_POST['path'], '/') === 0 || strpos($_POST['path'], '../') !== FALSE || strpos($_POST['path'], './') === 0) {
            $r = new Response();
            $r->response('wrong path')->send();
            exit;
        }
        $this->_debugBar->getCollector('config')->addMessage($this->_config->getConfig());

        $ctrl = new ExecuteController($this->_debugBar);
        $ctrl->execute($this->_config, $this->_translation);
        $this->_debugBar->collect();
    }

    public function ajaxCalls() {
        $this->_debugBar->getCollector('config')->addMessage($this->_config->getConfig());

        $ctrl = new AjaxController($this->_debugBar);
        $ctrl->execute($this->_config, $this->_translation);
        $this->_debugBar->collect();
    }

    public function upload() {
        $this->_debugBar->getCollector('config')->addMessage($this->_config->getConfig());

        $ctrl = new UploadController($this->_debugBar);
        $ctrl->execute($this->_config, $this->_translation);
        $this->_debugBar->collect();
    }

}
