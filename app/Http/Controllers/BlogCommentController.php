<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\Collection;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Statamic\Facades\Form;
use Statamic\Forms\Submission;
// use Statamic\Facades\Submission;

class BlogCommentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'comment' => 'required|string',
            'blog_entry' => 'required|string',
            'customer_entry' => 'required|string',
            'comment_date' => 'required|date',
        ]);

        // Manual check for blog_entry existence in 'blogs' collection
        if (! Entry::find($request->blog_entry)?->collectionHandle() === 'blogs') {
            $validator->errors()->add('blog_entry', 'Invalid blog entry ID.');
        }

        // Manual check for customer_entry existence in 'customers' collection
        if (! Entry::find($request->customer_entry)?->collectionHandle() === 'customers') {
            $validator->errors()->add('customer_entry', 'Invalid customer entry ID.');
        }

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $form = Form::find('blog_comments');

        if (! $form) {
            return response()->json(['message' => 'Form not found.'], 404);
        }

        $submission = $form->makeSubmission()
        ->data([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'comment' => $request->comment,
            'blog_entry' => $request->blog_entry,
            'customer_entry' => $request->customer_entry,
            'comment_date' => Carbon::parse($request->comment_date)->format('Y-m-d'),
        ]);

        $submission->save();

        return response()->json(['message' => 'Comment submitted successfully.']);
    }

}
