<?php

use Knp\Snappy\Pdf;

class Application_Model_File
{
    final public const MPDF_ADAPTER = 'mpdf';
    final public const WKHTMLTOPDF_ADAPTER = 'wkhtmltopdf';

    /**
     * @static
     * @param $fileName
     * @param null $title
     * @return Application_Model_File_Type_Csv
     *  |Application_Model_File_Type_Txt|Application_Model_File_Type_Xls
     * @throws Exception
     */
    public static function getInstance($fileName, $title = null)
    {
        return match (self::getType($fileName)) {
            Application_Model_File_Type_Csv::TYPE => new Application_Model_File_Type_Csv($fileName, $title),
            Application_Model_File_Type_Txt::TYPE => new Application_Model_File_Type_Txt($fileName, $title),
            Application_Model_File_Type_Xls::XLS_TYPE, Application_Model_File_Type_Xls::XLSX_TYPE => new Application_Model_File_Type_Xls($fileName, $title),
            default => throw new Exception('Passed file has unknown type.'),
        };
    }

    /**
     * @static
     * @param $fileName
     * @return null|string
     */
    public static function getType($fileName)
    {
        if ($fileName != null) {
            return strtolower(substr((string) $fileName, strrpos((string) $fileName, '.') + 1));
        } else {
            return null;
        }
    }

    /**
     * @static
     * @param $fileName
     * @return mixed|null
     */
    public static function getName($fileName)
    {
        if ($fileName != null) {
            $name = substr(
                (string) $fileName,
                strrpos((string) $fileName, '/') + 1,
                strrpos(substr((string) $fileName, strrpos((string) $fileName, '/') + 1), '.')
            );

            return self::getSafeName($name);
        } else {
            return null;
        }
    }

    /**
     * @static
     * @param $fileName
     * @return mixed|null
     */
    public static function getSafeName($fileName)
    {
        if ($fileName != null) {
            return preg_replace('/ /', '_', (string) $fileName);
        } else {
            return null;
        }
    }

    /**
     * @static
     * @return mixed
     */
    public static function getStorage()
    {
        $options = Zend_Registry::getInstance()->options;

        return $options['files']['storagePath'];
    }

    /**
     * @static
     * @param string $fileName
     */
    public static function download($fileName, $fullName = false, $contentType = null)
    {
        Zend_Layout::getMvcInstance()->disableLayout();

        if ($contentType) {
            header('Content-Type: ' . $contentType);
        }
        if (!$fullName) {
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            $fileName = Application_Model_File::getStorage() . '/' . $fileName;
        } else {
            header('Content-Disposition: attachment; filename="' . pathinfo($fileName, PATHINFO_BASENAME) . '"');
        }
        readfile($fileName);
        unlink($fileName);
        exit;
    }

    /**
     * @static
     * @param $html
     */
    public static function toPDF($html, $orientation = 'A4', $filename = 'report.pdf', $cssList = false, $fontKey = 'c')
    {
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        if ($config->getOption(
            'pdfAdapter'
        ) == static::MPDF_ADAPTER /* || strpos($filename, 'Check_Printing_File_') === 0*/) {
            //        require_once("dompdf/dompdf_config.inc.php");
            //        spl_autoload_register('DOMPDF_autoload');
            //        $dompdf = new DOMPDF();
            //        $dompdf->set_paper("a4", "landscape");
            //        $dompdf->load_html($html);
            //        $dompdf->render();
            //        $dompdf->stream('report-'.date('Ymd').'.pdf');
            //        require_once("MPDF57/mpdf.php");
            if (!$cssList) {
                $cssList = ['/css/mpdf.css'];
            }
            if (is_string($cssList)) {
                $cssList = [$cssList];
            }
            //        define('_MPDF_TTFONTDATAPATH', APPLICATION_PATH . '/data/cache/');
            $mpdf = new mPDF($fontKey, $orientation);
            $mpdf->SetDisplayMode('fullpage');
            //        $mpdf->SetAutoFont();
            $mpdf->autoScriptToLang = true;
            foreach ($cssList as $css) {
                $mpdf->WriteHTML(file_get_contents('../public' . $css), 1);
            }
            $mpdf->WriteHTML($html, 2);
            if (PHP_SAPI == 'cli') {
                $file = fopen($filename, 'w+');
                fclose($file);
                chmod($filename, 0777);
                $mpdf->Output($filename, 'F');
            } else {
                $mpdf->Output($filename, 'D');
                exit;
            }
        } else {
            //            $options = ['page-height' =>  '11.69in','page-width' => '8.27in'];
            //            $options['disable-smart-shrinking'] = true;
            $options['lowquality'] = false;
            $options['print-media-type'] = true;
            //            $options['viewport-size'] = '800x600';
            //            $options['use-xserver'] = true;
            //            $options['disable-toc-links'] = true;

            if ($orientation == 'A4-L') {
                $options['page-size'] = 'A4';
                $options['orientation'] = 'landscape';
            } else {
                if ($orientation == 'Letter') {
                    $options['margin-top'] = '11.1';
                    $options['margin-bottom'] = '0';
                    $options['margin-left'] = '7.5';
                    $options['margin-right'] = '7.9';
                }
                $options['page-size'] = $orientation;
            }
            if (!$cssList) {
                $cssList = ['/css/wkhtmltopdf.css'];
            }
            if (is_string($cssList)) {
                $cssList = [$cssList];
            }
            $html .= '<style>';
            foreach ($cssList as $css) {
                $html .= file_get_contents('../public' . $css);
            }
            $html .= '</style>';
            if (strlen(decbin(~0)) == 32) {
                $wkhtmltopdfPath = '/../vendor/h4cc/wkhtmltopdf-i386/bin/wkhtmltopdf-i386';
            } else {
                $wkhtmltopdfPath = '/../vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64';
            }
            $snappy = new Pdf(APPLICATION_PATH . $wkhtmltopdfPath);
            //            $snappy = new Pdf('/usr/local/bin/wkhtmltopdf');

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            if (PHP_SAPI == 'cli') {
                $snappy->setOptions($options);
                $snappy->generateFromHtml($html, $filename);
            } else {
                echo $snappy->getOutputFromHtml($html, $options);
                exit;
            }
        }
    }

    public static function getHeaderContentType($exportFormat)
    {
        $type = false;
        $type = match ($exportFormat) {
            Application_Model_File_Type_Xls::XLS_TYPE => 'application/vnd.ms-excel',
            Application_Model_File_Type_Xls::XLSX_TYPE => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            Application_Model_File_Type_Csv::TYPE => 'text/csv',
            Application_Model_File_Type_Txt::TYPE => 'text/plain',
            default => $type,
        };

        return $type;
    }
}
