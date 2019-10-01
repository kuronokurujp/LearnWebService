@extends('layouts.app')

@section('content')
    <div id="app">
        <example-component
        title="{{ __('Practice').'「'.$drill->title.'」' }}"
        :drill="{{$drill}}"
        category_name="{{$drill->category_name}}">
        </example-component>
    </div>
@endsection