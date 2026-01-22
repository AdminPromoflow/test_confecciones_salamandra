<?php

class PulseFile
{
    // public function uploadImage($fileName)
    // {
    //     $imageFileType = strtolower(pathinfo($_FILES[$fileName]['name'], PATHINFO_EXTENSION));
    //
    //     // Generar un nombre aleatorio único para la imagen
    //     $randomName = uniqid() . '.' . $imageFileType;
    //
    //     $targetFile = PROJECTROOT . UPLOADS . '/' . $randomName;
    //     $uploadOk = 1;
    //
    //     // Verificar si el archivo es una imagen real o una imagen falsa
    //     $check = getimagesize($_FILES[$fileName]["tmp_name"]);
    //     if ($check === false) {
    //         return "El archivo no es una imagen.";
    //     }
    //
    //     // Verificar el tamaño del archivo
    //     if ($_FILES[$fileName]["size"] > 5000000) // 50MB
    //     {
    //         return "El archivo es demasiado grande.";
    //     }
    //
    //     // Permitir ciertos formatos de archivo
    //     if (!in_array($imageFileType, ["ico", "jpg", "png", "jpeg", "gif", "webp", "avif"])) {
    //         return "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
    //     }
    //
    //
    //     // Intentar subir el archivo
    //     if (move_uploaded_file($_FILES[$fileName]["tmp_name"], $targetFile)) {
    //         // $randomName = $this->_compressImage($randomName);
    //         return $randomName;
    //     } else {
    //         return "Error al subir el archivo.";
    //     }
    // }
    //
    // public function deleteImage($imageName)
    // {
    //     $imagePath = PROJECTROOT . $imageName;
    //
    //     // Verifica que el archivo exista antes de eliminarlo
    //     if (file_exists($imagePath)) {
    //         clearstatcache();
    //         if (unlink($imagePath)) {
    //             return "The image $imageName has been deleted.";
    //         } else {
    //             return "Unable to delete the image.";
    //         }
    //     } else {
    //         return false;
    //     }
    // }
    //
    // public function uploadMultipleImages($fileInputName, $targetDirectory)
    // {
    //     $uploadedImages = [];
    //
    //     // Iterate through the array of files
    //     foreach ($_FILES[$fileInputName]['tmp_name'] as $key => $tmp_name) {
    //         $imageFileType = strtolower(pathinfo($_FILES[$fileInputName]["name"][$key], PATHINFO_EXTENSION));
    //
    //         // Generate a unique random name for the image
    //         $randomName = uniqid() . '.' . $imageFileType;
    //
    //         $targetFile = $targetDirectory . $randomName;
    //         $uploadOk = 1;
    //
    //         // Check if the file is a real image or a fake image
    //         $check = getimagesize($tmp_name);
    //         if ($check === false) {
    //             $uploadedImages[] = "File number " . ($key + 1) . " is not an image.";
    //         }
    //
    //         // Check the file size
    //         if ($_FILES[$fileInputName]["size"][$key] > 5000000) { // 5 MB
    //             $uploadedImages[] = "File number " . ($key + 1) . " is too large.";
    //         }
    //
    //         // Allow certain file formats
    //         if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
    //             $uploadedImages[] = "Only JPG, JPEG, PNG, and GIF files are allowed. (File number " . ($key + 1) . ")";
    //         }
    //
    //         // Attempt to upload the file
    //         if (move_uploaded_file($tmp_name, $targetFile)) {
    //             $uploadedImages[] = "File number " . ($key + 1) . " (" . htmlspecialchars($randomName) . ") has been uploaded.";
    //         } else {
    //             $uploadedImages[] = "Error uploading file number " . ($key + 1);
    //         }
    //     }
    //
    //     return $uploadedImages;
    // }
    //
    // private function _compressImage($fileName)
    // {
    //     $imagePath = PROJECTROOT . UPLOADS . '/' . $fileName;
    //
    //     // Obtener la imagen original
    //     $image = imagecreatefromstring(file_get_contents($imagePath));
    //
    //     if ($image !== false) {
    //         // Convertir la imagen a formato webp
    //         $quality = 80; // Calidad de la compresión
    //         $webpImage = imagecreatetruecolor(imagesx($image), imagesy($image));
    //
    //         if (imagewebp($image, $webpImage, $quality)) {
    //             // Guardar la imagen comprimida
    //             $randomName = uniqid() . '.webp';
    //             $targetFile = PROJECTROOT . UPLOADS . '/' . $randomName;
    //
    //             if (imagewebp($webpImage, $targetFile, $quality)) {
    //                 // Eliminar la imagen original
    //                 imagedestroy($image);
    //                 imagedestroy($webpImage);
    //
    //                 return $randomName;
    //             }
    //         }
    //
    //         // Si hay un error durante la compresión, puedes manejarlo aquí
    //         imagedestroy($image);
    //         imagedestroy($webpImage);
    //     }
    //
    //     // En caso de error o si no se pudo comprimir la imagen, puedes devolver un valor apropiado
    //     return "Error al comprimir la imagen.";
    // }
}
