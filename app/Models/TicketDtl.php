<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketDtl extends Model
{
    use HasFactory;
    protected $table = 'ticket_dtls'; 
    public $timestamps = false;
    protected $fillable = [
        'reply_details',
        'ticket_mst_id',
        'reply_by',
        'created_at',
    ];


    public function master()
    {
        return $this->belongsTo(TicketMst::class, 'ticket_mst_id');
    }
}
