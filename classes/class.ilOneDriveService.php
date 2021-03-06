<?php
require_once("./Modules/Cloud/classes/class.ilCloudPluginService.php");
require_once('./Modules/Cloud/exceptions/class.ilCloudException.php');
require_once("./Modules/Cloud/classes/class.ilCloudUtil.php");
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Client/class.exodClientFactory.php');

/**
 * Class ilExampleCloudService
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilOneDriveService extends ilCloudPluginService {

	/**
	 * @return exodAppBusiness
	 */
	public function getApp() {
		return $this->getPluginObject()->getExodApp();
	}


	/**
	 * @return exodClientBusiness|exodClientPublic
	 */
	public function getClient() {
		return $this->getApp()->getExodClient();
	}


	/**
	 * @return exodAuth
	 */
	public function getAuth() {
		return $this->getApp()->getExodAuth();
	}


	/**
	 * @param string $callback_url
	 */
	public function authService($callback_url = "") {
		$this->getAuth()->authenticate(htmlspecialchars_decode($callback_url));
	}


	public function afterAuthService() {
		$exodAuth = $this->getApp()->getExodAuth();
		$exodAuth->loadTokenFromSession();
		$this->getPluginObject()->storeToken($exodAuth->getExodBearerToken());
		//		return true;
		$ilObjCloud = $this->getPluginObject()->getCloudModulObject();
		//		$rootFolder = '/ILIASCloud/' . ltrim($ilObjCloud->getRootFolder(), '/');
		$rootFolder = $ilObjCloud->getRootFolder();
		//		var_dump($rootFolder); // FSX
		//		exit;
		//		$ilObjCloud->setRootFolder($rootFolder);
		//		$ilObjCloud->update();
		if (!$this->getClient()->folderExists($rootFolder)) {
			$this->createFolder($rootFolder);
		}

		return true;
	}


	/**
	 * @param ilCloudFileTree $file_tree
	 * @param string $parent_folder
	 *
	 * @throws Exception
	 */
	public function addToFileTree(ilCloudFileTree $file_tree, $parent_folder = "/") {
		try {
			$exodFiles = $this->getClient()->listFolder($parent_folder);

			foreach ($exodFiles as $item) {
				$size = ($item instanceof exodFile) ? $size = $item->getSize() : null;
				$is_Dir = $item instanceof exodFolder;
				$path = end(explode(':', $item->getFullPath()));
				$file_tree->addNode($path, $item->getId(), $is_Dir, strtotime($item->getDateTimeLastModified()), $size);
			}
			//		$file_tree->clearFileTreeSession();
		} catch (Exception $e) {
			$this->getPluginObject()->getCloudModulObject()->setAuthComplete(false);
			$this->getPluginObject()->getCloudModulObject()->update();
			throw $e;
		}
	}


	/**
	 * @param null $path
	 * @param ilCloudFileTree $file_tree
	 */
	public function getFile($path = null, ilCloudFileTree $file_tree = null) {
		$this->getClient()->deliverFile($path);
	}


	/**
	 * @param                 $file
	 * @param                 $name
	 * @param string $path
	 * @param ilCloudFileTree $file_tree
	 *
	 * @return mixed
	 */
	public function putFile($file, $name, $path = '', ilCloudFileTree $file_tree = null) {
		$path = ilCloudUtil::joinPaths($file_tree->getRootPath(), $path);
		if ($path == '/') {
			$path = '';
		}

		$return = $this->getClient()->uploadFile($path . "/" . $name, $file);

		return $return;
	}


	/**
	 * @param null $path
	 * @param ilCloudFileTree $file_tree
	 *
	 * @return bool
	 */
	public function createFolder($path = null, ilCloudFileTree $file_tree = null) {
		if ($file_tree instanceof ilCloudFileTree) {
			$path = ilCloudUtil::joinPaths($file_tree->getRootPath(), $path);
		}

		if ($path != '/') {
			$this->getClient()->createFolder($path);
		}

		return true;
	}


	/**
	 * @param null $path
	 * @param ilCloudFileTree $file_tree
	 *
	 * @return bool
	 */
	public function deleteItem($path = null, ilCloudFileTree $file_tree = null) {
		//		throw new ilCloudException(-1, print_r($file_tree, true));
		$path = ilCloudUtil::joinPaths($file_tree->getRootPath(), $path);

		return $this->getClient()->delete($path);
	}


	/**
	 * @return ilOneDrive
	 */
	public function getPluginObject() {
		return parent::getPluginObject();
	}


	/**
	 * @return ilOneDrivePlugin
	 */
	public function getPluginHookObject() {
		return parent::getPluginHookObject();
	}
}
