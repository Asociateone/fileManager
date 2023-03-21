<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{

    protected $fillable = [
        "original_name",
        "extension",
        "upload_name",
        "user_id",
        "password",
        "created_at",
        "updated_at",
    ];

    protected $casts = [
        "user_id" => "integer",
        "created_at" => "date:Y-m-d H:i:s",
        "updated_at" => "date:Y-m-d H:i:s",
    ];

    public function scopeWhereId($query, $id){
        return $query
            ->where("id", $id);
    }

    public function scopeWhereUserId($query, $id){
        return $query
            ->where("user_id", $id);
    }

    public function scopeWherePassword($query, $password){
        return $query
            ->where("password", $password);
    }
}
