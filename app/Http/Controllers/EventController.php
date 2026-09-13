<?php

namespace App\Http\Controllers;

use App\Models\Denomination;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function search(): View
    {
        $eventTypes = EventType::orderBy('name')->get();

        $denominations = Denomination::with('religion')
            ->orderBy('name')
            ->get();

        return view('events.search', [
            'eventTypes' => $eventTypes,
            'denominations' => $denominations,
        ]);
    }


    public function results(Request $request): View
    {
        $validated = $request->validate([
            'event_type_id' => [
                'required',
                'exists:event_types,id',
            ],
            'denomination_id' => [
                'required',
                'exists:denominations,id',
            ],
        ]);

        $eventType = EventType::findOrFail(
            $validated['event_type_id']
        );

        $denomination = Denomination::with('religion')
            ->findOrFail(
                $validated['denomination_id']
            );

        $events = Event::with([
                'eventType',
                'location',
                'group.organization.denomination.religion',
            ])
            ->where(
                'event_type_id',
                $eventType->id
            )
            ->whereHas(
                'group.organization',
                function ($query) use ($denomination) {
                    $query->where(
                        'denomination_id',
                        $denomination->id
                    );
                }
            )
            ->where(
                'start_datetime',
                '>=',
                now()
            )
            ->orderBy('start_datetime')
            ->paginate(5)
            ->withQueryString();

        return view('events.results', [
            'eventType' => $eventType,
            'denomination' => $denomination,
            'events' => $events,
        ]);
    }


    public function show(Event $event): View
    {
        /*
         * Do not expose an event publicly if its
         * group or organisation is archived.
         */
        $event->load([
            'eventType',
            'location',
            'group.organization.denomination.religion',
        ]);

        abort_unless(
            $event->group &&
            $event->group->organization,
            404
        );

        return view('events.show', [
            'event' => $event,
        ]);
    }
}