<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Filefunctions;
use App\Models\File as FileModel;
use App\Models\User;
use App\Models\LightApp;
use App\Models\ContextType;
use App\Models\App;
use App\Models\RecycleBin;
use App\Models\Comment;
use App\Models\CommentReciver;
use App\Helpers\ActivityHelper;

use Illuminate\Support\Facades\DB;



class FileManagerController extends Controller
{
    protected $filefunctions;
    public function __construct(Filefunctions $filefunctions)
    {
        $this->filefunctions = $filefunctions;
        
    }

    public function index($path = null)
    {
        $path = $path ? $path : '/';
        
       
        return view('filemanager', compact('path'));
    }

    public function recyclebin($path = null)
    {
        //$path = $path ? base64UrlDecode($path) : '/';
        $path = $path ? $path : '/';

        return view('filemanager', compact('path'));
    }
    public function editfile($fileid)
    {
        $file = FileModel::find(base64UrlDecode($fileid));
        $options = [];
        if ($file) {
            $fileExt = $file->extension;
            $fileName = $file->name;
            // $callbackUrl = route('savedocument');
            $fileUrl = url(Storage::url('app/root/' .session('userstoragepath') . $file->path));
            $token = $file->filehash;
            $user = User::find(auth()->user()->id);
            $userName = "admin";
            if ($user) {
                $userName = $user->name;
            }

            $options = [
                'document' => [
                    'fileType' => $this->filefunctions->fileTypeAlias($fileExt),
                    'key' => $token,
                    'title' => $fileName,
                    'url' => $fileUrl,
                    'permissions' => [
                        'download' => true,
                        'edit' => true,
                        'print' => true,
                    ],
                    'version' => true,
                ],
                'documentType' => $this->filefunctions->getDocumentType($fileExt),
                'type' => 'desktop',
                'editorConfig' => [
                    'callbackUrl' => '',
                    'lang' => "en",
                    'mode' => 'edit',
                    'user' => [
                        'id' => auth()->user()->id,
                        'name' => $userName,
                    ],
                    'customization' => [
                        'autosave' => true,
                        'chat' =>  true,
                        'commentAuthorOnly' => true,
                        'comments' =>  true,
                        'compactHeader' => false,
                        'compactToolbar' => false,
                        'help' =>  false,
                        'toolbarNoTabs' => true,
                        'hideRightMenu' => true,
                    ],
                ],
                'height' => "100%",
                'width' => "100%"
            ];
        }
        return view('editor', compact('options'));
    }
    public function createFolder(Request $request)
    {
        // Retrieve the file manager application
        $fileManagerApp = App::where('name', 'Filmanager')->where('status', 1)->first();
    
        // Decode the parent folder path and add user-specific storage path
        $parentFolder = session('userstoragepath') . base64UrlDecode($request->input('parentFolder'));
        // Define the default child folder name
        $childFolder = 'New Folder';
    
        // Resolve the full paths
        $parentFolderPath = Storage::disk('root')->path($parentFolder);
        $childFolderPath = $parentFolderPath . DIRECTORY_SEPARATOR . $childFolder;
        $actualPath = $parentFolder . DIRECTORY_SEPARATOR . $childFolder;
    
        // Ensure the parent folder exists
        if (!File::exists($parentFolderPath)) {
            return response()->json(['status' => false, 'message' => 'Parent folder does not exist.']);
        }
              // print_r($childFolderPath);die;

        // Handle naming conflicts by appending a number
        $counter = 1;
        $originalChildFolderPath = $childFolderPath;
        while (File::exists($childFolderPath)) {
            $childFolderPath = $originalChildFolderPath . " ($counter)";
            $actualPath = $parentFolder . DIRECTORY_SEPARATOR . $childFolder . " ($counter)";
            $counter++;
        }
    
        // Create a new folder record in the database
        $newFolder = new FileModel();
        $newFolder->folder = 1;
        $newFolder->extension = 'folder'; // Better descriptor for folders
        $newFolder->name = basename($childFolderPath);
        $newFolder->parentpath = base64UrlDecode($request->input('parentFolder'));
        $newFolder->path = base64UrlDecode($request->input('parentFolder')).'/'.$childFolder;
        $newFolder->openwith = $fileManagerApp ? $fileManagerApp->id : null;
        $newFolder->sort_order = 0; // Default sort order
        $newFolder->status = 1; // Active status
        $newFolder->created_by = auth()->user()->id; // Set created_by field for logged-in user
    
        // Save the folder metadata and create the folder on disk
        if ($newFolder->save()) {
            File::makeDirectory($childFolderPath, 0755, true);
        }
    
        return response()->json(['status' => true, 'message' => 'Folder successfully created', 'folderName' => basename($childFolderPath)]);
    }



    public function createFile(Request $request)
    {
        $fileatype = $request->input('filetype');
        $destinationParentPath = base64UrlDecode($request->input('destination')); // 
        $resultarr = $this->filefunctions->createNewFile($fileatype, $destinationParentPath);
        if ($resultarr) {
            return response()->json(['status' => true, 'message' => 'File sucessfully created', 'fileName' => $resultarr['filename'], 'filekey' => $resultarr['filekey']]);
        } else {
            return response()->json(['status' => false, 'message' => 'Path does not exist']);
        }
    }


    public function pathfiledetail(Request $request)
    {
    $filepath = $request->input('path') ? base64UrlDecode($request->input('path')) : '/';
    $parentPath = trim((string) ($filepath ?: '/'));
    $islist = $request->input('is_list')
           ?? (Session::has('is_list') && !empty(Session::get('is_list')) 
           ? Session::get('is_list') 
           : 2);
    $sortby = $request->input('sortby') 
    ?? (Session::has('sortby') && !empty(Session::get('sortby')) 
        ? Session::get('sortby') 
        : 'id');

    $sortorder = $request->input('sortorder') 
       ?? (Session::has('sortorder') && !empty(Session::get('sortorder')) 
           ? Session::get('sortorder') 
           : 'asc');
    $iconsize = $request->input('iconsize')?? 
           (Session::has('iconsize') && !empty(Session::get('iconsize')) 
           ? Session::get('iconsize') 
           : 'medium');

    /// save session 
    $dataArray = [];

        if (!empty($islist)) {
            $dataArray['is_list'] = $islist;
        }
        if (!empty($request->input('sortby'))) {
            $dataArray['sortby'] = $sortby;
        }
        if (!empty($request->input('sortorder'))) {
            $dataArray['sortorder'] = $sortorder;
        }
        if (!empty($request->input('iconsize'))) {
            $dataArray['iconsize'] = $iconsize;
        }

        // Use the sessionSave function
        $this->filefunctions->saveSession($dataArray);
    /// 
   
    $searchterm = $request->input('search');
    // Default values for folders and files
    $defaultfolders = collect();
    $files = collect();

    if ($parentPath !== 'RecycleBin') {
        // Retrieve files and folders (excluding recycle bin)
        $defaultfolders = App::where('parentpath', $parentPath)
            ->where('filemanager_display', 1)
            ->where('status', 1)
            ->when($searchterm, function ($query) use ($searchterm) {
                $query->where('name', 'LIKE', '%' . $searchterm . '%');
            })
            ->orderBy('name')
            ->get();


        $files = FileModel::where('parentpath', $parentPath)
            ->where('status', 1)
            ->where('created_by', auth()->user()->id)
            ->when($searchterm, function ($query) use ($searchterm) {
                $query->where('name', 'LIKE', '%' . $searchterm . '%');
            })
            ->orderBy($sortby, $sortorder)
            ->get();
            $islist = ($islist==1) ? 'appendview.listview'  : 'appendview.pathview'; 
            $html = view($islist, compact('defaultfolders', 'files'))->render();
    } else {
        // Retrieve files in the recycle bin
        $files = FileModel::where('parentpath', $parentPath)
            ->where('status', 0) // Recycle bin files typically have `status` 0
            ->where('created_by', auth()->user()->id)
            ->when($searchterm, function ($query) use ($searchterm) {
                $query->where('name', 'LIKE', '%' . $searchterm . '%');
            })
            ->orderBy($sortby, $sortorder)
            ->get();
        // Render the view for the recycle bin
        $html = view('appendview.recyclebin', compact('files'))->render();
    }

    return response()->json(['html' => $html,'iconsize'=>$iconsize,'sortby'=>$sortby,'sortorder'=>$sortorder]);
}



    public function upload(Request $request)
    {
        // Log the upload activity
        ActivityHelper::log('Upload', 'From Desktop', 'India');
        $uploaddirpath = base64UrlDecode($request->header('Upload-Directory'));
        // Resolve the upload directory path with user storage path
        $uploadDirectorypath = session('userstoragepath') . base64UrlDecode($request->header('Upload-Directory'));
    
        $uploadedFiles = [];
        $uploadDirectory = Storage::disk('root')->path($uploadDirectorypath);
    
        // Ensure the upload directory exists
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }
    
        // Process uploaded files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $filePath = $uploadDirectory . DIRECTORY_SEPARATOR . $originalName;
                $actualPath = $uploadDirectorypath . DIRECTORY_SEPARATOR . $originalName;
    
                // Resolve file name conflicts
                $originalName = $this->resolveUploadFileNameConflict($originalName, $uploadDirectory);
    
                $filePath = $uploadDirectory . DIRECTORY_SEPARATOR . $originalName;
                $actualPath = $uploadDirectorypath . DIRECTORY_SEPARATOR . $originalName;
                $actualdataPath = $uploaddirpath . DIRECTORY_SEPARATOR . $originalName;
    
                // Move the file to the upload directory
                if (move_uploaded_file($file->getPathname(), $filePath)) {
                    $this->saveUploadedFileMetadata($file, $originalName, $filePath, $actualdataPath, $uploaddirpath);
                    $uploadedFiles[] = [
                        'name' => $originalName,
                        'size' => $file->getSize(),
                        'path' => $actualPath,
                    ];
                }
            }
        }
    
        return response()->json(['files' => $uploadedFiles]);
    }
    
    private function resolveUploadFileNameConflict($originalName, $directory)
    {
        $fileName = pathinfo($originalName, PATHINFO_FILENAME);
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
    
        $count = 1;
        while (file_exists($directory . DIRECTORY_SEPARATOR . $originalName)) {
            $originalName = $fileName . " ($count)." . $fileExtension;
            $count++;
        }
    
        return $originalName;
    }
    
    private function saveUploadedFileMetadata($file, $originalName, $filePath, $actualPath, $parentPath)
    {
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
        $checkApp = checkLightApp($fileExtension);
    
        // Get the associated application
        $lightApp = LightApp::where('name', $checkApp)->where('status', 1)->first();
        $lightApp = $lightApp ?? App::where('name', $checkApp)->where('status', 1)->first();
    
        // Determine file type
        $fileType = $this->filefunctions->getFiletype($filePath);
    
        // Create a new file record in the database
        $newFile = new FileModel();
        $newFile->name = $originalName;
        $newFile->extension = $fileExtension;
        $newFile->filetype = $fileType;
        $newFile->parentpath = $parentPath;
        $newFile->path = $actualPath;
        $newFile->filehash = md5(date('d-M-Y H:i:s'));
        $newFile->openwith = $lightApp ? $lightApp->id : '';
        $newFile->size = $file->getSize();
        $newFile->status = 1; // Active status
        $newFile->created_by = auth()->user()->id;
    
        $newFile->save();
    }




    public function copyFile(Request $request)
    {
        $file = array('filepath' => $request->filepath, 'type' => $request->type, 'filekey' => $request->filekey, 'filetype' => $request->filetype);
        Session::put('copyfilepath', $file);

        return response()->json(['message' => 'File ' . $request->type . ' successfully!', 'file_path' => $request->filepath, 'status' => true]);
    }

    public function pasteFile(Request $request)
    {
        $destination = base64UrlDecode($request->path);
        $fullDestinationPath = session('userstoragepath') . $destination; // Full destination path with user storage path
    
        if (!Session::has('copyfilepath')) {
            return response()->json(['message' => 'No file to paste!', 'status' => false]);
        }
    
        $sessionData = Session::get('copyfilepath');
        $fileType = $sessionData['filetype'];
        $id = base64UrlDecode($sessionData['filekey']);
        $sourcepath = session('userstoragepath') . base64UrlDecode($sessionData['filepath']); // Full source path
        $sourcePath = Storage::disk('root')->path(session('userstoragepath') . base64UrlDecode($sessionData['filepath'])); // Full source path
        $destinationPath = Storage::disk('root')->path($fullDestinationPath); // Destination path in storage
    //print_r($sourcePath);
        if (!file_exists($sourcePath)) {
            Session::forget('copyfilepath');
            return response()->json(['message' => 'Source file does not exist.', 'status' => false]);
        }
    
        $filename = pathinfo($sourcePath, PATHINFO_BASENAME);
        $isCopy = ($sessionData['type'] == 'copy');
    
        $newFileName = $this->generateUniqueFilename($filename, $destinationPath, $isCopy, $fileType);
    
        if ($fileType != 'folder') {
            // Handle file copy or move
            $newFilePath = $fullDestinationPath . '/' . $newFileName;
            $newFilePathrename = $destinationPath . '/' . $newFileName;

            $copySuccess = $isCopy
                ? Storage::disk('root')->copy($sourcepath, $newFilePath)
                : rename($sourcePath, $newFilePathrename);
    
            if ($copySuccess) {
                $this->saveFileMetadata($id, $newFileName, $fileType, $destination, $isCopy,$sourcePath);
                Session::forget('copyfilepath');
                return response()->json(['message' => 'File pasted successfully!', 'status' => true]);
            }
        } else {
            // Handle folder copy or move
            $newFolderPath = $destinationPath . '/' . $newFileName;
            $copySuccess = $isCopy
                ? File::copyDirectory($sourcePath, $newFolderPath)
                : rename($sourcePath, $newFolderPath);
    
            if ($copySuccess) {
                $this->saveFolderMetadata($id, $newFileName, $destination, $sourcePath, $newFolderPath, $isCopy);
                Session::forget('copyfilepath');
                return response()->json(['message' => 'Folder pasted successfully!', 'status' => true]);
            }
        }
    
        Session::forget('copyfilepath');
        return response()->json(['message' => 'Failed to paste file or folder.', 'status' => false]);
    }
    
    private function generateUniqueFilename($filename, $destinationPath, $isCopy, $fileType)
    {
        $baseName = File::name($filename);
        $extension = ($fileType != 'folder') ? '.' . File::extension($filename) : '';
        $newFileName = $isCopy ? $baseName . ' - Copy' : $baseName;
    
        $counter = 1;
        $uniquePath = $destinationPath . '/' . $newFileName . $extension;
    
        while (file_exists($uniquePath)) {
            $newFileName = $isCopy
                ? $baseName . ' - Copy (' . $counter . ')'
                : $baseName . ' (' . $counter . ')';
            $uniquePath = $destinationPath . '/' . $newFileName . $extension;
            $counter++;
        }
    
        return $newFileName . $extension;
    }
    
    private function saveFileMetadata($id, $fileName, $fileType, $destination, $isCopy,$sourcePath)
    {
        if ($isCopy) {
            $checkApp = checkLightApp($fileType);
            $lightApp = LightApp::where('name', $checkApp)->where('status', 1)->first();
            $newFile = new FileModel();
            $newFile->name = $fileName;
            $newFile->extension = File::extension($fileName);
            $newFile->filetype = getFiletype($sourcePath);
            $newFile->parentpath = $destination; // Only the relative path is stored
            $newFile->path = $destination . '/' . $fileName; // Relative path for the file
            $newFile->openwith = $lightApp ? $lightApp->id : null;
            $newFile->status = 1;
            $newFile->created_by = auth()->user()->id;
            $newFile->save();
        } else {
            FileModel::where('id', $id)->update([
                'path' => $destination . '/' . $fileName, // Relative path
                'parentpath' => $destination, // Relative path
            ]);
        }
    }
    
    private function saveFolderMetadata($id, $folderName, $destination, $sourcePath, $newFolderPath, $isCopy)
    {
            $loggedInUserId = auth()->user()->id;

        if ($isCopy) {
            $fileManagerApp = App::where('name', 'Filmanager')->where('status', 1)->first();
            $newFolder = new FileModel();
            $newFolder->folder = 1;
            $newFolder->extension = 'folder';
            $newFolder->name = $folderName;
            $newFolder->parentpath = $destination; // Only the relative path is stored
            $newFolder->path = $destination . '/' . $folderName; // Relative path for the folder
            $newFolder->openwith = $fileManagerApp ? $fileManagerApp->id : null;
            $newFolder->status = 1;
            $newFolder->created_by = auth()->user()->id;
            $newFolder->save();
    
            // Copy folder contents
            $folderContents = FileModel::where('parentpath', $sourcePath)->where('created_by', $loggedInUserId)->get();
            foreach ($folderContents as $content) {
                $newContent = $content->replicate();
                $newContent->parentpath = $destination . '/' . $folderName; // Relative path for new content
                $newContent->path = $destination . '/' . $folderName . '/' . $content->name; // Relative path
                $newContent->save();
            }
        } else {
            FileModel::where('id', $id)->update([
                'path' => $destination . '/' . $folderName, // Relative path
                'parentpath' => $destination, // Relative path
            ]);
        }
    }
    
    

    public function downloadFile($id)
    {
        $id = base64UrlDecode($id);
        $file = FileModel::findOrFail($id);
        $filePath = Storage::disk('root')->path($file->path);
        $fileName = basename($filePath);
        return response()->download($filePath, $fileName, ['Content-Disposition' => 'attachment']);
    }

    public function renameFile(Request $request)
    {
        $type = $request->input('filetype');
        $id = base64UrlDecode($request->input('filekey'));
        $newName = $request->input('name');
        $loggedInUserId = auth()->user()->id;

    
        // Validate the new name
        if (empty($newName)) {
            return response()->json(['message' => 'Please enter something to rename.', 'status' => false]);
        }
    
        // Retrieve the file or folder
        $file = FileModel::findOrFail($id);
    
        // Check if the new name is the same as the current name
        if ($file->name === $newName) {
            return response()->json(['message' => 'Renamed successfully.', 'status' => true]);
        }
    
        // Check if a file or folder with the new name already exists in the same parent path
        $existingFile = FileModel::where('name', $newName)
            ->where('parentpath', $file->parentpath)
            ->where('created_by', $loggedInUserId)
            ->exists();
    
        if ($existingFile) {
            return response()->json(['message' => 'A file or folder with this name already exists.', 'status' => false]);
        }
    
        // Get the current and new paths
        $currentPath = Storage::disk('root')->path(session('userstoragepath') . $file->path);
        $newPath = Storage::disk('root')->path(session('userstoragepath') . $file->parentpath . '/' . $newName);
    
        // If it's a file, ensure the extension is preserved or added
        if ($type != 'folder') {
            $fileExtension = pathinfo($currentPath, PATHINFO_EXTENSION);
            $newExtension = pathinfo($newName, PATHINFO_EXTENSION);
    
            // Append the current extension if it's missing in the new name
            if (empty($newExtension)) {
                $newPath .= '.' . $fileExtension;
                $newName .= '.' . $fileExtension;
            }
        }
    
        // Rename the file or folder
        if (rename($currentPath, $newPath)) {
            $file->name = $newName;
            $file->path = $file->parentpath . '/' . $newName;
            $file->save();
    
            return response()->json(['message' => 'Renamed successfully.', 'status' => true]);
        }
    
        return response()->json(['message' => 'Failed to rename.', 'status' => false]);
    }


    public function deleteFile(Request $request)
    {
        // dd($request->all());
        $fileKey = base64UrlDecode($request->input('filekey'));




        $file = FileModel::find($fileKey);
        if (!$file) {
            return response()->json(['message' => 'File not found', 'status' => false]);
        }


        if ($file->status == '0') {
            //MaterRecycleBin

            $file->status = 2;
            $file->save();
            $currentPath = 'root/' . 'RecycleBin';

            $materRecycleBinPath = 'root/MaterRecycleBin/';
            $newFileName = $fileKey . '-' . $file->name;
            $newPath = $currentPath . '/' . $newFileName;
            /*echo $newPath;
                 die;*/
            try {
                if (Storage::exists($newPath)) {
                    Storage::move($newPath, $materRecycleBinPath . $newFileName);
                } else {
                    return response()->json(['message' => 'File does not exist', 'status' => false]);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to move the file: ' . $e->getMessage(), 'status' => false]);
            }

            return response()->json(['message' => 'File Deleted', 'status' => true]);
        } else {

            $file->status = 0;
            $file->save();


            $currentPath = 'root/' . $file->path;
            $recycleBinPath = 'root/RecycleBin/';
            $newFileName = $fileKey . '-' . $file->name;

            try {
                if (Storage::exists($currentPath)) {
                    Storage::move($currentPath, $recycleBinPath . $newFileName);
                } else {
                    return response()->json(['message' => 'File does not exist', 'status' => false]);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to move the file: ' . $e->getMessage(), 'status' => false]);
            }

            return response()->json(['message' => 'File moved to RecycleBin', 'status' => true]);
        }
    }

    public function restoreFile(Request $request)
    {
        $fileKey = base64UrlDecode($request->input('filekey'));

        // Find the file in the database
        $file = FileModel::find($fileKey);
        if (!$file) {
            return response()->json(['message' => 'File not found', 'status' => false]);
        }

        // Update the file status to active (1) or original status
        $file->status = 1;
        $file->save();

        // Paths
        $recycleBinPath = 'root/RecycleBin/';
        $originalPath = 'root/' . $file->path;
        $fileName = $fileKey . '-' . $file->name;

        // Move the file back to its original location
        try {
            if (Storage::exists($recycleBinPath . $fileName)) {
                Storage::move($recycleBinPath . $fileName, $originalPath);
            } else {
                return response()->json(['message' => 'File does not exist in RecycleBin', 'status' => false]);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restore the file: ' . $e->getMessage(), 'status' => false]);
        }

        return response()->json(['message' => 'File restored successfully', 'status' => true]);
    }


    public function contextMenu(Request $request)
    {
        $clicktype = $request->input('type');
        $clicktype = $request->input('type');
        if ($clicktype == 'rightclick' || $clicktype == 'recyclebin') {
            $contextTypes = ContextType::with(['contextOptions' => function ($query) {
                $query->orderBy('sort_order', 'asc'); // Sort options by sort_order
            }])
                ->where('show_on', $clicktype)
                ->orderBy('sort_order', 'asc') // Sort context types by sort_order
                ->get();
        } else {
            $contextTypes = ContextType::with(['contextOptions' => function ($query) {
                $query->orderBy('sort_order', 'asc'); // Sort options by sort_order
            }])
                ->whereIn('show_on', [$clicktype, 'all'])
                ->orderBy('sort_order', 'asc') // Sort context types by sort_order
                ->get();
        }

        $html = view('appendview.clickoption')->with('contextTypes', $contextTypes)->with('type', $clicktype)->render();
        return response()->json(['html' => $html]);
    }


    public function dotsImageViewer($file)
    {
        $file = FileModel::find(base64UrlDecode($file));
        return view('dotsimageviewer', compact('file'));
    }
    public function dotsVideoPlayer($file)
    {
        $file = FileModel::find(base64UrlDecode($file));
        return view('dotsvideoplayer', compact('file'));
    }
    public function dotsDocumentViewer($file)
    {
        $file = FileModel::find(base64UrlDecode($file));
        return view('dotsdocumentviewer', compact('file'));
    }

    public function leftArrowClick(Request $request)
    {
        $filepath = $request->input('path');
        if (!empty($filepath) && $filepath != "/") {
            Session::put('rightarrowpath', $filepath);
        } else {
            Session::forget('rightarrowpath');
        }
    }
    public function rightArrowClick(Request $request)
    {
        Session::forget('rightarrowpath');
    }
    public function moveFiles(Request $request)
    {
        $fileKeys = $request->input('fileKeys', []);
        $folderKeys = $request->input('folderKeys', []);
        $targetFolder = base64UrlDecode($request->input('targetFolder'));
        $loggedInUserId = auth()->user()->id;
    
        // Resolve the full target path
        $fullTargetPath = session('userstoragepath') . $targetFolder;
    
        // Find the target folder record, owned by the logged-in user
        $targetFolderRecord = FileModel::where('path', $targetFolder)
            ->where('folder', 1)
            ->where('created_by', $loggedInUserId)
            ->first();
    
        if (!$targetFolderRecord) {
            return response()->json(['status' => false, 'message' => 'Target folder not found.']);
        }
    
        // Move files and folders
        foreach ($fileKeys as $fileKey) {
            $this->moveFile($fileKey, $fullTargetPath, $targetFolderRecord->path);
        }
    
        foreach ($folderKeys as $folderKey) {
            $this->moveFolderAndContents($folderKey, $fullTargetPath, $targetFolderRecord->path);
        }
    
        return response()->json(['status' => true, 'message' => 'Files and folders moved successfully.']);
    }
    
    protected function moveFile($fileKey, $fullTargetPath, $relativeTargetPath)
    {
        $loggedInUserId = auth()->user()->id;
    
        $fileToMove = FileModel::where('id', base64UrlDecode($fileKey))
            ->where('created_by', $loggedInUserId)
            ->first();
    
        if (!$fileToMove || $fileToMove->folder == 1) {
            return;
        }
    
        $newPath = $relativeTargetPath . '/' . $fileToMove->name;
        $fullNewPath = $this->checkFilePathConflict($fullTargetPath . '/' . $fileToMove->name);
    
        try {
            // Move the file in the filesystem
            Storage::disk('root')->move(session('userstoragepath') . $fileToMove->path, $fullNewPath);
    
            // Update database record
            $fileToMove->path = $newPath;
            $fileToMove->parentpath = $relativeTargetPath;
            $fileToMove->save();
        } catch (\Exception $e) {
            throw new \Exception('Error moving file: ' . $fileToMove->name);
        }
    }
    
    protected function moveFolderAndContents($folderKey, $fullTargetPath, $relativeTargetPath)
    {
        $loggedInUserId = auth()->user()->id;
    
        $folderToMove = FileModel::where('id', base64UrlDecode($folderKey))
            ->where('folder', 1)
            ->where('created_by', $loggedInUserId)
            ->first();
    
        if (!$folderToMove) {
            return;
        }
    
        $newFolderPath = $relativeTargetPath . '/' . $folderToMove->name;
        $fullNewFolderPath = $this->checkFilePathConflict($fullTargetPath . '/' . $folderToMove->name);
    
        // Create the new folder
        Storage::disk('root')->makeDirectory($fullNewFolderPath);
    
        // Move files within the folder
        $filesInFolder = FileModel::where('parentpath', $folderToMove->path)
            ->where('folder', 0)
            ->where('created_by', $loggedInUserId)
            ->get();
    
        foreach ($filesInFolder as $file) {
            $newFilePath = $newFolderPath . '/' . $file->name;
            $fullNewFilePath = $this->checkFilePathConflict($fullNewFolderPath . '/' . $file->name);
    
            // Move the file
            Storage::disk('root')->move(session('userstoragepath') . $file->path, $fullNewFilePath);
    
            // Update database record
            $file->path = $newFilePath;
            $file->parentpath = $newFolderPath;
            $file->save();
        }
    
        // Recursively move subfolders
        $subfolders = FileModel::where('parentpath', $folderToMove->path)
            ->where('folder', 1)
            ->where('created_by', $loggedInUserId)
            ->get();
    
        foreach ($subfolders as $subfolder) {
            $this->moveFolderAndContents(base64UrlEncode($subfolder->id), $fullNewFolderPath, $newFolderPath);
        }
    
        // Remove the original folder from storage
        $this->removeOriginalFolder(session('userstoragepath') . $folderToMove->path);
    
        // Update folder database record
        $folderToMove->parentpath = $relativeTargetPath;
        $folderToMove->path = $newFolderPath;
        $folderToMove->save();
    }
    
    protected function removeOriginalFolder($fullPath)
    {
        if (Storage::disk('root')->exists($fullPath)) {
            Storage::disk('root')->deleteDirectory($fullPath);
        }
    }

}
