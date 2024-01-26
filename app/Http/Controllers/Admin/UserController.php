<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

use Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    private function generateProfileMenu()
    {
        return [
            [
                'label' => __('Profile'),
                'route' => 'admin.profile.edit',
                'link' => route('admin.profile.edit')
            ],
            [
                'label' => __('Change Password'),
                'route' => 'admin.profile.change-password',
                'link' => route('admin.profile.change-password')
            ]
        ];
    }

    public function index()
    {        
        if(Gate::allows('isSuperAdmin')){
            $users = User::all();
            return view('admin.pages.users.view', ['users' => $users, 'title' => 'Pengaturan User']);
        }else{
            return redirect()->route('admin.dashboard');
        }        

    }

    public function create()
    {
        $this->authorize('isSuperAdmin');
        $role = User::TYPE_ROLE;
        return view('admin.pages.users.create', ['role' => $role]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'alpha'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required'],
            'password' => ['required', 'confirmed', Password::min(6)],            
        ]);
        
        try {
            $user  = new User;
            $user->password = Hash::make($request->password);
            $user->email = $request->email;
            $user->name = ucwords($request->name);
            $user->status = 1;
            $user->role = $request->role;
            $user->save();
            return redirect()->route('admin.users.view');
        } catch (\Throwable $th) {
            return $th;
        }        
    }

    public function show($id){
        $this->authorize('isSuperAdmin');
        $role = User::TYPE_ROLE;
        $user = User::find($id);
        if($user != null){
            return view('admin.pages.users.edit', ['user' => $user, 'role' => $role]);
            // return response()->json(['success' => true, 'message' => "Found!", 'user' => $user], 200);
        }else{
            // return response()->json(['success' => false, 'message' => "not found", 'user' => ''], 404);
            abort(404);
        }        
    }

    public function edit()
    {
        $menu = $this->generateProfileMenu();
        $user = auth()->user();

        $content = view('admin.pages.profile.edit', compact('user'))->render();

        return view('admin.pages.profile.view', [
            'title' => 'Profile Setting',
            'header' => 'Profile Setting',
            'subheader' => 'This is a page where you can manage your profile settings',
        ], compact('menu', 'content'));
    }

    public function updateMineProfile(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'name' => ['required', 'string'],
            'email' => ['required', 'email:rfc,dns', 'unique:users,email,' . $user->id],
        ], [], [
            'name' => __('Full Name'),
            'email' => __('Email Address'),
        ]);

        $user->email = $request->email;
        $user->name = ucwords($request->name);
        $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated successfully');
    }

    public function changePassword()
    {
        $menu = $this->generateProfileMenu();
        $user = auth()->user();

        $content = view('admin.pages.profile.change-password', compact('user'))->render();

        return view('admin.pages.profile.view', [
            'title' => 'Change Password',
            'header' => 'Change Password',
            'subheader' => 'This is a page where you can change your password',
        ], compact('menu', 'content'));
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'current_password' => ['required', 'password:web'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [], [
            'current_password' => __('Current Password'),
            'password' => __('New Password'),
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.profile.change-password')->with('success', 'Password updated successfully');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ['required', 'alpha'],
            'email' => ['required', 'email'],
            'role' => ['required'],                        
        ]);

        try {
            $user  = User::findorfail($id);
            ($request->new_password!= null 
                ? $user->password = Hash::make($request->password) 
                : '');
            $user->email = $request->email;
            $user->name = ucwords($request->name);            
            $user->role = $request->role;
            $user->save();

            return redirect()->route('admin.users.view');
        } catch (\Throwable $th) {
            abort(500);
            return redirect()->back()->withInput()->with('error', 'Something went wrong !');
        }
    }

    public function deactive($id)
    {
        $user = User::find($id);
        if($user != null){        
            $user->status = 0;
            $user->save();
            return response()->json(['success' => true, 'message' => 'deactived success !!']);    
            
        }else{
            return response()->json(['success' => false, 'message' => 'record not found']);
        }
        return response()->json(['success' => false, 'message' => "request gagal!"]);
    }

    public function active($id)
    {
        $user = User::find($id);
        if($user != null){        
            $user->status = 1;
            $user->save();
            return response()->json(['success' => true, 'message' => 'actived success !!']);    
            
        }else{
            return response()->json(['success' => false, 'message' => 'record not found']);
        }
        return response()->json(['success' => false, 'message' => 'request gagal!']);
    }

    public function dtUser()
    {
        $data = User::where('role','!=','super admin')->orderBy('created_at', 'DESC')->get();
        $edit="";
            return DataTables::of($data)
        ->editColumn("status", function ($data) {
            if($data->status){
                return "<div class=\"mb-2 mr-2 badge badge-pill badge-success\">Aktif</div>";
            }else{
            return "<div class=\"mb-2 mr-2 badge badge-pill badge-danger\">Non Aktif</div>";
            }         
        })       
        ->editColumn('role', function ($data){
            return ucwords($data->role);
        })
        ->addColumn('Options', function ($data){
            $edit = "<a href=".route('admin.users.edit',['id' => $data->id])." aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-info\" style=\"margin-right:0.2rem;\">
                                                <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                                                    <i class=\"pe-7s-magic-wand fa-w-20\"></i>
                                                </span>
                                                Edit
                                            </a>";
            if($data->status){
                $edit .= "<a href=\"javascript:deactivateAction(".$data->id.");\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-danger\" style=\"margin-right:0.2rem;\">
                                                <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                                                    <i class=\"fa fa-trash fa-w-20\"></i>
                                                </span>
                                                Non Aktifkan
                                            </a>";
            }else{
                $edit .= "<a href=\"javascript:activateAction(".$data->id.");\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-success\" style=\"margin-right:0.2rem;\">
                                                <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                                                    <i class=\"pe-7s-magic-wand fa-w-20\"></i>
                                                </span>
                                                Aktifkan
                                            </a>";
            }
            return $edit;
        })
        ->rawColumns(['role','Options', 'status'])
        ->make(true);
    }
}
