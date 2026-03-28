<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCardInfoRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\UserCard;

class TraineePaymentController extends Controller
{

    public function getPaymentsHistory(){

        $query = Payment::whereHas('booking', fn ($q) => $q->where('user_id', auth()->id()))
            ->orderByDesc('created_at');

        $payments = $query->paginate(15);
        return response()->json(['payments' => $payments]);
    }


    public function storeCard(StoreCardInfoRequest $request)
    {
        $user = auth()->user();

        $card = $user->cards()->create($request->validated());

        return response()->json([
            'message' => 'Card added successfully',
            'data' => $card
        ]);
    }
    public function getCards()
    {
        $cards = auth()->user()->cards;

        return response()->json([
            'data' => $cards
        ]);
    }
    public function destroyCard($id)
    {
        $card = auth()->user()->cards()->findOrFail($id);

        $card->delete();

        return response()->json([
            'message' => 'Card deleted successfully'
        ]);
    }
}
