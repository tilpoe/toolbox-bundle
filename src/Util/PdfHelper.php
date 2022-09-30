<?php

namespace Feierstoff\ToolboxBundle\Util;

use Exception;
use Feierstoff\ToolboxBundle\Exception\InternalServerException;
use Mpdf\Mpdf;
use setasign\Fpdi\PdfParser\StreamReader;

class PdfHelper
{

    /**
     * @var Mpdf
     */
    private Mpdf $mpdf;

    /**
     * @param string $title
     * @param string $filenameOrPath
     * @return PdfHelper
     * @throws InternalServerException
     */
    public static function create(string $title, string $filenameOrPath, ?array $customOptions = null): PdfHelper
    {
        return new PdfHelper($title, $filenameOrPath, $customOptions);
    }

    public static function createFromRawData(string $data, string $title, string $filenameOrPath): PdfHelper
    {
        return new PdfHelper($title, $filenameOrPath, [], $data);
    }

    /**
     * @param string $title
     * @param string $filenameOrPath
     * @throws InternalServerException
     */
    public function __construct(
        string         $title,
        private string $filenameOrPath,
        ?array         $customOptions = null,
        ?string        $rawData = null
    )
    {
        try {
            if ($rawData) {
                $this->mpdf = new Mpdf();
                $stream = fopen("php://memory", "r+b");
                fwrite($stream, $rawData);
                rewind($stream);
                $pages = $this->mpdf->setSourceFile($stream);

                for ($i = 1; $i <= $pages; $i++) {
                    $template = $this->mpdf->importPage($i);
                    $this->mpdf->useTemplate($template);
                }
            } else {
                if ($customOptions) {
                    $this->mpdf = new Mpdf($customOptions);
                } else {
                    $this->mpdf = new Mpdf([
                        "margin_left" => 15,
                        "margin_right" => 15,
                        "format" => "A4",
                        "showBarcodeNumbers" => false
                    ]);
                }

            }
        } catch (Exception $e) {
            throw new InternalServerException("Error creating .pdf file.");
        }

        $this->mpdf->SetTitle($title);
    }


    /**
     * @throws InternalServerException
     */
    public function write(string $html, bool $newPage = false): Pdf
    {
        if ($newPage) {
            $this->mpdf->AddPage();
        }

        try {
            $this->mpdf->WriteHTML($html);
        } catch (Exception) {
            throw new InternalServerException("Error writing .pdf file.");
        }

        return $this;
    }

    /**
     * @throws InternalServerException
     */
    public function render(): void
    {
        try {
            $this->mpdf->Output("$this->filenameOrPath.pdf", "I");
        } catch (Exception) {
            throw new InternalServerException("Error rendering .pdf file.");
        }
    }

    /**
     * @return void
     * @throws InternalServerException
     */
    public function save(): void
    {
        try {
            $this->mpdf->Output($this->filenameOrPath, "F");
        } catch (Exception) {
            throw new InternalServerException("Error saving .pdf file.");
        }
    }

    public function getAsString(): string
    {
        try {
            return $this->mpdf->Output("", "S");
        } catch (Exception) {
            throw new InternalServerException("Error returning .pdf file as string.");
        }
    }

    /**
     * @param string $body
     * @param int $fontSize
     * @return string
     */
    public static function withBase(string $body, int $fontSize = 9): string
    {
        return "
            <html>
                <head>
                    <style>
                        body {
                            font-family: sans-serif;
                            font-size: {$fontSize}pt;
                        }
                
                        h5, p {
                            margin: 0;
                        }
                
                        table {
                            border-collapse: collapse;
                        }
                
                        .barcode {
                            padding: 1.5mm;
                            margin: auto;
                            vertical-align: top;
                            color: #000000;
                        }
                        
                        .table td {
                            font-size: 1.2em;
                            padding: 0.5em;
                            border: 1px solid black;
                        }
                    </style>
                </head>
                <body>
                    $body
                </body>
            </html>
        ";
    }


}

