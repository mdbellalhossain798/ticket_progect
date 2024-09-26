<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMst extends Model
{
    use HasFactory;
    protected $table = 'ticket_msts'; 
    public $timestamps = false;
    protected $fillable = [
        'subject',
        'ticket_details',
        'ticket_type',
        'user_id',
        'closed_by',
        'open_date',
        'closed_date',
        'created_at',
        'updated_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
    public function details()
    {
        return $this->hasMany(TicketDtl::class, 'ticket_mst_id');
    }
}
