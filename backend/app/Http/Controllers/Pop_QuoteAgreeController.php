<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Pop_QuoteAgreeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    // 查看報價
    public function getQuote(Request $request)
    {
        $demmandID = $request->input('did');
        if($demmandID){
            $quote = DB::table('demmand')
            ->leftjoin('quote', 'quote.did', '=', 'demmand.did')
            ->join('members', 'quote.mid', '=', 'members.mid')
            ->join('identity', 'members.identity', '=', 'iid')
            ->select('qid','d_name','members.mid', 'name', 'email',
            'i_identity as identity', 'q_amount','q_message')
            ->where('quote.did', $demmandID)->get();

            return response()->json($quote);
        }
    }

    // 送出報價表單
    public function sendQuote(Request $request)
    {
        $mid = Auth::id();

        $this->validate($request,[
            'did'=>['required'],
            'q_amount'=>['required'],
            // 'q_message'=>['required'],
        ]);

        $qoute = DB::table('quote')->insert([
            'mid'=> $mid,
            'did'=> $request->input('did'),
            'q_amount'=> $request->input('q_amount'),
            'q_message' => is_null($request->input('q_message')) ? "" : $request->input('q_message'),
        ]);
        return response()->json([
            'message' => '報價成功'
        ]);
    }

    // 同意報價
    public function agreeQuote(Request $request)
    {
        $demmand_id = Auth::id();
        // 先預設一次處理一筆，從前端傳來報價者id、案件id
        $quote_mid = $request->mid;
        $did = $request->did;
        // 單筆同意
        $agree = DB::table('demmand')
                ->join('quote','demmand.did','=','quote.did')
                ->select('demmand.mid as demmand_mid','quote.mid as quote_mid','d_name','d_type',
                'd_duration','d_description','d_active_location','q_amount','d_unit','d_contact_name',
                'd_email', 'd_mobile_phone')
                ->where('quote.did', $did)->where('quote.mid',$quote_mid)
                ->get();
            // dd($agree);
        foreach($agree as $row){
            DB::table('established_case')->insert([
                'mid_demmand' => $row->demmand_mid,
                'mid_service' => $row->quote_mid,
                'c_status'=>1,
                'c_name'=>$row->d_name,
                'c_type'=>$row->d_type,
                'c_amount'=>$row->q_amount,
                'c_unit'=>$row->d_unit,
                'c_active_location'=>$row->d_active_location,
                'c_duration'=>$row->d_duration,
                'c_description'=>$row->d_description,
                'c_contact_name'=>$row->d_contact_name,
                'c_email'=>$row->d_email,
                'c_mobile_phone'=>$row->d_mobile_phone,
                'created_at'=>now(),
                'updated_at'=>now()
            ]);
            DB::table('quote')->where('mid',$quote_mid)->where('did', $did)->delete();
        }

        return response()->json(['message'=>'已同意報價']);
    }

    // 拒絕報價
    public function disagreeQuote(Request $request)
    {
        DB::table('quote')->where('did', $request->did)->where('mid',$request->mid)->delete();

        return response()->json(['message'=>'已拒絕報價']);
    }
}
