<?php


namespace App\Http\Controllers\Api\Auth;


use App\Enums\DeviceEnum;
use Illuminate\Http\Request;

trait AuthTrait
{

    protected function rules(): array
    {
        return [
            'email' => ['required'],
            'password' => ['required'],
        ];
   }
}
