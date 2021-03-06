<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'auth_level', 'location', 'birth_date',
        'occupation', 'username', 'email', 'password',
    ];



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'birth_date' => 'date',
    ];
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    public static function createUser(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'auth_level' => 2,
            'location' => $data['location'],
            'birth_date' => $data['birth_date'],
            'occupation' => $data['occupation'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    protected function createStaff(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'auth_level'=>3,
        ]);
    }

    public static function updateUserStaff( array $data )
    {
        $user = User::find($data['id']);
        if($user == null) return false;

        // utente trovato
        $user->first_name = $data['nome'];
        $user->last_name = $data['cognome'];

        return $user->save();
    }

    public function getFullNameAttribute()
    {
        if($this->isStaff()) return "Staff";
        if($this->isAdmin()) return "Admin";
        return ucwords($this->first_name." ".$this->last_name);
    }

    public static function updateCurrentUser(array $data)
    {
        $user = User::find(Auth::user()->id);
        if ($user == null) return false;

        // utente trovato
        $user->first_name = $data['nome'];
        $user->last_name = $data['cognome'];
        $user->location = $data['residenza'];
        $user->birth_date = $data['datanascita'];
        $user->occupation = $data['occupazione'];
        return $user->save();
    }

    public static function updateCurrentUserAccess(array $data)
    {
        $user = User::find(Auth::user()->id);
        if ($user == null) return false;

        // utente trovato
        $user->email = $data['email'];
        $user->password =  Hash::make($data['password']);
        return $user->save();
    }


    public function isAdmin() {
        return $this->auth_level === 4;
    }

    public function isStaff() {
        return $this->auth_level === 3;
    }
}
