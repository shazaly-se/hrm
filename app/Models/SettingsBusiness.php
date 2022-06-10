<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingsBusiness extends Model
{
    use HasFactory;
    protected $fillable = [
        'logo','landing_page_logo','favicon','title_text','footer_text','default_language','rtl','gdpr_cookie','gdpr_cookie_discription','branch_id','created_by','updated_by',
    ];
}
