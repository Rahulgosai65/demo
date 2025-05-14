@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Booking Form</h2>
    @if(session('success'))
    <div style="color: green; margin-bottom: 10px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="color: red; margin-bottom: 10px;">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div style="color: red; margin-bottom: 10px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form method="POST" action="{{ route('booking.store') }}">
        @csrf
        <input type="text" name="customer_name" placeholder="Customer Name" required><br>
        <input type="email" name="customer_email" placeholder="Customer Email" required><br>
        <input type="date" name="booking_date" required><br>

        <select name="booking_type" id="booking_type" required onchange="toggleFields()">
            <option value="">Select Booking Type</option>
            <option value="full_day">Full Day</option>
            <option value="half_day">Half Day</option>
            <option value="custom">Custom</option>
        </select><br>

        <div id="slot_section" style="display:none;">
            <select name="booking_slot">
                <option value="">Select Slot</option>
                <option value="first_half">First Half</option>
                <option value="second_half">Second Half</option>
            </select><br>
        </div>

        <div id="time_section" style="display:none;">
            From: <input type="time" name="booking_from"><br>
            To: <input type="time" name="booking_to"><br>
        </div>

        <input type="submit" value="Submit Booking">
    </form>
</div>

<script>
function toggleFields() {
    let type = document.getElementById('booking_type').value;
    document.getElementById('slot_section').style.display = (type === 'half_day') ? 'block' : 'none';
    document.getElementById('time_section').style.display = (type === 'custom') ? 'block' : 'none';
}
</script>
@endsection
