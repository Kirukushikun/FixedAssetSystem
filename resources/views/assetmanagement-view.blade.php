@extends('layouts.app')

@section('content')
    <livewire:assetmanagement-form :mode="$mode" :targetID="$targetID" :category_type="$category_type" :category="$category" :sub_category="$sub_category"/>
@endsection