<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Events Events
 * @ingroup     UnaModules
 *
 * @{
 */

/**
 * Event forms functions
 */
class BxEventsFormsEntryHelper extends BxBaseModGroupsFormsEntryHelper
{
    public function __construct($oModule)
    {
        parent::__construct($oModule);
    }

    public function onDataDeleteAfter($iContentId, $aContentInfo, $oProfile)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        $this->_oModule->_oDb->deleteSessions(['event_id' => $iContentId]);

        return parent::onDataDeleteAfter($iContentId, $aContentInfo, $oProfile);
    }
}

/** @} */
