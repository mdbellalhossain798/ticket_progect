<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketDtl extends Model
{
    use HasFactory;


    public function master()
    {
        return $this->belongsTo(TicketMst::class, 'ticket_mst_id');
    }
}
