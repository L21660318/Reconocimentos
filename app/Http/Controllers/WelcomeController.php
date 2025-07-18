<?php

namespace App\Http\Controllers;

use App\Models\Call;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Event;


class WelcomeController extends Controller
{
    protected string $routeName;
    protected string $source;

    public function __construct()
    {
        $this->routeName = "welcome.";
        $this->source    = "Welcome/";
    }

   public function welcome(Request $request)
    {
        $search = $request->input('search');

        $events = Event::with('institution')
            ->when($search, function ($query, $search) {
                $query->where('nombre', 'like', "%$search%");
            })
            ->paginate(6);

        return Inertia::render('Welcome/Home/Index', [
            'title' => 'Eventos',
            'search' => $search,
            'events' => $events,
        ]);
    }


    public function committee()
    {
        return Inertia::render("{$this->source}Committee/Index", [
            'title'   => 'Comité',
            'routeName' => $this->routeName,
        ]);
    }

    public function place()
    {
        return Inertia::render("{$this->source}Place/Index", [
            'title'   => 'Lugar',
            'routeName' => $this->routeName,
        ]);
    }

    public function program()
    {
        return Inertia::render("{$this->source}Program/Index", [
            'title'   => 'Programa',
            'routeName' => $this->routeName,
        ]);
    }

    public function events(Request $request)
    {
        $events = Event::query()
            ->when($request->search, fn($q, $search) => $q->where('nombre', 'like', "%{$search}%"))
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Welcome/Event', [
            'events' => $events,
            'search' => $request->search ?? '',
        ]);
    }

}
