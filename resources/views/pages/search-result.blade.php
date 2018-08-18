@extends('templates.master')

@section('content')
<div class="container mt-5">
  <div class="row">
    <div class="col-lg-12">
      <h1>ผลการค้นหา "{{$search_data}}"</h1><hr>
    </div>
    @foreach($search_result as $search_results)
    <div class="col-lg-4 mt-5">
    <a class="course-link" href="/see-course/{{$search_results->course_id}}">
      <div class="card" style="width:80%;">
        <img class="card-img-top course-img" src="/assets/img/course/{{$search_results->course_img}}" alt="course_img">
        <div class="card-body">
          <h2 class="card-title">{{$search_results->course_name}}</h2><hr>
          <h2 class="card-text"><span class="badge badge-primary">฿ {{$search_results->course_price}}</span><span class="badge badge-secondary" style="float:right;">{{$search_results->course_now_joining}}/{{$search_results->course_max}}</span></h2>
          <small class="text-muted">เริ่มเรียน {{date('d/m/Y', strtotime($search_results->course_start_date))}} ถึง {{date('d/m/Y', strtotime($search_results->course_end_date))}}</small>
        </div>
      </div>
    </a>
   </div>
   @endforeach
  </div>
  <div class="row">
    <div class="col-lg-12 mt-5">
      {{ $search_result->links() }}
    </div>
  </div>
</div>
@endsection
