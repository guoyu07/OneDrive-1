<?php
require_once('class.exodAuth.php');

/**
 * Class exodAuthPublic
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodAuthPublic extends exodAuth {

	/**
	 * @return string
	 */
	protected function generateAuthUrl() {
		$base = parent::generateAuthUrl();
		$scopes = array(
			'wl.signin',
			'wl.basic',
			'wl.offline_access',
			//			'wl.skydrive_update',
			'onedrive.appfolder',
		);

		return $base . '&scope=' . implode('%20', $scopes);
	}


	/**
	 * @throws ilCloudException
	 */
	protected function loadToken() {
		$this->response->loadFromRequest(array( 'code', 'error', 'err' ));
		if ($this->response->getCode()) {
			$this->exod_app->buildURLs();
			$exodCurl = new exodCurl();
			$exodCurl->setUrl($this->exod_app->getTokenUrl());
			$exodCurl->setContentType(exodCurl::X_WWW_FORM_URL_ENCODED);

			$exodCurl->addPostField('code', $this->response->getCode());
			$exodCurl->addPostField('client_id', $this->exod_app->getClientId());
			$exodCurl->addPostField('redirect_uri', $this->exod_app->getRedirectUri());
			$exodCurl->addPostField('grant_type', 'authorization_code');
			$exodCurl->addPostField('client_secret', $this->getExodApp()->getClientSecret());
			$exodCurl->addPostField('resource', $this->exod_app->getRessourceUri());

			$exodCurl->post();

			$this->response->loadFromResponse($exodCurl->getResponseBody());

			$exodBearerToken = new exodBearerToken();
			$exodBearerToken->setAccessToken($this->getResponse()->getAccessToken());
			$exodBearerToken->setRefreshToken($this->getResponse()->getRefreshToken());
			$exodBearerToken->setValidThrough($this->getResponse()->getExpiresOn());
			$this->setExodBearerToken($exodBearerToken);
		} else {
			throw new ilCloudException(ilCloudException::UNKNONW_EXCEPTION, 'No Code received');
		}
	}
}

