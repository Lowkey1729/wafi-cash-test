<?php


namespace App\Services\Traits;


use App\Actions\CreateReservedAccount;
use App\Enums\ServiceType;
use App\Models\{User};
use App\Rules\Phone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait RegisterTrait
{

    protected function guard()
    {
        return Auth::guard();
    }


    protected function registered($user)
    {

    }


    protected function create($data)
    {

        $user = User::query()->create([
            'name' => Str::lower($data['name']),
            'email' => Str::lower($data['email']),
            'password' => bcrypt($data['password']),
        ]);
        $user->wallet()->create([
            'balance' => 0,
            'status' => 'ACTIVE',
        ]);
//        CreateReservedAccount::make(Str::tranxRef(ServiceType::ACCOUNT_REFERENCE()->value), $user);
        return $user->load('wallet');
    }


    protected function rules(): array
    {
        return [
            'name' => ['required', 'alpha', 'min:3', 'max:22'],
            'email' => ['required', 'string', 'email:rfc,dns', 'unique:users', 'max:255',],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.unique' => 'Email already exist.'
        ];
    }

}
