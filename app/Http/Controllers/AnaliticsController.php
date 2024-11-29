<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Roles;
use App\Models\Group;
use App\Models\Activity;
use App\Models\Analitics;


class AnaliticsController extends Controller
{

    public function index(Request $request)
    {
        $activityGroups = Analitics::where('status', 1)->get();
        return view('analitics.main', compact('activityGroups'));
    }

    public function getActivityById(Request $request)
    {
        $activityGroupId = $request->input('activityGroupId');
        $activityTypes = Analitics::where('parent', $activityGroupId)->get(['id', 'name']);
        return response()->json($activityTypes);
    }

    public function getGraphTypeById(Request $request)
    {
        $activityTypeId = $request->input('activityTypeId');
        $graphTypes = Analitics::where('id', $activityTypeId)->first(['id', 'graph_type']);
        if ($graphTypes) {
            $graphTypes = json_decode($graphTypes->graph_type, true); 
            return response()->json($graphTypes['types']); 
        }
        // return response()->json([], 404);
    }

    public function getGraph(Request $request)
    {
        $activityGroupId = $request->has('activityGroupId');
        $activityTypeId = $request->has('activityTypeId');
        $graphType = $request->has('graphType');
        return view('analitics.graph');
    }

    // public function userlistTEST(Request $request)
    // {
    //     $usertype = auth()->user()->usertype;
    //     $client_id = $request->has('client_id') ? $request->input('client_id') : auth()->user()->client_id;
    //     $company_id = $request->has('company_id') ? $request->input('company_id') : auth()->user()->company_id;
    //     $searchTerm = $request->input('searchTerm');

    //     $users = User::with(['client', 'company', 'group', 'role'])
    //         ->when(request()->is('users*'), function ($query) use ($client_id, $company_id,$usertype) {

    //             if (is_null($client_id) && is_null($company_id)) {
    //                 $query->whereNull('client_id')->whereNull('company_id');
    //             } elseif(is_null($company_id) && !is_null($client_id)) {
    //                 $query->where('client_id', $client_id)->whereNull('company_id');
    //             }else{
    //                 $query->where('client_id', $client_id)
    //                 ->where('company_id', $company_id);
    //             }
    //             if($usertype == 'group' || $usertype =='user'){
    //                 $query->where('group_id', auth()->user()->group_id);
    //             }
    //         })
    //         ->when(request()->is('client/users*'), function ($query) use ($client_id) {
    //             $query->whereNull('company_id')
    //                   ->whereNotNull('client_id'); // Ensures client_id is not null
                      
    //             if (!empty($client_id)) {
    //                 $query->where('client_id', $client_id);
    //             }
    //         })
    //         ->when( request()->is('client/company/users*'), function ($query) use ($client_id, $company_id) {
    //             $query->whereNotNull('company_id')->whereNotNull('client_id');
                    
    //                 if (!empty($client_id)) {
    //                     $query->where('client_id', $client_id);
    //                 }
        
    //             if (!empty($company_id)) {
    //                 $query->where('company_id', $company_id);
    //             }
    //         })
    //         ->when(request()->is('company/users*'), function ($query) use ($client_id, $company_id) {
    //             $query->whereNotNull('company_id')->whereNotNull('client_id');
    //             $query->where('client_id', $client_id);
    //             if (!empty($company_id)) {
    //                 $query->where('company_id', $company_id);
    //             }
    //         })
    //         ->when($searchTerm, function ($query) use ($searchTerm, $client_id,$company_id) {
    //             if (!empty($client_id)) {
    //                 $query->where('client_id', $client_id);
    //             }
    //             if (!empty($company_id)) {
    //                 $query->where('company_id', $company_id);
    //             }
    //             $query->where(function ($q) use ($searchTerm) {
    //                 $q->where('name', 'LIKE', "%{$searchTerm}%")
    //                 ->orWhereHas('role', function ($q) use ($searchTerm) {
    //                     $q->where('name', 'LIKE', "%{$searchTerm}%");
    //                 })
    //                 ->orWhereHas('group', function ($q) use ($searchTerm) {
    //                     $q->where('name', 'LIKE', "%{$searchTerm}%");
    //                 });
    //                 if (request()->is('client/users*') || request()->is('client/company/users*')) {
    //                     $q->orWhereHas('client', function ($q) use ($searchTerm) {
    //                         $q->where('name', 'LIKE', "%{$searchTerm}%");
    //                     });
    //                 }
    
    //                 if (request()->is('client/company/users*')) {
    //                     $q->orWhereHas('company', function ($q) use ($searchTerm) {
    //                         $q->where('name', 'LIKE', "%{$searchTerm}%");
    //                     });
    //                 }
    //             });
    //         })
    //         ->where('id', '!=', auth()->user()->id)
    //         ->where('usertype','user')  // Exclude the logged-in user
    //         ->paginate(200);

    //     $view = $this->getViewPath('users.list');
    //     $html = view($view)->with('users', $users)->render();
    //     return response()->json(['html' => $html]);
    // }

    // public function index(Request $request)
    // {
    //     $parentFilter = Analitics::where('status', 1)->get();
    //     return view(compact('parentFilter'));
    //     // return view('analitics.main');
    //     // return view('analitics');
    // }





    public function indexOLD(Request $request)
    {
        $filteredPermissions = PermissionHelper::getFilteredPermissions(auth()->id());
        $value = $request->input('value');
        $dateRange = $request->input('dateRange');
        echo $startDateInput = $request->input('startDateInput');

        if (empty($value) && empty($dateRange)) {
            // return view('analitics.main');
            return view('analitics');
        }

        $type = match ($value) {
            "Log Out", "Groups List" => "line",
            "Groups Size" => "pie",
            default => "bar",
        };

        //echo $startDateInput; die;
        //get date range
        [$startDate, $endDate] = $this->getDateRange($dateRange);

        if ($value == "Groups date") {
            $getCount = Group::selectRaw('count(id) as count,DATE(created_at) as created_date, name');
            if ($startDate && $endDate) {
                $getCount->whereBetween('created_at', [$startDate, $endDate]);
            }
            $getCount = $getCount->groupBy('created_date', 'name');
            $getLabels = $getCount->pluck('name')->toArray();
            $getData = $getCount->pluck('count')->toArray();
            // print_r($getData);die;
            // dd($getCount->toSql(), $getCount->getBindings());
        } // 
        elseif ($value == "Groups Active") {
            $getCount = DB::table('users')
                ->select('groups.name', DB::raw('COUNT(activities.user_id) as count'))
                ->leftJoin('groups', 'groups.id', '=', 'users.groupID')
                ->leftJoin('activities', function ($join) {
                    $join->on('activities.user_id', '=', 'users.id')
                        ->where('activities.action', 'Log In');
                })
                ->groupBy('groups.name')
                ->orderByDesc('count')
                ->get();
            if ($startDate && $endDate) {
                $getCount->whereBetween('created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('name')->toArray();
            $getData = $getCount->pluck('count')->toArray();
        } //
        elseif ($value == "Groups List") {
            $getCount = User::selectRaw('groups.name as group_name, count(users.id) as count')
                ->join('groups', 'users.groupID', '=', 'groups.id')
                ->groupBy('groups.id', 'groups.name')
                ->get();
            if ($startDate && $endDate) {
                $getCount->whereBetween('users.created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('group_name')->toArray();
            $getData = $getCount->pluck('count')->toArray();
        } //
        elseif ($value == "Groups Share") {
            $getCount =  Activity::select(
                DB::raw('COUNT(*) as count'),
                'users.name as user_name'
            )
                ->join('users', 'activities.user_id', '=', 'users.id')
                ->where('activities.action', 'Share')
                ->groupBy('users.id', 'users.name');
            if ($startDate && $endDate) {
                $getCount->whereBetween('activities.created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('user_name')->toArray();
            $getData = $getCount->pluck('count')->toArray();
        } //
        elseif ($value == "Groups Size") {
            $getCount = Group::selectRaw('name, sizeUse')->get();
            if ($startDate && $endDate) {
                $getCount->whereBetween('created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('name')->toArray();
            $getData = $getCount->pluck('sizeUse')->toArray();
        } //
        elseif ($value == "Groups Max") {
            $getCount = Group::selectRaw('name, sizeMax')->get();
            if ($startDate && $endDate) {
                $getCount->whereBetween('created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('name')->toArray();
            $getData = $getCount->pluck('sizeMax')->toArray();
        } //
        elseif ($value == "Roles Upload") {
            $getCount = Roles::selectRaw('name, upload_limit')->get();
            if ($startDate && $endDate) {
                $getCount->whereBetween('users.created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('name')->toArray();
            $getData = $getCount->pluck('upload_limit')->toArray();
        } //
        elseif ($value == "User Roles") {
            $getCount = Roles::selectRaw('roles.name as group_name, count(users.id) as count')
                ->join('users', 'users.roleID', '=', 'roles.id')
                ->groupBy('roles.id', 'roles.name')
                ->get();
            if ($startDate && $endDate) {
                $getCount->whereBetween('users.created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('group_name')->toArray();
            $getData = $getCount->pluck('count')->toArray();
        } //
        elseif ($value == "Roles Active") {
            $getCount = DB::table('users')
                ->select('roles.name', DB::raw('COUNT(activities.user_id) as count'))
                ->leftJoin('roles', 'roles.id', '=', 'users.roleID')
                ->leftJoin('activities', function ($join) {
                    $join->on('activities.user_id', '=', 'users.id')
                        ->where('activities.action', 'Log In');
                })
                ->groupBy('roles.name')
                ->orderByDesc('count')
                ->get();
            if ($startDate && $endDate) {
                $getCount->whereBetween('created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('name')->toArray();
            $getData = $getCount->pluck('count')->toArray(); // or any count-related data if needed
        } //
        elseif ($value == "Edit Files") {
            $getCount = Activity::selectRaw('user_id, COUNT(*) as edit_count')
                ->where('action', 'Edit')
                ->groupBy('user_id')
                ->with('user:id,name')
                ->get();
            if ($startDate && $endDate) {
                $getCount->whereBetween('users.created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('user.name')->toArray();
            $getData = $getCount->pluck('edit_count')->toArray();
        } //
        elseif ($value == "Upload Files") {
            $getCount = Activity::selectRaw('user_id, COUNT(*) as upload_count')
                ->where('action', 'File Upload')
                ->groupBy('user_id')
                ->with('user:id,name')
                ->get();
            if ($startDate && $endDate) {
                $getCount->whereBetween('users.created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('user.name')->toArray();
            $getData = $getCount->pluck('upload_count')->toArray();
        } //
        elseif ($value == "Delete Files") {
            $getCount = Activity::selectRaw('user_id, COUNT(*) as upload_count')
                ->where('action', 'Delete')
                ->groupBy('user_id')
                ->with('user:id,name')
                ->get();
            if ($startDate && $endDate) {
                $getCount->whereBetween('users.created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('user.name')->toArray();
            $getData = $getCount->pluck('upload_count')->toArray();
        } //
        elseif ($value == "Total Files") {
            $getCount = DB::table('files')
                ->selectRaw('extension, COUNT(*) as file_count')
                ->whereIn('extension', ['xlsx', 'docx', 'pptx', 'txt'])
                ->groupBy('extension')
                ->get();
            if ($startDate && $endDate) {
                $getCount->whereBetween('created_at', [$startDate, $endDate]);
            }
            $getLabels = $getCount->pluck('extension')->toArray();
            $getData = $getCount->pluck('file_count')->toArray();
        } //
        else {
            $getCount = Activity::selectRaw('users.name, count(*) as count')
                ->join('users', 'activities.user_id', '=', 'users.id');
            if ($value == "Active Users") {
                $getCount->where('users.status', '1');
            } //
            elseif ($value == "Inactive Users") {
                $getCount->where('users.status', '0');
            } //
            else { //Log Out,Download,File Upload
                $getCount->where('activities.action', $value);
            }

            if ($startDate && $endDate) {
                $getCount->whereBetween('activities.created_at', [$startDate, $endDate]);
            }
            $getCount = $getCount->groupBy('users.name');
            $getLabels = $getCount->pluck('name')->toArray();
            $getData = $getCount->pluck('count')->toArray();
            // print_r($getData);die;
        }
        return response()->json([
            'getLabels' => $getLabels,
            'type' => $type,
            'getData' => $getData,
            'filteredPermissions' => $filteredPermissions
        ]);
    }

    private function getDateRange($dateRange)
    {
        switch ($dateRange) {
            case 'today':
                return [now()->startOfDay(), now()->endOfDay()];
            case 'last7days':
                return [now()->subDays(6)->startOfDay(), now()->endOfDay()];
            case 'last30days':
                return [now()->subDays(29)->startOfDay(), now()->endOfDay()];
            default:
                return [null, null];
        }
    }



    public function customGraph(Request $request)
    {
        return view('analiticsCustom');
    }
}
