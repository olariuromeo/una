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
 * View entry all actions menu
 */
class BxEventsMenuViewActionsAll extends BxBaseModGroupsMenuViewActionsAll
{
    public function __construct($aObject, $oTemplate = false)
    {
        $this->_sModule = 'bx_events';

        parent::__construct($aObject, $oTemplate);
    }

    protected function _getMenuAttrs ($aMenuItem)
    {
        $s = parent::_getMenuAttrs ($aMenuItem);
        if ('ical-export' == $aMenuItem['name']) {
            $CNF = $this->_oModule->_oConfig->CNF;
            $s .= ' download="' . title2uri($this->_aContentInfo[$CNF['FIELD_TITLE']]) . '.ics"';
        }
        return $s;
    }

    protected function _getMenuItemJoinEventProfile($aItem)
    {
        return $this->_getMenuItemByNameActions($aItem);
    }

    protected function _getMenuItemEditEventCover($aItem)
    {
        return $this->_getMenuItemByNameActionsMore($aItem);
    }

    protected function _getMenuItemEditEventProfile($aItem)
    {
        return $this->_getMenuItemByNameActionsMore($aItem);
    }

    protected function _getMenuItemEditEventPricing($aItem)
    {
        return $this->_getMenuItemByNameActionsMore($aItem);
    }

    protected function _getMenuItemInviteToEvent($aItem)
    {
        return $this->_getMenuItemByNameActionsMore($aItem);
    }

    protected function _getMenuItemDeleteEventProfile($aItem)
    {
        return $this->_getMenuItemByNameActionsMore($aItem);
    }

    protected function _getMenuItemApproveEventProfile($aItem)
    {
        return $this->_getMenuItemByNameActionsMore($aItem);
    }

    protected function _getMenuItemProfileSetBadges($aItem)
    {
        return $this->_getMenuItemByNameActions($aItem);
    }
}

/** @} */
