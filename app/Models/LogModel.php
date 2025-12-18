<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogModel extends Model
{
    use HasFactory;
    use SoftDeletes; // [cite: 41]

    protected $table = 'log'; // [cite: 42, 46]
    protected $primaryKey = 'log_id'; // [cite: 43, 47]
    protected $fillable = [
        'log_id', 'user_id', 'log_method', 'log_url', 'log_ip', 'log_request', 'log_response'
    ]; // [cite: 44, 48]
    protected $hidden = ['created_at', 'updated_at', 'deleted_at']; // [cite: 45, 49]

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    } // [cite: 50, 51]
}
