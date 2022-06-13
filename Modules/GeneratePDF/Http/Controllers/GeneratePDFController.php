<?php

namespace Modules\GeneratePDF\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use PDF;
use PDFMerger;
use setasign\Fpdi\Fpdi;
//use setasign\Fpdi\PdfReader;
use NcJoes\OfficeConverter\OfficeConverter;

class GeneratePDFController extends Controller
{
    public $filesPDF;
    public $oMerger;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if ( $request->merge ) {
            $this->merger()
                 ->mergedPDF($request->pdf_portrait)
                 ->mergedPDF($request->pdf_landscape)
                 ->mergedPDF($request->pdf_landscape_portrait)
                 ->mergedPDF('doc/portrait.docx', 'doc')
                 ->mergedPDF('doc/landscape.docx', 'doc')
                 ->mergedPDF('doc/landscape_portrait.docx', 'doc')
                 ->stream();

        }
        
        return view('generatepdf::index');
    }



    // Merge PDF
    public function merger()
    {
        $this->oMerger = PDFMerger::init();

        return $this;
    }
    public function stream()
    {
        $this->oMerger->merge();

        return $this->oMerger->stream();
    }
    public function mergedPDF( $fileName , $type=false)
    {
        if ( $type ) {
            if ( $type == 'doc' ) {
                $this->convertWordToPDF($fileName);
                $fileName = $this->getNameFileNoExtension($fileName).'.pdf';
            }
        }

        $pdf = new Fpdi();

        // set the source file
        $pageCount = $pdf->setSourceFile( $fileName );
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            // import a page
            $templateId = $pdf->importPage($pageNo);

            // get the size of the imported page
            $size = $pdf->getTemplateSize($templateId);

            // get the orientation of the page
            $orientation = $size['orientation'];

            if ( $orientation == "L") {
                $this->oMerger->AddPdf($fileName, [$pageNo], $orientation);
            }
            
            else {
                $this->oMerger->AddPdf($fileName, [$pageNo], $orientation);
            }

        }
            
        return $this;
    }





    /* Convert Word To PDF
     */
    public function convertWordToPDF($fileName)
    {
        $converter = new OfficeConverter($fileName);
        $converter->convertTo(public_path('output-file.pdf'));
        dd($converter);

        $fileNameNoExtension = $this->getNameFileNoExtension($fileName);

        /* Set the PDF Engine Renderer Path */
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
         
        /*@ Reading doc file */
        //$template = new\PhpOffice\PhpWord\TemplateProcessor(public_path($fileName));
 
        /*@ Replacing variables in doc file */
        //$template->setValue('date', date('d-m-Y'));
        //$template->setValue('title', 'Mr.');
        //$template->setValue('firstname', 'Scratch');
        //$template->setValue('lastname', 'Coder');

        /*@ Save Temporary Word File With New Name */
        //$saveDocPath = public_path($fileNameNoExtension.'.docx');
        //$template->saveAs($saveDocPath);
         
        // Load temporarily create word file
        //$Content = \PhpOffice\PhpWord\IOFactory::load($saveDocPath); 
        $Content = \PhpOffice\PhpWord\IOFactory::load($fileName); 
 
        //Save it into PDF
        $savePdfPath = public_path($fileNameNoExtension.'.pdf');
 
        /*@ If already PDF exists then delete it */
        //if ( file_exists($savePdfPath) ) {
        //    unlink($savePdfPath);
        //}
 
        //Save it into PDF
        $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($Content,'PDF');
        $PDFWriter->save($savePdfPath); 
       // echo 'File has been successfully converted';
 
        /*@ Remove temporarily created word file */
        //if ( file_exists($saveDocPath) ) {
        //    unlink($saveDocPath);
        //}
    }

    public function getNameFileNoExtension($fileName)
    {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName);
    }









    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('generatepdf::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('generatepdf::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('generatepdf::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
