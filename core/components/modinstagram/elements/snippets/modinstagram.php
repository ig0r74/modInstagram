<?php
$access_token = $modx->getOption('access_token', $scriptProperties, $modx->getOption('modinstagram_acess_token'));
$tplWrapper = $modx->getOption('tplWrapper', $scriptProperties, false);
$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.modInstagram.item');
$limit = $modx->getOption('limit', $scriptProperties, false);
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);
$showLog = $modx->getOption('showLog', $scriptProperties, false);
$maxId = $modx->getOption('maxId', $scriptProperties, false);
$minId = $modx->getOption('minId', $scriptProperties, false);

// MAX_ID   -	Return media earlier than this max_id.
// MIN_ID   -	Return media later than this min_id.

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
    return false;
}

$output = '';

$response = json_decode($response, true);

if (!empty($response['data'])) {
    foreach($response['data'] as $row) {
        $output .= $pdo->getChunk($tpl, array(
            'id' => $row['id'],
            'thumbnail' => $row['images']['thumbnail']['url'],
            'low_resolution' => $row['images']['low_resolution']['url'],
            'standard_resolution' => $row['images']['standard_resolution']['url'],
            'created_time' => $row['created_time'],
            'caption_text' => $row['caption']['text'],
            'likes_count' => $row['likes']['count'],
            'comments_count' => $row['comments']['count'],
            'type' => $row['type'],
            'link' => $row['link'],
        ));
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