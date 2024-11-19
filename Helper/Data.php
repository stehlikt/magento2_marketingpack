<?php

namespace Railsformers\MarketingPack\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use GuzzleHttp\Client;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\HTTP\Header;
use Magento\Framework\UrlInterface;

class Data extends AbstractHelper
{
    const XML_PATH_CURRENCY = 'currency/options/default';

    protected $httpClient;
    protected $customerSession;
    protected $remoteAddress;
    protected $httpHeader;
    protected $urlBuilder;

    public function __construct(
        Context $context,
        Client $httpClient,
        CustomerSession $customerSession,
        RemoteAddress $remoteAddress,
        Header $httpHeader,
        UrlInterface $urlBuilder
    ) {
        parent::__construct($context);
        $this->httpClient = $httpClient;
        $this->customerSession = $customerSession;
        $this->remoteAddress = $remoteAddress;
        $this->httpHeader = $httpHeader;
        $this->urlBuilder = $urlBuilder;
    }

    public function sendConversionEvent($eventName, $eventData, $customerData = array(), $actionSource = 'website')
    {
        $accessToken = $this->getMetaApi();
        $pixelId = $this->getMetaId();
        $eventSourceUrl = $this->urlBuilder->getCurrentUrl();

        if ($accessToken && $pixelId) {
            $userData = $this->getUserData($pixelId);

            if (isset($customerData['em'])) {
                $userData['em'] = $customerData['em'];
            }

            if (isset($customerData['ph'])) {
                $userData['ph'] = $customerData['ph'];
            }

            $url = "https://graph.facebook.com/v21.0/$pixelId/events";
            $data = [
                'data' => [
                    [
                        'event_name' => $eventName,
                        'event_time' => time(),
                        'action_source' => $actionSource,
                        'event_source_url' => $eventSourceUrl,
                        'user_data' => $userData,
                        'custom_data' => $eventData
                    ]
                ],
                'access_token' => $accessToken
            ];
            
            try {
                $this->httpClient->post($url, ['json' => $data]);
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * @return array|string[]
     */
    private function getUserData($pixelId)
    {
        $userData = [
            'client_ip_address' => $this->remoteAddress->getRemoteAddress(),
            'client_user_agent' => $this->httpHeader->getHttpUserAgent()
        ];

        if (isset($_COOKIE['_fbp'])) {
            $userData['fbp'] = $_COOKIE['_fbp'];
        }

        if (isset($_COOKIE['_fbc'])) {
            $userData['fbc'] = $_COOKIE['_fbc'];
        } elseif (isset($pixelId)) {
            $userData['fbc'] = 'fb.1.' . $_SERVER['REQUEST_TIME'] . '.' . $pixelId;
        }

        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            $userData = array_merge($userData, [
                'em' => hash('sha256', $customer->getEmail()),
                'ph' => hash('sha256', $customer->getTelephone()),
                'fn' => hash('sha256', $customer->getFirstname()),
                'ln' => hash('sha256', $customer->getLastname()),
                'ct' => hash('sha256', $customer->getCity()),
                'st' => hash('sha256', $customer->getRegion()),
                'zp' => hash('sha256', $customer->getPostcode()),
                'country' => hash('sha256', $customer->getCountryId())
            ]);
        }

        return $userData;
    }

    /**
     * Get Google Analytics ID
     *
     * @param string $scope
     * @return mixed
     */
    public function getGoogleAnalyticsId($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/google_analytics/gtag_id', $scope);
    }

    /**
     *  Get Google Ads ID
     * @param $scope
     * @return mixed
     */
    public function getGoogleAdsId($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/google_ads/gads_id', $scope);
    }

    /**
     *   Get Google Ads Conversion Label
     * @param $scope
     * @return mixed
     */
    public function getGoogleAdsConversionLabel($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/google_ads/gads_conversion_label', $scope);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function getSklikRetargetingId($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/seznam_zobzi/sklik_retargeting_id', $scope);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function getSklikCOnversionId($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/seznam_zobzi/sklik_conversion_id', $scope);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function getZboziSecretKey($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/seznam_zobzi/zbozi_secret_key', $scope);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function getZboziPlaceId($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/seznam_zobzi/zbozi_place_id', $scope);
    }

    public function getZboziType($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/seznam_zobzi/zbozi_type', $scope);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function getMetaId($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/meta/meta_id', $scope);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function getMetaApi($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/meta/meta_api', $scope);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function getHeurekaId($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/heureka/heureka_id', $scope);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function getOverenozakazniky($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/heureka/heureka_overeno_key', $scope);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function getClarityId($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('railsformers_marketing_pack/microsoft/clarity_id', $scope);
    }

    /**
     * Get the default currency code
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getCurrencyCode($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CURRENCY, ScopeInterface::SCOPE_STORE, $store);
    }
}
