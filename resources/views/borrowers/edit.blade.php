@extends('layouts.app')
@section('title', 'Edit Borrower')
@section('heading', 'Edit Borrower')
@section('subheading', $borrower->full_name)
@section('content')
    @include('borrowers._form')
@endsection
