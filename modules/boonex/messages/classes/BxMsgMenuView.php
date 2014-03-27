<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    Messages Messages
 * @ingroup     DolphinModules
 *
 * @{
 */

bx_import('BxBaseModTextMenuView');

/**
 * View entry menu
 */
class BxMsgMenuView extends BxBaseModTextMenuView
{
    public function __construct($aObject, $oTemplate = false) 
    {
        $this->MODULE = 'bx_messages';
        parent::__construct($aObject, $oTemplate);
    }
}

/** @} */
