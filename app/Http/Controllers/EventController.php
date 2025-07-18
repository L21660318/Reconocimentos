<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;
use App\Models\User;
use App\Models\Institution;

;

class EventController extends Controller
{
    private Event $model;
    private string $source;
    private string $routeName;
    private string $module = 'event';

    public function __construct()
    {
        $this->middleware('auth');
        $this->source = 'Catalogs/Event/';
        $this->model = new Event();
        $this->routeName = 'event.';

        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['update', 'edit']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }

    public function index(Request $request): Response
    {
        $direction = $request->direction ?? 'desc';
        $order = $request->order ?? 'created_at';

        $records = $this->model->query()->when($request->search, function ($query, $search) {
            $query->where('nombre', 'LIKE', "%$search%")
                  ->orWhere('tipo', 'LIKE', "%$search%");
        });

        $records = $records->orderBy($order, $direction)
                           ->paginate(8)->withQueryString()
                           ->through(fn($event) => [
                               'id' => $event->id,
                               'nombre' => $event->nombre,
                               'tipo' => $event->tipo,
                               'fecha_inicio' => $event->fecha_inicio,
                               'fecha_fin' => $event->fecha_fin,
                           ]);

        return Inertia::render("{$this->source}Index", [
            'events'         => $records,
            'title'          => 'Gestión de Eventos',
            'routeName'      => $this->routeName,
            'loadingResults' => false,
            'search'         => $request->search ?? '',
            'direction'      => $direction
        ]);
    }

    public function create()
    {
        return Inertia::render('Catalogs/Event/Create', [
            'title' => 'Crear evento',
            'routeName' => 'event.',
            'institutions' => Institution::all(), // fuerza carga para probar
        ]);
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('eventos/imagenes', 'public');
        }

        if ($request->hasFile('archivo_pdf')) {
            $data['archivo_pdf'] = $request->file('archivo_pdf')->store('eventos/archivos', 'public');
        }

        $data['creado_por'] = auth()->id();

        Event::create($data);

        return redirect()->route("{$this->routeName}index")->with('success', 'Evento creado con éxito!');
    }


    public function show(Event $event)
    {
        abort(404);
    }

    public function edit(Event $event)
    {
        return Inertia::render("{$this->source}Edit", [
            'title'     => 'Editar Evento',
            'event'     => $event,
            'routeName' => $this->routeName
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $event->update($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Evento actualizado con éxito!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Evento eliminado con éxito!');
    }
    
    public function assignUsers(Event $event)
    {
        return Inertia::render('Catalogs/Event/AssignUsers', [
            'title' => "Asignar usuarios a: {$event->nombre}",
            'event' => $event,
            'users' => User::orderBy('name')->get(),
            'assigned' => $event->usuarios()->pluck('users.id'), // <--- FIX
            'routeName' => 'event.',
        ]);
    }

    
    public function storeAssignedUsers(Request $request, Event $event)
    {
        $request->validate([
            'users' => ['array'],
            'users.*' => ['exists:users,id'],
        ]);
    
        $event->usuarios()->sync($request->users);
    
        return redirect()->route('event.index')->with('success', 'Usuarios asignados correctamente.');
    }


}
