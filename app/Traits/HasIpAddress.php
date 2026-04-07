<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasIpAddress
{
    /**
     * Get the IP address of the request.
     *
     * @return string|null
     */
    protected function getIpAddress(): ?string
    {
        return request()->ip();
    }
}
