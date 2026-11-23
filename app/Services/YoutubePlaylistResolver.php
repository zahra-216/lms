<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Level;
use App\Models\YoutubePlaylist;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

/**
 * Decides which YouTube playlist a programme's recordings go into. A programme is a course plus a level,
 * and its playlist is called like "Diploma in Artificial Intelligence". Order of choice:
 *   1. the playlist the admin picked (or that was found earlier) on the YouTube Playlists page,
 *   2. a playlist on the channel with the same name,
 *   3. a new playlist with that name (made once, then remembered).
 */
class YoutubePlaylistResolver
{
    public function __construct(private YoutubeService $youtube)
    {
    }

    public static function titleFor(string $course, string $level): string
    {
        return trim($level.' in '.$course);
    }

    /** "Diploma in  Artificial-Intelligence" and "diploma in artificial intelligence" count as the same name. */
    public static function normal(string $name): string
    {
        return trim(preg_replace('/[^a-z0-9]+/', ' ', strtolower($name)));
    }

    /** @return array<int, array{id: string, title: string}> */
    public function channelPlaylists(bool $fresh = false): array
    {
        if ($fresh) {
            Cache::forget('youtube.playlists');
        }

        return Cache::remember('youtube.playlists', now()->addMinutes(10), fn () => $this->youtube->playlists());
    }

    /** The playlist for this programme: [id, title, created]. Makes it when it does not exist yet. */
    public function resolve(Course $course, Level $level): array
    {
        $saved = YoutubePlaylist::where('course_id', $course->id)->where('level_id', $level->id)->first();
        if ($saved) {
            return ['id' => $saved->playlist_id, 'title' => $saved->title, 'created' => false];
        }

        $title = self::titleFor($course->name, $level->name);
        $wanted = self::normal($title);

        foreach ($this->channelPlaylists(true) as $playlist) {
            if (self::normal($playlist['title']) === $wanted) {
                $this->remember($course, $level, $playlist['id'], $playlist['title']);

                return ['id' => $playlist['id'], 'title' => $playlist['title'], 'created' => false];
            }
        }

        $id = $this->youtube->createPlaylist($title, 'Class recordings: '.$title);
        Cache::forget('youtube.playlists');
        $this->remember($course, $level, $id, $title);

        return ['id' => $id, 'title' => $title, 'created' => true];
    }

    /**
     * Put a video into its programme's playlist. If the remembered playlist was deleted on YouTube,
     * forget it and use (or make) the right one.
     *
     * @return array{id: string, title: string, created: bool}
     */
    public function addVideo(Course $course, Level $level, string $videoId): array
    {
        $playlist = $this->resolve($course, $level);

        try {
            $this->youtube->addToPlaylist($playlist['id'], $videoId);
        } catch (RuntimeException $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }

            YoutubePlaylist::where('course_id', $course->id)->where('level_id', $level->id)->delete();
            Cache::forget('youtube.playlists');
            $playlist = $this->resolve($course, $level);
            $this->youtube->addToPlaylist($playlist['id'], $videoId);
        }

        return $playlist;
    }

    private function remember(Course $course, Level $level, string $id, string $title): void
    {
        YoutubePlaylist::updateOrCreate(
            ['course_id' => $course->id, 'level_id' => $level->id],
            ['playlist_id' => $id, 'title' => $title]
        );
    }
}
