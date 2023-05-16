<?php

namespace App\Http\Controllers;

use App\Http\Resources\CandidatoCollection;
use App\Models\Candidato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class CandidatoController extends Controller
{

    // Create a new Candidato record.
    public function create(Request $request)
    {
        // Validate the incoming request data.
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'owner' => 'required|numeric'
        ]);

        // If the validator fails, return JSON response with validation errors and HTTP status code 422 (Unprocessable Entity).
        if ($validator->fails()) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => $validator->errors()
                ],
            ], 422);
        }

        // Retrieve the authenticated user's role.
        $role = Auth::user()->role;
        // Check if the authenticated user has manager role
        if ($role === 'manager') {
            // Create an array to hold the candidate data.
            $array_candidato = array(
                'name' => $request->name,
                'source' => $request->source,
                'owner' => $request->owner,
                'created_by' => Auth::user()->id
            );

            // Attempt to create a new Candidato model instance using the candidate data.
            if($candidato = Candidato::create($array_candidato)){
                // Return a JSON response with the newly created candidate's data and HTTP status code 200 (OK) if the creation is successful.
                return response()->json([
                    'meta' => [
                        'success' => true,
                        'errors' => [],
                    ],
                    'data' => array(
                        'id' => $candidato->id,
                        'name' => $candidato->name,
                        'source' => $candidato->source,
                        'owner' => $candidato->owner,
                        'created_at' => date("Y-m-d H:m:s", strtotime($candidato->created_at)),
                        'created_by' => $candidato->created_by
                    ),
                ]);
            } else {
                // Return an error response with HTTP status code 500 (Internal Server Error) if there was an error creating the new Candidato instance.
                return response()->json([
                    'meta' => [
                        'success' => false,
                        'errors' => ["Error create lead"]
                    ],
                ], 500);
            }
        } else {
            // Return an error response with HTTP status code 500 if the user doesn't have sufficient permissions.
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ["Insufficient permissions"]
                ],
            ], 500);
        }
    }

    // This function retrieves a candidate with the given ID.
    public function get($id)
    {
        $candidato = Candidato::find($id);

        // If no candidate is found, return an error response with HTTP status code 500.
        if (!$candidato) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ["No lead found"]
                ],
            ], 500);
        }

        // Retrieve the authenticated user's role and ID.
        $role = Auth::user()->role;
        $userId = Auth::user()->id;

        // Check if the authenticated user has manager role or is the owner of the lead.
        if ($role === 'manager' || $userId === $candidato->owner) {
            // Return JSON response with the candidate's data and HTTP status code 200.
            return response()->json([
                'meta' => [
                    'success' => true,
                    'errors' => [],
                ],
                'data' => array(
                    'id' => $candidato->id,
                    'name' => $candidato->name,
                    'source' => $candidato->source,
                    'owner' => $candidato->owner,
                    'created_at' => $candidato->created_at->format("Y-m-d H:m:s"),
                    'created_by' => $candidato->created_by
                ),
            ]);
        } else {
            // Return an error response with HTTP status code 500 if the user doesn't have sufficient permissions.
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ["Insufficient permissions"]
                ],
            ], 500);
        }
    }

    // This function retorne all candidates
    public function all()
    {
        // Get the user's role and ID.
        $role = Auth::user()->role;
        $userId = Auth::user()->id;

        // Try to get cached data for candidates.
        $cachedData = Cache::get('candidatos');
        if ($cachedData) {
            // Return cached data if it exists.
            return response()->json([
                'meta' => [
                    'success' => true,
                    'errors' => []
                ],
                'data' => $cachedData
            ]);
        }

        // Retrieve candidates based on the user's role.
        $candidatos = ($role === 'manager')
            ? Candidato::all()
            : Candidato::where('owner', $userId)->get();

        // Create a new collection of candidates.
        $colletion_candidatos = new CandidatoCollection($candidatos);

        // Cache the collection for 24 hours.
        Cache::put('candidatos', $colletion_candidatos, 1440);

        // Return the collection as JSON response.
        return response()->json([
            'meta' => [
                'success' => true,
                'errors' => []
            ],
            'data' => $colletion_candidatos
        ]);
    }

}
