<?php
require_once('./Modules/Cloud/classes/class.ilCloudPluginConfig.php');

/**
 * Class exodConfig
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodConfig extends ilCloudPluginConfig {

	public function __construct() {
		parent::__construct('cld_cldh_exod_config');
	}


	const F_CLIENT_ID = 'client_id';
	const F_CLIENT_SECRET = 'client_secret';
	const F_TENANT_NAME = 'tenant_name';
	const F_TENANT_ID = 'tenant_id';
	const F_IP_RESOLVE_V_4 = 'ip_resolve_v4';
	const F_CLIENT_TYPE = 'client_type';
	const F_SSL_VERSION = 'ssl_version';
	/**
	 * @var array
	 */
	protected static $value_cache = array();


	/**
	 * @throws ilCloudException
	 */
	public function checkComplete() {
		if (! $this->getClientType()) {
			throw new ilCloudException(- 1, 'Configuration of OneDrive inclomplete. Please contact your system administrator');
		}
		switch ($this->getClientType()) {
			case exodApp::TYPE_BUSINESS:
				if (! $this->getClientId() || ! $this->getClientSecret() || ! $this->getTenantName() || ! $this->getTentantId()) {
					throw new ilCloudException(- 1, 'Configuration of OneDrive inclomplete. Please contact your system administrator');
				}
				break;
			case exodApp::TYPE_PUBLIC:
				if (! $this->getClientId() || ! $this->getClientSecret()) {
					throw new ilCloudException(- 1, 'Configuration of OneDrive inclomplete. Please contact your system administrator');
				}
				break;
		}

		return true;
	}


	/**
	 * @return string
	 * @throws ilCloudPluginConfigException
	 */
	public function getClientId() {
		return $this->getValue(self::F_CLIENT_ID);
	}


	/**
	 * @return string
	 * @throws ilCloudPluginConfigException
	 */
	public function getClientSecret() {
		return $this->getValue(self::F_CLIENT_SECRET);
	}


	/**
	 * @return string
	 * @throws ilCloudPluginConfigException
	 */
	public function getTenantName() {
		return $this->getValue(self::F_TENANT_NAME);
	}


	/**
	 * @return string
	 * @throws ilCloudPluginConfigException
	 */
	public function getTentantId() {
		return $this->getValue(self::F_TENANT_ID);
	}


	/**
	 * @return bool
	 * @throws ilCloudPluginConfigException
	 */
	public function getResolveIpV4() {
		return (bool)$this->getValue(self::F_IP_RESOLVE_V_4);
	}


	/**
	 * @return int
	 */
	public function getClientType() {
		return $this->getValue(self::F_CLIENT_TYPE);
	}


	/**
	 * @return int
	 */
	public function getSSLVersion() {
		return $this->getValue(self::F_SSL_VERSION);
	}


	/**
	 * @param $key
	 *
	 * @return bool|string
	 * @throws ilCloudPluginConfigException
	 */
	public function getValue($key) {
		if (! isset(self::$value_cache[$key])) {
			self::$value_cache[$key] = parent::getValue($key);
		}

		return self::$value_cache[$key];
	}


	/**
	 * @param $key
	 * @param $value
	 *
	 * @throws ilCloudPluginConfigException
	 */
	public function setValue($key, $value) {
		unset(self::$value_cache[$key]);
		parent::setValue($key, $value); // TODO: Change the autogenerated stub
	}
}

?>