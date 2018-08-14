<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Category;
use App\Slide;
use App\Course;
use App\Study;
use Carbon;

class UIViewController extends Controller
{
    public function ShowIndex() {
      $category = Category::all();
      $slide = Slide::all();
      $suggest_course = Course::where('course_suggest',1)->get();
      $popular_course = Course::orderBy('course_now_joining','asc')->take(8)->get();
      if ($slide->count() == 0) {
        $otherslide = null;
        $firstslide = null;
        return view('index',[
                              'show_category' => $category,
                              'otherslide' => $otherslide,
                              'firstslide' => $firstslide,
                              'suggest_course' => $suggest_course,
                              'popular_course' => $popular_course,
                            ]);

      }
      else {
        $firstslide = Slide::where('slide_number',$slide->min('slide_number'))->first();
        $otherslide = Slide::where('slide_id','!=',$firstslide->slide_id)->get();
        return view('index',[
                              'show_category' => $category,
                              'firstslide' => $firstslide,
                              'otherslide' => $otherslide,
                              'suggest_course' => $suggest_course,
                            ]);
      }
    }

    public function ShowRegister()  {
      if (session('user_log')) {
        return redirect('/');
      }
      else {
        $category = Category::all();
        return view('pages.register',[
                                      'show_category' => $category,
                                     ]);
      }
    }

    public function ShowLogin()  {
      if (session('user_log')) {
        return redirect('/');
      }
      else {
        $category = Category::all();
        return view('pages.login',[
                                    'show_category' => $category,
                                  ]);
      }
    }

    public function ShowEditProfile($profile_id) {
      $profile = User::where('user_id',session('user_id'))->first();
      $profile_see = User::where('user_id',$profile_id)->first();
      $profile_check = User::where('user_id',$profile_id)->get();
      $category = Category::all();

      if(session('user_id') == $profile_id) {
        $dateOfBirth = $profile_see->user_birthdate;
        $today = date("Y-m-d");
        $age = date_diff(date_create($dateOfBirth), date_create($today));
        return view('pages.edit-profile',[
                                          'myprofile' => $profile,
                                          'show_category' => $category,
                                         ]);
      }
      elseif($profile_check->count() == 0) {
        abort(404);
      }
      else {
        $dateOfBirth = $profile_see->user_birthdate;
        $today = date("Y-m-d");
        $age = date_diff(date_create($dateOfBirth), date_create($today));
        return view('pages.profile',[
                                      'show_category' => $category,
                                      'profile' => $profile_see,
                                      'age' => $age,
                                    ]);
      }

    }

    public function ShowAdmin() {
      return view('pages.admin-dashboard');
    }

    public function ShowAdminCategory() {
      $category = Category::all();
      return view('pages.admin-category',[
                                          'show_category' => $category,
                                         ]);
    }

    public function ShowAdminCreateCategory() {
      return view('pages.admin-create-category');
    }

    public function ShowAdminEditCategory($category_id) {
      $category = Category::where('category_id',$category_id)->first();
      return view('pages.admin-edit-category',[
                                              'edit_category' => $category,
                                              ]);
    }

    public function ShowCategory($category_id)  {
      $category = Category::all();
      $that_category = Category::where('category_id',$category_id)->first();
      $course_in_category = Course::where('category_id',$category_id)->get();
      return view('pages.category',[
                                    'that_category' => $that_category,
                                    'show_category' => $category,
                                    'course_in_category' => $course_in_category,
                                   ]);
    }

    public function ShowAdminSlide()  {
      $slide = Slide::all();
      return view('pages.admin-slide',[
                                        'show_slide' => $slide,
                                      ]);
    }

    public function ShowAdminCreateSlide()  {
      return view('pages.admin-create-slide');
    }

    public function ShowAdminEditSlide($slide_id)  {
      $slide = Slide::where('slide_id',$slide_id)->first();
      return view('pages.admin-edit-slide',[
                                            'edit_slide' => $slide,
                                           ]);
    }

    public function ShowCourse($user_id)  {
      $user = User::where('user_id',$user_id)->first();
      $user2 = User::where('user_id',session('user_id'))->first();
      $checkuser = User::where('user_id',$user_id)->get();
      $category = Category::all();
      $checkcourse = Course::where('user_id',$user_id)->get();
      $seecourse = Course::where('user_id',$user_id)->get();
      $course_qty = Course::where('user_id',$user_id)->count();

      if ($user_id == session('user_id')) {
        return view('pages.show-course',[
                                            'user' => $user2,
                                            'show_category' => $category,
                                            'course' => $seecourse,
                                            'course_qty' => $course_qty,
                                          ]);
      }

      elseif ($checkcourse->count() == 0 & $checkuser->count() == 0) {
        abort(404);
      }

      else {
        $course = User::find($user_id)->mycourse;
        return view('pages.show-course-info',[
                                            'user' => $user,
                                            'show_category' => $category,
                                            'course' => $seecourse,
                                            'course_qty' => $course_qty,
                                          ]);
      }

    }

    public function ShowCreateCourse($user_id)  {
      $category = Category::all();
      return view('pages.create-course',[
                                          'show_category' => $category,
                                        ]);
    }

    public function ShowEditCourse($course_id)  {
      $category = Category::all();
      $course = Course::where('course_id',$course_id)->first();
      $now_category = Category::where('category_id',$course->category_id)->first();
      return view('pages.edit-course',[
                                          'show_category' => $category,
                                          'course' => $course,
                                          'now_category' => $now_category,
                                        ]);
    }

    public function ShowSeeCourse($course_id)  {
      $mycourse = Course::find($course_id);
      $category = Category::all();
      $course = Course::where('course_id',$course_id)->first();
      $num_course = Course::find($course_id)->study()->count();
      $mytime = Carbon\Carbon::now();
      $already_join = Study::where('course_id',$course_id)->where('user_id',session('user_id'))->first();
      return view('pages.course',[
                                  'show_category' => $category,
                                  'course' => $course,
                                  'mycourse' => $mycourse,
                                  'num_course' => $num_course,
                                  'already_join' => $already_join,
                                  'mytime' => $mytime,
                                 ]);
    }

    public function ShowAdminCourse() {
      $course = Course::all();
      return view('pages.admin-course',[
                                        'course' => $course,
                                       ]);
    }

    public function ShowAdminEditCourse($course_id)  {
      $category = Category::all();
      $course = Course::where('course_id',$course_id)->first();
      return view('pages.admin-edit-course',[
                                              'show_category' => $category,
                                              'course' => $course,
                                            ]);
    }

    public function ShowAdminSeeCourse($course_id)  {
      $mycourse = Course::find($course_id);
      $course = Course::where('course_id',$course_id)->first();
      return view('pages.admin-see-course',[
                                            'course' => $course,
                                            'mycourse' => $mycourse,
                                           ]);
    }

    public function ShowAdminCourseBan() {
      $course = Course::onlyTrashed()->get();
      return view('pages.admin-course-ban',[
                                            'course' => $course,
                                           ]);
    }

    public function AdminSeeBanCourse($course_id) {
      $mycourse = Course::onlyTrashed()->find($course_id);
      $course = Course::where('course_id',$course_id)->onlyTrashed()->first();
      return view('pages.admin-see-ban-course',[
                                                'course' => $course,
                                                'mycourse' => $mycourse,
                                               ]);
    }

    public function ShowAdminCourseApprove()  {
      $course = Course::where('course_approve',1)->get();
      return view('pages.admin-course-approve',[
                                                'course' => $course,
                                               ]);
    }

    public function ShowAdminCourseNotApprove() {
    $course = Course::where('course_approve',null)->where('course_reject',null)->get();
      return view('pages.admin-course-not-approve',[
                                                'course' => $course,
                                               ]);
    }

    public function ShowAdminCourseReject() {
    $course = Course::where('course_reject','!=',null)->get();
      return view('pages.admin-course-reject',[
                                                'course' => $course,
                                               ]);
    }

    public function ShowAdminSeeCourseApprove($course_id) {
      $mycourse = Course::find($course_id);
      $course = Course::where('course_id',$course_id)->first();
      return view('pages.admin-see-course-approve',[
                                                    'mycourse' => $mycourse,
                                                    'course' => $course,
                                                   ]);
    }

    public function ShowAdminSeeCourseNotApprove($course_id) {
      $mycourse = Course::find($course_id);
      $course = Course::where('course_id',$course_id)->first();
      return view('pages.admin-see-course-not-approve',[
                                                    'mycourse' => $mycourse,
                                                    'course' => $course,
                                                   ]);
    }

    public function ShowAdminSeecourseReject($course_id)  {
      $mycourse = Course::find($course_id);
      $course = Course::where('course_id',$course_id)->first();
      return view('pages.admin-see-course-reject',[
                                                    'mycourse' => $mycourse,
                                                    'course' => $course,
                                                   ]);
    }
}
