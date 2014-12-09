<?php

/**
 * 
 *
 * @author <marcus@silverstripe.com.au>
 * @license BSD License http://www.silverstripe.org/bsd-license
 */
class VuforiaUploadField extends UploadField {
	
	protected $accessKey;
	protected $secretKey;
	
	/**
	 *
	 * @var VuforiaAPIService
	 */
	public $vuforiaAPIService;
	
	public function __construct($name, $title = null, \SS_List $items = null) {
		parent::__construct($name, $title, $items);
	}
	
	public function setAccessKey($key) {
		$this->accessKey = $key;
		return $this;
	}
	
	public function setSecretKey($key) {
		$this->secretKey = $key;
		return $this;
	}
	
	public function getRelationAutosetClass($default = 'File') {
		return 'VuforiaImage';
	}
	
	public function saveTemporaryFile($tmpFile, &$error = null) {
		$file = parent::saveTemporaryFile($tmpFile, $error);
		return $file;
	}
	
	public function Field($properties = array()) {
		$items = $this->getItems();
		
		$unprocessed = false;
		
		$messages = array();
		
		// check that all the items have been uploaded
		foreach ($items as $vFile) {
			if ($vFile->ProcessStatus != VuforiaImage::COMPLETE_STATUS) {
				// do a status check
				$update = $this->vuforiaAPIService->checkImageStatus($vFile);
				if ($update->ProcessStatus == VuforiaImage::PROCESS_STATUS) {
					$unprocessed = true;
				}
				if ($update->ProcessStatus == VuforiaImage::ERROR_STATUS) {
					$messages[] = $update->Messages;
				}
			}
		}

//		
//		if (count($messages)) {
//			$field .= '<div class="vuforia-messages" style="padding: 3px; color: #f33">' . implode('<br/>', $messages) . '</div>';
//		}
//		
		$field = parent::Field($properties);
		return $field;
	}
}
