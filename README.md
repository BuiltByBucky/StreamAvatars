# StreamAvatars

A custom **Stream Avatars-style overlay** for Twitch, built with **Vue.js**, **Laravel**, and a **Twitch bot**. Viewers can customize their avatar via a personal URL. The longer they watch and the more they support, the more cosmetic items, outfits, and upgrades they can unlock.

Avatars are displayed at the bottom of the stream via an **OBS Browser Source overlay URL**.

---

## Table of Contents

* [Concept](#concept)
* [Key features](#key-features)
* [Tech stack](#tech-stack)
* [Architecture](#architecture)
* [User roles](#user-roles)
* [Avatar system](#avatar-system)
* [Progression and rewards](#progression-and-rewards)
* [Twitch integration](#twitch-integration)
* [Twitch bot](#twitch-bot)
* [OBS overlay](#obs-overlay)
* [Viewer customization portal](#viewer-customization-portal)
* [Admin dashboard](#admin-dashboard)
* [Database models](#database-models)
* [Realtime updates](#realtime-updates)
* [Installation](#installation)
* [Environment variables](#environment-variables)
* [Development commands](#development-commands)
* [API endpoints](#api-endpoints)
* [Roadmap](#roadmap)
* [Possible extensions](#possible-extensions)

---

## Concept

Twitch viewers get their own avatar that is visible on the stream. This avatar can be customized via a web interface. Viewers earn points, XP, or unlocks by being active in the stream.

Examples of progression:

* Watching longer
* Chatting
* Following
* Subscribing
* Donating bits
* Raids/hosts
* Channel point redemptions
* Special events
* Moderator or VIP status

The streamer receives an overlay URL for OBS. This overlay displays all active avatars at the bottom of the stream.

---

## Key features

### Viewer features

* Twitch login for viewers
* Create a personal avatar
* Customize avatar via a webpage
* Adjust outfit, hair, face, accessories, and effects
* View unlocked items
* Show locked items with unlock conditions
* See avatar update live on stream
* View points, XP, levels, or watchtime
* View support rewards
* Save favorite outfit
* Random outfit button
* Preview avatar before changes go live

### Streamer features

* Generate OBS overlay URL
* Manage overlay settings
* Adjust position, size, and behavior of avatars
* Create and manage items
* Link rewards to watchtime, subs, bits, or events
* Manually grant points or items
* Search and manage users
* Temporarily hide or ban avatars from overlay
* Start special events
* Trigger global animations

### Overlay features

* Display avatars at the bottom of the stream
* Realtime updates without refreshing OBS
* Automatically show new viewers
* Automatically hide inactive viewers
* Avatar animations such as idle, walking, jumping, dancing
* Chat command animations
* Special effects on subs, bits, raids, or follows
* Collision/spacing so avatars don't fully overlap
* Transparent background for OBS
* Browser Source compatible

### Twitch bot features

* Bot connects to Twitch chat
* Read chat commands
* Track viewer activity
* Distribute points or XP
* Commands for viewers
* Commands for mods/streamer
* Send event triggers to Laravel
* Twitch EventSub integration possible

---

## Tech stack

### Frontend

* Vue.js
* Vite
* Pinia for state management
* Vue Router
* Tailwind CSS
* Axios or Fetch API
* WebSocket client for realtime updates

### Backend

* Laravel
* Laravel Sanctum for authentication (SPA tokens)
* Laravel Socialite for Twitch OAuth
* Laravel Echo for realtime events
* Redis for queues/cache/pub-sub
* MySQL or PostgreSQL

### Realtime

* Laravel Reverb (primary choice — official Laravel WebSocket server)

### Twitch

* Twitch OAuth
* Twitch Helix API
* Twitch EventSub
* Twitch IRC chat bot

### OBS

* OBS Browser Source
* Transparent overlay URL
* No login required for overlay, but secured with token

---

## Architecture

```text
Twitch Chat / Twitch Events
        |
        v
Twitch Bot / EventSub Listener
        |
        v
Laravel Backend  <----> Database
        |
        | Realtime events
        v
Vue Overlay URL  ---> OBS Browser Source
        |
        v
Stream output

Viewer URL
        |
        v
Vue Customization Portal
        |
        v
Laravel API
```

### Key components

1. **Laravel API**

   * Manages users, avatars, items, unlocks, rewards, and overlay state.

2. **Vue Customization Portal**

   * Webpage where viewers customize their avatar.

3. **OBS Overlay**

   * Vue page with transparent background showing all active avatars.

4. **Twitch Bot**

   * Reads chat, processes commands, and sends events to Laravel.

5. **Realtime layer**

   * Ensures changes are immediately visible in OBS.

---

## User roles

### Viewer

A regular Twitch viewer.

Can:

* Log in with Twitch
* Customize avatar
* Unlock items
* Earn points/XP
* Use commands

### Subscriber

A viewer with an active sub.

Extra capabilities:

* Exclusive subscriber items
* Faster XP gain
* Special effects
* Subscriber badge/cosmetic

### VIP

A Twitch VIP.

Extra capabilities:

* VIP-only cosmetics
* Special avatar effects
* Priority in overlay

### Moderator

A Twitch moderator.

Can:

* Use certain bot commands
* Temporarily hide avatars
* Start events
* Help users reset

### Streamer/Admin

Full access.

Can:

* Manage items
* Configure rewards
* Configure overlay
* Manage users
* Adjust points
* Manage shop
* Start events

---

## Avatar system

An avatar consists of multiple layers. Each layer can be customized separately.

Example layers:

* Body/base
* Skin color
* Eyes
* Mouth
* Hair
* Shirt
* Pants
* Shoes
* Hat
* Glasses
* Accessory
* Back item
* Pet
* Aura/effect
* Badge

### Layer-based rendering

Avatars are built from stacked PNG/SVG layers.

Example:

```text
base.png
skin-tone.png
eyes-blue.png
hair-black.png
shirt-hoodie-red.png
pants-jeans.png
shoes-sneakers.png
hat-cap.png
effect-glow.png
```

### Item properties

An item can have the following properties:

* Name
* Type/layer
* Rarity
* Image path
* Animated image path
* Unlock condition
* Price
* Is subscriber-only
* Is VIP-only
* Is event-only
* Is hidden
* Sort order

### Rarities

Example rarities:

* Common
* Uncommon
* Rare
* Epic
* Legendary
* Mythic
* Event
* Subscriber
* VIP

---

## Progression and rewards

Viewers earn progression through interaction with the stream.

### Watchtime

Viewers receive XP or points per x minutes of watch time.

Example:

* 1 point per 5 minutes watching
* 10 XP per 10 minutes watching
* Bonus XP during special streams

### Chat activity

Viewers can earn extra XP by actively chatting.

Anti-spam rules:

* Maximum 1 reward per x minutes
* No points for repeated messages
* No points for emote-only spam, unless allowed

### Support rewards

Support can grant extra unlocks.

Examples:

* Follow: starter item
* Sub: subscriber outfit
* Gift sub: special gift badge
* Bits: glow effect or temporary animation
* Raid: raid cape or raid badge
* Channel points: temporary action or unlock

### Levels

Example level structure:

| Level | Required XP | Reward                |
| ----: | ----------: | --------------------- |
|     1 |           0 | Basic avatar          |
|     2 |         100 | Extra shirt           |
|     5 |         500 | Hat                   |
|    10 |       1,500 | Rare outfit           |
|    25 |      10,000 | Legendary aura        |
|    50 |      50,000 | Special prestige item |

### Currency

Besides XP there can be a separate currency.

Examples:

* Coins
* Stream tokens
* Rave points
* Techno coins
* Bass coins

This currency can be used in an avatar shop.

---

## Twitch integration

### Twitch OAuth

Viewers log in via Twitch. After login the Twitch user is linked to a local user in Laravel.

Stored data:

* Twitch user ID
* Twitch username
* Display name
* Profile image
* Access token
* Refresh token
* Token expiry

### Twitch Helix API

Use the Twitch API for:

* Fetching user data
* Checking subscriber status
* Checking moderator status
* Checking VIP status
* Fetching channel information

### Twitch EventSub

Use EventSub for events such as:

* Follow
* Subscribe
* Resub
* Gift sub
* Bits / cheer
* Raid
* Channel point redemption
* Stream online/offline

### Twitch IRC

Use Twitch IRC for chat messages and commands.

---

## Twitch bot

The Twitch bot runs as a separate service or Laravel command.

Possible implementations:

* Node.js bot with tmi.js
* Laravel Artisan command with IRC client
* Standalone service making API calls to Laravel

### Bot responsibilities

* Connect to Twitch chat
* Read chat messages
* Parse commands
* Track viewer activity
* Distribute points
* Send events to Laravel API
* Execute mod/admin commands

### Viewer commands

Examples:

```text
!avatar
!outfit
!points
!level
!watchtime
!dance
!jump
!shop
!unlock
```

### Moderator commands

Examples:

```text
!avatar hide <username>
!avatar show <username>
!givepoints <username> <amount>
!giveitem <username> <item>
!event start <event-name>
!event stop
```

### Bot responses

Examples:

```text
@username customize your avatar at: https://example.com/avatar
@username you have 420 Techno Coins and are level 8.
@username unlocked a new outfit: Neon Hoodie.
```

---

## OBS overlay

The overlay is added as a **Browser Source** in OBS.

Example overlay URL:

```text
https://example.com/overlay/{channel}/{overlay_token}
```

### OBS Browser Source settings

Recommended settings:

```text
Width: 1920
Height: 1080
FPS: 30 or 60
Custom CSS: optional
Shutdown source when not visible: enabled
Refresh browser when scene becomes active: optional
```

### Overlay properties

* Transparent background
* Avatars positioned at the bottom
* Responsive for 720p, 1080p, and 1440p
* No login required
* Secured with secret token
* Realtime updates via WebSocket

### Overlay modes

Possible modes:

* Always show active chatters
* Show only recent chatters
* Show only subscribers
* Show VIPs and mods bigger
* Event mode
* Dance floor mode
* Minimal mode

### Avatar behavior on overlay

Possible behaviors:

* Idle animation
* Random walking
* Jump on chat message
* Dance on command
* Glow on sub/bits
* Spawn animation when joining
* Despawn animation after inactivity
* Special raid animation

---

## Viewer customization portal

Viewer URL:

```text
https://example.com/avatar
```

After Twitch login the viewer can:

* View avatar
* Customize outfit
* Filter items by type
* Filter items by unlocked/locked
* View unlock conditions
* Buy shop items
* See current points and level
* Reset avatar
* View preview
* Save outfit

### Pages

Example routes:

```text
/avatar
/avatar/editor
/avatar/shop
/avatar/inventory
/avatar/progress
/avatar/settings
```

### Editor features

* Live preview
* Tabs per item type
* Show locked items greyed out
* Rarity badges
* Search/filter
* Save button
* Randomize button
* Reset outfit button

---

## Admin dashboard

Admin URL:

```text
https://example.com/admin
```

### Dashboard features

* Overview of active viewers
* Search viewers
* View avatars
* Manage items
* Manage rewards
* Manage overlay settings
* View bot status
* View EventSub status
* Manually adjust points
* Adjust user inventory
* View logs

### Item management

Admin can:

* Add item
* Edit item
* Upload image
* Set rarity
* Set unlock condition
* Temporarily hide item
* Delete or archive item

### Reward management

Admin can configure rewards for:

* Watchtime
* Chat activity
* Follow
* Sub
* Bits
* Raid
* Channel points
* Events
* Manual giveaways

---

## Database models

### users

Contains local users.

Key fields:

```text
id
name
email nullable
twitch_id
twitch_username
twitch_display_name
twitch_profile_image
role
created_at
updated_at
```

### avatars

Contains the active avatar configuration per user.

```text
id
user_id
base_item_id
skin_item_id
eyes_item_id
mouth_item_id
hair_item_id
shirt_item_id
pants_item_id
shoes_item_id
hat_item_id
glasses_item_id
accessory_item_id
back_item_id
pet_item_id
effect_item_id
badge_item_id
is_visible
last_active_at
created_at
updated_at
```

### avatar_items

All available cosmetic items.

```text
id
name
slug
type
rarity
image_path
animated_image_path nullable
price nullable
unlock_type nullable
unlock_value nullable
is_default
is_subscriber_only
is_vip_only
is_event_only
is_hidden
sort_order
created_at
updated_at
```

### user_avatar_items

Pivot table of unlocked items per user.

```text
id
user_id
avatar_item_id
unlocked_at
source
created_at
updated_at
```

### user_progress

Progression per viewer.

```text
id
user_id
xp
level
coins
watchtime_minutes
chat_messages_count
last_rewarded_at
created_at
updated_at
```

### reward_rules

Configurable reward rules.

```text
id
name
event_type
reward_type
reward_value
required_value nullable
cooldown_seconds nullable
is_active
created_at
updated_at
```

### overlay_settings

Settings per streamer/channel.

```text
id
channel_id
overlay_token
avatar_scale
max_visible_avatars
show_inactive_viewers
inactive_timeout_seconds
position
animation_mode
created_at
updated_at
```

### bot_events

Log of bot and Twitch events.

```text
id
user_id nullable
event_type
payload json
processed_at nullable
created_at
updated_at
```

---

## Realtime updates

Realtime communication is essential so OBS updates automatically.

### Events

Examples of events:

```text
AvatarUpdated
AvatarJoinedOverlay
AvatarLeftOverlay
ViewerLeveledUp
ItemUnlocked
PointsUpdated
OverlaySettingsUpdated
GlobalAnimationTriggered
```

### Event flow

```text
Viewer updates avatar
        |
        v
Laravel saves avatar
        |
        v
Laravel broadcasts AvatarUpdated event
        |
        v
OBS overlay receives event via WebSocket
        |
        v
Avatar updates live without refresh
```

---

## Installation

### Requirements

* PHP 8.3+
* Composer
* Node.js 20+
* npm/pnpm/yarn
* MySQL or PostgreSQL
* Redis
* Laravel 13
* Twitch Developer Application

### Install backend

```bash
git clone git@github.com:username/streamavatars.git
cd streamavatars
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

### Install frontend

```bash
npm install
npm run dev
```

### Start queue worker

```bash
php artisan queue:work
```

### Start realtime server

With Laravel Reverb:

```bash
php artisan reverb:start
```

### Start Twitch bot

As a Laravel command:

```bash
php artisan twitch:bot
```

As a Node.js bot:

```bash
node bot/index.js
```

---

## Environment variables

Example `.env` configuration:

```env
APP_NAME="StreamAvatars"
APP_URL=https://example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=streamavatars
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=redis
CACHE_STORE=redis

TWITCH_CLIENT_ID=
TWITCH_CLIENT_SECRET=
TWITCH_REDIRECT_URI=https://example.com/auth/twitch/callback
TWITCH_BOT_USERNAME=
TWITCH_BOT_OAUTH_TOKEN=
TWITCH_CHANNEL=
TWITCH_EVENTSUB_SECRET=

OVERLAY_BASE_URL=https://example.com/overlay
```

---

## Development commands

```bash
# Backend
php artisan serve
php artisan migrate:fresh --seed
php artisan queue:work
php artisan reverb:start
php artisan twitch:bot

# Frontend
npm run dev
npm run build
npm run preview

# Tests
php artisan test
npm run test
```

---

## API endpoints

### Auth

```text
GET  /auth/twitch/redirect
GET  /auth/twitch/callback
POST /logout
```

### Viewer avatar

```text
GET    /api/me
GET    /api/avatar
PUT    /api/avatar
POST   /api/avatar/randomize
POST   /api/avatar/reset
GET    /api/avatar/items
GET    /api/avatar/inventory
GET    /api/avatar/progress
```

### Shop

```text
GET  /api/shop/items
POST /api/shop/items/{item}/buy
```

### Overlay

```text
GET /overlay/{channel}/{token}
GET /api/overlay/{channel}/{token}/state
```

### Admin

```text
GET    /api/admin/users
GET    /api/admin/users/{user}
PUT    /api/admin/users/{user}/progress
POST   /api/admin/users/{user}/items
DELETE /api/admin/users/{user}/items/{item}

GET    /api/admin/items
POST   /api/admin/items
PUT    /api/admin/items/{item}
DELETE /api/admin/items/{item}

GET    /api/admin/reward-rules
POST   /api/admin/reward-rules
PUT    /api/admin/reward-rules/{rule}
DELETE /api/admin/reward-rules/{rule}

GET    /api/admin/overlay-settings
PUT    /api/admin/overlay-settings
POST   /api/admin/overlay/events/global-animation
```

### Bot webhooks/API

```text
POST /api/bot/chat-message
POST /api/bot/viewer-active
POST /api/bot/command
POST /api/twitch/eventsub
```

---

## Security

Key considerations:

* Secure overlay URL with secret token
* Protect admin routes with policies/roles
* Store Twitch OAuth tokens securely
* Secure bot API with secret token
* Rate limiting on bot endpoints
* Rate limiting on avatar saves
* Input validation on all item configurations
* Never send admin data to overlay
* Never bundle secret tokens in frontend

---

## Performance

Considerations for OBS overlay:

* Use optimized PNG/WebP/SVG assets
* Limit number of visible avatars
* Cache avatar configurations
* Use lazy loading for assets
* Debounce avatar updates
* Use CSS transforms for animations
* Avoid heavy DOM updates
* Test overlay at 1080p60

---

## Roadmap

### Phase 1: MVP

* Laravel project setup
* Vue setup
* Twitch OAuth login
* Basic avatar editor
* Avatar items via database
* Inventory/unlocks
* OBS overlay URL
* Realtime avatar updates
* Basic Twitch bot with `!avatar` command

### Phase 2: Progression

* Watchtime tracking
* Points/coins system
* XP and levels
* Unlocks based on level
* Basic shop
* Admin item management

### Phase 3: Twitch events

* EventSub integration
* Follow rewards
* Sub rewards
* Bits rewards
* Raid effects
* Channel point redemptions

### Phase 4: Overlay polish

* Animations
* Spawn/despawn effects
* Dance/jump commands
* Collision/spacing
* Special event modes
* Performance tuning

### Phase 5: Admin & moderation

* Admin dashboard
* User management
* Manual rewards
* Moderation tools
* Logs
* Analytics

---

## Possible extensions

* Seasonal items
* Limited-time drops
* Event-exclusive cosmetics
* Trading between viewers
* Leaderboard
* Achievements
* Pet system
* Minigames in chat
* Boss events
* Community goals
* Avatar battles
* Dance party mode
* DJ/techno-themed cosmetics
* Beat-reactive animations
* Spotify/stream BPM integration
* Mobile-friendly avatar editor
* Public viewer profile pages
* Discord integration

---

## Example user flow

1. Viewer types `!avatar` in Twitch chat.
2. Bot replies with the avatar customization URL.
3. Viewer logs in with Twitch.
4. Viewer customizes outfit.
5. Laravel saves avatar configuration.
6. Laravel broadcasts realtime update.
7. OBS overlay receives update.
8. Avatar changes live at the bottom of the stream.

---

## Example support flow

1. Viewer subscribes on Twitch.
2. Twitch EventSub sends sub event to Laravel.
3. Laravel processes reward rule.
4. Viewer receives subscriber item and extra coins.
5. Overlay temporarily shows sub effect on avatar.
6. Bot sends a chat message with the unlock.

---

## Project goal

The goal of this project is to build a custom, extensible, and community-driven avatar overlay that rewards viewers for presence, interaction, and support. The tool should be more personal and flexible than existing solutions, with full control over design, rewards, events, and stream branding.
