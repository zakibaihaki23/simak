<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Camat extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'instansi_id', 'periode', 
        'waktu', 'tempat','uraian_kegiatan', 'is_verified'];
    protected $hidden = ['created_at', 'updated_at'];

    protected $table = 'db_kegiatan_camat';
}
