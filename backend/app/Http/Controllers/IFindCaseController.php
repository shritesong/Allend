<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Undefined;

class IFindCaseController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = DB::table('demmand')
        ->leftJoin('country','demmand.d_active_location','=','country.country_id')
        ->leftJoin('category','category.catid','=','demmand.d_type')
        ->leftJoin('quote', 'quote.did', '=', 'demmand.did')
        ->select('demmand.did','d_name', 'type', 'd_duration','d_description','d_amount','d_unit',
        DB::raw('date_format(created_at, "%Y/%m/%d") as created_at'),DB::raw('count(quote.mid) as quote_total'),'country_city',
        // DB::raw('date_format(updated_at, "%Y/%m/%d") as updated_at')
        'updated_at')
        ->groupBy('demmand.did','d_name', 'type', 'd_duration','d_description','d_amount','d_unit', 'country_city','updated_at','created_at');

        // 選擇地區、案件金額 (以url方式傳參，複選以,隔開)
        $location = $request->input('location');
        $amount = $request->input('amount');
        if (!(empty($location) && empty($amount))) {

            if(!empty($location) && DB::table('country')->whereIn('country_city', explode(',', $location))->exists()){
                $query->whereIn('country_city',explode(',', $location));
            }

            if(!empty($amount)){
                $query->where(function ($query) use ($amount){
                    foreach(explode(',', $amount) as $val){
                        switch($val){
                            case "1":
                                $query->orWhereBetween('d_amount', [0, 5000]);
                                break;
                            case "2":
                                $query->orWhereBetween('d_amount', [5001, 10000]);
                                break;
                            case "3":
                                $query->orWhereBetween('d_amount', [10001, 50000]);
                                break;
                            case "4":
                                $query->orWhereBetween('d_amount', [50001, 100000]);
                                break;
                            case "5":
                                $query->orWhereBetween('d_amount', [100001, 300000]);
                                break;
                        }
                    }
                }
            );
            }
        }

        // 期程 (短、長)
        if($request->has('d_duration') && ($request->input('d_duration') === "短" || $request->input('d_duration') === "長")){
            $query->where('d_duration',$request->d_duration);
        }

        // 發案地點
        if($request->has('d_active_location') && DB::table('contry')->where('country_city', $request->input('d_active_location'))->exists()){
            $query->whereIn('d_active_location',explode(',',$request->d_active_location));
        }

        // 案件金額
        if($request->has('d_amount')){
            $query->whereIn('d_amount',$request->d_amount);
        }

        // 指定類別
        switch($request->type){
            case '1':
                $query->where('d_type', $request->type);
                break;
            case '2':
                $query->where('d_type', $request->type);
                break;
            case '3':
                $query->where('d_type', $request->type);
                break;
            case '4':
                $query->where('d_type', $request->type);
                break;
            case '5':
                $query->where('d_type', $request->type);
                break;
            default:
                break;
        }

        // 指定排序方式
        $order = $request->input('order');
        switch($order){
            // 最新刊登
            case '1':
                $query->orderBy('created_at','desc')->orderBy('did', 'desc');
                break;
            // 最近更新
            case '2':
                $query->orderBy('updated_at','desc');
                break;
            // 預算由高到低
            case '3':
                $query->orderBy('d_amount','desc');
                break;
            // 報價人數
            case '4':
                $query->orderBy('quote_total', 'desc');
                break;
            // 預設最新刊登
            default:
                $query->orderBy('created_at', 'desc')->orderBy('did', 'desc');
                break;
            }

        // 案件搜索
        if($request->has('casesearch') && $request->input('casesearch') !== "undefined"){
            $query->where('d_name', 'like', '%'.$request->input('casesearch').'%');
        }

        $demands = $query->get();
        // 案件更新時間
        foreach($demands as $demand){
            $updateAt = new \DateTime($demand->updated_at);
            // dd($updateAt);
            $now = new \DateTime('now',new \DateTimeZone('Asia/Taipei'));
            // dd($now);
            $interval = $updateAt->diff($now);
            // dd($interval);
            if($interval->h < 1 && $interval->days < 1){
                $difference = $interval->i . '分鐘前更新';
            }elseif($interval->days < 1 && $interval->h > 1){
                $difference = $interval->h . '小時前更新';
            }else{
                $difference = $interval->days . '天前更新';
            }

            $demand->updated_at = $difference;
        }
        return response()->json($demands);
    }
}
