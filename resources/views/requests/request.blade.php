@extends('layout.default')

@section('title')
    <title>Request - {{ config('other.title') }} - {{ $torrentRequest->name }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('requests.index') }}" class="breadcrumb__link">
            {{ __('request.requests') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $torrentRequest->name }}
    </li>
@endsection

@section('content')
    <div class="container">
        @if ($user->can_request == 0)
            <div class="container">
                <div class="jumbotron shadowed">
                    <div class="container">
                        <h1 class="mt-5 text-center">
                            <i class="{{ config('other.font-awesome') }} fa-times text-danger"></i> {{ __('request.no-privileges') }}
                        </h1>
                        <div class="separator"></div>
                        <p class="text-center">{{ __('request.no-privileges-desc') }}!</p>
                    </div>
                </div>
            </div>
        @else
            <h1 class="title h2">
                {{ $torrentRequest->name }}
                <span class="text-green">{{ __('request.for') }} <i
                            class="{{ config('other.font-awesome') }} fa-coins text-gold">
            </i> <strong>{{ $torrentRequest->bounty }}</strong> {{ __('bon.bon') }}</span>
            </h1>
            <div class="block single">
                <div class="row mb-10">
                    <div class="col-sm-12">
                        <div class="pull-right">
                            <button class="btn btn-xs btn-danger" data-toggle="modal"
                                    data-target="#modal_request_report"><i
                                        class="{{ config('other.font-awesome') }} fa-eye"></i> {{ __('request.report') }}
                            </button>
                            @if ($torrentRequest->filled_hash == null)
                                <button class="btn btn-xs btn-success btn-vote-request" data-toggle="modal"
                                        data-target="#vote"><i class="{{ config('other.font-awesome') }} fa-thumbs-up">
                                    </i> {{ __('request.vote') }}</button>
                            @endif @if ($user->group->is_modo && $torrentRequest->filled_hash != null)
                                <button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#reset"><i
                                            class="{{ config('other.font-awesome') }} fa-undo">
                                    </i> {{ __('request.reset-request') }}</button>
                            @endif @if ($user->group->is_modo || ($torrentRequest->user->id == $user->id && $torrentRequest->filled_hash == null))
                                <a class="btn btn-warning btn-xs"
                                   href="{{ route('edit_request', ['id' => $torrentRequest->id]) }}" role="button"><i
                                            class="{{ config('other.font-awesome') }} fa-edit"
                                            aria-hidden="true"> {{ __('request.edit-request') }}</i></a>
                                <button class="btn btn-xs btn-danger" data-toggle="modal" data-target="#delete"><i
                                            class="{{ config('other.font-awesome') }} fa-trash">
                                    </i> {{ __('common.delete') }}</button>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($torrentRequest->category->movie_meta)
                    @include('torrent.partials.movie_meta', ['torrent' => $torrentRequest])
                @endif

                @if ($torrentRequest->category->tv_meta)
                    @include('torrent.partials.tv_meta', ['torrent' => $torrentRequest])
                @endif

                @if ($torrentRequest->category->game_meta)
                    @include('torrent.partials.game_meta', ['torrent' => $torrentRequest])
                @endif

                <div class="table-responsive mt-20">
                    <table class="table table-condensed table-bordered table-striped">
                        <tbody>
                        <tr>
                            <td>
                                <strong>{{ __('torrent.title') }}</strong>
                            </td>
                            <td>
                                {{ $torrentRequest->name }}
                            </td>
                        </tr>
                        <tr>
                            <td class="col-sm-2">
                                <strong>{{ __('torrent.category') }}</strong>
                            </td>
                            <td>
                                <i class="{{ $torrentRequest->category->icon }} torrent-icon torrent-icon-small"
                                   data-toggle="tooltip"
                                   data-original-title="{{ $torrentRequest->category->name }} Torrent"></i> {{ $torrentRequest->category->name }}
                            </td>
                        </tr>
                        <tr>
                            <td class="col-sm-2">
                                <strong>{{ __('torrent.type') }}</strong>
                            </td>
                            <td>
                                {{ $torrentRequest->type->name }}
                            </td>
                        </tr>
                        <tr>
                            <td class="col-sm-2">
                                <strong>{{ __('torrent.resolution') }}</strong>
                            </td>
                            <td>
                                {{ $torrentRequest->resolution->name ?? 'No Res' }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ __('bon.bon') }}</strong>
                            </td>
                            <td>
                                <i class="{{ config('other.font-awesome') }} fa-coins text-gold">
                                </i>
                                <strong>{{ $torrentRequest->bounty }}</strong> {{ __('bon.bon') }} {{ strtolower(__('request.reward-from')) }}
                                <strong>{{ $torrentRequest->requestBounty->count() }}</strong> {{ strtolower(__('request.voters')) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ __('torrent.description') }}</strong>
                            </td>
                            <td>
                                <div class="panel-body torrent-desc">
                                    <p>
                                        @joypixels($torrentRequest->getDescriptionHtml())
                                    </p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ __('request.requested-by') }}</strong>
                            </td>
                            <td>
                                @if ($torrentRequest->anon == 0)
                                    <span class="badge-user">
                                    <a href="{{ route('users.show', ['username' => $torrentRequest->user->username]) }}">
                                        {{ $torrentRequest->user->username }}
                                    </a>
                                </span>
                                @else
                                    <span class="badge-user">{{ strtoupper(__('common.anonymous')) }}
                                        @if ($user->group->is_modo || $torrentRequest->user->username == $user->username)
                                            <a href="{{ route('users.show', ['username' => $torrentRequest->user->username]) }}">
                                        ({{ $torrentRequest->user->username }})
                                    </a>
                                        @endif
                                </span>
                                @endif
                                <span class="badge-user">
                                    <em>({{ $torrentRequest->created_at->diffForHumans() }})</em>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ __('request.claim') }} / {{ __('common.upload') }}</strong>
                            </td>
                            <td>
                                @if ($torrentRequest->claimed == null && $torrentRequest->filled_hash == null)
                                    <button class="btn btn-xs btn-info btn-vote-request" data-toggle="modal"
                                            data-target="#claim"><i
                                                class="{{ config('other.font-awesome') }} fa-hand-paper">
                                        </i> {{ __('request.claim') }}
                                    </button>
                                    @if ($torrentRequest->category->movie_meta || $torrentRequest->category->tv_meta)
                                        <a href="{{ route('upload_form', ['category_id' => $torrentRequest->category_id, 'title' => $meta->title ?? ' ', 'imdb' => $meta->imdb ?? 0, 'tmdb' => $meta->tmdb ?? 0]) }}"
                                           class="btn btn-xs btn-success"> {{ __('common.upload') }} {{ $meta->title ?? ''}}
                                        </a>
                                    @endif
                                @elseif ($torrentRequest->filled_hash != null && $torrentRequest->approved_by == null)
                                    <button class="btn btn-xs btn-warning" disabled><i
                                                class="{{ config('other.font-awesome') }} fa-question-circle"></i>{{ __('request.pending') }}
                                    </button>
                                @elseif ($torrentRequest->filled_hash != null)
                                    <button class="btn btn-xs btn-success" disabled><i
                                                class="{{ config('other.font-awesome') }} fa-check-square"></i>{{ __('request.filled') }}
                                    </button>
                                @else
                                    @if ($torrentRequestClaim->anon == 0)
                                        <span class="badge-user">
                                        <a href="{{ route('users.show', ['username' => $torrentRequestClaim->username]) }}">
                                            {{ $torrentRequestClaim->username }}
                                        </a>
                                    </span>
                                    @else
                                        <span class="badge-user">{{ strtoupper(__('common.anonymous')) }}
                                            @if ($user->group->is_modo || $torrentRequestClaim->username == $user->username)
                                                ({{ $torrentRequestClaim->username }})
                                            @endif
                                    </span>
                                    @endif

                                    <span class="badge-user">
                                        <em>({{ $torrentRequestClaim->created_at->diffForHumans() }})</em>
                                    </span>

                                    @if ($user->group->is_modo || $torrentRequestClaim->username == $user->username)
                                        <form role="form" method="POST"
                                              action="{{ route('unclaimRequest', ['id' => $torrentRequest->id]) }}"
                                              style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-danger">
                                                <i class="{{ config('other.font-awesome') }} fa-times"></i> {{ __('request.unclaim') }}
                                            </button>
                                        </form>
                                        <a href="{{ route('upload_form', ['category_id' => $torrentRequest->category_id, 'title' => $meta->title ?? ' ', 'imdb' => $meta->imdb ?? 0, 'tmdb' => $meta->tmdb ?? 0]) }}"
                                           class="btn btn-xs btn-success"> {{ __('common.upload') }} {{ $meta->title ?? ''}}
                                        </a>
                                    @endif
                                @endif
                                @if($torrentRequest->filled_hash == null)
                                    @if($torrentRequest->claimed == 1 && ($torrentRequestClaim->username == $user->username || $user->group->is_modo))
                                        <button id="btn_fulfil_request" class="btn btn-xs btn-info" data-toggle="modal"
                                                data-target="#fill"><i
                                                    class="{{ config('other.font-awesome') }} fa-link">
                                            </i> {{ __('request.fulfill') }}</button>
                                    @elseif ($torrentRequest->claimed == 0)
                                        <button id="btn_fulfil_request" class="btn btn-xs btn-info" data-toggle="modal"
                                                data-target="#fill"><i
                                                    class="{{ config('other.font-awesome') }} fa-link">
                                            </i> {{ __('request.fulfill') }}</button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @if ($torrentRequest->filled_hash != null && $torrentRequest->approved_by != null)
                            <tr>
                                <td>
                                    <strong>{{ __('request.filled-by') }}</strong>
                                </td>
                                <td>
                                    @if ($torrentRequest->filled_anon == 0)
                                        <span class="badge-user">
                                        <a href="{{ route('users.show', ['username' => $torrentRequest->FillUser->username]) }}">
                                            {{ $torrentRequest->FillUser->username }}
                                        </a>
                                    </span>
                                    @else
                                        <span class="badge-user">{{ strtoupper(__('common.anonymous')) }}
                                            @if ($user->group->is_modo || $torrentRequest->FillUser->username == $user->username)
                                                <a href="{{ route('users.show', ['username' => $torrentRequest->FillUser->username]) }}">
                                                ({{ $torrentRequest->FillUser->username }})
                                            </a>
                                            @endif
                                    </span>
                                    @endif
                                    <span class="badge-user">
                                        {{ $torrentRequest->approved_when->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>{{ __('torrent.torrent') }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('torrent', ['id' => $torrentRequest->torrent->id]) }}">{{ $torrentRequest->torrent->name }}</a>
                                </td>
                            </tr>
                        @endif

                        @if (($torrentRequest->user_id == $user->id && $torrentRequest->filled_hash != null && $torrentRequest->approved_by == null) || (auth()->user()->group->is_modo && $torrentRequest->filled_hash != null && $torrentRequest->approved_by == null))
                            <tr>
                                <td>
                                    <strong>{{ __('request.filled-by') }}</strong>
                                </td>
                                <td>
                                    @if ($torrentRequest->filled_anon == 0)
                                        <span class="badge-user">
                                        <a href="{{ route('users.show', ['username' => $torrentRequest->FillUser->username]) }}">
                                            {{ $torrentRequest->FillUser->username }}
                                        </a>
                                    </span>
                                    @else
                                        <span class="badge-user">{{ strtoupper(__('common.anonymous')) }}
                                            @if ($user->group->is_modo || $torrentRequest->FillUser->username == $user->username)
                                                ({{ $torrentRequest->FillUser->username }})
                                            @endif
                                    </span>
                                    @endif
                                    <span class="badge-extra">
                                        {{ $torrentRequest->filled_when->diffForHumans() }}
                                    </span>
                                    <form role="form" method="POST"
                                          action="{{ route('approveRequest', ['id' => $torrentRequest->id]) }}"
                                          style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-success">
                                            {{ __('request.approve') }}
                                        </button>
                                    </form>
                                    <form role="form" method="POST"
                                          action="{{ route('rejectRequest', ['id' => $torrentRequest->id]) }}"
                                          style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-warning">
                                            {{ __('request.reject') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>{{ __('torrent.torrent') }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('torrent', ['id' => $torrentRequest->torrent->id]) }}">{{ $torrentRequest->torrent->name }}</a>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    <div class="panel panel-default panel-collapse">
                        <div class="panel-heading collapsed" data-toggle="collapse" data-target="#collapseVoters"
                             aria-expanded="false">
                            <strong><a href="#/">{{ __('request.voters') }}</a></strong>
                        </div>
                        <div id="collapseVoters" class="panel-body collapse" aria-expanded="false">
                            <div class="pull-right">
                            </div>
                            <table class="table table-condensed table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>
                                        {{ __('common.user') }}
                                    </th>
                                    <th>
                                        {{ __('bon.bonus') }} {{ strtolower(__('bon.points')) }}
                                    </th>
                                    <th>
                                        {{ __('request.last-vote') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($voters as $voter)
                                    <tr>
                                        <td>
                                            @if ($voter->anon == 0)
                                                <span class="badge-user">
                                                <a href="{{ route('users.show', ['username' => $voter->user->username]) }}">
                                                    {{ $voter->user->username }}
                                                </a>
                                            </span>
                                            @else
                                                <span class="badge-user">{{ strtoupper(__('common.anonymous')) }}
                                                    @if ($user->group->is_modo || $voter->user->username == $user->username)
                                                        <a href="{{ route('users.show', ['username' => $voter->user->username]) }}">
                                                        ({{ $voter->user->username }})
                                                    </a>
                                                    @endif
                                            </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $voter->seedbonus }}
                                        </td>
                                        <td>
                                            {{ $voter->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12">
                <livewire:comments :model="$torrentRequest"/>
            </div>
        </div>
    @include('requests.request_modals')
    @endif
@endsection

