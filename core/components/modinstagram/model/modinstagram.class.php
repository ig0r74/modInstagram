<?php

class modInstagram
{
    /** @var modX $modx */
    public $modx;

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        $corePath = MODX_CORE_PATH . 'components/modinstagram/';
        $assetsUrl = MODX_ASSETS_URL . 'components/modinstagram/';

        $this->config = array_merge([
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',

            'connectorUrl' => $assetsUrl . 'connector.php',
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
        ], $config);

        $this->modx->addPackage('modinstagram', $this->config['modelPath']);
        $this->modx->lexicon->load('modinstagram:default');
    }

	public function getData($url) {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        //verify https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch); 
        
        $output = array();
        $output['result'] = $response;
        
        if ($response === false) {
            $output['error'] = curl_error($ch);
        }
        if ($info['http_code'] != 200) {
            $output['error'] = $info['http_code'];
        }
        return $output;
	}

	public function getScraperData($username, $limit, $password, $json) {
	    require_once $this->config['corePath'] . 'vendor/autoload.php';
        
        try{
            if ($password) {
                $instagram = \InstagramScraper\Instagram::withCredentials(
                    $username,
                    $password,
                    new Phpfastcache\Helper\Psr16Adapter('Files',
                        new Phpfastcache\Config\ConfigurationOption(['defaultTtl' => 43200]) // Auth cache 5 days
                    )
                );
                $instagram->login();
    	    } else {
                $instagram = new \InstagramScraper\Instagram();
    	    }
    	    if (!empty($this->modx->getOption('modinstagram_proxy_address'))) {
                Instagram::setProxy([
                    'address' => $this->modx->getOption('modinstagram_proxy_address'),
                    'port'    => $this->modx->getOption('modinstagram_proxy_port'),
                    'tunnel'  => $this->modx->getOption('modinstagram_proxy_tunnel'),
                    'timeout' => $this->modx->getOption('modinstagram_proxy_timeout'),
                    'auth' => [
                        'user' => $this->modx->getOption('modinstagram_proxy_user'),
                        'pass' => $this->modx->getOption('modinstagram_proxy_pass'),
                        'method' => $this->modx->getOption('modinstagram_proxy_method'),
                    ],
                ]);
    	    }
    	    
            if ($json) {
                $medias = $instagram->getMediasFromFeed($username, $limit ?: 20);
            } else {
                $medias = $instagram->getMedias($username, $limit ?: 20);
            }
        }
        
        catch (Exception $ex) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR, 'modInstagram scraper error: ' . $ex->getMessage());
            return false;
        }
        
        $output = array();
        
        if (is_array($medias)) {
            $output['result'] = $medias;
        } else {
            $output['error'] = $medias;
        }
        return $output;
	}
}