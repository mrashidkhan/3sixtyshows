<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShowRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:shows',
            'category_id' => 'required|exists:show_categories,id',
            'venue_id' => 'required|exists:venues,id',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'nullable|numeric|min:0|max:999999.99',
            'available_tickets' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'status' => 'required|in:upcoming,ongoing,past,cancelled',
            'is_active' => 'required|boolean',
            'redirect' => 'boolean',
            'redirect_url' => 'nullable|url|required_if:redirect,true',
        ];
    }

    public function messages()
    {
        return [
            'start_date.after' => 'Show start date must be in the future.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'featured_image.required' => 'Please upload a featured image for the show.',
            'redirect_url.required_if' => 'Redirect URL is required when redirect is enabled.',
        ];
    }
}
