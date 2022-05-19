<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

use Auth;
use Gate;
use App\Models\User;

use App\Http\Traits\DataTablesTrait;

class UserController extends Controller
{

    use DataTablesTrait;
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
        $role = 
        [
            "admin",
            "author",
            "editor"
        ];
        $user = User::find($id);
        if($user != null){
            return view('admin.pages.users.edit', ['user' => $user, 'role' => $role]);
            // return response()->json(['success' => true, 'message' => "Found!", 'user' => $user], 200);
        }else{
            // return response()->json(['success' => false, 'message' => "not found", 'user' => ''], 404);
            abort(404);
        }        
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
            $user->log_name = $request->name;
            $user->log_password = ($request->new_password!= null ? Hash::make($request->password) : $user->log_password);
            $user->email = $request->email;
            $user->name = ucwords($request->name);            
            $user->role = $request->role;
            $user->save();
            return redirect()->route('admin.users.view');
        } catch (\Throwable $th) {
            abort(500);
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
        return $this->AllUserListDt();
    }
}
