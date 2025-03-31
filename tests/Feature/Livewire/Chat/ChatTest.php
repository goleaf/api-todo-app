<?php

namespace Tests\Feature\Livewire\Chat;

use App\Events\MessageSent;
use App\Livewire\Chat\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatRoom as ChatRoomModel;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\Feature\Livewire\LivewireTestCase;

class ChatTest extends LivewireTestCase
{
    protected ChatRoomModel $room;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a chat room for testing
        $this->room = ChatRoomModel::create([
            'name' => 'Test Room',
            'description' => 'A test chat room',
        ]);

        // Add the test user to the chat room
        $this->room->users()->attach($this->user->id);
    }

    /** @test */
    public function chat_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(ChatRoom::class, ['roomId' => $this->room->id])
            ->assertSee('Test Room')
            ->assertSee('A test chat room');
    }

    /** @test */
    public function it_shows_chat_messages()
    {
        // Create messages in the chat room
        $messages = $this->createChatMessages($this->room, $this->user, 3);

        Livewire::actingAs($this->user)
            ->test(ChatRoom::class, ['roomId' => $this->room->id])
            ->assertSee($messages[0]->content)
            ->assertSee($messages[1]->content)
            ->assertSee($messages[2]->content);
    }

    /** @test */
    public function it_can_send_message()
    {
        // Mock the event facade to ensure the event is dispatched
        Event::fake();

        Livewire::actingAs($this->user)
            ->test(ChatRoom::class, ['roomId' => $this->room->id])
            ->set('messageText', 'Hello, world!')
            ->call('sendMessage')
            ->assertEmitted('message-sent')
            ->assertSet('messageText', ''); // Message input should be cleared

        // Verify message was saved to database
        $this->assertDatabaseHas('chat_messages', [
            'room_id' => $this->room->id,
            'user_id' => $this->user->id,
            'content' => 'Hello, world!',
        ]);

        // Verify event was dispatched
        Event::assertDispatched(MessageSent::class, function ($event) {
            return $event->message->content === 'Hello, world!'
                && $event->message->user_id === $this->user->id
                && $event->message->room_id === $this->room->id;
        });
    }

    /** @test */
    public function it_receives_new_messages_in_real_time()
    {
        // Setup component
        $component = Livewire::actingAs($this->user)
            ->test(ChatRoom::class, ['roomId' => $this->room->id])
            ->assertDontSee('New live message');

        // Create a message from another user
        $otherUser = User::factory()->create();
        $this->room->users()->attach($otherUser->id);

        $newMessage = ChatMessage::create([
            'room_id' => $this->room->id,
            'user_id' => $otherUser->id,
            'content' => 'New live message',
        ]);

        // Emit the event that would be triggered by WebSocket
        $component->emit('message-received', $newMessage->id)
            ->assertSee('New live message');
    }

    /** @test */
    public function it_shows_typing_indicator_when_user_is_typing()
    {
        // Create another user
        $otherUser = User::factory()->create([
            'name' => 'Jane Doe',
        ]);
        $this->room->users()->attach($otherUser->id);

        // Get component for current user
        $component = Livewire::actingAs($this->user)
            ->test(ChatRoom::class, ['roomId' => $this->room->id])
            ->assertDontSee('Jane Doe is typing...');

        // Emit the typing event from another user
        $component->emit('user-typing', [
            'user' => $otherUser->name,
            'room' => $this->room->id,
        ])
            ->assertSee('Jane Doe is typing...');

        // After 3 seconds (simulated), typing indicator should disappear
        $component->emit('refresh')
            ->assertDontSee('Jane Doe is typing...');
    }

    /** @test */
    public function it_broadcasts_typing_indicator_while_user_is_typing()
    {
        Event::fake();

        Livewire::actingAs($this->user)
            ->test(ChatRoom::class, ['roomId' => $this->room->id])
            ->set('messageText', 'H')
            ->assertEmitted('typing', [
                'user' => $this->user->name,
                'room' => $this->room->id,
            ]);

        Event::assertDispatched('typing', function ($eventName, $payload) {
            return $payload[0]['user'] === $this->user->name
                && $payload[0]['room'] === $this->room->id;
        });
    }

    /** @test */
    public function it_shows_unread_message_count()
    {
        // Create another user
        $otherUser = User::factory()->create();
        $this->room->users()->attach($otherUser->id);

        // Create unread messages by the other user while the main user was offline
        $unreadMessages = $this->createChatMessages($this->room, $otherUser, 3);

        // Set the last_read_at timestamp for this user to before the messages were created
        $this->room->users()->updateExistingPivot($this->user->id, [
            'last_read_at' => now()->subDay(),
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatRoom::class, ['roomId' => $this->room->id])
            ->assertSee('3 unread messages');
    }

    /** @test */
    public function it_can_load_older_messages()
    {
        // Create 25 messages
        $messages = $this->createChatMessages($this->room, $this->user, 25);

        // ChatRoom component typically loads only 15 most recent messages by default
        $component = Livewire::actingAs($this->user)
            ->test(ChatRoom::class, ['roomId' => $this->room->id]);

        // Most recent 15 messages should be visible
        $component->assertSee($messages[24]->content) // Most recent message
            ->assertSee($messages[10]->content) // 15th most recent message
            ->assertDontSee($messages[9]->content); // 16th most recent message (too old)

        // Load older messages
        $component->call('loadOlderMessages')
            ->assertSee($messages[9]->content) // Now visible
            ->assertSee($messages[0]->content); // Oldest message now visible
    }

    /** @test */
    public function it_updates_last_read_timestamp_when_room_is_viewed()
    {
        // Set initial timestamp to yesterday
        $this->room->users()->updateExistingPivot($this->user->id, [
            'last_read_at' => now()->subDay(),
        ]);

        // Get original timestamp
        $originalTimestamp = $this->room->users()
            ->where('user_id', $this->user->id)
            ->first()
            ->pivot
            ->last_read_at;

        // View the chat room
        Livewire::actingAs($this->user)
            ->test(ChatRoom::class, ['roomId' => $this->room->id]);

        // Get updated timestamp
        $updatedTimestamp = $this->room->users()
            ->where('user_id', $this->user->id)
            ->first()
            ->pivot
            ->last_read_at;

        // Timestamp should be updated to now
        $this->assertTrue($updatedTimestamp > $originalTimestamp);
    }

    /**
     * Helper to create test chat messages
     */
    private function createChatMessages(ChatRoomModel $room, User $user, int $count = 1)
    {
        $messages = [];

        for ($i = 0; $i < $count; $i++) {
            $messages[] = ChatMessage::create([
                'room_id' => $room->id,
                'user_id' => $user->id,
                'content' => "Test Message {$i}",
            ]);
        }

        return $messages;
    }
}
