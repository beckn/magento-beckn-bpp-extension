<?php

namespace Beckn\Core\Model;

use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\ScopeInterface;
use Beckn\Core\Api\Data\BecknLookupInterface;
use Beckn\Core\Model\ResourceModel\BecknLookup\CollectionFactory as LookupCollectionFactory;

/**
 * Class DigitalSignature
 * @author Indglobal
 * @package Beckn\Core\Model
 */
class DigitalSignature
{
    const SIGN_PUBLIC_KEY_PATH = 'security_config/security/signing_public_key';
    const SIGN_PRIVATE_KEY_PATH = 'security_config/security/signing_private_key';
    const ENCRYPTION_PUBLIC_KEY_PATH = 'security_config/security/encryption_public_key';
    const ENCRYPTION_PRIVATE_KEY_PATH = 'security_config/security/encryption_private_key';
    const XML_PATH_SUBSCRIBER_ID = "subscriber_config/subscriber/subscriber_id";
    const XML_UNIQUE_KEY_ID = "security_config/security/unique_key_id";
    const XML_PATH_SECURITY_REGISTRY_URL = "security_config/security/url";
    const REGISTRY_LOOKUP = "/lookup";

    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $_configInterface;
    /**
     * @var TypeListInterface
     */
    protected $_cacheTypeList;
    /**
     * @var Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Curl
     */
    protected $_curl;

    /**
     * @var \Beckn\Core\Model\BecknLookupFactory
     */
    protected $_becknLookupFactory;

    /**
     * @var LookupCollectionFactory
     */
    protected $_lookupCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * DigitalSignature constructor.
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Curl $curl
     * @param \Beckn\Core\Model\BecknLookupFactory $becknLookupFactory
     * @param LookupCollectionFactory $lookupCollectionFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Curl $curl,
        \Beckn\Core\Model\BecknLookupFactory $becknLookupFactory,
        LookupCollectionFactory $lookupCollectionFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_configInterface = $configInterface;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_scopeConfig = $scopeConfig;
        $this->_curl = $curl;
        $this->_becknLookupFactory = $becknLookupFactory;
        $this->_lookupCollectionFactory = $lookupCollectionFactory;
        $this->_logger = $logger;
    }

    /**
     * @param string $path
     * @param string $scope
     * @param int $storeId
     * @return mixed
     */
    public function getConfigData($path, $scope = ScopeInterface::SCOPE_STORE, $storeId = 0)
    {
        return $this->_scopeConfig->getValue($path, $scope, $storeId);
    }

    /**
     * @param string $type
     * @return bool
     * @throws \SodiumException
     */
    public function generateKeyPair($type)
    {
        try {
            switch ($type) {
                case "signing":
                    $this->generateSigningkeys();
                    break;
                case "encryption":
                    $this->generateEncryptionKeys();
                    break;
            }
            return true;
        } catch (\SodiumException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * @return bool|string
     */
    private function generateSigningKeys()
    {
        try {
            $cryptoSignkeyPair = $this->crypto_sign_keypair();
            $alice_sign_secretkey = \Sodium_crypto_sign_secretkey($cryptoSignkeyPair);
            $alice_sign_publickey = \Sodium_crypto_sign_publickey($cryptoSignkeyPair);
            $secretKey = sodium_bin2base64($alice_sign_secretkey, SODIUM_BASE64_VARIANT_ORIGINAL);
            $publicKey = sodium_bin2base64($alice_sign_publickey, SODIUM_BASE64_VARIANT_ORIGINAL);
            $this->saveConfigData(self::SIGN_PUBLIC_KEY_PATH, $publicKey);
            $this->saveConfigData(self::SIGN_PRIVATE_KEY_PATH, $secretKey);
            $this->flushCache();
        } catch (\SodiumException $ex) {
            return $ex->getMessage();
        }
        return true;
    }

    /**
     * @return bool|string
     */
    private function generateEncryptionKeys()
    {
        try {
            $cryptoBoxKeyPair = \Sodium_crypto_box_keypair();
            $aliceSecretkey = \Sodium_crypto_box_secretkey($cryptoBoxKeyPair);
            $alicePublickey = \Sodium_crypto_box_publickey($cryptoBoxKeyPair);
            $secretKey = sodium_bin2base64($aliceSecretkey, SODIUM_BASE64_VARIANT_ORIGINAL);
            $publicKey = sodium_bin2base64($alicePublickey, SODIUM_BASE64_VARIANT_ORIGINAL);
            $this->saveConfigData(self::ENCRYPTION_PUBLIC_KEY_PATH, $publicKey);
            $this->saveConfigData(self::ENCRYPTION_PRIVATE_KEY_PATH, $secretKey);
            $this->flushCache();
        } catch (\SodiumException $ex) {
            return $ex->getMessage();
        }
        return true;
    }

    /**
     * @return mixed
     */
    private function getPrivateKey()
    {
        return $this->getConfigData(self::SIGN_PRIVATE_KEY_PATH);
    }

    /**
     * @return mixed
     */
    private function getPublicKey()
    {
        return $this->getConfigData(self::SIGN_PUBLIC_KEY_PATH);
    }

    /**
     * @return mixed
     */
    public function getSubscriberId()
    {
        $path = self::XML_PATH_SUBSCRIBER_ID;
        return $this->getConfigData($path);
    }

    /**
     * @return string
     * @throws \SodiumException
     */
    public function crypto_sign_keypair(): string
    {
        $alice_sign_kp = \Sodium_crypto_sign_keypair();
        return $alice_sign_kp;
    }


    /**
     * @param $path
     * @param $value
     * @return mixed
     */
    public function saveConfigData($path, $value)
    {
        return $this->_configInterface->saveConfig($path, $value, $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
    }

    /**
     * @param string $body
     * @return array
     */
    public function createAuthorization(string $body): array
    {
        $authResponse = [
            "success" => false,
            "message" => "",
            "auth" => "",
        ];
        try {
//            $digestSize = 64; // Blake hash 512
//            $digest = \Sodium_crypto_generichash($body, "", $digestSize);
//            $digest = bin2hex($digest);
//            $blake512 = base64_encode(pack('H*', $digest));
            //$signing_string = "(created): " . $created . "\n(expires): " . $expires . "\ndigest: BLAKE-512=" . $blake512;
            $created = time();
            $expires = time() + 36000;
            $signing_string = $this->createSigningString($body, $created, $expires);
            $privateKey = \Sodium_base642bin($this->getPrivateKey(), SODIUM_BASE64_VARIANT_ORIGINAL);
            $publicKey = \Sodium_base642bin($this->getPublicKey(), SODIUM_BASE64_VARIANT_ORIGINAL);
            $signature = \Sodium_crypto_sign_detached($signing_string, $privateKey);
            $subscriberId = $this->getSubscriberId();
            $headers = "(created) (expires) digest";
            $signature = \Sodium_bin2base64($signature, SODIUM_BASE64_VARIANT_ORIGINAL);
            $uniqueKeyId = $this->getConfigData(self::XML_UNIQUE_KEY_ID);
            $authorizationHeader = 'Signature keyId="' . $subscriberId . '|'.$uniqueKeyId.'|ed25519",algorithm="ed25519",created="' . $created . '",expires="' . $expires . '",headers="' . $headers . '",signature="' . $signature . '"';
            $authResponse["auth"] = $authorizationHeader;
            $authResponse["success"] = true;
        } catch (\SodiumException $e) {
            $authResponse["success"] = false;
            $authResponse["message"] = __($e->getMessage());
        }
        return $authResponse;
    }


    /**
     * @return array
     * @throws \SodiumException
     */
    private function getEncryptionSecretkeyAndPublickey()
    {
        $configSecretKey = $this->getConfigData(self::ENCRYPTION_PRIVATE_KEY_PATH);
        $configPublicKey = $this->getConfigData(self::ENCRYPTION_PUBLIC_KEY_PATH);
        $secretKey = \Sodium_base642bin($configSecretKey, SODIUM_BASE64_VARIANT_ORIGINAL);
        $publicKey = \Sodium_base642bin($configPublicKey, SODIUM_BASE64_VARIANT_ORIGINAL);
        return [
            "secret_key" => $secretKey,
            "public_key" => $publicKey
        ];
    }

    /**
     * @return string
     * @throws \SodiumException
     */
    public function getSodiumCryptoBoxKeypairFromSecretkeyAndPublickey()
    {
        try {
            $keys = $this->getEncryptionSecretkeyAndPublickey();
            return \Sodium_crypto_box_keypair_from_secretkey_and_publickey($keys["secret_key"], $keys["public_key"]);
        } catch (\SodiumException $ex) {
            throw new \SodiumException('Unable to generate key pair. ' . $ex->getMessage());
        }
    }

    /**
     * Flush Cache
     */
    public function flushCache()
    {
        $types = array('config', 'layout', 'block_html', 'collections', 'reflection', 'db_ddl', 'eav', 'config_integration', 'config_integration_api', 'full_page', 'translate', 'config_webservice');

        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }

    /**
     * @param $authData
     * @param $body
     * @return bool
     * @throws \SodiumException
     */
    public function validateAuth($authData, $body)
    {
        try{
            $keyId = $this->getDataFromAuth($authData, "keyId");
            $subscriberId = $this->getSubscriberIdFromAuth($keyId);
            $publicKey = $this->getSigningPublicKeyFromLookup($subscriberId);
            $publicKey = \Sodium_base642bin($publicKey, SODIUM_BASE64_VARIANT_ORIGINAL);
            $created = $this->getDataFromAuth($authData, "created");
            $expires = $this->getDataFromAuth($authData, "expires");
            $signing_string = $this->createSigningString($body, $created, $expires);
            $signature = \Sodium_base642bin($this->getSignatureFromAuth($authData), SODIUM_BASE64_VARIANT_ORIGINAL);
            return \Sodium_crypto_sign_verify_detached($signature, $signing_string, $publicKey);
        }
        catch (\SodiumException $ex) {
            $this->_logger->info($ex->getMessage());
            return false;
        }
    }

    /**
     * @param $keyId
     * @return mixed|string
     */
    public function getSubscriberIdFromAuth($keyId)
    {
        $keyData = explode("|", $keyId);
        return $keyData[0] ?? "";
    }

    /**
     * @param $body
     * @param string $created
     * @param string $expires
     * @return string
     * @throws \SodiumException
     */
    public function createSigningString($body, $created = "", $expires = "")
    {
        $digestSize = 64; // 64 (512-bit), 48 (384-bit), 32 (256-bit), 28 (224-bit)
        $digest = sodium_crypto_generichash($body, "", $digestSize);
        $digest = bin2hex($digest);
        $blake512 = base64_encode(pack('H*', $digest));
        $created = ($created == "") ? time() : $created;
        $expires = ($expires == "") ? time() + 36000 : $expires;
        return "(created): " . $created . "\n(expires): " . $expires . "\ndigest: BLAKE-512=" . $blake512;
    }

    /**
     * @param $authData
     * @return string|string[]
     */
    public function getSignatureFromAuth($authData)
    {
        $header = str_replace("Signature ", "", $authData);
        $authArray = array_filter(explode(',', $header), 'strlen');
        $signatureData = end($authArray);
        $parseSignature = array_filter(explode('signature="', $signatureData), 'strlen');
        $finalSignature = $parseSignature[1] ?? "";
        $signature = str_replace('"', "", $finalSignature);
        $this->_logger->info("Signature from Auth => ".$signature);
        return $signature;
    }

    /**
     * @param $authData
     * @param $type
     * @return string|string[]
     */
    public function getDataFromAuth($authData, $type)
    {
        $header = str_replace("Signature ", "", $authData);
        $authArray = array_filter(explode(',', $header), 'strlen');
        $date = "";
        foreach ($authArray as $eachItem) {
            $item = array_filter(explode('=', $eachItem), 'strlen');
            $key = $item[0] ?? "";
            $value = $item[1] ?? "";
            if ($key == $type) {
                $date = str_replace('"', "", $value);
            }
        }
        return $date;
    }

    /**
     * @param $subscriberId
     * @return string|null
     */
    public function getSigningPublicKeyFromLookup($subscriberId)
    {
        /**
         * @var \Beckn\Core\Model\ResourceModel\BecknLookup\Collection $lookupCollection
         * @var \Beckn\Core\Model\BecknLookup $becknLookup
         */
        $lookupCollection = $this->_lookupCollectionFactory->create();
        $becknLookup = $lookupCollection->addFieldToFilter("subscriber_id", $subscriberId)
            ->setOrder("entity_id")->getFirstItem();
        if ($becknLookup->getEntityId()) {
            $todayDate = date('Y-m-d');
            $todayDate = date('Y-m-d', strtotime($todayDate));
            $validFrom = date('Y-m-d', strtotime($becknLookup->getValidFrom()));
            $validUntil = date('Y-m-d', strtotime($becknLookup->getValidUntil()));
            if (($todayDate >= $validFrom) && ($todayDate <= $validUntil)) {
                $signingPublicKey = $becknLookup->getSigningPublicKey();
            } else {
                $signingPublicKey = $this->saveLookup($subscriberId);
            }
        } else {
            $signingPublicKey = $this->saveLookup($subscriberId);
        }
        return $signingPublicKey;
    }

    /**
     * @param $subscriberId
     * @return mixed|string
     */
    public function saveLookup($subscriberId)
    {
        $signingPublicKey = "";
        $url = $this->getConfigData(self::XML_PATH_SECURITY_REGISTRY_URL);
        $apiUrl = $url . self::REGISTRY_LOOKUP;
        $this->_curl->addHeader('content-type', 'application/json');
        $postBody = [
            "subscriber_id" => $subscriberId
        ];
        $this->_curl->post($apiUrl, json_encode($postBody));
        $response = $this->_curl->getBody();
        if (!empty($response)) {
            $data = json_decode($response, true);
            foreach ($data as $_data) {
                $signingPublicKey = $_data["signing_public_key"];
                $saveData = [
                    BecknLookupInterface::COUNTRY => $_data["country"] ?? "",
                    BecknLookupInterface::CITY => $_data["city"] ?? "",
                    BecknLookupInterface::CREATED_AT => $_data["created"] ?? "",
                    BecknLookupInterface::VALID_FROM => $_data["valid_from"] ?? "",
                    BecknLookupInterface::TYPE => $_data["type"] ?? "",
                    BecknLookupInterface::SIGNING_PUBLIC_KEY => $_data["signing_public_key"] ?? "",
                    BecknLookupInterface::SUBSCRIBER_ID => $_data["subscriber_id"] ?? "",
                    BecknLookupInterface::VALID_UNTIL => $_data["valid_until"] ?? "",
                    BecknLookupInterface::SUBSCRIBER_URL => $_data["subscriber_url"] ?? "",
                    BecknLookupInterface::DOMAIN => $_data["domain"] ?? "",
                    BecknLookupInterface::ENCR_PUBLIC_KEY => $_data["encr_public_key"] ?? "",
                    BecknLookupInterface::UPDATED_AT => $_data["updated"] ?? "",
                    BecknLookupInterface::STATUS => $_data["status"] ?? "",
                ];
                $model = $this->_becknLookupFactory->create();
                $model->setData($saveData)->save();
            }
        }
        return $signingPublicKey;
    }
}
