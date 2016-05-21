<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Album extends App_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Trying to add an exists image to exists album bu their ids.
     * @param int $imageId Exists image ID.
     * @param int $albumId Exists album ID.
     * @return boolean True if succeeded to add image to album. Else false.
     */
    public function addImage($imageId, $albumId) {
        // Note: output parameter 'id' is NOT image_id and NOT album_id.
        //  (this id belongs to new record of `images_of_albums` table)
        $dbResult = & $this->app_db->callSP('dating', "addImageToAlbum", false, array($imageId, $albumId), array('id'));
        $id = & $dbResult->getOutParameter('id');
        return is_numeric($id) && $id > 0;
    }

    public function delete($albumId) {
        // SP Name: deleteAlbum
        // OUT Parameter: $isSuccess
        // TODO
    }

    public function deleteImage($imageId, $albumId) {
        // SP Name: deleteImageFromAlbum
        // OUT Parameter: $isSuccess
        // TODO
    }

    public function set($albumUserId, $albumName, $albumeDescription) {
        // SP Name: setAlbum
        // OUT Parameter: $actionType; (Bit)
        // TODO
    }

}
