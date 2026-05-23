<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'phone' => $this->user->phone,
                    'status' => $this->user->status,
                    'created_at' => $this->user->created_at,
                ];
            }),
            'county_id' => $this->county_id,
            'county' => $this->whenLoaded('county', function () {
                return [
                    'id' => $this->county->id,
                    'name' => $this->county->name,
                    'code' => $this->county->code,
                ];
            }),
            'constituency_id' => $this->constituency_id,
            'constituency' => $this->whenLoaded('constituency', function () {
                return [
                    'id' => $this->constituency->id,
                    'name' => $this->constituency->name,
                    'code' => $this->constituency->code,
                ];
            }),
            'ward_id' => $this->ward_id,
            'ward' => $this->whenLoaded('ward', function () {
                return [
                    'id' => $this->ward->id,
                    'name' => $this->ward->name,
                    'code' => $this->ward->code,
                ];
            }),
            'passport_number' => $this->passport_number,
            'national_identification_number' => $this->national_identification_number,
            'party_membership_number' => $this->party_membership_number,
            'configuration' => $this->configuration,
            'is_featured' => $this->is_featured,
            'is_synced' => $this->is_synced,
            'metadata' => $this->metadata,
            'last_verified_at' => $this->last_verified_at,
            'verified_by' => $this->verified_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
