@extends('layouts.base')
@section('title', 'Hadith')
@section('main', 'HAdiths Management')
@section('link')
    <link rel="stylesheet" href="/panel-v2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="/panel-v2/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />

@endsection
@section('content')

    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center h-px">
                        <form class="w-px-500 border rounded p-3 p-md-5" method="POST" action="{{ Request::url() }}"
                            enctype="multipart/form-data">
                            @csrf
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    {{ session()->get('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session()->has('edit'))
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    {{ session()->get('edit') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session()->has('delete'))
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    {{ session()->get('delete') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            <h3 class="mb-4 ">Add Hadith</h3>
                            <div class="row mb-3">
                                <label class="col-sm-12 col-form-label" for="form-alignment-username">Book</label>
                                <div class="col-sm-12">
                                    <select id="defaultSelect" class="form-select" name="book_id" required>
                                        <option selected value="" disabled>Select Book</option>
                                        @foreach ($books as $book)
                                            <option value="{{ $book->id }} {{ old('book_id') == $book->id ? 'selected' : '' }}">{{ $book->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-12 col-form-label" for="form-alignment-username">Book Category</label>
                                <div class="col-sm-12">
                                    <select id="defaultSelect" class="form-select" name="category_id" required >
                                        <option selected value="" disabled>Select Book Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }} {{ old('category_id') == $book->id ? 'selected' : '' }}">{{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-12 col-form-label" for="form-alignment-username">Chapter</label>
                                <div class="col-sm-12">
                                    <input type="text" name="chapter" id="" class="form-control"
                                        placeholder="Chapter" value="{{ old('chapter') }}" required />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-12 col-form-label" for="form-alignment-username">Title</label>
                                <div class="col-sm-12">
                                    <input type="text" name="title" id="" class="form-control"
                                        placeholder="Title" value="{{ old('title') }}" required />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-12 col-form-label" for="form-alignment-username">Hadith</label>
                                <div class="col-sm-12">
                                    <textarea rows="10" name="hadith" id=""  required  class="form-control">{{ old('hadith') }}</textarea>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- / Content -->
    @endsection

    @section('script')

        <script src="/panel-v2/assets/js/app-user-list.js"></script>

    @endsection
