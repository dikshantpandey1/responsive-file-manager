<?php

namespace FileManager;

use FileManager\Config\ConfigurationInterface;
use FileManager\Controller\FileManagerController;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

    /**
     *
     */
    public function __construct() {

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
        $ctrl = new FileManagerController();
        return $ctrl->render($this->_config, $this->_translation);
    }

}
