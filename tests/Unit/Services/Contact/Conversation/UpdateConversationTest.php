<?php

namespace Tests\Unit\Services\Contact\Conversation;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Contact\Contact;
use App\Models\Contact\Conversation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\Contact\Conversation\UpdateConversation;

class UpdateConversationTest extends TestCase
{
    use DatabaseTransactions;

    protected $jsonStructureConversation = [
        'account_id',
        'happened_at',
    ];

    public function test_it_updates_a_conversation()
    {
        $conversation = factory(Conversation::class)->create([
            'happened_at' => '2008-01-01',
        ]);

        $request = [
            'account_id' => $conversation->account->id,
            'conversation_id' => $conversation->id,
            'happened_at' => '2010-02-02',
        ];

        $conversationService = new UpdateConversation;
        $conversation = $conversationService->execute($request);

        $this->assertDatabaseHas('conversations', [
            'id' => $conversation->id,
            'happened_at' => '2010-02-02 00:00:00',
        ]);

        $this->assertInstanceOf(
            Conversation::class,
            $conversation
        );
    }

    public function test_it_fails_if_wrong_parameters_are_given()
    {
        $contact = factory(Contact::class)->create([]);

        $request = [
            'contact_id' => $contact->id,
            'happened_at' => Carbon::now(),
        ];

        $this->expectException(\Exception::class);

        $updateConversation = new UpdateConversation;
        $conversation = $updateConversation->execute($request);
    }

    public function test_it_throws_an_exception_if_contact_is_not_linked_to_account()
    {
        $conversation = factory(Conversation::class)->create([]);

        $request = [
            'account_id' => 231,
            'conversation_id' => $conversation->id,
            'happened_at' => '2010-02-02',
        ];

        $this->expectException(ModelNotFoundException::class);

        $updateConversation = new UpdateConversation;
        $conversation = $updateConversation->execute($request);
    }
}
