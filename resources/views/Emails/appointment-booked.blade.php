<h2>Appointment Confirmed</h2>

<p>Status: <strong>{{ $appointment->status }}</strong> </p>

<p>Reference: {{ $appointment->reference_no }}</p>

<p>Doctor: {{ $appointment->doctor->name }}</p>

<p>Date: {{ $appointment->created_at }}</p>

<p>Time: {{ $appointment->slot->formatted_time  }}</p>