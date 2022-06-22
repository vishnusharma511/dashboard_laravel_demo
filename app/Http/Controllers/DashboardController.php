<?php

namespace App\Http\Controllers;
use App\Models\Revenue;
use App\Models\User;

use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $users = User::count();
        $new_user = User::whereDate('created_at', '=', date('Y-m-d'))->get();

        $videos = Video::count();
        $video_this_week = Video::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();

        $revenue = Revenue::sum('price');

        $video_watch_db_time = Video::get()->pluck('watch_time')->toArray();
        $video_watch_time = $this->totaltime($video_watch_db_time);
        return view('dashboard',[
            'user'=>$users,
            'new_user'=>$new_user,'date' => date('Y-m-d'),
            'videos'=>$videos,
            'video_this_week'=>$video_this_week,
            'revenue' => $revenue,
            'video_watch_time' => $video_watch_time
        ]);
    }

    private function totaltime($time){
        $sum = strtotime('00:00:00');
        $totaltime = 0;
        foreach( $time as $element ) {
            $timeinsec = strtotime($element) - $sum;
            $totaltime = $totaltime + $timeinsec;
        }
        $h = intval($totaltime / 3600);
        $totaltime = $totaltime - ($h * 3600);
        $m = intval($totaltime / 60);
        $s = $totaltime - ($m * 60);
        return ("$h:$m:$s");
    }
}
