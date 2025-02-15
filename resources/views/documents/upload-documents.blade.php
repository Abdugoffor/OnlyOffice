@extends('layouts.admin')
@section('title', 'Upload-Documents')
@section('content')
    <section class="content">
        <form action="{{ route('store.documents') }}" method="post" enctype="multipart/form-data">
            @csrf<div class="form-group">
                <label for="exampleInputFile">File input</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" name="document" class="custom-file-input" id="exampleInputFile">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                    </div>
                    <div class="input-group-append">
                        <span class="input-group-text">Upload</span>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </section>
@endsection
