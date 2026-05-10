<?php

namespace App\Http\Controllers\API;

use App\Models\Member;
use App\Http\Resources\API\MemberResource;
use App\Http\Requests\API\MemberRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::with(['user', 'county', 'constituency', 'ward'])
            ->search(request()->search)
            ->latest()
            ->paginate(request()->per_page ?? 10);

        return MemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MemberRequest $request)
    {
        $member = Member::create($request->validated());
        
        return new MemberResource($member->load(['user', 'county', 'constituency', 'ward']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        return new MemberResource($member->load(['user', 'county', 'constituency', 'ward']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MemberRequest $request, Member $member)
    {
        $member->update($request->validated());
        
        return new MemberResource($member->load(['user', 'county', 'constituency', 'ward']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
