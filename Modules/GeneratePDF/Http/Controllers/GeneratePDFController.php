<?php

namespace Modules\GeneratePDF\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;

use PDF;
use PDFMerger;
use setasign\Fpdi\Fpdi;
//use setasign\Fpdi\PdfReader;
use Exception;

class MergePDF {
    protected $type;
    protected $files;
    protected $oMerger;
    protected $fileFiltered;
    protected $temporaryPath;

    public function __construct( $files )
    {
        $this->files = $files;
        $this->filterType = [
            'image/jpeg',
            'image/png',

            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];

        $this->filterFile();
    }

    public function stream()
    {
        $this->oMerger();
        try {
            return $this->oMerger->stream();
        }

        catch (Exception $e) {}
    }

    protected function oMerger()
    {
        try {
            $this->oMerger = PDFMerger::init();

            foreach ($this->filterFile as $file) {
               $this->addPdf($file); 
            } 
            $this->oMerger->merge();

            //remove temporary file
            //$file = new Filesystem;
            //dd($file->cleanDirectory(Storage::path($this->temporaryPath)));
        }

        catch (Exception $e) {
            echo ($e->getMessage());
        }

    }

    protected function filterFile()
    {
        $this->filterFile = [];

        $this->temporaryPath = '/public/tmp/' .date('Y-m-d h-i-s');
        Storage::makeDirectory($this->temporaryPath);


        foreach ($this->files as $file) {
            $mimeType = Storage::getMimeType($file);
            if ( in_array($mimeType, $this->filterType) ) {
                if ($mimeType != 'application/pdf') {
                    $temporaryFileName = $this->temporaryPath .'/' .pathinfo($file, PATHINFO_FILENAME).'.pdf';

                    exec('soffice --headless --convert-to pdf "'.Storage::path($file).'" --outdir "'.Storage::path($this->temporaryPath) .'" ' );

                    $file = $temporaryFileName;
                }

                $this->filterFile[] = $file;
            
            }
        }
    }

    public function addPdf( $fileName )
    {
        $fileName = (Storage::path($fileName));
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
            
    }

}

class ConvertFilesToPDF {

}

class GeneratePDFController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        //$src = fopen('https://pslb3.menlhk.go.id/internal/uploads/pengumuman/1545111808_contoh-pdf.pdf', 'r');
        //$dest1 = fopen('storage/files/Merge to PDF/first1k.pdf', 'w');
        //dump(file_put_contents($src));
        $allFiles = Storage::files('public/files/Merge to PDF');
        //dd($allFiles);
        //$allFiles[] = 'https://pslb3.menlhk.go.id/internal/uploads/pengumuman/1545111808_contoh-pdf.pdf';
        $merge = new MergePDF($allFiles);
        $merge->stream();
        
        //return view('generatepdf::index');
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
