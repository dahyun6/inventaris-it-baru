<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pesan'       => 'required|string',
            'tipe'        => 'required|in:response,note,status_change',
            'status'      => 'nullable|in:Open,In Progress,Pending,Resolved,Closed',
            'assigned_to' => 'nullable|exists:users,id',
            'solusi'      => 'nullable|string',
        ];
    }
}
