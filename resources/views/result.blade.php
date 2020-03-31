@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Match No</th>
            <th scope="col">Team Match</th>
            <th scope="col">Description</th>
          </tr>
        </thead>
        <tbody>
          @foreach($brackets as $data)
          <tr>
            <th scope="row">{{ $data['match_no'] }}</th>
            <td>{{ $data['team_a'] }} vs {{ $data['team_b'] }}</td>
            <td>{{ $data['description'] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection