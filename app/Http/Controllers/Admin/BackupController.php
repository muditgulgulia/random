<?php

namespace App\Http\Controllers\Admin;

use App\ACME\Admin\AdminHelper;
use Artisan;
use Exception;
use Illuminate\Http\Request;
use League\Flysystem\Adapter\Local;
use Log;
use Response;
use Storage;

//use Request;
class BackupController extends AdminHelper
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (!count(config('backup.backup.destination.disks'))) {
            dd(trans('backpack::backup.no_disks_configured'));
        }

        $this->data['backups'] = [];
        foreach (config('backup.backup.destination.disks') as $disk_name) {
            $disk = Storage::disk($disk_name);
            $adapter = $disk->getDriver()->getAdapter();
            $files = $disk->allFiles();
            // make an array of backup files, with their filesize and creation date
            foreach ($files as $k => $f) {
                // only take the zip files into account
                if (substr($f, -4) == '.zip' && $disk->exists($f)) {
                    $this->data['backups'][] = [
                        'file_path' => $f,
                        'file_name' => str_replace('backups/', '', $f),
                        'file_size' => $disk->size($f),
                        'last_modified' => $disk->lastModified($f),
                        'disk' => $disk_name,
                        'download' => ($adapter instanceof Local) ? true : false,
                    ];
                }
            }
        }
        // reverse the backups, so the newest one would be on top
        $this->data['backups'] = array_reverse($this->data['backups']);
        $this->data['title'] = 'Backups';
        return view('admin.backup.backup', $this->data);
    }

    /**
     * @return string
     */
    public function create()
    {
        try {
            ini_set('max_execution_time', 300);
            // start the backup process
            Artisan::call('backup:run');
            $output = Artisan::output();
            // log the results
            Log::info("Backpack\BackupManager -- new backup started from admin interface \r\n" . $output);
            // return the results as a response to the ajax call
            echo $output;
        } catch (Exception $e) {
            Response::make($e->getMessage(), 500);
            var_dump($e);
        }
        return 'success';
    }

    /**
     * Downloads a backup zip file.
     */
    public function download()
    {
        $disk = Storage::disk(Request::input('disk'));
        $file_name = Request::input('file_name');
        $adapter = $disk->getDriver()->getAdapter();
        if ($adapter instanceof Local) {
            $storage_path = $disk->getDriver()->getAdapter()->getPathPrefix();
            if ($disk->exists($file_name)) {
                return response()->download($storage_path . $file_name);
            } else {
                abort(404, trans('backpack::backup.backup_doesnt_exist'));
            }
        } else {
            abort(404, trans('backpack::backup.only_local_downloads_supported'));
        }
    }

    /**
     * Deletes a backup file.
     */
    public function destory($file_name)
    {
        $disk = Storage::disk(Request::input('disk'));
        if ($disk->exists($file_name)) {
            $disk->delete($file_name);
            return 'success';
        } else {
            abort(404, trans('backpack::backup.backup_doesnt_exist'));
        }
    }
}