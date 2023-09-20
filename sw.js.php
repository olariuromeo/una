<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

define('BX_SERVICE_WORKER', true);

require_once('./inc/header.inc.php');
$oTemplate = BxDolTemplate::getInstance();

$sCacheName = 'app' . $oTemplate->getRevision();

$aAssets = [];

//--- CSS/JS files
foreach(['css' , 'js'] as $sType) {
    $aFiles = $oTemplate->includeFiles($sType, true, false);
    if(!is_array($aFiles))
        $aFiles = [$aFiles];

    $aAssets = array_merge($aAssets, $aFiles);
}

if(($sCache = getParam('sys_pwa_sw_cache')) != '') {
    $aItems = preg_split("/\\r\\n|\\r|\\n/", $sCache);
    if(!empty($aItems) && is_array($aItems))
        foreach($aItems as $sItem) {
            if(strpos($sItem, BX_DOL_URL_ROOT) === false)
                $sItem = BX_DOL_URL_ROOT . $sItem;

            $aAssets[] = $oTemplate->addRevision($sItem);
        }
}

//--- Offline page
if(($sOffline = getParam('sys_pwa_sw_offline')) != '')
    $aAssets[] = BX_DOL_URL_ROOT . $sOffline;

//--- Favicon or SVG icon
$sIconUrl = '';
if(($iId = (int)getParam('sys_site_icon')) != 0)
    $sIconUrl = BxDolStorage::getObjectInstance(BX_DOL_STORAGE_OBJ_FILES)->getFileUrlById($iId);          
if(!$sIconUrl && ($iId = (int)getParam('sys_site_icon_svg')) != 0)
    $sIconUrl = BxDolStorage::getObjectInstance(BX_DOL_STORAGE_OBJ_IMAGES)->getFileUrlById($iId);
if(!empty($sIconUrl))
    $aAssets[] = $sIconUrl;

//--- Fonts
if(getParam('sys_css_icons_default') != '') {
    $sHomeUrl = BX_DOL_URL_ROOT;
    if(BxDolModuleQuery::getInstance()->isEnabledByName('bx_fontawesome'))
        $sHomeUrl = BxDolModule::getInstance('bx_fontawesome')->_oConfig->getHomeUrl();

    $aAssets[] = $sHomeUrl . 'template/fonts/fa-solid-900.woff2';
    $aAssets[] = $sHomeUrl . 'template/fonts/fa-regular-400.woff2';
}

//$aAssets[] = 'https://ci.una.io/test/cache_public/bx_templ_css_61777a77ec925fa4b36db9699d01c960.css'; // some basic styles are included here instead of main css file, these styles need to be moved there then tis cache isn't needed

header("Content-Type: text/javascript");
?>

// Core assets
let coreAssets = <?php echo json_encode($aAssets); ?>;

// On install, cache core assets
self.addEventListener('install', function (event) {
    console.log("Service Worker: <?php echo $sCacheName; ?>");
    // Cache core assets
    event.waitUntil((async () => { 
        caches.open('<?php echo $sCacheName; ?>').then(function (cache) {
            for (let asset of coreAssets) {
                cache.add(new Request(asset));
            }
            return cache;
        });
    })());
});

const deleteCache = async (key) => {
  await caches.delete(key);
};

const deleteOldCaches = async () => {
  const cacheKeepList = ["<?php echo $sCacheName; ?>"];
  const keyList = await caches.keys();
  const cachesToDelete = keyList.filter((key) => !cacheKeepList.includes(key));
  await Promise.all(cachesToDelete.map(deleteCache));
};

// delete old cache on activate
self.addEventListener("activate", (event) => {
  event.waitUntil(deleteOldCaches());
});

// Listen for request events
self.addEventListener('fetch', function (event) {

    // Get the request
    let request = event.request;

    // Bug fix
    // https://stackoverflow.com/a/49719964
    if (event.request.cache === 'only-if-cached' && event.request.mode !== 'same-origin') return;

    // regular pages and static assets
    if (request.mode === 'navigate' || request.url.match(/\.(css|js|woff2|svg|png|jpg)$/)) {
        const failedResponse = async (err) => {
            let reponse = null;
            if (request.mode === 'navigate') {
                const cache = await caches.open('<?php echo $sCacheName; ?>');
                reponse = await cache.match('<?php echo BX_DOL_URL_ROOT . 'offline'; ?>');
            }
            return reponse || new Response("Network error happened", {
                status: 408,
                headers: { "Content-Type": "text/plain" },
            });
        }
        event.respondWith(
            caches.match(request).then((response) => {
                try {
                    return response || fetch(request)
                        .then((response) => {
                            return response;
                        })
                        .catch((err) => {
                            return failedResponse(err);
                        });
                } catch (error) {
                    return failedResponse(err);
                }
            })
        );
    }

});
