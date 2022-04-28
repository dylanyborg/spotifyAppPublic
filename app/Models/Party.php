<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;

    //get user that hosts the party
    public function host(){
        //return 
        return $this->belongsTo(User::class, 'host_id'); //double check this
    }

    //get all users in the party
    public function users() {
        return $this->hasMany(User::class);
    }

    protected $fillable = [
        'host_id',
        'partyName',
        'password',
        'hideHostLibrary',
        
    ];

    protected $hidden = [
        'password',
    ];


}
