<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintTemplate;

class PrintController extends Controller
{
    public function dashboard()
    {
        return view('pages.admin.print.dashboard');
    }

    public function templates()
    {
        return view('pages.admin.print.templates');
    }

    public function builder(?PrintTemplate $template = null)
    {
        return view('pages.admin.print.builder', compact('template'));
    }

    public function paperSizes()
    {
        return view('pages.admin.print.paper-sizes');
    }

    public function printerProfiles()
    {
        return view('pages.admin.print.printer-profiles');
    }

    public function branding()
    {
        return view('pages.admin.print.branding');
    }

    public function headerBuilder()
    {
        return view('pages.admin.print.header');
    }

    public function footerBuilder()
    {
        return view('pages.admin.print.footer');
    }

    public function fonts()
    {
        return view('pages.admin.print.fonts');
    }

    public function qrBarcode()
    {
        return view('pages.admin.print.qr-barcode');
    }

    public function variables()
    {
        return view('pages.admin.print.variables');
    }

    public function preview()
    {
        return view('pages.admin.print.preview');
    }

    public function versions(PrintTemplate $template)
    {
        return view('pages.admin.print.versions', compact('template'));
    }

    public function importExport()
    {
        return view('pages.admin.print.import-export');
    }

    public function settings()
    {
        return view('pages.admin.print.settings');
    }
}
