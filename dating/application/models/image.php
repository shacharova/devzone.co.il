<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Image extends App_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Delete image by ID (if exists).<br />
     *  Image wiil not belong anymore to albums.
     * @param int $imageId Image ID.
     * @return \App_DBSPResult
     */
    public function deleteOne($imageId) {
        return $this->app_db->callSP('dating', 'deleteImage', true, array($imageId), array('rowCount'));
    }

    /**
     * Set (Insert|Update) image record on images table.
     * @param string $imagePath
     * @param string $imageTitle
     * @param string $imageDescription
     * @return \App_DBSPResult
     */
    public function setOne($imagePath, $imageTitle, $imageDescription) {
        return $this->app_db->callSP('dating', 'setImage', true, array($imagePath, $imageTitle, $imageDescription), array('rowCount'));
    }

}
