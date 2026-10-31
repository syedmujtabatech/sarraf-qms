<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\PriorityCategory;
use App\Models\ServiceCategory;
use App\Services\QueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    public function index()
    {
        $branches = Branch::where('is_active', true)->get();
        $services = ServiceCategory::where('is_active', true)->get();
        $priorities = PriorityCategory::where('is_active', true)->get();

        $timeSlots = [
            '09:30 AM - 10:00 AM',
            '10:00 AM - 10:30 AM',
            '10:30 AM - 11:00 AM',
            '11:00 AM - 11:30 AM',
            '11:30 AM - 12:00 PM',
            '12:00 PM - 12:30 PM',
            '02:00 PM - 02:30 PM',
            '02:30 PM - 03:00 PM',
            '03:00 PM - 03:30 PM',
            '03:30 PM - 04:00 PM',
        ];

        return view('customer.appointment', compact('branches', 'services', 'priorities', 'timeSlots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'priority_category_id' => 'required|exists:priority_categories,id',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'customer_cnic' => 'nullable|string|max:25',
            'customer_email' => 'nullable|email|max:100',
            'appointment_date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string',
            'notes' => 'nullable|string|max:300',
        ]);

        $code = 'APT-' . strtoupper(Str::random(6));

        $appointment = Appointment::create([
            'appointment_code' => $code,
            'branch_id' => $validated['branch_id'],
            'service_category_id' => $validated['service_category_id'],
            'priority_category_id' => $validated['priority_category_id'],
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_cnic' => $validated['customer_cnic'] ?? null,
            'customer_email' => $validated['customer_email'] ?? null,
            'appointment_date' => $validated['appointment_date'],
            'time_slot' => $validated['time_slot'],
            'status' => 'booked',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('appointment.confirmation', $appointment->id)
            ->with('success', 'Your appointment has been booked successfully!');
    }

    public function confirmation(Appointment $appointment)
    {
        $appointment->load(['branch', 'serviceCategory', 'priorityCategory', 'token']);
        return view('customer.appointment_confirmation', compact('appointment'));
    }

    public function checkIn(Appointment $appointment)
    {
        // When customer arrives at the branch on their appointment day, they can check in to generate an active queue token!
        if ($appointment->status === 'checked_in' && $appointment->token_id) {
            return redirect()->route('kiosk.printSlip', $appointment->token_id);
        }

        $token = $this->queueService->generateToken(
            $appointment->branch_id,
            $appointment->service_category_id,
            $appointment->priority_category_id,
            [
                'customer_name' => $appointment->customer_name . ' (Appt)',
                'customer_phone' => $appointment->customer_phone,
                'customer_cnic' => $appointment->customer_cnic,
            ],
            'appointment'
        );

        $appointment->update([
            'status' => 'checked_in',
            'token_id' => $token->id,
        ]);

        return redirect()->route('kiosk.printSlip', $token->id)
            ->with('success', 'Appointment checked in! Token issued.');
    }
}
