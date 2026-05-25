@extends('layouts.app')
@section('title', 'Edit Equipment')
@section('heading', 'Edit Equipment')
@section('subheading', $equipment->name)
@section('content')
    @include('equipment._form')
@endsection
