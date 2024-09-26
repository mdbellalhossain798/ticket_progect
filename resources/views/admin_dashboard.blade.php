@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style>
    #ticketsTable,
    #ticketsTable th,
    #ticketsTable td {
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
                <div class="card-header">
                    <h3>{{ __('Ticket List') }}</h3>
                </div>

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
                                <th>Open By</th>
                                <th>Closed By</th>
                                <th>Closed Date</th>
                                <th class="text-center" style="width: 120px !important; ">Action</th>
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
                    <h5 class="modal-title" id="ticketModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="height:200px; overflow-x: auto; overflow-y: auto;">

                </div>
                <div style="margin-top:20px">
                    <form action="{{route('send-reply')}}" method="post" id="send_reply">
                        @csrf
                        <input type="hidden" id="ticket_mst_id" name="ticket_mst_id" value="">
                        <input type="hidden" name="auth_id" id="auth_id" value="{{Auth::id()}}">
                        <textarea name="reply" id="reply" rows="3" class="form-control" placeholder="Reply......."></textarea>
                        <button type="button" id="save_reply" class="btn btn-success btn-md mt-2">Send</button>
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
        getAdminData();

        // Function to load ticket data
        $('#save_reply').on('click', function() {
            replySave();
        });
    });

    function replySave() {
        var formData = $('#send_reply').serialize();
        $.ajax({
            url: "{{route('send-reply')}}", // Ensure this route exists
            method: 'post',
            data: formData,
            success: function(response) {
                $('#reply').val('');
                loadReplyData($('#ticket_mst_id').val());
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }

    function loadReplyData(ticketId) {
        $('#ticket_mst_id').val(ticketId);
        $.ajax({
            url: '/tickets-comment/' + ticketId, // Ensure this route exists
            method: 'GET',
            success: function(data) {
                // console.log(data)
                $('.modal-body').html('');
                
                var ticketReply = '';
                if (data.tickets_comment.length > 0) {

                    $.each(data.tickets_comment, function(index, ticket) {
                        var align_css = ticket.reply_by == $('#auth_id').val() ? 'text-align:right;color:blue;' : 'text-align:left;';
                        ticketReply += `
                            <div style="${align_css}">${ticket.reply_details} <small style="font-size:9px">${ticket.created_at ? ticket.created_at : 'N/A'}</small></div>
                            `;
                    });
                    $('.modal-body').html(ticketReply);

                } else {
                    $('.modal-body').html('No Reply Found');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr.responseText); // Handle errors here
            }
        });
    }

    function closeTicket(element) {
        let ticket_id = $(element).data('id');
        if (confirm('Are you sure ?')) {
            $.ajax({
                url: "{{ route('delete-tickets') }}", // Ensure this route exists
                method: 'post',
                data: {
                    ticket_id: ticket_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    getAdminData();

                },

            });
        }
    }

    function getAdminData() {
        $.ajax({
            url: "{{ route('admin.get-tickets') }}", // Ensure this route exists
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
                                <td class="text-center ${ticket.status ==="CLOSED" ? `text-danger`: `text-primary`}""><strong>${ticket.status}</strong></td>
                                <td>${ticket.open_date ? ticket.open_date : 'N/A'}</td>                                
                                <td>${ticket.user_id ? ticket.user_id : 'N/A'}</td>
                                <td>${ticket.closed_by ? ticket.closed_by : 'N/A'}</td>
                                <td>${ticket.closed_date ? ticket.closed_date : 'N/A'}</td>
                                <td class="text-center"> 
                                    <button type="button" class="btn btn-sm ${ticket.status ==="CLOSED" ? `btn-danger`: `btn-primary`}"  data-toggle="modal" data-target="#ticketModal"  data-id="${ticket.id}" >Reply</button>
                                    <button type="button" class="btn btn-sm btn-warning ${ticket.status ==="CLOSED" ? `d-none`: ''}"  onclick="closeTicket(this)" data-id="${ticket.id}" >Close</button>
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
    $('#ticketModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var ticketId = button.data('id'); // Extract ticket ID
        $('#ticket_mst_id').val(ticketId);

        // AJAX request to fetch ticket details
        $.ajax({
            url: '/tickets-comment/' + ticketId, // Ensure this route exists
            method: 'GET',
            success: function(data) {
                console.log(data.tickets_comment)
                $('.modal-body').html('');
                $('#ticketModalLabel').html(data.reply_name);
                var ticketReply = '';
                if (data.tickets_comment.length > 0) {

                    $.each(data.tickets_comment, function(index, ticket) {
                        var align_css = ticket.reply_by == $('#auth_id').val() ? 'text-align:right;color:blue;' : 'text-align:left;';
                        ticketReply += `
                            <div style="${align_css}">${ticket.reply_details} <small style="font-size:9px">${ticket.created_at ? ticket.created_at : 'N/A'}</small></div>
                            `;
                    });
                    $('.modal-body').html(ticketReply);

                } else {
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