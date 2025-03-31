<?php

namespace Tests\Feature\Livewire\Uploads;

use App\Http\Livewire\TaskAttachmentUpload;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $task;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a fake disk for testing uploads
        Storage::fake('attachments');

        // Create a user and task for testing
        $this->user = User::factory()->create();
        $this->task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function upload_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(TaskAttachmentUpload::class, ['taskId' => $this->task->id])
            ->assertSee('Upload Files')
            ->assertSee('Drop files here or click to upload');
    }

    /** @test */
    public function it_can_upload_a_file()
    {
        // Create a fake file
        $file = UploadedFile::fake()->create('document.pdf', 500); // 500kb

        // Test the upload functionality
        Livewire::actingAs($this->user)
            ->test(TaskAttachmentUpload::class, ['taskId' => $this->task->id])
            ->set('attachment', $file)
            ->call('save')
            ->assertEmitted('attachment-uploaded');

        // Verify the file was stored correctly
        Storage::disk('attachments')->assertExists($file->hashName());

        // Verify a database record was created
        $this->assertDatabaseHas('attachments', [
            'filename' => 'document.pdf',
            'task_id' => $this->task->id,
        ]);
    }

    /** @test */
    public function it_validates_file_size()
    {
        // Create a large file exceeding the maximum size
        $largeFile = UploadedFile::fake()->create('large.pdf', 12000); // 12MB

        // Test size validation
        Livewire::actingAs($this->user)
            ->test(TaskAttachmentUpload::class, ['taskId' => $this->task->id])
            ->set('attachment', $largeFile)
            ->call('save')
            ->assertHasErrors(['attachment' => 'max']);
    }

    /** @test */
    public function it_validates_file_type()
    {
        // Create a file with unsupported type
        $exeFile = UploadedFile::fake()->create('script.exe', 100);

        // Test type validation
        Livewire::actingAs($this->user)
            ->test(TaskAttachmentUpload::class, ['taskId' => $this->task->id])
            ->set('attachment', $exeFile)
            ->call('save')
            ->assertHasErrors(['attachment' => 'mimes']);
    }

    /** @test */
    public function it_can_upload_multiple_files()
    {
        // Create multiple fake files
        $files = [
            UploadedFile::fake()->create('doc1.pdf', 500),
            UploadedFile::fake()->image('image.jpg'),
        ];

        // Test multiple upload functionality
        Livewire::actingAs($this->user)
            ->test(TaskAttachmentUpload::class, ['taskId' => $this->task->id])
            ->set('attachments', $files)
            ->call('save')
            ->assertEmitted('attachments-uploaded', 2);

        // Verify all files were stored
        foreach ($files as $file) {
            Storage::disk('attachments')->assertExists($this->task->id.'/'.$file->hashName());
        }

        // Verify database records
        $this->assertEquals(2, $this->task->attachments()->count());
    }

    /** @test */
    public function it_shows_upload_progress()
    {
        $file = UploadedFile::fake()->create('document.pdf', 5000);

        $component = Livewire::test(TaskAttachmentUpload::class, ['taskId' => $this->task->id])
            ->set('attachments', [$file]);

        // Test different progress states
        $component->dispatch('upload:started')
            ->assertSee('Uploading...');

        $component->dispatch('upload:progress', ['progress' => 50])
            ->assertSeeHtml('width: 50%');

        $component->dispatch('upload:finished')
            ->assertSee('Processing...');

        $component->call('save')
            ->assertSee('Upload Complete');
    }

    /** @test */
    public function it_handles_upload_errors()
    {
        // Test component with error handling
        $component = Livewire::test(TaskAttachmentUpload::class, ['taskId' => $this->task->id]);

        // Simulate upload error
        $component->dispatch('upload:error', ['error' => 'Server error during file upload'])
            ->assertSee('Server error during file upload')
            ->assertSeeHtml('alert-danger');
    }

    /** @test */
    public function it_can_cancel_uploads()
    {
        // Create a fake file
        $file = UploadedFile::fake()->create('document.pdf', 1000);

        // Test component with cancel functionality
        $component = Livewire::test(TaskAttachmentUpload::class, ['taskId' => $this->task->id])
            ->set('attachments', [$file]);

        // Simulate upload started
        $component->dispatch('upload:started')
            ->assertSee('Uploading...');

        // Cancel the upload
        $component->call('cancelUpload', 'attachments')
            ->assertDontSee('Uploading...')
            ->assertSet('attachments', []);
    }

    /** @test */
    public function it_can_remove_a_file_before_upload()
    {
        // Create multiple fake files
        $files = [
            UploadedFile::fake()->create('doc1.pdf', 500),
            UploadedFile::fake()->image('image.jpg'),
        ];

        // Test file removal functionality
        $component = Livewire::test(TaskAttachmentUpload::class, ['taskId' => $this->task->id])
            ->set('attachments', $files);

        // Remove the first file
        $component->call('removeFile', 0)
            ->assertCount('attachments', 1);

        // Save the remaining file
        $component->call('save')
            ->assertEmitted('attachments-uploaded', 1);
    }

    /** @test */
    public function it_enforces_authorization()
    {
        // Create another user
        $otherUser = User::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 500);

        // Test authorization check
        Livewire::actingAs($otherUser)
            ->test(TaskAttachmentUpload::class, ['taskId' => $this->task->id])
            ->set('attachment', $file)
            ->call('save')
            ->assertForbidden();
    }

    /**
     * Create a set of test attachments for the specified task
     */
    private function createAttachments($task, $count = 3)
    {
        $attachments = [];

        for ($i = 1; $i <= $count; $i++) {
            $file = UploadedFile::fake()->create("document_{$i}.pdf", 500);

            // Store the file
            $path = Storage::disk('attachments')->putFile($task->id, $file);

            // Create a database record
            $attachments[] = $task->attachments()->create([
                'filename' => "document_{$i}.pdf",
                'path' => $path,
                'size' => $file->getSize(),
                'type' => 'application/pdf',
            ]);
        }

        return $attachments;
    }
}
