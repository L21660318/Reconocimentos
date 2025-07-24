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
use Illuminate\Support\Facades\Validator;
use App\Models\EventUserRequest;
;

class EventController extends Controller
{
    private Event $model;
    private string $source;
    private string $routeName;
    private string $module = 'event';

    public function __construct()
    {
        $this->middleware('auth')->except(['register']);
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



    public function register(Request $request, Event $event)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Correo no encontrado.'], 404);
        }

        // Verifica si ya hay una solicitud pendiente o aceptada
        $exists = \DB::table('event_user_requests')
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pendiente', 'aceptado'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Ya se ha registrado previamente.'], 409);
        }

        // Inserta la solicitud
        \DB::table('event_user_requests')->insert([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Registro exitoso']);
    }

    public function acceptRequest(EventUserRequest $request)
    {
        // Cambiar estado a aceptado
        $request->update(['status' => 'aceptado']);

        // Insertar en tabla event_user si no existe ya
        $exists = \DB::table('event_user')->where([
            'event_id' => $request->event_id,
            'user_id' => $request->user_id,
        ])->exists();

        if (!$exists) {
            \DB::table('event_user')->insert([
                'event_id' => $request->event_id,
                'user_id' => $request->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Solicitud aceptada y usuario inscrito.');
    }


    public function rejectRequest(EventUserRequest $request)
    {
        $request->update(['status' => 'rechazado']);

        return back()->with('success', 'Solicitud rechazada.');
    }

    

    public function requests(Event $event)
    {
        $requests = \App\Models\EventUserRequest::with('user')
            ->where('event_id', $event->id)
            ->where('status', 'pendiente') // ← SOLO solicitudes pendientes
            ->get();

        return Inertia::render('Catalogs/Event/Requests', [
            'event' => $event,
            'requests' => $requests,
            'title' => 'Solicitudes de inscripción',
        ]);
    }



}
