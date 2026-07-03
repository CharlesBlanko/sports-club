@php
    // Icons adapted from Tabler Icons, MIT License: https://tabler.io/icons
    $iconName = match ($sport) {
        'Run', 'TrailRun' => 'run',
        'VirtualRun' => 'treadmill',
        'Ride', 'VirtualRide', 'MountainBikeRide', 'GravelRide', 'EBikeRide', 'EMountainBikeRide', 'Handcycle', 'Velomobile' => 'bike',
        'Walk' => 'walk',
        'Hike' => 'trekking',
        'Swim' => 'swimming',
        'WeightTraining', 'Workout', 'Crossfit' => 'barbell',
        'Yoga' => 'yoga',
        'Pilates' => 'stretching',
        'Soccer' => 'football',
        'Tennis', 'Pickleball', 'Racquetball', 'Squash' => 'tennis',
        'Golf' => 'golf',
        'AlpineSki', 'BackcountrySki', 'NordicSki', 'RollerSki' => 'ski',
        'Snowboard' => 'snowboard',
        'Canoeing', 'Kayaking', 'Kitesurf', 'Rowing', 'Sail', 'StandUpPaddling', 'Surfing', 'Windsurf' => 'sail',
        default => 'activity',
    };

    $paths = [
        'activity' => ['M3 12h4l3 8l4 -16l3 8h4'],
        'barbell' => ['M2 12h1', 'M6 8h-2a1 1 0 0 0 -1 1v6a1 1 0 0 0 1 1h2', 'M6 7v10a1 1 0 0 0 1 1h1a1 1 0 0 0 1 -1v-10a1 1 0 0 0 -1 -1h-1a1 1 0 0 0 -1 1', 'M9 12h6', 'M15 7v10a1 1 0 0 0 1 1h1a1 1 0 0 0 1 -1v-10a1 1 0 0 0 -1 -1h-1a1 1 0 0 0 -1 1', 'M18 8h2a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-2', 'M22 12h-1'],
        'bike' => ['M2 18a3 3 0 1 0 6 0a3 3 0 0 0 -6 0', 'M16 18a3 3 0 1 0 6 0a3 3 0 0 0 -6 0', 'M12 19v-4l-3 -3l5 -4l2 3h3', 'M13.007 5a2 2 0 1 0 4 0a2 2 0 1 0 -4 0'],
        'football' => ['M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0', 'M12 7l4.76 3.45l-1.76 5.55h-6l-1.76 -5.55l4.76 -3.45', 'M12 7v-4m3 13l2.5 3m-.74 -8.55l3.74 -1.45m-11.44 7.05l-2.56 2.95m.74 -8.55l-3.74 -1.45'],
        'golf' => ['M12 18v-15l7 4l-7 4', 'M9 17.67c-.62 .36 -1 .82 -1 1.33c0 1.1 1.8 2 4 2s4 -.9 4 -2c0 -.5 -.38 -.97 -1 -1.33'],
        'run' => ['M11.007 5a2 2 0 1 0 4 0a2 2 0 1 0 -4 0', 'M4 17l5 1l.75 -1.5', 'M15 21v-4l-4 -3l1 -6', 'M7 12v-3l5 -1l3 3l3 1'],
        'sail' => ['M2 20a2.4 2.4 0 0 0 2 1a2.4 2.4 0 0 0 2 -1a2.4 2.4 0 0 1 2 -1a2.4 2.4 0 0 1 2 1a2.4 2.4 0 0 0 2 1a2.4 2.4 0 0 0 2 -1a2.4 2.4 0 0 1 2 -1a2.4 2.4 0 0 1 2 1a2.4 2.4 0 0 0 2 1a2.4 2.4 0 0 0 2 -1', 'M4 18l-1 -3h18l-1 3', 'M11 12h7l-7 -9v9', 'M8 7l-2 5'],
        'ski' => ['M17 17.5l-5 -4.5v-6l5 4', 'M7 17.5l5 -4.5', 'M15.103 21.58l6.762 -14.502a2 2 0 0 0 -.968 -2.657', 'M8.897 21.58l-6.762 -14.503a2 2 0 0 1 .968 -2.657', 'M7 11l5 -4', 'M10.007 4a2 2 0 1 0 4 0a2 2 0 1 0 -4 0'],
        'snowboard' => ['M15 3a1 1 0 1 0 2 0a1 1 0 0 0 -2 0', 'M7 19l4 -2.5l-.5 -1.5', 'M16 21l-1 -6l-4.5 -3l3.5 -6', 'M7 9l1.5 -3h5.5l2 4l3 1', 'M3 17c.399 1.154 .899 1.805 1.5 1.951c6 1.464 10.772 2.262 13.5 2.927c1.333 .325 2.333 0 3 -.976'],
        'stretching' => ['M15 5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0', 'M5 20l5 -.5l1 -2', 'M18 20v-5h-5.5l2.5 -6.5l-5.5 1l1.5 2'],
        'swimming' => ['M15 9a1 1 0 1 0 2 0a1 1 0 1 0 -2 0', 'M6 11l4 -2l3.5 3l-1.5 2', 'M3 16.75a2.4 2.4 0 0 0 1 .25a2.4 2.4 0 0 0 2 -1a2.4 2.4 0 0 1 2 -1a2.4 2.4 0 0 1 2 1a2.4 2.4 0 0 0 2 1a2.4 2.4 0 0 0 2 -1a2.4 2.4 0 0 1 2 -1a2.4 2.4 0 0 1 2 1a2.4 2.4 0 0 0 2 1a2.4 2.4 0 0 0 1 -.25'],
        'tennis' => ['M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0', 'M6 5.3a9 9 0 0 1 0 13.4', 'M18 5.3a9 9 0 0 0 0 13.4'],
        'trekking' => ['M11 4a1 1 0 1 0 2 0a1 1 0 1 0 -2 0', 'M7 21l2 -4', 'M13 21v-4l-3 -3l1 -6l3 4l3 2', 'M10 14l-1.827 -1.218a2 2 0 0 1 -.831 -2.15l.28 -1.117a2 2 0 0 1 1.939 -1.515h1.439l4 1l3 -2', 'M17 12v9', 'M16 20h2'],
        'treadmill' => ['M10 3a1 1 0 1 0 2 0a1 1 0 0 0 -2 0', 'M3 14l4 1l.5 -.5', 'M12 18v-3l-3 -2.923l.75 -5.077', 'M6 10v-2l4 -1l2.5 2.5l2.5 .5', 'M21 22a1 1 0 0 0 -1 -1h-16a1 1 0 0 0 -1 1', 'M18 21l1 -11l2 -1'],
        'walk' => ['M12 4a1 1 0 1 0 2 0a1 1 0 1 0 -2 0', 'M7 21l3 -4', 'M16 21l-2 -4l-3 -3l1 -6', 'M6 12l2 -3l4 -1l3 3l3 1'],
        'yoga' => ['M4 20h4l1.5 -3', 'M17 20l-1 -5h-5l1 -7', 'M4 10l4 -1l4 -1l4 1.5l4 1.5', 'M10.007 5a2 2 0 1 0 4 0a2 2 0 1 0 -4 0'],
    ];
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true">
    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
    @foreach ($paths[$iconName] as $path)
        <path d="{{ $path }}" />
    @endforeach
</svg>
