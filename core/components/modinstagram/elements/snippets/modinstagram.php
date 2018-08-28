<?php
$access_token = $modx->getOption('accessToken', $scriptProperties, $modx->getOption('modinstagram_acess_token'));
$tplWrapper = $modx->getOption('tplWrapper', $scriptProperties, false);
$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.modInstagram.item');
$limit = $modx->getOption('limit', $scriptProperties, 20);
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);
$showLog = $modx->getOption('showLog', $scriptProperties, false);
$maxId = $modx->getOption('maxId', $scriptProperties, false);
$minId = $modx->getOption('minId', $scriptProperties, false);

if (!$access_token) {
    $modx->log(MODX_LOG_LEVEL_ERROR, 'No ACCESS TOKEN');
    return '';
}

$pdo = $modx->getService('pdoTools');

$pdo->addTime('pdoTools loaded');

$query = array(
    'access_token' => $access_token,
    'count' => $limit,
    'max_id' => $maxId,
    'min_id' => $minId
);

$response = file_get_contents('https://api.instagram.com/v1/users/self/media/recent/?' . http_build_query($query));

if ($http_response_header[0] != 'HTTP/1.1 200 OK') {
    $modx->log(MODX_LOG_LEVEL_ERROR, 'modInstagram error: ' . $http_response_header[0]);
    return false;
}

$output = '';

$response = json_decode($response, true);

$idx = 1;

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
        ));
        $idx++;
    }
} else {
    return false;
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