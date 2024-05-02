<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdController extends Controller
{
    public function apply(Ad $ad, Request $request)
    {
        if ($ad->availablePositionsLeft() <= 0) {
            return response()->json([
                'message' => 'No available positions left.'
            ], 403);
        }

        $application = new Application();
        $application->user_id = $request->user()->id;
        $application->ad_id = $ad->id;
        $application->save();

        return [
            'message' => 'Success.'
        ];
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:job,volunteering',
            'name' => 'required',
            'description' => 'required',
            'max_available_positions' => 'required_if:type,job|gte:0',
        ]);

        $ad = new Ad();
        $ad->type = $validated['type'];
        $ad->name = $validated['name'];
        $ad->description = $validated['description'];

        if ($validated['max_available_positions']) {
            $ad->max_available_positions = $validated['max_available_positions'];
        }

        if ($request->hasFile('photo')) {
            $path = $request->photo->store('images', 'public');

            $ad->photo = $path;
        }

        $ad->save();
    }

    public function index(Request $request)
    {
        $collection = null;

        if ($request->query('type')) {
            $collection = Ad::where('company_id', $request->user()->id)
                ->where('type', $request->query('type'))
                ->get();
        } else {
            $collection = Ad::where('company_id', $request->user()->id)->get();
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

    public function delete(Ad $ad, Request $request)
    {
        if ($ad->company_id != $request->user()->id) {
            return response()->json([
                'message' => 'You cannot delete another company\'s ad'
            ], 403);
        }

        $ad->delete();

        return [
            'message' => 'Success.'
        ];
    }

    public function applications(Ad $ad, Request $request)
    {
        if ($ad->company_id != $request->user()->id) {
            return response()->json([
                'message' => 'You cannot view another company\'s applications'
            ], 403);
        }

        return Application::where('ad_id', $ad->id)->get()
            ->map(function (Application $application) {
                return User::find($application->user_id);
            });
    }
}
