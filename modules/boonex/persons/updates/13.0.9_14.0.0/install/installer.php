<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

class BxPersonsUpdater extends BxDolStudioUpdater
{
    function __construct($aConfig)
    {
        parent::__construct($aConfig);
    }

    public function actionExecuteSql($sOperation)
    {
        if($sOperation == 'install') {
            if(!$this->oDb->isFieldExists('bx_persons_data', 'settings'))
                $this->oDb->query("ALTER TABLE `bx_persons_data` ADD `settings` text NOT NULL AFTER `allow_contact_to`");
        }

        return parent::actionExecuteSql($sOperation);
    }
}
