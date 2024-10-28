<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(auth()->id());
        $notifications = $user->unreadNotifications; // Get unread notifications

        return view('notifications.index', compact('notifications')); // Pass them to the view
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        $notification->markAsRead(); // Mark the notification as read

        return response()->json(['success' => true]); // Return a JSON response
    }

    public function getUnreadCount()
    {
        $user = auth()->user();
        $count = $user->unreadNotifications()->count(); // Get the count of unread notifications


        return response()->json(['count' => $count]); // Return the count as JSON
    }

    public function getCount()
    {
        $user = auth()->user();
        $count = $user->unreadNotifications()->count(); // Get the count of unread notifications


        return response()->json(['count' => $count]); // Return the count as JSON
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
