<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\YoutubePlaylist;
use App\Services\YoutubePlaylistResolver;
use App\Support\YoutubeSettings;
use Illuminate\Http\Request;

/**
 * "YouTube Playlists": every programme (course + level) has one playlist on the campus channel for its class
 * recordings. By default it is found by name or made by itself; here the admin can point a programme to any
 * playlist that already exists on the channel.
 */
class YoutubePlaylistController extends Controller
{
    private const LEVEL_ORDER = ['Diploma', 'HND', 'Top-up', 'Degree', 'Postgraduate Diploma'];

    /** The programmes the campus offers now, in a steady order: course name, then level. */
    public static function programmes()
    {
        return Level::active()->whereHas('course', fn ($q) => $q->active())->with('course')->get()
            ->sortBy(fn ($l) => strtolower($l->course->name).'|'.str_pad((string) (array_search($l->name, self::LEVEL_ORDER, true) === false ? 9 : array_search($l->name, self::LEVEL_ORDER, true)), 2, '0', STR_PAD_LEFT))
            ->values();
    }

    public function index(YoutubePlaylistResolver $resolver)
    {
        if (! YoutubeSettings::isConnected()) {
            return redirect()->route('admin.youtube.show')->with('error', 'Connect YouTube first.');
        }

        $problem = null;
        $channel = [];

        try {
            $channel = $resolver->channelPlaylists();
        } catch (\Throwable $e) {
            $problem = $e->getMessage();
        }

        $saved = YoutubePlaylist::all()->keyBy('level_id');
        $byName = collect($channel)->keyBy(fn ($p) => YoutubePlaylistResolver::normal($p['title']));

        $rows = self::programmes()->map(function ($level) use ($saved, $byName) {
            $title = YoutubePlaylistResolver::titleFor($level->course->name, $level->name);
            $match = $byName->get(YoutubePlaylistResolver::normal($title));

            return [
                'level_id' => $level->id,
                'title' => $title,
                'saved' => $saved->get($level->id)?->playlist_id,
                'match' => $match['title'] ?? null,
            ];
        });

        return view('admin.youtube.playlists', [
            'rows' => $rows,
            'channel' => $channel,
            'problem' => $problem,
            'canManage' => YoutubeSettings::canManagePlaylists(),
        ]);
    }

    public function refresh(YoutubePlaylistResolver $resolver)
    {
        try {
            $resolver->channelPlaylists(true);
        } catch (\Throwable $e) {
            return redirect()->route('admin.youtube.playlists')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.youtube.playlists')->with('success', 'The playlist list was refreshed from YouTube.');
    }

    public function save(Request $request, YoutubePlaylistResolver $resolver)
    {
        $request->validate(['map' => ['nullable', 'array'], 'map.*' => ['nullable', 'string', 'max:80']]);

        try {
            $channel = collect($resolver->channelPlaylists(true))->keyBy('id');
        } catch (\Throwable $e) {
            return redirect()->route('admin.youtube.playlists')->with('error', $e->getMessage());
        }

        $levels = self::programmes()->keyBy('id');
        $changed = 0;

        foreach ((array) $request->input('map', []) as $levelId => $playlistId) {
            $level = $levels->get((int) $levelId);
            if (! $level) {
                continue;
            }

            $current = YoutubePlaylist::where('level_id', $level->id)->first();
            $playlistId = trim((string) $playlistId);

            if ($playlistId === '') {
                if ($current) {
                    $current->delete();
                    $changed++;
                }

                continue;
            }

            $playlist = $channel->get($playlistId);
            if (! $playlist) {
                continue;   // not a playlist of this channel: ignore
            }

            if (! $current || $current->playlist_id !== $playlistId) {
                YoutubePlaylist::updateOrCreate(
                    ['course_id' => $level->course_id, 'level_id' => $level->id],
                    ['playlist_id' => $playlistId, 'title' => $playlist['title']]
                );
                $changed++;
            }
        }

        return redirect()->route('admin.youtube.playlists')->with('success', $changed ? 'Playlists saved.' : 'Nothing was changed.');
    }
}
