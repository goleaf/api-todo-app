<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attachment\StoreAttachmentRequest;
use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    /**
     * Display a listing of attachments for a task.
     *
     * @param Request $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $query = $task->attachments();

        // Sort options
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $allowedSortFields = ['name', 'size', 'created_at'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        }

        $attachments = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $attachments->items(),
            'meta' => [
                'current_page' => $attachments->currentPage(),
                'last_page' => $attachments->lastPage(),
                'per_page' => $attachments->perPage(),
                'total' => $attachments->total()
            ]
        ]);
    }

    /**
     * Store a newly created attachment.
     *
     * @param StoreAttachmentRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreAttachmentRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $file = $request->file('file');
        $path = $file->store('attachments/' . $task->id, 'public');

        $attachment = new Attachment();
        $attachment->task_id = $task->id;
        $attachment->user_id = auth()->id();
        $attachment->name = $file->getClientOriginalName();
        $attachment->path = $path;
        $attachment->mime_type = $file->getMimeType();
        $attachment->size = $file->getSize();
        $attachment->save();

        return response()->json([
            'message' => 'Attachment uploaded successfully',
            'data' => $attachment
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified attachment.
     *
     * @param Attachment $attachment
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Attachment $attachment)
    {
        $this->authorize('view', $attachment);

        return response()->json([
            'data' => $attachment
        ]);
    }

    /**
     * Download the specified attachment.
     *
     * @param Attachment $attachment
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Attachment $attachment)
    {
        $this->authorize('view', $attachment);

        return Storage::disk('public')->download($attachment->path, $attachment->name);
    }

    /**
     * Remove the specified attachment.
     *
     * @param Attachment $attachment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment);

        // Delete the file from storage
        Storage::disk('public')->delete($attachment->path);

        // Delete the attachment record
        $attachment->delete();

        return response()->json([
            'message' => 'Attachment deleted successfully'
        ]);
    }
}
