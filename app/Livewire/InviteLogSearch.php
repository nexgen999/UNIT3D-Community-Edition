<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Livewire;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class InviteLogSearch extends Component
{
    use WithPagination;

    #[Url]
    public string $sender = '';

    #[Url]
    public string $email = '';

    #[Url]
    public string $code = '';

    #[Url]
    public string $receiver = '';

    #[Url]
    public string $custom = '';

    #[Url]
    public string $groupBy = 'none';

    #[Url]
    public int $threshold = 25;

    #[Url]
    public string $sortField = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    #[Url]
    public int $perPage = 25;

    final public function mount(): void
    {
        $this->sortField = match ($this->groupBy) {
            'user_id' => 'created_at_max',
            default   => 'created_at',
        };
    }

    final public function updatedPage(): void
    {
        $this->dispatch('paginationChanged');
    }

    final public function updatingGroupBy($value): void
    {
        $this->sortField = match ($value) {
            'user_id' => 'created_at_max',
            default   => 'created_at',
        };
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<Invite>
     */
    #[Computed]
    final public function invites(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Invite::withTrashed()
            ->with(['sender.group', 'receiver.group'])
            ->when($this->sender, fn ($query) => $query->whereIn('user_id', User::select('id')->where('username', '=', $this->sender)))
            ->when($this->email, fn ($query) => $query->where('email', 'LIKE', '%'.$this->email.'%'))
            ->when($this->code, fn ($query) => $query->where('code', 'LIKE', '%'.$this->code.'%'))
            ->when($this->receiver, fn ($query) => $query->whereIn('accepted_by', User::select('id')->where('username', '=', $this->receiver)))
            ->when($this->custom, fn ($query) => $query->where('custom', 'LIKE', '%'.$this->custom.'%'))
            ->when(
                $this->groupBy === 'user_id',
                fn ($query) => $query->groupBy('user_id')
                    ->from('invites as i1')
                    ->select([
                        'user_id',
                        DB::raw('MIN(created_at) as created_at_min'),
                        DB::raw('FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(created_at))) as created_at_avg'),
                        DB::raw('MAX(created_at) as created_at_max'),
                        DB::raw('COUNT(*) as sent_count'),
                        DB::raw('SUM(IF(accepted_by IS NULL, 0, 1)) as accepted_by_count'),
                        DB::raw("
                            (select
                                count(*)
                            from
                                users
                            where
                                id in (select accepted_by from invites i2 where i2.user_id = i1.user_id)
                                and group_id in (select id from `groups` where slug in ('banned', 'pruned', 'disabled'))
                            ) as inactive_count
                        "),
                        DB::raw("
                            100.0 * 
                            (select
                                count(*)
                            from
                                users
                            where
                                id in (select accepted_by from invites i2 where i2.user_id = i1.user_id)
                                and group_id in (select id from `groups` where slug in ('banned', 'pruned', 'disabled'))
                            )
                            / COUNT(*) as inactive_ratio
                        "),
                    ])
                    ->withCasts([
                        'created_at_min' => 'datetime',
                        'created_at_avg' => 'datetime',
                        'created_at_max' => 'datetime',
                    ])
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.invite-log-search', [
            'invites' => $this->invites,
        ]);
    }

    final public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }
}