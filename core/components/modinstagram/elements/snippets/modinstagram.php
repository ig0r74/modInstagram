<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var modInstagram $modInstagram */
$modInstagram = $modx->getService('modinstagram', 'modInstagram', MODX_CORE_PATH . 'components/modinstagram/model/', $scriptProperties);
if (!$modInstagram) {
    return 'Could not load modInstagram class!';
}

$accessToken = $modx->getOption('accessToken', $scriptProperties, $modx->getOption('modinstagram_acess_token'), true);
$miUsername = $modx->getOption('miUsername', $scriptProperties, $modx->getOption('modinstagram_username'), false);
if (empty($miUsername)) $miUsername = $modx->getOption('username', $scriptProperties, $modx->getOption('modinstagram_username'), false);
$miPassword = $modx->getOption('miPassword', $scriptProperties, $modx->getOption('modinstagram_password'), false);
$fromJson = $modx->getOption('fromJson', $scriptProperties, false);
$tplWrapper = $modx->getOption('tplWrapper', $scriptProperties, false);
$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.modInstagram.item');
$cacheTime = $modx->getOption('cacheTime', $scriptProperties, 1800, true);
$cachePrefix = $modx->getOption('cachePrefix', $scriptProperties, 'mod_instagram');
$limit = $modx->getOption('limit', $scriptProperties, 20, true);
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);
$showLog = $modx->getOption('showLog', $scriptProperties, false);
$maxId = $modx->getOption('maxId', $scriptProperties, false);
$minId = $modx->getOption('minId', $scriptProperties, false);

$cacheManager = $modx->getCacheManager();

if (empty($accessToken) && !$miUsername) {
    $modx->log(MODX_LOG_LEVEL_ERROR, 'No ACCESS TOKEN & username');
    return '';
}

$pdo = $modx->getService('pdoTools');

$pdo->addTime('pdoTools loaded');

if (!$output = $cacheManager->get($cachePrefix)) {
    
    // get data with token
    if (!empty($accessToken)) {
        $query = array(
            'access_token' => $accessToken,
            'count' => $limit,
            'max_id' => $maxId,
            'min_id' => $minId
        );
        
        $url = 'https://api.instagram.com/v1/users/self/media/recent/?' . http_build_query($query);
        
        $apiResponse = $modInstagram->getData($url);
        
        if (!empty($apiResponse['error'])) {
            if (empty($miUsername)) {
                $modx->log(MODX_LOG_LEVEL_ERROR, 'modInstagram error: ' . $apiResponse['error']);
                $apiResponse = '';
                // return false;
            }
        } else {
            $apiResponse['data'] = json_decode($apiResponse['result'], true);
        }
    }
    
    // get data with scraper
    if (empty($apiResponse['data']) && !empty($miUsername)) {
        $data = $modInstagram->getScraperData($miUsername, $limit, $miPassword, $fromJson);
        if (!empty($data['error'])) {
            $modx->log(MODX_LOG_LEVEL_ERROR, 'modInstagram Scraper error: ' . $data['error']);
            return false;
        }
        $scraperData = $data['result'];
    }
    
    $pdo->addTime('File received');
    
    $output = '';
    
    $idx = 1;
    
    if (empty($apiResponse['data']) && !empty($scraperData)) {
        $apiResponse['data'] = array();
        foreach ($scraperData as $item){
            $new_item = array(
                'id' => $item->getId(),
                'images' => array(
                    'thumbnail' => array(
                        'url' => $item->getImageThumbnailUrl(),
                    ),
                    'low_resolution' => array(
                        'url' => $item->getImageLowResolutionUrl(),
                    ),
                    'standard_resolution' => array(
                        'url' => $item->getImageHighResolutionUrl(),
                    ),
                ),
                'created_time' => $item->getCreatedTime(),
                'caption' => array(
                    'text' => $item->getCaption()
                ),
                'likes' => array(
                    'count' => $item->getLikesCount()
                ),
                'comments' => array(
                    'count' => $item->getCommentsCount()
                ),
                'type' => str_replace(array('GraphSidecar', 'GraphVideo'), array('carousel','video'), $item->getType()),
                'link' => $item->getLink(),
                'location' => array(
                    'name' => $item->getLocationName()
                ),
                'videos' => array( // unavailable
                    'standard_resolution' => array(
                        'url' => $item->getVideoStandardResolutionUrl(),
                    ),
                    'low_bandwidth' => array(
                        'url' => $item->getVideoLowBandwidthUrl(),
                    ),
                    'low_resolution' => array(
                        'url' => $item->getVideoLowResolutionUrl(),
                    ),
                ),
                'carousel_media' => array(), // unavailable
            );
            array_push($apiResponse['data'], $new_item);
        }
    }
    
    if (!empty($apiResponse['data'])) {
        foreach($apiResponse['data'] as $row) {
            $output .= $pdo->getChunk($tpl, array(
                'idx' => $idx,
                'id' => $row['id'],
                'image_thumbnail' => $row['images']['thumbnail']['url'],
                'image_low_resolution' => $row['images']['low_resolution']['url'],
                'image_standard_resolution' => $row['images']['standard_resolution']['url'],
                'created_time' => $row['created_time'],
                'caption_text' => $row['caption']['text'],
                'likes_count' => $row['likes']['count'],
                'comments_count' => $row['comments']['count'],
                'type' => $row['type'],
                'link' => $row['link'],
                'location_name' => $row['location']['name'],
                'video_standard_resolution' => $row['videos']['standard_resolution']['url'],
                'video_low_bandwidth' => $row['videos']['low_bandwidth']['url'],
                'video_low_resolution' => $row['videos']['low_resolution']['url'],
                'carousel' => $row['carousel_media'],
            ));
            $idx++;
        }
    } else {
        return false;
    }
    
    $cacheManager->set($cachePrefix, $output, $cacheTime);
}

if (!empty($tplWrapper)) {
    $output = $pdo->getChunk($tplWrapper, array('output' => $output));
    $pdo->addTime('Rows wrapped');
}

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="modInstagramLog">' . print_r($pdo->getTime(), 1) . '</pre>';
}

if (!empty($toPlaceholder)) {
    // If using a placeholder, output nothing and set output to specified placeholder
    $modx->setPlaceholder($toPlaceholder, $output);
    return '';
}

return $output;