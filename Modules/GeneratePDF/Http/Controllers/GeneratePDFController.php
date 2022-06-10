<?php

namespace Modules\GeneratePDF\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

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
                 ->stream();

        }
        
        return view('generatepdf::index');
    }
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
    public function mergedPDF( $fileName )
    {
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
