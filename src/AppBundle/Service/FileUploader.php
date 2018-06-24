<?php namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;

class FileUploader {

    public function upload(File $file, $targetDir){

        $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
        $file->move(
            $targetDir,
            $fileName
        );

        return $fileName;
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}