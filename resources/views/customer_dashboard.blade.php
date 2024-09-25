@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    th {
        background-color: black;
        color: white;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3>{{ __('Create Ticket') }}</h3></div>

                <div class="card-body">
                    <form action="{{route('customer.save-ticket')}}" id="ticket_save" method="post">
                        @csrf
                        <label for="ticket_subject"><strong> Ticket Subject</strong></label>
                        <input type="text" class="form-control" id="ticket_subject" name="ticket_subject" value="" autocomplete="off">
                        <label for="ticket_details"><strong> Ticket Detalis</strong></label>
                        <textarea class="form-control" name="ticket_details" id="ticket_details" rows="5"></textarea>
                        <button type="submit" class="btn btn-success mt-2">Save</button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>{{ __('Ticket List') }}</h3></div>

                <div class="card-body">
                    <table id="ticketsTable" width="100%" style="border-collapse:collapse">
                        <thead>
                            <tr>
                                <th class="text-center">Sl.</th>
                                <th class="text-center">Ticket No</th>
                                <th>Ticket Subject</th>
                                <th>Description</th>
                                <th class="text-center">Status</th>
                                <th>Open Date</th>
                                <th>Closed By</th>
                                <th>Closed Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content p-5">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketModalLabel">Ticket Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   
                </div>
                <div>
                    <form action="{{route('send-reply')}}" method="post" id="send_reply">
                        @csrf
                        <input type="hidden" id="ticket_mst_id" name="ticket_mst_id" value="">
                        <textarea name="reply" id="reply" rows="3" class="form-control" placeholder="Reply......."></textarea>
                        <button type="submit" class="btn btn-success btn-md mt-2">Send</button>
                        <button type="button" class="btn btn-secondary mt-2" data-dismiss="modal">Close</button>
                    </form>
                </div>
                <div class="modal-footer">
                  
                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#ticketsTable').DataTable({
            "processing": true,
            "serverSide": false,
            "paging": true,
            "searching": true,
            "ordering": true,
        });

        // Fetch ticket data
        getData();

        // Function to load ticket data
        function getData() {
            $.ajax({
                url: "{{ route('customer.get-tickets') }}", // Ensure this route exists
                method: 'GET',
                success: function(response) {
                    console.log(response.tickets);
                    let ticketTable = '';
                    if (response.tickets.length > 0) {
                        $.each(response.tickets, function(index, ticket) {
                            ticketTable += `<tr>
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${ticket.id}</td>
                                <td>${ticket.ticket_subject}</td>
                                <td>${ticket.ticket_details}</td>
                                <td class="text-center"><strong>${ticket.status}</strong></td>
                                <td>${ticket.open_date ? ticket.open_date : 'N/A'}</td>
                                <td>${ticket.closed_by ? ticket.closed_by : 'N/A'}</td>
                                <td>${ticket.closed_date ? ticket.closed_date : 'N/A'}</td>
                                <td class="text-center"> 
                                    <button type="button" class="btn btn-primary"  data-toggle="modal" data-target="#ticketModal" data-id="${ticket.id}" >View</button>
                                </td>
                            </tr>`;
                        });
                    } else {
                        ticketTable = '<tr><td colspan="9" class="text-center">No tickets available</td></tr>';
                    }
                    $('tbody').html(ticketTable);
                },
                error: function(xhr) {
                    console.log(xhr.responseText); // Debugging if the AJAX call fails
                }
            });
        }

        // Trigger modal and load ticket details via AJAX

    });

    $('#ticketModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var ticketId = button.data('id'); // Extract ticket ID

        // AJAX request to fetch ticket details
        $.ajax({
            url: '/tickets-comment/' + ticketId, // Ensure this route exists
            method: 'GET',
            success: function(data) {
                console.log(data.tickets_comment)              
                $('.modal-body').html('');
                var ticketReply = '<table width="100%">';
                if (data.tickets_comment.length > 0) {
                        $.each(data.tickets_comment, function(index, ticket) {
                            ticketReply += `<tr>
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${ticket.id}</td>
                                <td>${ticket.reply_details}</td>
                                <td>${ticket.reply_by}</td>
                                <td>${ticket.created_at ? ticket.created_at : 'N/A'}</td>
                                
                            </tr>`;
                        });
                        ticketReply +='</table>'
                        $('.modal-body').html(ticketReply);
                      
                    }else{
                        $('.modal-body').html('No Reply Found');
                    }
            },
            error: function(xhr) {
                console.log('Error:', xhr.responseText); // Handle errors here
            }
        });
    });
</script>
@endsection