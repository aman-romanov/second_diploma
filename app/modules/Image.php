<?php
namespace App\modules;
use \Tamtamchik\SimpleFlash\Flash;
use function Tamtamchik\SimpleFlash\flash;

/**
 * Класс с методами для загрузки изображения в бд.
 */

class Image {

    /**
     * Загрузка прикрепленного изображения в папку avatars. Метод принимает название файла и директорию к временной папке из глобального массива $_FILES. При наличии в имении странных символов, они заменяются на нижнее подчеркивание. Если файл с таким названием уже существует, то в название добавляется число и проверяется повторно.
     * 
     * @param string $image_name Название файла
     * @param string $tmp_name Путь к временной папке хранения
     * @return string $filename Название файла вместе с расширением
     */

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

    /**
     * Удаление старой фотографии при загрузке новой. 
     * 
     * @param string $img Название файла
     * @return null
     */
    
    public function deleteImage($img){
        $destination = $_SERVER['DOCUMENT_ROOT'] . '/img/demo/avatars/' . $img;
        if(file_exists($destination)){
            unlink($_SERVER['DOCUMENT_ROOT'] . '/img/demo/avatars/' . $img);
        }
    }

    /**
     * Проверка ошибок при загрузке фотографии пользователя. Название ошибки берется с глобального массива $_FILES и после через цикл проверяется на соответсвие перечисленных ошибок. При наличии ошибки, она записвается во флэш сообщение. Если флэш сообщение пустое, то возрващается отрицательное значение.
     * 
     * @param string $img Название файла
     * @return boolean Возвращает false, при отсутсвии ошибок и обратное при наличии
     */
    
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