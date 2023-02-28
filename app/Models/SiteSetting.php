<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;
    const MAIL_DRIVER = ['array', 'log', 'sparkpost', 'ses', 'mandrill', 'mailgun', 'sendmail', 'smtp', 'mail'];
    const IDENTIFIER = 4646;
    const CORP_LOGO_PATH = 'images/logo/corp';
    const LOGO_PATH = 'images/logo';
    const ICON_PATH = 'icon';

    protected $fillable = [
        'site_name', 'welcome', 'site_logo', 'site_icon', 'admin_email', 'level_sistem', 'is_mail_editable',
        'mail_driver', 'mail_host', 'mail_port', 'mail_from_address', 'mail_from_name',
        'mail_encryption', 'mail_username', 'mail_password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_mail_editable' => 'boolean'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'mail_password'
    ];
}
