<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function fileView($date = null, $name = null)
    {

        if ($name == '' || $date == '') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filePath = WRITEPATH . 'uploads/' . $date . "/" . $name;
        if (!file_exists($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $fp = fopen($filePath, 'rb');
        header("Content-Type: " . mime_content_type($filePath));
        header("Content-Length: " . filesize($filePath));

        fpassthru($fp);
        exit;
    }
}
