<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublishCaseController extends Controller
{
    public function __invoke(Request $request)
    {
        $mid = $request->input('mid');
        if($mid){

            //發案
            $demmand_query = DB::table('demmand')
            ->select('d_name','d_required','d_amount','d_unit','created_at')
            ->where('mid',$mid);

            //發案進行中
            $demmand_progress_query = DB::table('established_case')
            ->select('c_name','c_amount','created_at')
            ->where('mid_service',$mid)
            ->where('c_status',1);

            //案件完成
            $demmand_completed_query = DB::table('established_case')
            ->select('c_name','c_amount','created_at')
            ->where('mid_service',$mid)
            ->where('c_status',2);

            if($request->has('demmandSearch')){
                $demmand_query->where('d_name','like','%'.$request->input('demmandSearch').'%');
            }

            if($request->has('demmandProgressSearch')){
                $demmand_progress_query->where('c_name','like','%'.$request->input('demmandProgressSearch').'%');
            }

            if($request->has('demmandCompletedSearch')){
                $demmand_completed_query->where('c_name','like','%'.$request->input('demmandCompletedSearch').'%');
            }

            return response()->json([
                'demmand' => $demmand_query,
                'demmand_progress' => $demmand_progress_query,
                'demmand_completed' => $demmand_completed_query
            ]);
        }
    }
}
