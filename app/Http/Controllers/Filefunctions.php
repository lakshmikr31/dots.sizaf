<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\File as FileModel ;
use App\Models\LightApp;
use App\Models\App;
use Illuminate\Support\Facades\Session;


class Filefunctions extends Controller
{

    public function createNewFile($fileType, $destinationParentPath)
    {
        // Resolve user-specific paths
        $destinationParentPathNew = session('userstoragepath') . $destinationParentPath;
        $destinationPath = Storage::disk('root')->path($destinationParentPathNew);
        $sourcePath = Storage::disk('public')->path('newfile.' . $fileType);
    
        // Default file name
        $newFileName = 'New File.' . $fileType;
        $actualPath = $destinationParentPathNew . '/' . $newFileName;
        $destinationFilePath = $destinationPath . '/' . $newFileName;
    
        // Handle file name conflicts
        $count = 1;
        while (file_exists($destinationFilePath)) {
            $newFileName = 'New File(' . $count . ').' . $fileType;
            $destinationFilePath = $destinationPath . '/' . $newFileName;
            $actualPath = $destinationParentPathNew . '/' . $newFileName;
            $count++;
        }
    
        // Determine the associated application
        $checkApp = checkLightApp($fileType);
        $lightApp = LightApp::where('name', $checkApp)->where('status', 1)->first();
        $lightApp = $lightApp ?? App::where('name', $checkApp)->where('status', 1)->first();
    
        // Copy the template file to the destination
        if (copy($sourcePath, $destinationFilePath)) {
            // Retrieve file metadata
            $fileTypeAlias = $this->getFiletype($destinationFilePath);

            // Create the file record in the database
            $newFile = new FileModel();
            $newFile->name = $newFileName;
            $newFile->extension = $fileType;
            $newFile->filetype = File::extension($newFileName);
            $newFile->parentpath = $destinationParentPath;
            $newFile->path = $destinationParentPath.'/'.$newFileName;
            $newFile->openwith = $lightApp ? $lightApp->id : null;
            $newFile->filehash = md5(date('d-M-Y H:i:s'));
            $newFile->status = 1; // Active
            $newFile->created_by = auth()->user()->id; // Created by logged-in user
            $newFile->save();
    
            // Prepare the return array
            $appName = $lightApp ? $lightApp->name : '';
            $appIcon = $lightApp ? $lightApp->icon : '';
            return [
                'filekey' => $newFile->id,
                'appkey' => $newFile->openwith,
                'filename' => $newFile->name,
                'appname' => $appName,
                'appicon' => $appIcon,
            ];
        }
    
        return false;
    }


    // public function createNewFile($fileatype, $destinationParentPath){
    //     $destinationPath = Storage::disk('root')->path($destinationParentPath);
    //     $sourcePath = Storage::disk('public')->path('newfile.'.$fileatype);
    //     $newFileName = 'New File.'.$fileatype;
    //     $actualpath = $destinationParentPath.'/'.$newFileName;
    //     $count = 1;
    //     $destinationfPath = $destinationPath.'/'.$newFileName;
    //     while (file_exists($destinationfPath)) {
    //         $newFileName = 'New File('.($count).').'.$fileatype;
    //         $destinationfPath = $destinationPath.'/'.$newFileName;
    //         $actualpath = $destinationParentPath.'/'.$newFileName;
    //         $count++;
    //     }
        
    //     $checkapp = checkLightApp($fileatype);
    //     $lightapp = LightApp::where('name',$checkapp)->where('status',1)->first();
    //     $lightapp = empty($lightapp) ? App::where('name',$checkapp)->where('status',1)->first():$lightapp;
    //     if (copy($sourcePath, $destinationfPath)) {
    //         $filetype = $this->getFiletype($destinationfPath);
    //         $newFile = new FileModel();
    //         $newFile->name = $newFileName;
    //         $newFile->extension = $fileatype;
    //         $newFile->filetype = $filetype;
    //         $newFile->parentpath = $destinationParentPath;
    //         $newFile->path = $actualpath;
    //         $newFile->openwith = ($lightapp) ? $lightapp->id : '';
    //         $newFile->path = $actualpath;
    //         $newFile->filehash = md5(date('d-M-Y H:i:s'));
    //         $newFile->status = 1; // Assuming 1 means active
    //         $newFile->created_by = auth()->id(); // Assuming you want to save the ID of the authenticated user
    //         $newFile->save();
    //         $appname =($lightapp) ? $lightapp->name :'';
    //         $appicon =($lightapp) ? $lightapp->icon :'';
    //         $returnarr = array('filekey'=> $newFile->id,'appkey'=> $newFile->openwith,'filename'=>$newFile->name,'appname'=>$appname,'appicon'=>$appicon);
    //         return $returnarr;

    //     }else{
    //         return false;
    //     }
    // }

    public function getDocumentType($ext) {
        $ExtsDoc = array("doc", "docm", "docx", "dot", "dotm", "dotx", "epub", "fodt", "ott", "htm", "html", "mht", "odt", "pdf", "rtf", "txt", "djvu", "xps");
        $ExtsPre = array("fodp", "odp", "pot", "potm", "potx", "pps", "ppsm", "ppsx", "ppt", "pptm", "pptx", "otp");
        $ExtsSheet = array("xls", "xlsx", "xltx", "ods", "ots", "csv", "xlt", "xltm", "fods");
        if (in_array($ext,$ExtsDoc)) {
            return "word";
        } elseif (in_array($ext,$ExtsPre)) {
            return "presentation";
        } elseif (in_array($ext,$ExtsSheet)) {
            return "spreadsheet";
        } else {
            return "undefined";
        }
    }
    
    public function fileTypeAlias($ext) {
        if (strpos(".docm.dotm.dot.wps.wpt",'.'.$ext) !== false) {
            $ext = 'doc';
        } else if (strpos(".xlt.xltx.xlsm.dotx.et.ett",'.'.$ext) !== false) {
            $ext = 'xls';
        } else if (strpos(".pot.potx.pptm.ppsm.potm.dps.dpt",'.'.$ext) !== false) {
            $ext = 'ppt';
        }
        return $ext;
    }
    
    public function getFiletype($file){
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file);
        finfo_close($finfo);
        $mimeParts = explode('/', $mime);
        return $mimeParts[0];
    }

    public function getFileSize($filePath)
    {
        // Prepend user storage path

        // Check if the file exists and return its size
        if (File::exists($filePath)) {
            return File::size($filePath);
        }
    
        return false;
    }
    
    private function folderSize($directory)
    {
        // Prepend user storage path
        $directoryPath =  $directory;
    
        // Resolve the directory path
        if (!File::exists($directoryPath)) {
            $directoryPath = Storage::disk('root')->path($directory);
        }
    
        $size = 0;
    
        // Calculate the total size of all files in the directory
        foreach (File::allFiles($directoryPath) as $file) {
            $size += $file->getSize();
        }
    
        return $size;
    }


    function saveSession(array $dataArray, $mainArrayName = null)
    {
        if ($mainArrayName) {
            $currentData = Session::get($mainArrayName, []);

            foreach ($dataArray as $key => $value) {
                if (array_key_exists($key, $currentData)) {
                    $currentData[$key] = $value;
                } else {
                    $currentData[$key] = $value;
                }
            }

            Session::put($mainArrayName, $currentData);
        } else {
            foreach ($dataArray as $key => $value) {
                if (Session::has($key)) {
                    Session::put($key, $value);
                } else {
                    Session::put($key, $value);
                }
            }
        }
    }

    // Function to reorder the array
    public function moveSubArrayToTop(&$array, $filekeyToMove) {
        foreach ($array as $key => $subArray) {
            foreach ($subArray as $item) {
                if ($item['filekey'] === $filekeyToMove) {
                    // Move the matching sub-array to the top
                    $temp = [$key => $array[$key]];
                    unset($array[$key]);
                    $array = $temp + $array;
                    break 2;
                }
            }
        }
    }
}
