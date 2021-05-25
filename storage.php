<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

require_once('./inc/header.inc.php');

$sStorageObject = bx_process_input(bx_get('o'));
$sFile = bx_process_input(bx_get('f'));
$sToken = bx_process_input(bx_get('t'));

$oStorage = BxDolStorage::getObjectInstance($sStorageObject);

if (!$oStorage || !method_exists($oStorage, 'download')) {
    ob_end_clean();
    bx_storage_download_error_occured();
    exit;
}

$i = strrpos($sFile, '.');
$sRemoteId = ($i !== false) ? substr($sFile, 0, $i) : $sFile;
if (!$sRemoteId) {
    ob_end_clean();
    bx_storage_download_error_occured();
    exit;
}

// redirect for remote storage in case if some references still pointing to local storage
$aObject = $oStorage->getObjectData();
if ('Local' != $aObject['engine']) {
    if (!($sUrl = $oStorage->getFileUrlByRemoteId($sFile))) {

        $sFile = preg_replace("/\.[A-Za-z0-9]+$/", '', $sFile);
        $sUrl = $oStorage->getFileUrlByRemoteId($sFile);

        // Tmp fix for storages renaming in the past
        if (!$sUrl && 'bx_posts_files' == $sStorageObject && ($oStorage = BxDolStorage::getObjectInstance('bx_posts_covers'))) 
            $sUrl = $oStorage->getFileUrlByRemoteId($sFile);

        if (!$sUrl) {
            bx_storage_download_error_occured();
            exit;
        }
    }
    header("Location: " . $sUrl);
}

if (!$oStorage->download($sRemoteId, $sToken)) {
    $iError = $oStorage->getErrorCode();
    switch ($iError) {
        case BX_DOL_STORAGE_ERR_FILE_NOT_FOUND:
            bx_storage_download_error_occured();
            exit;
        case BX_DOL_STORAGE_ERR_PERMISSION_DENIED:
            bx_storage_download_error_occured('displayAccessDenied');
            exit;
        default:
            bx_storage_download_error_occured('displayErrorOccured');
            exit;
    }
}

function bx_storage_download_error_occured($sMethod = 'displayPageNotFound')
{
    require_once(BX_DIRECTORY_PATH_INC . "design.inc.php");

    $oTemplate = BxDolTemplate::getInstance();
    $oTemplate->$sMethod ();
}

/** @} */
