<?php

namespace App\Http\Controllers;

use App\Models\HotelPrivilege;
use App\Models\Infrastructure;
use App\Models\Variable;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;

class HotelController extends Controller
{
    public function index()
    {
        $variable = Variable::whereSlug('hotel')->first();
        $hotelPrivileges = HotelPrivilege::orderBy('position')->get();
        $infrastructures = Infrastructure::with('attachment')->orderBy('position')->get();
        $hotelBanners = Attachment::where('group', 'hotel-banners')->get();
        $facilitiesVariable = Variable::whereSlug('hotel-facilities')->first();
        $servicesVariable = Variable::whereSlug('hotel-services')->first();
//	dd($infrastructures);
        return view('v2.hotel', compact('variable', 'hotelPrivileges', 'infrastructures', 'hotelBanners', 'facilitiesVariable', 'servicesVariable'));
    }
}
