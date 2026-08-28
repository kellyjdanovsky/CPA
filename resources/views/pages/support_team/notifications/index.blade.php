@extends('layouts.master')
@section('page_title', 'Centre de Notifications')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title"><i class="icon-bell3 mr-2"></i> Centre de Notifications</h6>
        <div class="header-elements">
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-light btn-sm"><i class="icon-checkmark3 mr-2"></i> Tout marquer comme lu</button>
            </form>
        </div>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item">
                <a href="{{ route('notifications.index') }}" class="nav-link {{ $filter == 'all' ? 'active' : '' }}">
                    Toutes
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="nav-link {{ $filter == 'unread' ? 'active' : '' }}">
                    Non lues
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('notifications.index', ['filter' => 'urgent']) }}" class="nav-link {{ $filter == 'urgent' ? 'active' : '' }}">
                    Urgentes
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active">
                @forelse($notifications as $notif)
                    <div class="card mb-3 border-left-{{ $notif->color ?? 'info' }} {{ !$notif->is_read ? 'bg-light' : '' }}" style="border-left-width: 4px;">
                        <div class="card-body d-flex align-items-center">
                            <div class="mr-3">
                                <div class="bg-{{ $notif->color ?? 'info' }}-400 rounded-circle p-2">
                                    <i class="{{ $notif->icon ?? 'icon-info22' }} text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 {{ !$notif->is_read ? 'font-weight-bold' : '' }}">
                                    {{ $notif->title }}
                                    @if($notif->priority == 'urgent' || $notif->priority == 'high')
                                        <span class="badge badge-danger ml-2">Urgent</span>
                                    @endif
                                </h6>
                                <p class="text-muted mb-0">{{ $notif->message }}</p>
                                <span class="text-muted font-size-sm"><i class="icon-time mr-1"></i> {{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="ml-3">
                                @if($notif->link)
                                    <a href="{{ $notif->link }}" class="btn btn-outline-primary btn-sm"><i class="icon-link mr-1"></i> Voir</a>
                                @endif
                                
                                @if(!$notif->is_read)
                                    <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm"><i class="icon-checkmark3"></i></button>
                                    </form>
                                @endif

                                <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cette notification ?')"><i class="icon-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info border-0 alert-dismissible">
                        <span class="font-weight-semibold">Aucune notification trouvée.</span>
                    </div>
                @endforelse
                
                <div class="d-flex justify-content-center mt-3">
                    {{ $notifications->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
