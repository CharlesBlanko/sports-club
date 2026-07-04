<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $calendarMonth = Carbon::parse($request->string('calendar', now()->format('Y-m'))->toString().'-01');
        $calendar = $this->calendar($calendarMonth);

        return view('dashboard.calendar', [
            'calendar' => $calendar,
            'calendarMonth' => $calendarMonth,
            'previousMonth' => $calendarMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $calendarMonth->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function group(Request $request): View
    {
        $period = $request->string('period', 'month')->toString();
        $sort = $request->string('sort', 'distance')->toString();
        $direction = $request->string('direction', 'desc')->toString() === 'asc' ? 'asc' : 'desc';

        [$from, $to] = $this->dateRange($period);

        $members = User::query()
            ->withCount(['activities' => fn (Builder $query) => $this->applyDateRange($query, $from, $to)])
            ->withSum(['activities as distance_sum' => fn (Builder $query) => $this->applyDateRange($query, $from, $to)], 'distance')
            ->withSum(['activities as moving_time_sum' => fn (Builder $query) => $this->applyDateRange($query, $from, $to)], 'moving_time')
            ->withSum(['activities as elevation_sum' => fn (Builder $query) => $this->applyDateRange($query, $from, $to)], 'total_elevation_gain')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'profile' => $user->profile,
                'activities_count' => (int) $user->activities_count,
                'distance' => (float) $user->distance_sum,
                'moving_time' => (int) $user->moving_time_sum,
                'elevation' => (float) $user->elevation_sum,
            ]);

        $members = $this->sortMembers($members, $sort, $direction);

        return view('dashboard.group', [
            'period' => $period,
            'sort' => $sort,
            'direction' => $direction,
            'members' => $members,
            'currentUserId' => Auth::id(),
        ]);
    }

    public function activities(): View
    {
        $latestActivities = Activity::query()
            ->with('user')
            ->latest('started_at')
            ->limit(15)
            ->get()
            ->map(function (Activity $activity) {
                $activity->sport_label = $this->sportLabel($this->calendarSportGroup($activity->sport_type ?: $activity->type ?: 'Autre'));

                return $activity;
            });

        return view('dashboard.activities', [
            'latestActivities' => $latestActivities,
        ]);
    }

    private function dateRange(string $period): array
    {
        return match ($period) {
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'last_month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            'last_year' => [now()->subYearNoOverflow()->startOfYear(), now()->subYearNoOverflow()->endOfYear()],
            'all' => [null, null],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    private function applyDateRange(Builder $query, ?Carbon $from, ?Carbon $to): Builder
    {
        return $query
            ->when($from, fn (Builder $query) => $query->where('started_at', '>=', $from))
            ->when($to, fn (Builder $query) => $query->where('started_at', '<=', $to));
    }

    private function sortMembers(Collection $members, string $sort, string $direction): Collection
    {
        $allowed = ['name', 'moving_time', 'activities_count', 'distance', 'elevation'];
        $sort = in_array($sort, $allowed, true) ? $sort : 'distance';
        $sorted = $sort === 'name' ? $members->sortBy('name') : $members->sortBy($sort);

        return ($direction === 'desc' ? $sorted->reverse() : $sorted)->values();
    }

    private function calendar(Carbon $month): Collection
    {
        $first = $month->copy()->startOfMonth()->startOfWeek();
        $last = $month->copy()->endOfMonth()->endOfWeek();
        $activities = Activity::query()
            ->with('user')
            ->whereBetween('started_at', [$first, $last])
            ->get()
            ->groupBy(fn (Activity $activity) => $activity->started_at->toDateString());

        return collect(CarbonPeriod::create($first, $last))
            ->chunk(7)
            ->map(fn (Collection $week) => $week->map(function (Carbon $day) use ($month, $activities) {
                $daily = $activities->get($day->toDateString(), collect());

                return [
                    'date' => $day->copy(),
                    'in_month' => $day->isSameMonth($month),
                    'sports' => $daily
                        ->groupBy(fn (Activity $activity) => $this->calendarSportGroup($activity->sport_type ?: $activity->type ?: 'Autre'))
                        ->map(fn (Collection $items, string $sport) => [
                            'sport' => $sport,
                            'label' => $this->sportLabel($sport),
                            'members' => $items->pluck('user.name')->unique()->sort()->values(),
                            'count' => $items->count(),
                        ])
                        ->values(),
                ];
            }));
    }

    private function calendarSportGroup(string $sport): string
    {
        return match ($sport) {
            'MountainBikeRide', 'EMountainBikeRide' => 'MountainBikeRide',
            'Ride', 'VirtualRide', 'GravelRide', 'EBikeRide', 'Handcycle', 'Velomobile' => 'Ride',
            'Run', 'TrailRun', 'VirtualRun' => 'Run',
            'AlpineSki', 'BackcountrySki', 'NordicSki', 'RollerSki' => 'AlpineSki',
            'Kitesurf', 'Rowing', 'Sail', 'StandUpPaddling', 'Surfing', 'Windsurf' => 'Sail',
            'Tennis', 'Pickleball', 'Racquetball', 'Squash' => 'Tennis',
            'WeightTraining', 'Crossfit' => 'Workout',
            default => $sport,
        };
    }

    private function sportLabel(string $sport): string
    {
        return match ($sport) {
            'Run' => 'Course',
            'Ride' => 'V&eacute;lo',
            'MountainBikeRide' => 'V&eacute;lo de montagne',
            'Walk' => 'Marche',
            'Hike' => 'Randonn&eacute;e',
            'Swim' => 'Natation',
            'Kayaking' => 'Kayak',
            'Canoeing' => 'Canot',
            'Workout' => 'Entra&icirc;nement',
            'Yoga' => 'Yoga',
            'Pilates' => 'Pilates',
            'Soccer' => 'Soccer',
            'Tennis' => 'Tennis',
            'Golf' => 'Golf',
            'AlpineSki' => 'Ski',
            'Snowboard' => 'Snowboard',
            'Sail' => 'Sport nautique',
            'Autre' => 'Autre',
            default => e($sport),
        };
    }
}
