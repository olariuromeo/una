<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    DolphinInstall Dolphin Install
 * @{
 */

class BxDolInstallView 
{
    protected $_sDirPlugins = BX_INSTALL_DIR_PLUGINS;
    protected $_sDirTemplates = BX_INSTALL_DIR_TEMPLATES;

    protected $_sUrlCss = '../template/css/';
    protected $_sPathCss = '../template/css/';
    protected $_sUrlJs = '../';

    protected $_aFilesCss = array (
        'common.css',
        'default.less',
        'general.css',
        'icons.css',
        'colors.css',
        'media-desktop.css',
        'media-tablet.css',
        'media-phone.css',
        'media-print.css',
    );

    protected $_aFilesJs = array (
        'plugins/jquery/jquery.js',
        'inc/js/jquery.dolPopup.js',
    );

    function __construct()
    {
    }

    function out ($sTemplate, $aVars = array())
    {
        extract($aVars);
        include($this->_sDirTemplates . $sTemplate);
    }

    function pageStart ()
    {
        ob_start();
    }

    function pageEnd ($sTitle)
    {
        $sInlineCSS = $this->_getInlineCSS();
        $sFilesCSS = $this->_getFilesCSS();
        $sFilesJS = $this->_getFilesJS();
        $sCode = ob_get_clean();
        include($this->_sDirTemplates . '_page.php');
    }

    protected function _getFilesCSS()
    {
        $s = '';
        foreach ($this->_aFilesCss as $sFile)
            if (substr($sFile, -4) === '.css')
                $s .= '<link rel="stylesheet" href="' . $this->_sUrlCss . $sFile . '" />';
        return $s; 
    }

    protected function _getFilesJS()
    {
        $s = '';
        foreach ($this->_aFilesJs as $sFile)
            $s .= '<script src="' . $this->_sUrlJs . $sFile . '"></script>';
        return $s; 
    }

    protected function _getInlineCSS()
    {
        $s = '';
    	require_once($this->_sDirPlugins . 'lessphp/lessc.inc.php');
        $oLess = new lessc();
        $oConfigBase = new BxBaseConfig();
        $oLess->setVariables($oConfigBase->aLessConfig);
        foreach ($this->_aFilesCss as $sFile) {
            if (substr($sFile, -5) !== '.less')
                continue;

            $s .= $oLess->compileFile($this->_sPathCss . $sFile) . "\n";
        }
        return $s; 
    }
}

/** @} */
