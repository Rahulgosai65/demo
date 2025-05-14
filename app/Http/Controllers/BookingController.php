<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        return view('booking.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_date' => 'required|date',
            'booking_type' => 'required|in:full_day,half_day,custom',
            'booking_slot' => 'nullable|in:first_half,second_half',
            'booking_from' => 'nullable|date_format:H:i',
            'booking_to' => 'nullable|date_format:H:i|after:booking_from',
        ]);

        $date = $request->booking_date;
        $type = $request->booking_type;

        $conflict = Booking::where('booking_date', $date)
            ->where(function ($query) use ($type, $request) {
                if ($type === 'full_day') {
                    $query->where(function ($q) {
                        $q->where('booking_type', 'full_day')
                        ->orWhere('booking_type', 'half_day')
                        ->orWhere('booking_type', 'custom');
                    });
                }

                if ($type === 'half_day') {
                    $slot = $request->booking_slot;
                    $query->where(function ($q) use ($slot) {
                        $q->where('booking_type', 'full_day')
                        ->orWhere(function ($q2) use ($slot) {
                            $q2->where('booking_type', 'half_day')
                                ->where('booking_slot', $slot);
                        })
                        ->orWhere(function ($q2) use ($slot) {
                            if ($slot === 'first_half') {
                                $q2->where('booking_type', 'custom')
                                    ->whereTime('booking_from', '<', '12:00')
                                    ->whereTime('booking_to', '>', '09:00');
                            } elseif ($slot === 'second_half') {
                                $q2->where('booking_type', 'custom')
                                    ->whereTime('booking_from', '<', '18:00')
                                    ->whereTime('booking_to', '>', '12:00');
                            }
                        });
                    });
                }

                if ($type === 'custom') {
                    $from = Carbon::createFromFormat('H:i', $request->booking_from);
                    $to = Carbon::createFromFormat('H:i', $request->booking_to);

                    $query->where(function ($q) use ($from, $to) {
                        $q->where('booking_type', 'full_day')
                        ->orWhere(function ($q2) {
                            $q2->where('booking_type', 'half_day');
                        })
                        ->orWhere(function ($q2) use ($from, $to) {
                            $q2->where('booking_type', 'custom')
                                ->where(function ($q3) use ($from, $to) {
                                    $q3->whereTime('booking_from', '<', $to)
                                        ->whereTime('booking_to', '>', $from);
                                });
                        });
                    });
                }
            })->exists();

        if ($conflict) {
            return back()->with('error', 'Booking overlaps with an existing booking.');
        }

        Booking::create([
            'user_id' => auth()->id(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'booking_date' => $date,
            'booking_type' => $type,
            'booking_slot' => $request->booking_slot,
            'booking_from' => $request->booking_from,
            'booking_to' => $request->booking_to,
        ]);

        return redirect()->back()->with('success', 'Booking created successfully!');
    }
}
