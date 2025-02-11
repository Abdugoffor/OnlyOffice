@extends('layouts.admin')
@section('title', 'Documents')
@section('content')
    <section class="content">
        <form action="{{ route('store.documents') }}" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-10">
                    @csrf
                    <div class="form-group">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="document" class="custom-file-input" id="exampleInputFile">
                                <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <button type="submit" class="btn btn-primary">Upload</button>

                </div>
            </div>
        </form>
        <table class="table table-striped table-bordered table-hover">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Format</th>
                <th>Size</th>
                @if (auth()->user()->role == 'admin')
                    <th>User</th>
                @endif
                <th>Edit</th>
                <th>Created</th>
                <th>History</th>
                <th>
                    Delete
                </th>
            </tr>
            @foreach ($models as $model)
                <tr>
                    <td>{{ $model->id }}</td>
                    <td>{{ $model->name }}</td>
                    <td>{{ $model->format }}</td>
                    <td>{{ number_format($model->size / 1024 / 1024, 2) }} MB</td>
                    @if (auth()->user()->role == 'admin')
                        <td>{{ $model->user->name }}</td>
                    @endif
                    <td><a href="{{ route('documents.edit', $model->id) }}" target="_blank">Edit</a></td>
                    <td>{{ $model->created_at->diffForHumans() }}</td>
                    <td>
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-default">
                            History
                        </button>
                        <div class="modal fade" id="modal-default">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-group ml-5">
                                            @foreach ($model->history as $history)
                                                <li>
                                                    {{ $history->user->name }},
                                                    {{ $history->created_at->diffForHumans() }} ,
                                                    <a href="{{ asset('storage/' . $history->path) }}"
                                                        target="_blank">File</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                    </td>
                    <td>
                        <form action="{{ route('delete.document', $model->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
        {{ $models->links() }}
    </section>
@endsection
