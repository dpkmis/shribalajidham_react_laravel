<?php

namespace App\Http\Controllers;

use App\Models\MasterLogs;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Breadcrumbs;


class LogController extends Controller
{
    public function index()
    {
        Breadcrumbs::add('Dashboard', route('dashboard'));
        Breadcrumbs::add('Admin Management');
        Breadcrumbs::add('Activity Log');

      $log_dropdown = config('custom.LOG_CONTROLLER');
        return view('master_logs.logs',compact('log_dropdown'));
    }

    public function ajaxLogList(Request $request)
    {
      // dd(config('custom.LOG_CONTROLLER'));
        $query = MasterLogs::select(['id', 'user_name', 'module', 'updated_at'])
            ->orderBy('updated_at', 'desc');

        // 🔹 Apply column filters
        if ($request->has('columns')) {
            foreach ($request->get('columns') as $col) {
                $colName = $col['data'];
                $searchValue = $col['search']['value'];
                if($colName =='controller' || $colName == 'method'){
                  $colName = 'module';
                }
                if (!empty($searchValue)) {
                  
                    if ($colName === 'updated_at') {
                        // Date range filter
                        $dates = explode(' - ', $searchValue);
                        if (count($dates) === 2) {
                            $start = date('Y-m-d 00:00:00', strtotime($dates[0]));
                            $end = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('updated_at', [$start, $end]);
                        }
                    } else {
                        // Normal text search
                        
                          $query->where($colName, 'like', "%{$searchValue}%");
                        
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addColumn('controller', function ($row) {
                // Split before @
                return $this->controller_log_name(strstr($row->module, '@', true) ?: $row->module);
            })
            ->addColumn('method', function ($row) {
                // Get part after @
                return strstr($row->module, '@') ? substr(strstr($row->module, '@'), 1) : '';
            })
            ->addColumn('action', function ($row) {
                return '<a href="#" class="btn btn-sm btn-primary view_logs" data-id="'.$row->id.'">View</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function ajaxUserDetail(){
    
      if($_GET){
          $query = MasterLogs::select('*')
                   ->where('id',$_GET['id'])
                   ->orderBy('updated_at', 'desc')
                   ->get();
          
        return $query;
      }
    }

    private function controller_log_name($string){
      $predefined_name = config('custom.LOG_CONTROLLER');
      return $predefined_name[$string] ?? $string;
    }
}
