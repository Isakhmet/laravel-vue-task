@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="margin: auto">Fact Data</div>
                    <button class="btn btn-primary" type="button" onclick="window.location='{{ url("chart") }}'">Chart</button>
                    <div class="form-group mb-4" style="max-width: 500px; margin: 0 auto;">
                        <table-component></table-component>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection