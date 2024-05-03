<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function index(Request $request)
    {
        $collection = null;

        if ($request->query('types')) {
            $types = explode(',', $request->query('types'));

            $collection = Ad::whereIn('type', $types)->get();
        } else {
            $collection = Ad::all();
        }

        return $collection
            ->map(function (Ad $ad) {
                return [
                    'id' => $ad->id,
                    'name' => $ad->name,
                    'description' => $ad->description,
                    'photo' => $ad->photo,
                ];
            });
    }

    public function show(Ad $ad, Request $request)
    {
        return [
            'id' => $ad->id,
            'name' => $ad->name,
            'description' => $ad->description,
            'photo' => $ad->photo,
            'positions_left' => $ad->availablePositionsLeft(),
            'company' => User::find($ad->company_id),
        ];
    }
}
