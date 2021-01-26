<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulir extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    function alamat_asal()
    {
        return $this->hasOne(AlamatAsal::class);
    }
    
    function asal()
    {
        return $this->hasOne(AlamatAsal::class);
    }

    function berkas_pendaftaran()
    {
        return $this->hasOne(BerkasPendaftaran::class);
    }
    
    function berkas()
    {
        return $this->hasOne(BerkasPendaftaran::class);
    }

    function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    function ayah()
    {
        return $this->hasOne(DataAyah::class);
    }

    function ibu()
    {
        return $this->hasOne(DataIbu::class);
    }

    function data_diri()
    {
        return $this->hasOne(DataDiri::class);
    }

    function diri()
    {
        return $this->hasOne(DataDiri::class);
    }

    function wali()
    {
        return $this->hasOne(DataWali::class);
    }

    function data_pendidikan()
    {
        return $this->hasOne(DataPendidikan::class);
    }
    
    function pendidikan()
    {
        return $this->hasOne(DataPendidikan::class);
    }

    function data_rencana_sekolah()
    {
        return $this->hasOne(DataRencanaSekolah::class);
    }
    
    function rencana()
    {
        return $this->hasOne(DataRencanaSekolah::class);
    }
}
