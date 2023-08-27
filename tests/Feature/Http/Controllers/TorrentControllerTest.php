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

use App\Http\Controllers\TorrentController;
use App\Http\Requests\StoreTorrentRequest;
use App\Http\Requests\UpdateTorrentRequest;
use App\Models\Audit;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\FeaturedTorrent;
use App\Models\History;
use App\Models\Movie;
use App\Models\Region;
use App\Models\Resolution;
use App\Models\Torrent;
use App\Models\Tv;
use App\Models\Type;
use App\Models\User;

test('create returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $categories = Category::factory()->times(3)->create();
    $types = Type::factory()->times(3)->create();
    $resolutions = Resolution::factory()->times(3)->create();
    $regions = Region::factory()->times(3)->create();
    $distributors = Distributor::factory()->times(3)->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('torrents.create'));

    $response->assertOk();
    $response->assertViewIs('torrent.create');
    $response->assertViewHas('categories', $categories);
    $response->assertViewHas('types', $types);
    $response->assertViewHas('resolutions', $resolutions);
    $response->assertViewHas('regions', $regions);
    $response->assertViewHas('distributors', $distributors);
    $response->assertViewHas('user', $user);
    $response->assertViewHas('category_id');
    $response->assertViewHas('title');
    $response->assertViewHas('imdb');
    $response->assertViewHas('tmdb');
    $response->assertViewHas('mal');
    $response->assertViewHas('tvdb');
    $response->assertViewHas('igdb');

    // TODO: perform additional assertions
});

test('destroy returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $torrent = Torrent::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('torrents.destroy', ['id' => $torrent->id]));

    $response->assertOk();
    $this->assertModelMissing($torrent);

    // TODO: perform additional assertions
});

test('destroy aborts with a 403', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $torrent = Torrent::factory()->create();
    $user = User::factory()->create();

    // TODO: perform additional setup to trigger `abort_unless(403)`...

    $response = $this->actingAs($user)->delete(route('torrents.destroy', ['id' => $torrent->id]));

    $response->assertForbidden();
});

test('edit returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $torrent = Torrent::factory()->create();
    $categories = Category::factory()->times(3)->create();
    $types = Type::factory()->times(3)->create();
    $resolutions = Resolution::factory()->times(3)->create();
    $regions = Region::factory()->times(3)->create();
    $distributors = Distributor::factory()->times(3)->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('torrents.edit', ['id' => $torrent->id]));

    $response->assertOk();
    $response->assertViewIs('torrent.edit');
    $response->assertViewHas('categories', $categories);
    $response->assertViewHas('types', $types);
    $response->assertViewHas('resolutions', $resolutions);
    $response->assertViewHas('regions', $regions);
    $response->assertViewHas('distributors', $distributors);
    $response->assertViewHas('keywords');
    $response->assertViewHas('torrent', $torrent);
    $response->assertViewHas('user', $user);

    // TODO: perform additional assertions
});

test('edit aborts with a 403', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $torrent = Torrent::factory()->create();
    $categories = Category::factory()->times(3)->create();
    $types = Type::factory()->times(3)->create();
    $resolutions = Resolution::factory()->times(3)->create();
    $regions = Region::factory()->times(3)->create();
    $distributors = Distributor::factory()->times(3)->create();
    $user = User::factory()->create();

    // TODO: perform additional setup to trigger `abort_unless(403)`...

    $response = $this->actingAs($user)->get(route('torrents.edit', ['id' => $torrent->id]));

    $response->assertForbidden();
});

test('index returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('torrents.index'));

    $response->assertOk();
    $response->assertViewIs('torrent.index');

    // TODO: perform additional assertions
});

test('show returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $torrent = Torrent::factory()->create();
    $tv = Tv::factory()->create();
    $movie = Movie::factory()->create();
    $featuredTorrent = FeaturedTorrent::factory()->create();
    $history = History::factory()->create();
    $audits = Audit::factory()->times(3)->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('torrents.show', ['id' => $torrent->id]));

    $response->assertOk();
    $response->assertViewIs('torrent.show');
    $response->assertViewHas('torrent', $torrent);
    $response->assertViewHas('user', $user);
    $response->assertViewHas('personal_freeleech');
    $response->assertViewHas('freeleech_token');
    $response->assertViewHas('meta');
    $response->assertViewHas('trailer');
    $response->assertViewHas('platforms');
    $response->assertViewHas('total_tips');
    $response->assertViewHas('user_tips');
    $response->assertViewHas('featured');
    $response->assertViewHas('mediaInfo');
    $response->assertViewHas('last_seed_activity');
    $response->assertViewHas('playlists');
    $response->assertViewHas('audits', $audits);

    // TODO: perform additional assertions
});

test('store validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        TorrentController::class,
        'store',
        StoreTorrentRequest::class
    );
});

test('store returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $category = Category::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('torrents.store'), [
        // TODO: send request data
    ]);

    $response->assertOk();

    // TODO: perform additional assertions
});

test('update validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        TorrentController::class,
        'update',
        UpdateTorrentRequest::class
    );
});

test('update returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $torrent = Torrent::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('torrents.update', ['id' => $torrent->id]), [
        // TODO: send request data
    ]);

    $response->assertOk();

    // TODO: perform additional assertions
});

test('update aborts with a 403', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $torrent = Torrent::factory()->create();
    $user = User::factory()->create();

    // TODO: perform additional setup to trigger `abort_unless(403)`...

    $response = $this->actingAs($user)->patch(route('torrents.update', ['id' => $torrent->id]), [
        // TODO: send request data
    ]);

    $response->assertForbidden();
});

// test cases...