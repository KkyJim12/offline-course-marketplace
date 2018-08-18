<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Study;
use App\Course;
use App\Transfer;
use Carbon\Carbon;

class StudyController extends Controller
{

    public function TransferStudyProcess(Request $request, $course_id)  {

      $checkstudy = Study::where('user_id',session('user_id'))->where('course_id',$course_id)->first();
      $thiscourse = Course::where('course_id',$course_id)->first();
      $now_time = Carbon::now();

      if ($now_time > $thiscourse->course_expire_date) {
        return redirect()->back()->with('error','คอร์สนี้ปิดรับสมัครแล้ว');
      }

      elseif ($thiscourse->course_now_joining == $thiscourse->course_max) {
        return redirect()->back()->with('error','คอร์สนี้เต็มแล้ว');
      }

      elseif($checkstudy) {
        return redirect()->back()->with('error','คุณได้สมัครเข้าร่วมคอร์สนี้แล้ว');
      }

      else {
        $study = new Study;
        $study->user_id = session('user_id');
        $study->course_id = $course_id;
        $study->study_status = false;
        $study->save();

        $course = Course::where('course_id',$course_id)->first();
        $course->course_now_joining = $course->course_now_joining+1;
        $course->save();
        return redirect()->back();
      }

    }

    public function UploadSlip(Request $request)  {

      $validatedData = $request->validate([
        'course_transfer' => 'required|image|max:2048',
      ]);

      $image = $request->file('course_transfer');

      $img_name= time().'.'.$image->getClientOriginalExtension();

      $destinationPath = public_path('/assets/img/slip');

      $image->move($destinationPath, $img_name);

      $transfer = new Transfer;
      $transfer->transfer_course_id = $request->course_id;
      $transfer->transfer_course_name = $request->course_name;
      $transfer->transfer_course_price = $request->course_price;
      $transfer->transfer_user_id = session('user_id');
      $transfer->transfer_user_name = session('user_fname').''.session('user_lname');
      $transfer->transfer_img = $img_name;
      $transfer->save();

      $transfer_status = Study::where('course_id',$request->course_id)->where('user_id',session('user_id'))->first();
      $transfer_status->study_status = true;
      $transfer_status->save();

      return redirect()->back()->with('success','รอตรวจสอบ');
    }

    public function ChangeTransferMethod(Request $request,$course_id) {

      $study_status = Study::where('course_id',$course_id)->where('user_id',session('user_id'))->first();
      $study_status->delete();

      $course = Course::where('course_id',$course_id)->first();
      $course->course_now_joining = $course->course_now_joining-1;
      $course->save();

      return redirect()->back();
    }

    public function CancleTransfer(Request $request,$course_id) {
      $transfer = Transfer::where('transfer_course_id',$course_id)->where('transfer_user_id',session('user_id'))->first();
      $transfer->delete();

      $study_status = Study::where('course_id',$course_id)->where('user_id',session('user_id'))->first();
      $study_status->delete();

      $course = Course::where('course_id',$course_id)->first();
      $course->course_now_joining = $course->course_now_joining-1 ;
      $course->save();

      return redirect()->back();
    }

    public function AdminTransferApprove(Request $request,$course_id) {
      $transfer_status = Transfer::where('transfer_course_id',$request->course_id)->where('transfer_user_id',$request->user_id)->first();
      $transfer_status->transfer_accept = true;

      $study_status =Study::where('course_id',$request->course_id)->where('user_id',$request->user_id)->first();
      $study_status->study_status = true;
      $study_status->save();
      return redirect()->route('admin-transfer');
    }

    public function AdminTransferReject(Request $request,$course_id) {
      $transfer_status = Transfer::where('transfer_course_id',$request->course_id)->where('transfer_user_id',$request->user_id)->first();
      $transfer_status->transfer_accept = false;

      $study_status =Study::where('course_id',$request->course_id)->where('user_id',$request->user_id)->first();
      $study_status->delete();
      return redirect()->route('admin-transfer');
    }

    public function StudyThisCourse(Request $request, $course_id) {

      $checkstudy = Study::where('user_id',session('user_id'))->where('course_id',$course_id)->first();
      $thiscourse = Course::where('course_id',$course_id)->first();
      $now_time = Carbon::now();

      if ($now_time > $thiscourse->course_expire_date) {
        return redirect()->back()->with('error','คอร์สนี้ปิดรับสมัครแล้ว');
      }

      elseif ($thiscourse->course_now_joining == $thiscourse->course_max) {
        return redirect()->back()->with('error','คอร์สนี้เต็มแล้ว');
      }

      elseif($checkstudy) {
        return redirect()->back()->with('error','คุณได้สมัครเข้าร่วมคอร์สนี้แล้ว');
      }

      else {
        $study = new Study;
        $study->user_id = session('user_id');
        $study->course_id = $course_id;
        $study->study_status = true;
        $study->save();

        $course = Course::where('course_id',$course_id)->first();
        $course->course_now_joining = $course->course_now_joining+1;
        $course->save();
        return redirect()->back();
      }

    }
}
