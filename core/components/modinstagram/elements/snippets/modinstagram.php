<?php
$access_token = $modx->getOption('accessToken', $scriptProperties, $modx->getOption('modinstagram_acess_token'), true);
$username = $modx->getOption('username', $scriptProperties, $modx->getOption('modinstagram_username'), true);
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

if (!$access_token && !$username) {
    $modx->log(MODX_LOG_LEVEL_ERROR, 'No ACCESS TOKEN & username');
    return '';
}

$pdo = $modx->getService('pdoTools');

$pdo->addTime('pdoTools loaded');

if (!$output = $cacheManager->get($cachePrefix)) {
    
    if (!empty($access_token)) {
        $query = array(
            'access_token' => $access_token,
            'count' => $limit,
            'max_id' => $maxId,
            'min_id' => $minId
        );
        
        $response = file_get_contents('https://api.instagram.com/v1/users/self/media/recent/?' . http_build_query($query));
        
        if ($http_response_header[0] != 'HTTP/1.1 200 OK') {
            if (empty($username)) {
                $modx->log(MODX_LOG_LEVEL_ERROR, 'modInstagram error: ' . $http_response_header[0]);
                return false;
            }
        } else {
            $response = json_decode($response, true);
        }
    }
    
    if (empty($response['data']) && !empty($username)) {
        $url = "https://www.instagram.com/" . $username . "/";
        $json = file_get_contents($url);
        if ($http_response_header[0] != 'HTTP/1.1 200 OK') {
            $modx->log(MODX_LOG_LEVEL_ERROR, 'modInstagram error: ' . $http_response_header[0]);
            return false;
        }
        $json = explode("window._sharedData = ", $json)[1];
        $json = explode(";</script>", $json)[0];
        $array = json_decode($json, true);
        $sharedData = $array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
    }
    
    $pdo->addTime('File received');
    
    $output = '';
    
    $idx = 1;
    
    if (empty($response) && !empty($sharedData)) {
        $response['data'] = array();
        foreach ($sharedData as $item){
            $new_item = array(
                'id' => $item['node']['id'],
                'images' => array(
                    'thumbnail' => array(
                        'url' => $item['node']['thumbnail_resources'][0]['src'],
                    ),
                    'low_resolution' => array(
                        'url' => $item['node']['thumbnail_resources'][2]['src'],
                    ),
                    'standard_resolution' => array(
                        'url' => $item['node']['display_url'],
                    ),
                ),
                'created_time' => $item['node']['taken_at_timestamp'],
                'caption' => array(
                    'text' => $item['node']['edge_media_to_caption']['edges'][0]['node']['text']
                ),
                'likes' => array(
                    'count' => $item['node']['edge_liked_by']['count']
                ),
                'comments' => array(
                    'count' => $item['node']['edge_media_to_comment']['count']
                ),
                'type' => str_replace(array('GraphSidecar', 'GraphVideo'), array('carousel','video'), $item['node']['__typename']),
                'link' => 'https://www.instagram.com/p/' . $item['node']['shortcode'],
                'location' => array(
                    'name' => $item['node']['location']['name']
                ),
                'videos' => array( // unavailable
                    'standard_resolution' => array(
                        'url' => '',
                    ),
                    'low_bandwidth' => array(
                        'url' => '',
                    ),
                    'low_resolution' => array(
                        'url' => '',
                    ),
                ),
                'carousel_media' => array(), // unavailable
            );
            array_push($response['data'], $new_item);
        }
    }
    
    if (!empty($response['data'])) {
        foreach($response['data'] as $row) {
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