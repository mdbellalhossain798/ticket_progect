@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Customer Dashboard') }}</div>

                <div class="card-body">
                    <form action="{{route('customer.save-ticket')}}" id="ticket_save" method="post">
                        @csrf
                        <label for="ticket_subject"> Ticket Subject</label>
                        <input type="text" class="form-control" id="ticket_subject" name="ticket_subject" value="">
                        <label for="ticket_details"> Ticket Detalis</label>
                        <textarea class="form-control" name="ticket_details" id="ticket_details" rows="5"></textarea>
                        <button type="submit" class="btn btn-success mt-2 "> Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection