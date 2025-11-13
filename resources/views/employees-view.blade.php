@extends('layouts.app')

@section('content')
    <livewire:employee-view :targetID="$targetID" />
@endsection