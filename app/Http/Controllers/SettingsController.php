<?php

namespace App\Http\Controllers;

use App\Models\UserWallpaper;
use App\Models\Wallpaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;
class SettingsController extends Controller
{
    public function index()
    {
        $desktopWallpapers = Wallpaper::where('type', 0)
            ->where('status', 1)
            ->where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $loginWallpapers = Wallpaper::where('type', 1)
            ->where('status', 1)
            ->where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        $userWallpaper = UserWallpaper::where('user_id', Auth::id())->first();
        if ($userWallpaper) {
            $desktopWallpaper = Wallpaper::find($userWallpaper->dashboard_display);
            $loginWallpaper = Wallpaper::find($userWallpaper->login_display);
            $user = $userWallpaper->dashboard_display = $desktopWallpaper ? $desktopWallpaper->image : 'Wallpaper.png';
            $login = $userWallpaper->login_display = $loginWallpaper ? $loginWallpaper->image : 'Wallpaper.png';
            // dd($user);
        }
        return view('dashboard', compact('desktopWallpapers', 'loginWallpapers', 'user', 'login'));
    }


    public function storeWallpaper(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:1024',
        ], [
            'image.max' => 'The image may not be greater than 1 MB.',
        ]);
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = $request->type == 0
                ? public_path('images/wallpapers/dashboard')
                : public_path('images/wallpapers/login');
    
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }
    
            $image->move($destinationPath, $imageName);
    
            $wallpaper = Wallpaper::create([
                'image' => $imageName,
                'type' => $request->type,
                'status' => 1,
                'created_by' => Auth::id(),
                'default' => 0
            ]);
    
            $imagePath = $request->type == 0
                ? url('public/images/wallpapers/dashboard/' . $imageName)
                : url('public/images/wallpapers/login/' . $imageName);
    
            $user = Auth::user();
            Notification::send($user, new GeneralNotification("Wallpaper Upload", "A new wallpaper has been uploaded."));
    
            return response()->json([
                'success' => true,
                'message' => 'Wallpaper uploaded successfully!',
                'image' => $imagePath,
                'wallpaper_id' => $wallpaper->id,
                'type' => $request->type
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload wallpaper.'
            ]);
        }
    }
    
    public function deleteWallpaper($id)
    {
        $wallpaper = Wallpaper::find($id);
        if (!$wallpaper) {
            return response()->json([
                'success' => false,
                'message' => 'Wallpaper not found.'
            ]);
        }
        $wallpaper->status = 0;
        $wallpaper->save();
        $userWallpapers = UserWallpaper::where('dashboard_display', $wallpaper->id)
                            ->orWhere('login_display', $wallpaper->id)
                            ->get();
    
        $defaultWallpaper = 'Wallpaper.png'; 
        foreach ($userWallpapers as $userWallpaper) {
            if ($userWallpaper->dashboard_display == $wallpaper->id) {
                $userWallpaper->dashboard_display = $defaultWallpaper;
            }
            if ($userWallpaper->login_display == $wallpaper->id) {
                $userWallpaper->login_display = $defaultWallpaper;
            }
            $userWallpaper->save();
        }
        $user = Auth::user();
        Notification::send($user, new GeneralNotification("Wallpaper Deleted", "A wallpaper has been removed from your account."));
    
        return response()->json([
            'success' => true,
            'message' => 'Wallpaper deleted successfully!'
        ]);
    }
    
    public function updateUserWallpaper(Request $request)
    {
        $userId = Auth::id();
        $type = $request->input('type');
        $wallpaperId = $request->input('wallpaper_id');
        $defaultWallpaper = 'Wallpaper.png';
        $wallpaper = Wallpaper::find($wallpaperId);
        if (!$wallpaper) {
            $wallpaperId = $defaultWallpaper;
            $imagePath = $type == 0
                ? url('public/images/wallpapers/dashboard/' . $defaultWallpaper)
                : url('public/images/wallpapers/login/' . $defaultWallpaper);
        } else {
            $imagePath = $type == 0
                ? url('public/images/wallpapers/dashboard/' . $wallpaper->image)
                : url('public/images/wallpapers/login/' . $wallpaper->image);
        }
        
        $userWallpaper = UserWallpaper::updateOrCreate(
            ['user_id' => $userId],
            [
                'dashboard_display' => $type == 0 ? $wallpaperId : UserWallpaper::where('user_id', $userId)->value('dashboard_display'),
                'login_display' => $type == 1 ? $wallpaperId : UserWallpaper::where('user_id', $userId)->value('login_display'),
            ]
        );
    
        $user = Auth::user();
        $wallpaperType = $type == 0 ? "Dashboard" : "Login";
        Notification::send($user, new GeneralNotification("Wallpaper Set", "Your $wallpaperType wallpaper has been updated."));
    
        return response()->json([
            'success' => true,
            'message' => 'Wallpaper updated successfully.',
            'image' => $imagePath 
        ]);
    }
    public function showLoginPage()
    {
        $userWallpaper = UserWallpaper::where('user_id', Auth::id())->first();
        $loginWallpaper = $userWallpaper ? url('images/wallpapers/login/' . $userWallpaper->login_display) : url('images/wallpapers/login/Wallpaper.png');

        return view('auth.login', compact('loginWallpaper'));
    }
    public function getWallpapers(Request $request)
    {
        dd($request);
        $type = $request->query('type');
        $userId = Auth::id();
    
        if ($type === 'desktop') {
            $desktopWallpapers = Wallpaper::where('type', 0)
                ->where('status', 1)
                ->where('created_by', $userId)
                ->get(['id', 'image']);
            return response()->json(['desktopWallpapers' => $desktopWallpapers]);
        } elseif ($type === 'login') {
            $loginWallpapers = Wallpaper::where('type', 1)
                ->where('status', 1)
                ->where('created_by', $userId)
                ->get(['id', 'image']);
            return response()->json(['loginWallpapers' => $loginWallpapers]);
        }
    
        return response()->json(['error' => 'Invalid type'], 400);
    }

}
