<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'designation' => $this->designation,
            'department' => $this->department,
            'is_active' => $this->is_active,
            'status_label' => $this->status_label,
            'property' => new PropertyResource($this->whenLoaded('property')),
            'roles' => $this->whenLoaded('roles', fn() => $this->roles->pluck('slug')),
            'permissions' => $this->when($request->routeIs('api.v1.auth.profile'), fn() => $this->getAllPermissions()),
            'last_login_at' => $this->last_login_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
