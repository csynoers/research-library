@extends('generatepdf::layouts.master')

@section('content')
    <div class="container">
        <form class="row g-3 my-5">
            {{-- File Type PDF --}}
            <div class="col-12">
                <label for="" class="form-label">PDF Portrait</label>
                <embed type="application/pdf" src="pdf/portrait.pdf" class="w-100" height="500">
                <input type="hidden" name="pdf_portrait" value="pdf/portrait.pdf" >
            </div>
            <div class="col-12">
                <label for="" class="form-label">PDF Landscape</label>
                <embed type="application/pdf" src="pdf/landscape.pdf" class="w-100" height="500">
                <input type="hidden" name="pdf_landscape" value="pdf/landscape.pdf" >
            </div>
            <div class="col-12">
                <label for="" class="form-label">PDF Landscape & Portrait</label>
                <embed type="application/pdf" src="pdf/landscape_portrait.pdf" class="w-100" height="500">
                <input type="hidden" name="pdf_landscape_portrait" value="pdf/landscape_portrait.pdf" >
            </div>
            <div class="col-12">
                <hr>
                <input type="hidden" name="merge" value="true" >
                <button type="submit" class="btn btn-outline-primary w-100">Generate</button>
            </div>
        </form>        
    </div>
@endsection
