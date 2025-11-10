@extends('layouts.app')

@section('content')
    <livewire:assetcreate-form :category_type="$category_type" :category="$category" :sub_category="$sub_category"/>
@endsection