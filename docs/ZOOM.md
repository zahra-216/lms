# Zoom classes and automatic attendance

Admins create a Zoom class in the LMS (**Zoom Classes** in the sidebar), tick the modules it covers, and share the join link.
When the class ends, the LMS reads who joined from Zoom and saves attendance by itself.

## What the LMS does

1. **Create.** The LMS asks Zoom to make a meeting in the campus Zoom account and stores the meeting ID, join link and passcode with the ticked modules.
2. **After the class.** Zoom calls `POST /zoom/webhook` (event `meeting.ended`). The request is checked against the app's secret token (HMAC signature, 5 minute window). The LMS then asks Zoom for the participant list. If Zoom has not prepared it yet, `php artisan zoom:sync` (scheduled every 5 minutes) tries again, up to 12 times, then marks the class "Needs attention". It also picks up classes whose webhook never arrived.
3. **Match.** A Zoom name counts as a student when it contains a registration number that exists in the LMS. Capitals, spaces, dashes and slashes are ignored, so `ttmc ml dip hrm-o 24 02 Kamal` matches `TTMC/ML/DIP/HRM-O/24/02`. A longer number is never mistaken for a shorter one (`...02` vs `...021`).
4. **Mark.** For each ticked module the students are those of the module's course, level and current semester (the same list the Attendance page shows). Joined = present, everyone else = absent. The date is the class's start date.
5. **Hand marks win.** A mark made on the Attendance page is never overwritten (`attendances.source = 'manual'`). Marks made by Zoom (`source = 'zoom'`) are refreshed when the list is fetched again.
6. **Names that did not match** (lecturers, guests, typos) are listed on the class page. Typing the right registration number there matches the student and marks them present.

## One-time setup (needs the campus Zoom admin)

In the Zoom App Marketplace create a **Server-to-Server OAuth** app for the campus account:

- Scopes: `meeting:write:meeting:admin` (create), `meeting:update:meeting:admin` (edit a class that has not started), `meeting:delete:meeting:admin` (cancel), `report:read:list_meeting_participants:admin` (read who joined; needs a Pro or higher plan), and, for **Link a Zoom meeting**, `meeting:read:meeting:admin` ("View a meeting") and `meeting:read:past_meeting:admin` ("View past meeting details") so the LMS can read a meeting's title, date, time and length. Exact scope names are shown in the Marketplace screen.
- Event subscription: **Meeting - End Meeting** and **Recording - All Recordings have completed**, address `https://lms.ttmetrocampus.com/zoom/webhook`. Copy its **Secret Token**.
- For class recordings also add the recording read scopes (search "recording" in the Scopes box and tick every permission that reads or views recordings, including the one that allows downloading them).
- Activate the app.

**Easiest: the admin page.** Sidebar **Zoom Connection** (`/admin/zoom-connection`) takes the five values below, saves them encrypted in the database (table `app_settings`, keys `zoom.*`), and has a **Test connection** button and the click-by-click steps for a new Zoom account. Values saved there win over the `.env` ones; boxes left empty fall back to `.env`. Changing the Zoom account therefore needs no developer. Classes made under an old account cannot be read after the switch (Zoom shows a class only to the account that made it), so let them finish first. The cached Zoom access token is keyed by account and client ID, so a new account never reuses the old token.

Or put the values in the server's `.env` (never in GitHub, never in chat), then `php artisan config:clear`:

```
ZOOM_ACCOUNT_ID=
ZOOM_CLIENT_ID=
ZOOM_CLIENT_SECRET=
ZOOM_WEBHOOK_SECRET=      # the event subscription's Secret Token
ZOOM_HOST_EMAIL=          # Zoom login (email) that owns the meetings
```

Leave `ZOOM_FAKE` off on the live server.

Hostinger cron job (hPanel, Advanced, Cron Jobs), every minute, so the retry command runs (check the folder path with `pwd` inside the LMS folder on the server and adjust it):

```
cd /home/u422071295/domains/ttmetrocampus.com/public_html/lms.ttmetrocampus.com && /opt/alt/php84/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

Update routine: `git pull`, `php artisan migrate --force` (new tables `zoom_meetings`, `zoom_meeting_subject`, `zoom_participants`, column `attendances.source`), `php artisan optimize:clear`.

## Local testing

`ZOOM_FAKE=true` pretends to be Zoom: meetings get made-up numbers and the participants are read from `storage/app/zoom-fake/<meeting id>.json`. Test scripts (outside the repo, in `lms-tools`): `zoom_seed.php`, `test_zoom.py`, `zoom_service_test.php`.

## Limits

- The real Zoom was not called during development. The first live class is the real test.
- The attendance date is the day the class really started (from Zoom's "meeting ended" signal; if that is missing, the day of the first person to join; if there is no list, the planned day).
- A class can be edited (title, date, time, length, modules) only until it starts. The meeting is updated in Zoom too and the join link stays the same. After it starts, it is locked.
- Only meetings the LMS knows are tracked: classes created on the Zoom Classes page, or meetings made in the Zoom app and linked with **Link a Zoom meeting** (paste the meeting link or ID and add the modules; the LMS reads the title, date, time and length from Zoom, and the four details can be typed in only as a fallback when Zoom cannot give them, for example when the two read permissions are missing). A meeting that is neither is ignored when it ends, so no attendance is saved.
- A linked meeting is never changed or cancelled in Zoom by the LMS: editing it only changes the LMS record, and deleting it only removes the LMS record. If it already took place, its attendance is fetched as soon as it is linked. One Zoom meeting ID can be linked once, so a recurring meeting that reuses one ID cannot be linked again for each class.
- If a meeting is rescheduled in the Zoom app, the attendance still lands on the day it really started.
- Two classes at the same time need two different Zoom hosts (a Zoom plan limit).


## Class recordings: Zoom cloud to YouTube

Every class the LMS creates asks Zoom to record to the cloud automatically (`auto_recording = cloud`). Classes made in the Zoom app and linked rely on the Zoom account setting (Settings, Recording, Automatic recording, cloud).

1. When Zoom has prepared the video it sends **recording completed** to `/zoom/webhook`. The LMS queues the video of each recording part (the best MP4: speaker view with shared screen first; audio, chat, transcript and tiny files are skipped). A class paused and restarted has several parts.
2. `php artisan zoom:recordings` (scheduled **every minute**, needs the Hostinger cron job for `schedule:run`) does the rest, one recording at a time:
   - it also asks Zoom for the recording of any class that ended (safety net, every 15 minutes, up to 14 times, for 7 days) and the **Check Zoom for the recording** button on the class page does the same at once;
   - downloads the video to `storage/app/zoom-recordings` (needs free disk space of about the size of the video), uploads it to YouTube as **Unlisted** in pieces (an interrupted upload carries on), then deletes the file;
   - makes a **Recording** for every module of the class (students see it on the module's Recordings page at once), titled "Module - date - Part n";
   - adds the video to the playlist of each programme (course + level), see **YouTube Playlists**.
3. A problem is retried later (YouTube's daily limit: 3 hours; others: 5 to 60 minutes) up to 6 times, then the class page shows **Needs attention** with a **Try again** button. If only the playlist step fails, the video still counts as done after 3 tries, with a note.
4. Zoom's recordings are never deleted by the LMS.

Needed once: YouTube connected (YouTube Connection page), Zoom app with the recording event and scopes (above), and the cron job running every minute. Tables: `zoom_recordings`, plus two columns on `zoom_meetings`.
