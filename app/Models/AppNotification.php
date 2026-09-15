<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';
    protected $fillable = ['user_id', 'title', 'message', 'link', 'is_read'];
    protected $casts = ['is_read' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public static function notify(int $userId, string $title, string $message, ?string $link = null): self
    {
        return self::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
        ]);
    }
}