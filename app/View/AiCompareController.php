<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\TblLocationModel;

// You need these parsers
use     \PdfParser\Parser as PdfParser;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpPresentation\IOFactory as PptIOFactory;
use App\Services\DeepSeekService;

class AiCompareController extends Controller
{
    public function page()
    {
        $admin = auth()->user();
        $locations = TblLocationModel::all();
        return view('vendor.ai_compare', [
            'admin' => $admin,
            'locations' => $locations,
        ]);
    }

    public function run(Request $request, GeminiService $gemini)
    {
        $request->validate([
            'prompt'   => 'required|string|max:5000',
            'files.*'  => 'nullable|file|mimes:pdf,txt,csv,xls,xlsx,docx,ppt,pptx|max:10240', // 10MB
        ]);

        try {
            $filesData = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $extension = strtolower($file->getClientOriginalExtension());
                    $path = $file->getRealPath();

                    switch ($extension) {
                        case 'txt':
                        case 'csv':
                        case 'md':
                            $filesData[] = file_get_contents($path);
                            break;

                        case 'pdf':
                            $parser = new PdfParser();
                            $pdf = $parser->parseFile($path);
                            $filesData[] = $pdf->getText();
                            break;

                        case 'xls':
                        case 'xlsx':
                            $spreadsheet = IOFactory::load($path);
                            $sheetData = $spreadsheet->getActiveSheet()->toArray();
                            $filesData[] = $this->convertArrayToText($sheetData);
                            break;

                        case 'docx':
                            $phpWord = WordIOFactory::load($path);
                            $text = '';
                            foreach ($phpWord->getSections() as $section) {
                                foreach ($section->getElements() as $element) {
                                    if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                                        $text .= $element->getText() . " ";
                                    } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                        foreach ($element->getElements() as $child) {
                                            if ($child instanceof \PhpOffice\PhpWord\Element\Text) {
                                                $text .= $child->getText() . " ";
                                            }
                                        }
                                    } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                                        foreach ($element->getRows() as $row) {
                                            foreach ($row->getCells() as $cell) {
                                                foreach ($cell->getElements() as $cellElement) {
                                                    if ($cellElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                                        $text .= $cellElement->getText() . " ";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $filesData[] = $text;
                            break;

                        case 'ppt':
                        case 'pptx':
                            $ppt = PptIOFactory::createReader('PowerPoint2007')->load($path);
                            $text = '';
                            foreach ($ppt->getAllSlides() as $slide) {
                                foreach ($slide->getShapeCollection() as $shape) {
                                    if ($shape instanceof \PhpOffice\PhpPresentation\Shape\RichText) {
                                        foreach ($shape->getParagraphs() as $paragraph) {
                                            foreach ($paragraph->getRichTextElements() as $element) {
                                                if ($element instanceof \PhpOffice\PhpPresentation\Shape\RichText\TextElement) {
                                                    $text .= $element->getText() . " ";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $filesData[] = $text;
                            break;


                        default:
                            $filesData[] = sprintf(
                                "[File: %s, Type: %s, Size: %s]",
                                $file->getClientOriginalName(),
                                $file->getMimeType(),
                                $this->formatBytes($file->getSize())
                            );
                            break;
                    }
                }
            }

            $result = $gemini->generateWithPrompt(
                $request->input('prompt'),
                $filesData
            );

            return response()->json([
                "prompt" => $request->input('prompt'),
                "result" => $result
            ]);
        } catch (\Exception $e) {
            Log::error('AI Compare Error: '.$e->getMessage());
            return response()->json([
                "error" => "An error occurred while processing your request"
            ], 500);
        }
    }
public function deeprun(Request $request, DeepSeekService $deepseek)
{
    $request->validate([
        'prompt'   => 'required|string|max:5000',
        'files.*'  => 'nullable|file|mimes:pdf,txt,csv,xls,xlsx,docx,ppt,pptx|max:10240',
    ]);

    try {
        $filesData = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $content = null;

                switch ($extension) {
                    case 'txt':
                        $content = file_get_contents($file->getRealPath());
                        break;

                    case 'csv':
                        $rows = array_map('str_getcsv', file($file->getRealPath()));
                        $content = json_encode($rows);
                        break;

                    case 'pdf':
                        // needs a library like smalot/pdfparser
                        $parser = new \Smalot\PdfParser\Parser();
                        $pdf = $parser->parseFile($file->getRealPath());
                        $content = $pdf->getText();
                        break;

                    case 'docx':
                        // PHPWord or ZipArchive
                        $zip = new \ZipArchive;
                        if ($zip->open($file->getRealPath()) === true) {
                            $xml = $zip->getFromName("word/document.xml");
                            $zip->close();
                            $content = strip_tags($xml);
                        }
                        break;

                    case 'xls':
                    case 'xlsx':
                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                        $content = $spreadsheet->getActiveSheet()->toArray();
                        $content = json_encode($content);
                        break;

                    case 'ppt':
                    case 'pptx':
                        // using PHPPresentation
                        $pptReader = \PhpOffice\PhpPresentation\IOFactory::createReader($extension === 'pptx' ? 'PowerPoint2007' : 'PowerPoint97');
                        $presentation = $pptReader->load($file->getRealPath());
                        $textContent = '';
                        foreach ($presentation->getAllSlides() as $slide) {
                            foreach ($slide->getShapeCollection() as $shape) {
                                if ($shape instanceof \PhpOffice\PhpPresentation\Shape\RichText) {
                                    foreach ($shape->getParagraphs() as $paragraph) {
                                        foreach ($paragraph->getRichTextElements() as $element) {
                                            if ($element instanceof \PhpOffice\PhpPresentation\Shape\RichText\TextElement) {
                                                $textContent .= $element->getText() . " ";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $content = $textContent;
                        break;
                }

                if ($content) {
                    $filesData[] = [
                        'name' => $file->getClientOriginalName(),
                        'content' => $content,
                    ];
                }
            }
        }

        $result = $deepseek->generateWithPrompt(
            $request->input('prompt'),
            $filesData
        );

        return response()->json([
            "prompt" => $request->input('prompt'),
            "result" => $result
        ]);
    } catch (\Exception $e) {
        Log::error('AI Compare Error: '.$e->getMessage());
        return response()->json([
            "error" => "An error occurred while processing your request"
        ], 500);
    }
}


    private function convertArrayToText($array)
    {
        $lines = [];
        foreach ($array as $row) {
            $lines[] = implode("\t", $row);
        }
        return implode("\n", $lines);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
