<?php
namespace App\modules;
use \Tamtamchik\SimpleFlash\Flash;
use function Tamtamchik\SimpleFlash\flash;

class Image {

    public function uploadImage($image_name, $tmp_name){
        $pathinfo = pathinfo($image_name);
        $base = $pathinfo['filename'];
        $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
        $filename = $base . "." . $pathinfo['extension'];
        $destination = $_SERVER['DOCUMENT_ROOT'] . '/img/demo/avatars/' . $filename;
        $i = 1;
        while(file_exists($destination)){
            $filename = $base . "($i)." . $pathinfo['extension'];
            $destination = $_SERVER['DOCUMENT_ROOT'] . '/img/demo/avatars/' . $filename;
            $i++;
        }
        move_uploaded_file($tmp_name, $destination);
        return $filename;
    }
    
    public function deleteImage($img){
        $destination = $_SERVER['DOCUMENT_ROOT'] . '/img/demo/avatars/' . $img;
        if(file_exists($destination)){
            unlink($_SERVER['DOCUMENT_ROOT'] . '/img/demo/avatars/' . $img);
        }
    }
    
    public function checkForErrors($error){
        switch($error){
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                flash()->error("Прикрепите файл");
                break;
            case UPLOAD_ERR_INI_SIZE:
                flash()->error("Размер изображения не должно превышать 2M");
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                flash()->error("Папка не найденa");
                break;
            case UPLOAD_ERR_CANT_WRITE:
                flash()->error("Изображение не переместилось");
                break;
            default:
                flash()->error("Возникла ошибка");
                break;
        }
        $mime_types = ['image/jpg', 'image/jpeg', 'image/png'];
        if($_FILES['image']['tmp_name']>0){
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['image']['tmp_name']);
            if(!in_array($mime_type, $mime_types)){
                flash()->error("Изображение должно соответствовать форматам: jpeg/jpg/png");
            }
        }
        if(flash()->display('error')){
            return true;
        }
        return false;
    }

}

?>