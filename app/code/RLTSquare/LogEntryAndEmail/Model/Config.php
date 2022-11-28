<?php
namespace RLTSquare\LogEntryAndEmail\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 */
class Config
{
	const RLTSQUARE_RECEIVER_EMAIL = 'email_settings/basic_configrations/receiver_email';
	const RLTSQUARE_SENDER_EMAIL   = 'email_settings/basic_configrations/sender_email';
	const RLTSQUARE_SENDER_NAME    = 'email_settings/basic_configrations/sender_name';
	const RLTSQUARE_EMAIL_TEMPLATE = 'email_settings/basic_configrations/template';

	/**
     * Config constructor.
     *
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
	public function __construct(
		ScopeConfigInterface  $scopeConfig,
 		StoreManagerInterface $storeManager
	){
		$this->scopeConfig = $scopeConfig;
 		$this->storeManager = $storeManager;
	}

	/**
     * @return string|null
     */
    public function getReceiverEmail() {
		return $this->scopeConfig->getValue(
			Self::RLTSQUARE_RECEIVER_EMAIL, 
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE, 
			$this->storeManager->getStore()->getStoreId()
		);
	}

	/**
     * @return string|null
     */
	public function getSenderEmail() {
		return $this->scopeConfig->getValue(
			Self::RLTSQUARE_SENDER_EMAIL, 
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE, 
			$this->storeManager->getStore()->getStoreId()
		);
	}

	/**
     * @return string|null
     */
	public function getSenderName() {
		return $this->scopeConfig->getValue(
			Self::RLTSQUARE_SENDER_NAME, 
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE, 
			$this->storeManager->getStore()->getStoreId()
		);
	}

	/**
     * @return string|null
     */
	public function getEmailTemplate() {
		return $this->scopeConfig->getValue(
			Self::RLTSQUARE_EMAIL_TEMPLATE, 
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE, 
			$this->storeManager->getStore()->getStoreId()
		);
	}
}