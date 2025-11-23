@extends('layouts.app')

@section('content')
    @if($mode == 'audit')
        <livewire:audit-form />
    @else 
        <livewire:assetmanagement-form :mode="$mode" :targetID="$targetID" :category_type="$category_type" :category="$category" :sub_category="$sub_category"/>
    @endif
@endsection