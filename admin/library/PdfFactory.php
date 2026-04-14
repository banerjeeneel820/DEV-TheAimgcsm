<?php
defined('ROOTPATH') or exit('No direct script access allowed');

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfFactory
{

    private static $config;

    // Load config once
    private static function getConfig()
    {
        if (!self::$config) {
            self::$config = require ROOTPATH . '/config/pdf.php';
        }
        return self::$config;
    }

    // -----------------------------
    // CREATE INSTANCE
    // -----------------------------
    public static function make()
    {
        $config = self::getConfig();

        $options = new Options();
        $options->set('isRemoteEnabled', $config['isRemoteEnabled']);
        $options->set('isHtml5ParserEnabled', $config['isHtml5ParserEnabled']);
        $options->set('defaultFont', $config['defaultFont']);

        return new Dompdf($options);
    }

    // -----------------------------
    // GENERATE & SAVE FILE
    // -----------------------------
    public static function generate($html, $filePath)
    {
        $config = self::getConfig();

        $pdf = self::make();

        $pdf->loadHtml($html);
        $pdf->setPaper($config['paper']);
        $pdf->render();

        file_put_contents($filePath, $pdf->output());

        return true;
    }

    // -----------------------------
    // STREAM PDF TO BROWSER
    // -----------------------------
    public static function stream($html, $filename = 'document.pdf')
    {
        $config = self::getConfig();

        $pdf = self::make();

        $pdf->loadHtml($html);
        $pdf->setPaper($config['paper']);
        $pdf->render();

        // Stream to browser (inline view)
        $pdf->stream($filename, ["Attachment" => false]);
    }
}