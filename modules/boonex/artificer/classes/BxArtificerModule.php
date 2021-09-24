<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Artificer Artificer template
 * @ingroup     UnaModules
 *
 * @{
 */

bx_import ('BxBaseModTemplateModule');

class BxArtificerModule extends BxBaseModTemplateModule
{
    function __construct(&$aModule)
    {
        parent::__construct($aModule);
    }

    public function serviceGetSafeServices()
    {
        return array_merge(parent::serviceGetSafeServices(), [
            'GetSplashMarker' => '',
        ]);
    }

    public function serviceIncludeCssJs()
    {
        if(BxDolTemplate::getInstance()->getCode() != $this->_oConfig->getUri())
            return '';

        return $this->_oTemplate->getIncludeCssJs();
    }

    public function serviceGetSplashMarker($sName)
    {
        $sResult = '';

        switch($sName) {
            case 'header':
                $sResult = $this->_oTemplate->getHeader();
                break;

            case 'members':
                $sResult = '0';
                if($aMembers = BxDolAccountQuery::getInstance()->getAccounts(['type' => 'confirmed']))
                    $sResult = count($aMembers);
                break;

            case 'posts':
                $iPosts = 0;
                $aModules = bx_srv('system', 'get_modules_by_type', ['content']);
                foreach($aModules as $aModule)
                    if(BxDolRequest::serviceExists($aModule['name'], 'get_all'))
                        $iPosts += bx_srv($aModule['name'], 'get_all', [['type' => 'all', 'count' => true]]);
                
                $sResult = $iPosts;
                break;

            case 'comments':
                $sResult = (int)BxDolCmtsQuery::getInfoBy(['type' => 'all', 'count' => true]);
                break;

            case 'login_agreement':
                $oPermalink = BxDolPermalinks::getInstance();
                $sLinkTerms = BX_DOL_URL_ROOT . $oPermalink->permalink('page.php?i=terms');
                $sLinkPrivacy = BX_DOL_URL_ROOT . $oPermalink->permalink('page.php?i=privacy');

                $sResult = _t('_bx_artificer_txt_splash_login_agreement', $sLinkTerms, $sLinkPrivacy);
                break;
        }

        return $sResult;
    }
}

/** @} */
