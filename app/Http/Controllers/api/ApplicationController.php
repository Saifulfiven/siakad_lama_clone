<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Application;
use Rmt, DB, Response, Sia;
use Illuminate\Http\Request;
use Carbon\Carbon;
class ApplicationController extends Controller
{
    public function store(Request $request){
        $result = [
            'name' => $request->name,
            'country_origin' => $request->country_origin,
            'card_number' => $request->card_number,
            'faculty_objectives' => $request->faculty_objectives,
            'phone' => $request->phone,
            'birthday' => $request->birthday,
            'identity_card' => $request->identity_card,
            'expired_date' => $request->expired_date,
            'study_program' => $request->study_program,
            'transcript' => $request->transcript,
            'medical_certificate' => $request->medical_certificate,
            'passport1' => $request->passport1,
            'passport2' => $request->passport2
        ];
        $result['created_at'] = Carbon::now();
        $result['updated_at'] = Carbon::now();
        Application::insert($result);
        return response()->json($result);
    }



}
