<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\Group;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->hasRole('admin')) {
            $events = Event::withCount('guests')->paginate(10);
            return view('events.index', ['events' => $events]);
        }

        $events = Event::where('group_id', auth()->user()->group_id)->withCount('guests')->paginate(10);
        return view('events.index', ['events' => $events]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('event.create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $groups = Group::all()->pluck('name', 'id')->prepend('Please select group', '');

        return view('events.create', ['groups' => $groups]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        Event::create($request->all());

        return redirect()->route('events.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::where('id', $id)->first();

        return view('events.show', ['event' => $event]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort_if(Gate::denies('event.edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $event = Event::where('id', $id)->first();
        $groups = Group::all()->pluck('name', 'id')->prepend('Please select group', '');

        return view('events.edit', ['event' => $event, 'groups' => $groups]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, string $id)
    {
        Event::where('id', $id)->update($request->validated());

        return redirect()->route('events.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort_if(Gate::denies('event.delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        Event::where('id', $id)->delete();

        return redirect()->route('events.index');
    }
}
