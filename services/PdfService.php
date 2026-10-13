<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class PdfService
{
    public function generate($html, $filename, $configType)
    {
        $filePath = USER_UPLOAD_DIR . "runtime_upload/" . $filename;
        $fileUrl  = USER_UPLOAD_URL . "runtime_upload/" . $filename;

        PdfFactory::generate($html, $filePath,$configType);

        return [
            'check' => 'success',
            'file_upload_dir' => $filePath,
            'file_url' => $fileUrl
        ];
    }
}