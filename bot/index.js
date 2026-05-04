require('dotenv').config({ path: require('path').resolve(__dirname, '../.env') });

// Dev only: Herd's local CA isn't in Node's trust store
process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';

const tmi = require('tmi.js');

const CHANNEL        = process.env.TWITCH_CHANNEL;
const APP_URL        = (process.env.APP_URL || 'https://streamavatars.test').replace(/\/$/, '');
const BOT_SECRET     = process.env.BOT_SECRET;
const CLIENT_ID      = process.env.TWITCH_CLIENT_ID;
const BOT_TOKEN      = process.env.TWITCH_BOT_OAUTH_TOKEN;       // oauth:xxxxxx  (IRC login)
const ACCESS_TOKEN   = process.env.TWITCH_BOT_ACCESS_TOKEN;      // xxxxxx        (Helix API)
const BOT_USERNAME   = process.env.TWITCH_BOT_USERNAME;

if (!CHANNEL || !BOT_SECRET) {
    console.error('[Bot] TWITCH_CHANNEL and BOT_SECRET must be set in .env');
    process.exit(1);
}

// ─── Helpers ────────────────────────────────────────────────────────────────

async function apiPost(path, body) {
    try {
        const res = await fetch(`${APP_URL}${path}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-Bot-Secret': BOT_SECRET,
            },
            body: JSON.stringify(body),
        });
        if (!res.ok) {
            const text = await res.text();
            console.error(`[API] ${res.status} on ${path}:`, text.slice(0, 200));
        }
    } catch (e) {
        console.error(`[API] fetch error on ${path}:`, e.message);
    }
}

async function notifyActive(viewer) {
    await apiPost('/api/bot/viewer-active', viewer);
}

// ─── Helix chatters poll ─────────────────────────────────────────────────────

let broadcasterId = null;

async function fetchBroadcasterId() {
    if (!CLIENT_ID || !ACCESS_TOKEN) return null;
    try {
        const res = await fetch(`https://api.twitch.tv/helix/users?login=${encodeURIComponent(CHANNEL)}`, {
            headers: {
                'Client-ID':     CLIENT_ID,
                'Authorization': `Bearer ${ACCESS_TOKEN}`,
            },
        });
        const data = await res.json();
        return data.data?.[0]?.id ?? null;
    } catch (e) {
        console.error('[Helix] Failed to fetch broadcaster ID:', e.message);
        return null;
    }
}

async function pollChatters() {
    if (!CLIENT_ID || !ACCESS_TOKEN) return;

    if (!broadcasterId) {
        broadcasterId = await fetchBroadcasterId();
        if (!broadcasterId) return;
    }

    let cursor = null;
    let total  = 0;

    do {
        const url = new URL('https://api.twitch.tv/helix/chat/chatters');
        url.searchParams.set('broadcaster_id', broadcasterId);
        url.searchParams.set('moderator_id',   broadcasterId);
        url.searchParams.set('first',           '1000');
        if (cursor) url.searchParams.set('after', cursor);

        let data;
        try {
            const res = await fetch(url.toString(), {
                headers: {
                    'Client-ID':     CLIENT_ID,
                    'Authorization': `Bearer ${ACCESS_TOKEN}`,
                },
            });
            data = await res.json();

            if (data.error) {
                console.warn('[Helix] Chatters error:', data.message);
                return;
            }
        } catch (e) {
            console.error('[Helix] Chatters fetch error:', e.message);
            return;
        }

        for (const chatter of data.data ?? []) {
            await notifyActive({
                twitch_id:            chatter.user_id,
                twitch_username:      chatter.user_login,
                twitch_display_name:  chatter.user_name,
            });
            total++;
        }

        cursor = data.pagination?.cursor ?? null;
    } while (cursor);

    if (total > 0) console.log(`[Chatters] Synced ${total} viewer(s)`);
}

// ─── IRC client ──────────────────────────────────────────────────────────────

const clientOpts = {
    channels: [`#${CHANNEL}`],
    options:  { debug: false },
};

if (BOT_USERNAME && BOT_TOKEN) {
    clientOpts.identity = {
        username: BOT_USERNAME,
        password: BOT_TOKEN, // must start with oauth:
    };
} else {
    console.warn('[Bot] No identity set — connecting anonymously. Commands will not work.');
}

const client = new tmi.Client(clientOpts);

client.on('message', async (channel, tags, message, self) => {
    if (self) return;

    const viewer = {
        twitch_id:           tags['user-id'],
        twitch_username:     tags.username,
        twitch_display_name: tags['display-name'] || tags.username,
    };

    await notifyActive(viewer);

    const cmd = message.trim().toLowerCase();

    if (cmd === '!avatar') {
        client.say(channel, `@${tags['display-name']} → customize your avatar at: ${APP_URL}/avatar`);
    }

    if (cmd === '!overlay' && tags.mod) {
        client.say(channel, `Overlay URL: ${APP_URL}/overlay/${CHANNEL}/${process.env.OVERLAY_TOKEN ?? '(set OVERLAY_TOKEN in .env)'}`);
    }
});

client.on('connected', (addr, port) => {
    console.log(`[Bot] Connected to ${addr}:${port} — watching #${CHANNEL}`);
});

client.on('disconnected', reason => {
    console.warn('[Bot] Disconnected:', reason);
    setTimeout(() => client.connect(), 5_000);
});

// ─── Boot ────────────────────────────────────────────────────────────────────

client.connect().then(() => {
    pollChatters();
    setInterval(pollChatters, 60_000);
}).catch(err => {
    console.error('[Bot] Failed to connect:', err);
    process.exit(1);
});
