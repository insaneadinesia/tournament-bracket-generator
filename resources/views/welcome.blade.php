@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if($errors->any())
            <div class="alert alert-danger" role="alert">
                {{ implode('', $errors->all(':message')) }}
            </div>
            @endif
            <form action="{{ route('process-bracket') }}" method="post">
                @csrf
                <div class="form-group">
                    <label>Participant Size</label>
                    <input type="number" class="form-control" name="participant_size">
                    <small id="help" class="form-text text-muted">Valid value 4, 8, 16, 32, 64, 128 and so on.</small>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection