@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Drills Update') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('drills.update', $drill->id) }}">
                        @csrf

                        <div class="form-group row">
                            <label for="title" class="col-md-4 col-form-label text-md-right">{{ ('Title') }}</label>

                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ $drill->title }}" autocomplete="title" autofocus>

                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="category_name" class="col-md-4 col-form-label text-md-right">{{ __('CategoryName') }}</label>

                            <div class="col-md-6">
                                <input id="category_name" type="text" class="form-control @error('category_name') is-invalid @enderror" name="category_name" value="{{ $drill->category_name }}" autocomplete="category_name">

                                @error('category_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        @for ($i = 1; $i <= 10; ++$i)
                            <div class="form-group row">
                                <label for="problem{{$i - 1}}" class="col-md-4 col-form-label text-md-right">{{ __('Problem').$i }}</label>

                                <div class="col-md-6">
                                    <input id="problem{{$i - 1}}" type="text" class="form-control @error('problem'.($i - 1)) is-invalid @enderror" name="problem{{$i - 1}}" value="{{ $drill['problem'.($i - 1)] }}" autocomplete="problem{{$i - 1}}">

                                    @error('problem'.($i - 1))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endfor

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
